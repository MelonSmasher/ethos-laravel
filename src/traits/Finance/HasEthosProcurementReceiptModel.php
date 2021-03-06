<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\ProcurementReceiptsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosProcurementReceiptModel
*
* Useful on models that have a related Ethos procurement receipt model. The relation is connected via the `ethos_procurement_receipt_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosProcurementReceiptModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosProcurementReceiptAttribute()
    {
        return $this->ethosProcurementReceipt();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_procurement_receipt_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosProcurementReceipt()
    {
        if (!empty($this->ethos_procurement_receipt_id)) {
            $client = new ProcurementReceiptsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_procurement_receipt_id;
            $cacheKey = 'ms.ethos-php.laravel.procurement-receipt.' . $ethosId;

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
            return (object)$client->readById($this->ethos_procurement_receipt_id)->data();
        }
        return (object)[];
    }
}