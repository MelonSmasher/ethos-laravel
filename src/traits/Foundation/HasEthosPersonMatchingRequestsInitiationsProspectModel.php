<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\PersonMatchingRequestsInitiationsProspectsClient;

    /**
    * Trait HasEthosPersonMatchingRequestsInitiationsProspectModel
    *
    * Useful on models that have a related Ethos person matching requests initiations prospect model. The relation is connected via the `ethos_person_matching_requests_initiations_prospect_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosPersonMatchingRequestsInitiationsProspectModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosPersonMatchingRequestsInitiationsProspect']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_person_matching_requests_initiations_prospect_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosPersonMatchingRequestsInitiationsProspect()
      {
          if (!empty($this->ethos_person_matching_requests_initiations_prospect_id)) {
              $client = new PersonMatchingRequestsInitiationsProspectsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_person_matching_requests_initiations_prospect_id;
              $cacheKey = 'ms.ethos-php.laravel.person-matching-requests-initiations-prospect.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_person_matching_requests_initiations_prospect_id)->data();
              }
          }

          return (object)[];
      }

    }