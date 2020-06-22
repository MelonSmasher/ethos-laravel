<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


    use MelonSmasher\EthosPHP\Recruitment\AdmissionApplicationWithdrawalReasonsClient;

    /**
    * Trait HasEthosAdmissionApplicationWithdrawalReasonModel
    *
    * Useful on models that have a related Ethos admission application withdrawal reason model. The relation is connected via the `ethos_admission_application_withdrawal_reason_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Recruitment
    */
    trait HasEthosAdmissionApplicationWithdrawalReasonModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosAdmissionApplicationWithdrawalReason']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_admission_application_withdrawal_reason_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosAdmissionApplicationWithdrawalReason()
      {
          if (!empty($this->ethos_admission_application_withdrawal_reason_id)) {
              $client = new AdmissionApplicationWithdrawalReasonsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_admission_application_withdrawal_reason_id;
              $cacheKey = 'ms.ethos-php.laravel.admission-application-withdrawal-reason.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_admission_application_withdrawal_reason_id)->data();
              }
          }

          return (object)[];
      }

    }