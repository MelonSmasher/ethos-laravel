<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\PayScalesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosPayScaleModel
*
* Useful on models that have a related Ethos pay scale model. The relation is connected via the `ethos_pay_scale_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosPayScaleModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosPayScaleAttribute()
    {
        return $this->ethosPayScale();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_pay_scale_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosPayScale()
    {
        if (!empty($this->ethos_pay_scale_id)) {
            $client = new PayScalesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_pay_scale_id;
            $cacheKey = 'ms.ethos-php.laravel.pay-scale.' . $ethosId;

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
            return (object)$client->readById($this->ethos_pay_scale_id)->data();
        }
        return (object)[];
    }
}