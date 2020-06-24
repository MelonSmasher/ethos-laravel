<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\VendorHoldReasonsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosVendorHoldReasonModel
*
* Useful on models that have a related Ethos vendor hold reason model. The relation is connected via the `ethos_vendor_hold_reason_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosVendorHoldReasonModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosVendorHoldReasonAttribute()
    {
        return $this->ethosVendorHoldReason();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_vendor_hold_reason_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosVendorHoldReason()
    {
        if (!empty($this->ethos_vendor_hold_reason_id)) {
            $client = new VendorHoldReasonsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_vendor_hold_reason_id;
            $cacheKey = 'ms.ethos-php.laravel.vendor-hold-reason.' . $ethosId;

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
            return (object)$client->readById($this->ethos_vendor_hold_reason_id)->data();
        }
        return (object)[];
    }
}