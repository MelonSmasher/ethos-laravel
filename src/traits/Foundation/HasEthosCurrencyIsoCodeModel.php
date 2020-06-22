<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\CurrencyIsoCodesClient;

    /**
    * Trait HasEthosCurrencyIsoCodeModel
    *
    * Useful on models that have a related Ethos currency iso code model. The relation is connected via the `ethos_currency_iso_code_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosCurrencyIsoCodeModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosCurrencyIsoCode']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_currency_iso_code_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosCurrencyIsoCode()
      {
          if (!empty($this->ethos_currency_iso_code_id)) {
              $client = new CurrencyIsoCodesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_currency_iso_code_id;
              $cacheKey = 'ms.ethos-php.laravel.currency-iso-code.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_currency_iso_code_id)->data();
              }
          }

          return (object)[];
      }

    }