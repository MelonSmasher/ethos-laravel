<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


    use MelonSmasher\EthosPHP\Finance\VendorsClient;

    /**
    * Trait HasEthosVendorModel
    *
    * Useful on models that have a related Ethos vendor model. The relation is connected via the `ethos_vendor_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Finance
    */
    trait HasEthosVendorModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosVendor']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_vendor_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosVendor()
      {
          if (!empty($this->ethos_vendor_id)) {
              $client = new VendorsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_vendor_id;
              $cacheKey = 'ms.ethos-php.laravel.vendor.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_vendor_id)->data();
              }
          }

          return (object)[];
      }

    }