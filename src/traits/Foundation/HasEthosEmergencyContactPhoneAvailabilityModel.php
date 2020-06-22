<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\EmergencyContactPhoneAvailabilitiesClient;

    /**
    * Trait HasEthosEmergencyContactPhoneAvailabilityModel
    *
    * Useful on models that have a related Ethos emergency contact phone availability model. The relation is connected via the `ethos_emergency_contact_phone_availability_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosEmergencyContactPhoneAvailabilityModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosEmergencyContactPhoneAvailability']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_emergency_contact_phone_availability_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosEmergencyContactPhoneAvailability()
      {
          if (!empty($this->ethos_emergency_contact_phone_availability_id)) {
              $client = new EmergencyContactPhoneAvailabilitiesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_emergency_contact_phone_availability_id;
              $cacheKey = 'ms.ethos-php.laravel.emergency-contact-phone-availability.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_emergency_contact_phone_availability_id)->data();
              }
          }

          return (object)[];
      }

    }