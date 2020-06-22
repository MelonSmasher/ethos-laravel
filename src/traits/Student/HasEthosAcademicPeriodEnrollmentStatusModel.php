<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


    use MelonSmasher\EthosPHP\Student\AcademicPeriodEnrollmentStatusesClient;

    /**
    * Trait HasEthosAcademicPeriodEnrollmentStatusModel
    *
    * Useful on models that have a related Ethos academic period enrollment status model. The relation is connected via the `ethos_academic_period_enrollment_status_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Student
    */
    trait HasEthosAcademicPeriodEnrollmentStatusModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosAcademicPeriodEnrollmentStatus']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_academic_period_enrollment_status_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosAcademicPeriodEnrollmentStatus()
      {
          if (!empty($this->ethos_academic_period_enrollment_status_id)) {
              $client = new AcademicPeriodEnrollmentStatusesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_academic_period_enrollment_status_id;
              $cacheKey = 'ms.ethos-php.laravel.academic-period-enrollment-status.' . $ethosId;

              // If we are caching the result attempt to pull from the cache
              // If its not in the cache store it for next time
              // Expiry is controlled by $cacheTTL
              if ($cacheTTL) {
                  return (object)unserialize(
                      Cache::remember($cacheKey, $cacheTTL, function () use ($client, $ethosId) {
                          return serialize($client->readById($ethosId)->data());
                      })
                  );
              } else {
                  // If the cache $cacheTTL is 0 or false just pull the object
                  return (object)$client->readById($this->ethos_academic_period_enrollment_status_id)->data();
              }
          }

          return (object)[];
      }

    }