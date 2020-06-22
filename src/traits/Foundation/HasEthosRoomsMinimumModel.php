<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\RoomsMinimumClient;

    /**
    * Trait HasEthosRoomsMinimumModel
    *
    * Useful on models that have a related Ethos rooms minimum model. The relation is connected via the `ethos_rooms_minimum_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosRoomsMinimumModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosRoomsMinimum']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_rooms_minimum_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosRoomsMinimum()
      {
          if (!empty($this->ethos_rooms_minimum_id)) {
              $client = new RoomsMinimumClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_rooms_minimum_id;
              $cacheKey = 'ms.ethos-php.laravel.rooms-minimum.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_rooms_minimum_id)->data();
              }
          }

          return (object)[];
      }

    }