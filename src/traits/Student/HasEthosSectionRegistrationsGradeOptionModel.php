<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


    use MelonSmasher\EthosPHP\Student\SectionRegistrationsGradeOptionsClient;

    /**
    * Trait HasEthosSectionRegistrationsGradeOptionModel
    *
    * Useful on models that have a related Ethos section registrations grade option model. The relation is connected via the `ethos_section_registrations_grade_option_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Student
    */
    trait HasEthosSectionRegistrationsGradeOptionModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosSectionRegistrationsGradeOption']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_section_registrations_grade_option_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosSectionRegistrationsGradeOption()
      {
          if (!empty($this->ethos_section_registrations_grade_option_id)) {
              $client = new SectionRegistrationsGradeOptionsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_section_registrations_grade_option_id;
              $cacheKey = 'ms.ethos-php.laravel.section-registrations-grade-option.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_section_registrations_grade_option_id)->data();
              }
          }

          return (object)[];
      }

    }