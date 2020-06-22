<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\EducationalInstitutionUnitsClient;

    /**
    * Trait HasEthosEducationalInstitutionUnitModel
    *
    * Useful on models that have a related Ethos educational institution unit model. The relation is connected via the `ethos_educational_institution_unit_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosEducationalInstitutionUnitModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosEducationalInstitutionUnit']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_educational_institution_unit_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosEducationalInstitutionUnit()
      {
          if (!empty($this->ethos_educational_institution_unit_id)) {
              $client = new EducationalInstitutionUnitsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_educational_institution_unit_id;
              $cacheKey = 'ms.ethos-php.laravel.educational-institution-unit.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_educational_institution_unit_id)->data();
              }
          }

          return (object)[];
      }

    }