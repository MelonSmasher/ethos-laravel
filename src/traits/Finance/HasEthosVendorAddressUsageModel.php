<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\VendorAddressUsagesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosVendorAddressUsageModel
*
* Useful on models that have a related Ethos vendor address usage model. The relation is connected via the `ethos_vendor_address_usage_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosVendorAddressUsageModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosVendorAddressUsageAttribute()
    {
        return $this->ethosVendorAddressUsage();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_vendor_address_usage_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosVendorAddressUsage()
    {
        if (!empty($this->ethos_vendor_address_usage_id)) {
            $client = new VendorAddressUsagesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_vendor_address_usage_id;
            $cacheKey = 'ms.ethos-php.laravel.vendor-address-usage.' . $ethosId;

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
            return (object)$client->readById($this->ethos_vendor_address_usage_id)->data();
        }
        return (object)[];
    }
}