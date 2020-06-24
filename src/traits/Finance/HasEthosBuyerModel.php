<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\BuyersClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosBuyerModel
*
* Useful on models that have a related Ethos buyer model. The relation is connected via the `ethos_buyer_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosBuyerModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosBuyerAttribute()
    {
        return $this->ethosBuyer();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_buyer_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosBuyer()
    {
        if (!empty($this->ethos_buyer_id)) {
            $client = new BuyersClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_buyer_id;
            $cacheKey = 'ms.ethos-php.laravel.buyer.' . $ethosId;

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
            return (object)$client->readById($this->ethos_buyer_id)->data();
        }
        return (object)[];
    }
}