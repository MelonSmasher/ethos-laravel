<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\RoomRatesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosRoomRateModel
*
* Useful on models that have a related Ethos room rate model. The relation is connected via the `ethos_room_rate_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosRoomRateModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosRoomRateAttribute()
    {
        return $this->ethosRoomRate();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_room_rate_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosRoomRate()
    {
        if (!empty($this->ethos_room_rate_id)) {
            $client = new RoomRatesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_room_rate_id;
            $cacheKey = 'ms.ethos-php.laravel.room-rate.' . $ethosId;

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
            return (object)$client->readById($this->ethos_room_rate_id)->data();
        }
        return (object)[];
    }
}