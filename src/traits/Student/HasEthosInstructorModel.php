<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\InstructorsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosInstructorModel
*
* Useful on models that have a related Ethos instructor model. The relation is connected via the `ethos_instructor_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosInstructorModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosInstructorAttribute()
    {
        return $this->ethosInstructor();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_instructor_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosInstructor()
    {
        if (!empty($this->ethos_instructor_id)) {
            $client = new InstructorsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_instructor_id;
            $cacheKey = 'ms.ethos-php.laravel.instructor.' . $ethosId;

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
            return (object)$client->readById($this->ethos_instructor_id)->data();
        }
        return (object)[];
    }
}