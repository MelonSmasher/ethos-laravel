<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


    use MelonSmasher\EthosPHP\Student\InstructorStaffTypesClient;

    /**
    * Trait HasEthosInstructorStaffTypeModel
    *
    * Useful on models that have a related Ethos instructor staff type model. The relation is connected via the `ethos_instructor_staff_type_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Student
    */
    trait HasEthosInstructorStaffTypeModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosInstructorStaffType']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_instructor_staff_type_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosInstructorStaffType()
      {
          if (!empty($this->ethos_instructor_staff_type_id)) {
              $client = new InstructorStaffTypesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_instructor_staff_type_id;
              $cacheKey = 'ms.ethos-php.laravel.instructor-staff-type.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_instructor_staff_type_id)->data();
              }
          }

          return (object)[];
      }

    }