<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\LanguageIsoCodesClient;

    /**
    * Trait HasEthosLanguageIsoCodeModel
    *
    * Useful on models that have a related Ethos language iso code model. The relation is connected via the `ethos_language_iso_code_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosLanguageIsoCodeModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosLanguageIsoCode']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_language_iso_code_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosLanguageIsoCode()
      {
          if (!empty($this->ethos_language_iso_code_id)) {
              $client = new LanguageIsoCodesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_language_iso_code_id;
              $cacheKey = 'ms.ethos-php.laravel.language-iso-code.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_language_iso_code_id)->data();
              }
          }

          return (object)[];
      }

    }