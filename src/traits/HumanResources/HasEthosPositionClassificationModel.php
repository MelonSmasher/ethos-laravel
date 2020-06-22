<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


    use MelonSmasher\EthosPHP\HumanResources\PositionClassificationsClient;

    /**
    * Trait HasEthosPositionClassificationModel
    *
    * Useful on models that have a related Ethos position classification model. The relation is connected via the `ethos_position_classification_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\HumanResources
    */
    trait HasEthosPositionClassificationModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosPositionClassification']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_position_classification_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosPositionClassification()
      {
          if (!empty($this->ethos_position_classification_id)) {
              $client = new PositionClassificationsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_position_classification_id;
              $cacheKey = 'ms.ethos-php.laravel.position-classification.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_position_classification_id)->data();
              }
          }

          return (object)[];
      }

    }