<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


    use MelonSmasher\EthosPHP\Student\BillingOverrideReasonsClient;

    /**
    * Trait HasEthosBillingOverrideReasonModel
    *
    * Useful on models that have a related Ethos billing override reason model. The relation is connected via the `ethos_billing_override_reason_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Student
    */
    trait HasEthosBillingOverrideReasonModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosBillingOverrideReason']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_billing_override_reason_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosBillingOverrideReason()
      {
          if (!empty($this->ethos_billing_override_reason_id)) {
              $client = new BillingOverrideReasonsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_billing_override_reason_id;
              $cacheKey = 'ms.ethos-php.laravel.billing-override-reason.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_billing_override_reason_id)->data();
              }
          }

          return (object)[];
      }

    }