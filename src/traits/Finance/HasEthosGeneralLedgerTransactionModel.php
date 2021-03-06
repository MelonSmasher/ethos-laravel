<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\GeneralLedgerTransactionsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosGeneralLedgerTransactionModel
*
* Useful on models that have a related Ethos general ledger transaction model. The relation is connected via the `ethos_general_ledger_transaction_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosGeneralLedgerTransactionModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosGeneralLedgerTransactionAttribute()
    {
        return $this->ethosGeneralLedgerTransaction();
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
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_general_ledger_transaction_id;
            $cacheKey = 'ms.ethos-php.laravel.general-ledger-transaction.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                $model = Cache::get($cacheKey, null);
                if (empty($model)) {
                    $model = serialize($client->readById($ethosId)->data());
                    Cache::put($cacheKey, $model, $cacheTTL);
                }
                return (object)unserialize($model);
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_general_ledger_transaction_id)->data();
        }
        return (object)[];
    }
}