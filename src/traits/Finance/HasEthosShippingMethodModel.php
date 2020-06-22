<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


    use MelonSmasher\EthosPHP\Finance\ShippingMethodsClient;

    /**
    * Trait HasEthosShippingMethodModel
    *
    * Useful on models that have a related Ethos shipping method model. The relation is connected via the `ethos_shipping_method_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Finance
    */
    trait HasEthosShippingMethodModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosShippingMethod']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_shipping_method_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosShippingMethod()
      {
          if (!empty($this->ethos_shipping_method_id)) {
              $client = new ShippingMethodsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_shipping_method_id;
              $cacheKey = 'ms.ethos-php.laravel.shipping-method.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_shipping_method_id)->data();
              }
          }

          return (object)[];
      }

    }