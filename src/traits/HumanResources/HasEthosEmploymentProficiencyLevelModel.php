<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


    use MelonSmasher\EthosPHP\HumanResources\EmploymentProficiencyLevelsClient;

    /**
    * Trait HasEthosEmploymentProficiencyLevelModel
    *
    * Useful on models that have a related Ethos employment proficiency level model. The relation is connected via the `ethos_employment_proficiency_level_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\HumanResources
    */
    trait HasEthosEmploymentProficiencyLevelModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosEmploymentProficiencyLevel']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_employment_proficiency_level_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosEmploymentProficiencyLevel()
      {
          if (!empty($this->ethos_employment_proficiency_level_id)) {
              $client = new EmploymentProficiencyLevelsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_employment_proficiency_level_id;
              $cacheKey = 'ms.ethos-php.laravel.employment-proficiency-level.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_employment_proficiency_level_id)->data();
              }
          }

          return (object)[];
      }

    }