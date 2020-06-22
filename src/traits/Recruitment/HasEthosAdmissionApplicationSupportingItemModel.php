<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


    use MelonSmasher\EthosPHP\Recruitment\AdmissionApplicationSupportingItemsClient;

    /**
    * Trait HasEthosAdmissionApplicationSupportingItemModel
    *
    * Useful on models that have a related Ethos admission application supporting item model. The relation is connected via the `ethos_admission_application_supporting_item_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Recruitment
    */
    trait HasEthosAdmissionApplicationSupportingItemModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosAdmissionApplicationSupportingItem']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_admission_application_supporting_item_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosAdmissionApplicationSupportingItem()
      {
          if (!empty($this->ethos_admission_application_supporting_item_id)) {
              $client = new AdmissionApplicationSupportingItemsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_admission_application_supporting_item_id;
              $cacheKey = 'ms.ethos-php.laravel.admission-application-supporting-item.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_admission_application_supporting_item_id)->data();
              }
          }

          return (object)[];
      }

    }