<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\InstructionalEventsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosInstructionalEventModel
*
* Useful on models that have a related Ethos instructional event model. The relation is connected via the `ethos_instructional_event_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosInstructionalEventModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosInstructionalEventAttribute()
    {
        return $this->ethosInstructionalEvent();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_instructional_event_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosInstructionalEvent()
    {
        if (!empty($this->ethos_instructional_event_id)) {
            $client = new InstructionalEventsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_instructional_event_id;
            $cacheKey = 'ms.ethos-php.laravel.instructional-event.' . $ethosId;

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
            return (object)$client->readById($this->ethos_instructional_event_id)->data();
        }
        return (object)[];
    }
}