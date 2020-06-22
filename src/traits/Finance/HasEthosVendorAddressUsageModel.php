<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


    use MelonSmasher\EthosPHP\Finance\VendorAddressUsagesClient;

    /**
    * Trait HasEthosVendorAddressUsageModel
    *
    * Useful on models that have a related Ethos vendor address usage model. The relation is connected via the `ethos_vendor_address_usage_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Finance
    */
    trait HasEthosVendorAddressUsageModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosVendorAddressUsage']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_vendor_address_usage_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosVendorAddressUsage()
      {
          if (!empty($this->ethos_vendor_address_usage_id)) {
              $client = new VendorAddressUsagesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_vendor_address_usage_id;
              $cacheKey = 'ms.ethos-php.laravel.vendor-address-usage.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_vendor_address_usage_id)->data();
              }
          }

          return (object)[];
      }

    }