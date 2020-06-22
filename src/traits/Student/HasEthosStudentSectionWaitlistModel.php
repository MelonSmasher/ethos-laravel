<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


    use MelonSmasher\EthosPHP\Student\StudentSectionWaitlistsClient;

    /**
    * Trait HasEthosStudentSectionWaitlistModel
    *
    * Useful on models that have a related Ethos student section waitlist model. The relation is connected via the `ethos_student_section_waitlist_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Student
    */
    trait HasEthosStudentSectionWaitlistModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosStudentSectionWaitlist']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_student_section_waitlist_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosStudentSectionWaitlist()
      {
          if (!empty($this->ethos_student_section_waitlist_id)) {
              $client = new StudentSectionWaitlistsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_student_section_waitlist_id;
              $cacheKey = 'ms.ethos-php.laravel.student-section-waitlist.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_student_section_waitlist_id)->data();
              }
          }

          return (object)[];
      }

    }