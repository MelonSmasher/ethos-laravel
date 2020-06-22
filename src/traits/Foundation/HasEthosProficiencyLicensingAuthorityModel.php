<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\ProficiencyLicensingAuthoritiesClient;

    /**
    * Trait HasEthosProficiencyLicensingAuthorityModel
    *
    * Useful on models that have a related Ethos proficiency licensing authority model. The relation is connected via the `ethos_proficiency_licensing_authority_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosProficiencyLicensingAuthorityModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosProficiencyLicensingAuthority']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_proficiency_licensing_authority_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosProficiencyLicensingAuthority()
      {
          if (!empty($this->ethos_proficiency_licensing_authority_id)) {
              $client = new ProficiencyLicensingAuthoritiesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_proficiency_licensing_authority_id;
              $cacheKey = 'ms.ethos-php.laravel.proficiency-licensing-authority.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_proficiency_licensing_authority_id)->data();
              }
          }

          return (object)[];
      }

    }