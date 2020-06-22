<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


    use MelonSmasher\EthosPHP\Student\StudentAcademicPeriodsClient;

    /**
    * Trait HasEthosStudentAcademicPeriodModel
    *
    * Useful on models that have a related Ethos student academic period model. The relation is connected via the `ethos_student_academic_period_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Student
    */
    trait HasEthosStudentAcademicPeriodModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosStudentAcademicPeriod']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_student_academic_period_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosStudentAcademicPeriod()
      {
          if (!empty($this->ethos_student_academic_period_id)) {
              $client = new StudentAcademicPeriodsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_student_academic_period_id;
              $cacheKey = 'ms.ethos-php.laravel.student-academic-period.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_student_academic_period_id)->data();
              }
          }

          return (object)[];
      }

    }