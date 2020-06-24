<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\JobApplicationSourcesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosJobApplicationSourceModel
*
* Useful on models that have a related Ethos job application source model. The relation is connected via the `ethos_job_application_source_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosJobApplicationSourceModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosJobApplicationSourceAttribute()
    {
        return $this->ethosJobApplicationSource();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_job_application_source_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosJobApplicationSource()
    {
        if (!empty($this->ethos_job_application_source_id)) {
            $client = new JobApplicationSourcesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_job_application_source_id;
            $cacheKey = 'ms.ethos-php.laravel.job-application-source.' . $ethosId;

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
            return (object)$client->readById($this->ethos_job_application_source_id)->data();
        }
        return (object)[];
    }
}