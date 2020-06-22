<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


    use MelonSmasher\EthosPHP\Student\CourseTransferStatusesClient;

    /**
    * Trait HasEthosCourseTransferStatusModel
    *
    * Useful on models that have a related Ethos course transfer status model. The relation is connected via the `ethos_course_transfer_status_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Student
    */
    trait HasEthosCourseTransferStatusModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosCourseTransferStatus']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_course_transfer_status_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosCourseTransferStatus()
      {
          if (!empty($this->ethos_course_transfer_status_id)) {
              $client = new CourseTransferStatusesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_course_transfer_status_id;
              $cacheKey = 'ms.ethos-php.laravel.course-transfer-status.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_course_transfer_status_id)->data();
              }
          }

          return (object)[];
      }

    }