<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


    use MelonSmasher\EthosPHP\Finance\GeneralLedgerTransactionsClient;

    /**
    * Trait HasEthosGeneralLedgerTransactionModel
    *
    * Useful on models that have a related Ethos general ledger transaction model. The relation is connected via the `ethos_general_ledger_transaction_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Finance
    */
    trait HasEthosGeneralLedgerTransactionModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosGeneralLedgerTransaction']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_general_ledger_transaction_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosGeneralLedgerTransaction()
      {
          if (!empty($this->ethos_general_ledger_transaction_id)) {
              $client = new GeneralLedgerTransactionsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_general_ledger_transaction_id;
              $cacheKey = 'ms.ethos-php.laravel.general-ledger-transaction.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_general_ledger_transaction_id)->data();
              }
          }

          return (object)[];
      }

    }