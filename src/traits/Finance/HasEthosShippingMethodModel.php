<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\ShippingMethodsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosShippingMethodModel
*
* Useful on models that have a related Ethos shipping method model. The relation is connected via the `ethos_shipping_method_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosShippingMethodModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosShippingMethodAttribute()
    {
        return $this->ethosShippingMethod();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_shipping_method_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosShippingMethod()
    {
        if (!empty($this->ethos_shipping_method_id)) {
            $client = new ShippingMethodsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_shipping_method_id;
            $cacheKey = 'ms.ethos-php.laravel.shipping-method.' . $ethosId;

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
            return (object)$client->readById($this->ethos_shipping_method_id)->data();
        }
        return (object)[];
    }
}