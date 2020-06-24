<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\SubregionsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosSubregionModel
*
* Useful on models that have a related Ethos subregion model. The relation is connected via the `ethos_subregion_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosSubregionModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosSubregionAttribute()
    {
        return $this->ethosSubregion();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_subregion_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosSubregion()
    {
        if (!empty($this->ethos_subregion_id)) {
            $client = new SubregionsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_subregion_id;
            $cacheKey = 'ms.ethos-php.laravel.subregion.' . $ethosId;

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
            return (object)$client->readById($this->ethos_subregion_id)->data();
        }
        return (object)[];
    }
}