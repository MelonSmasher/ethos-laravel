<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\JobApplicationStatusesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosJobApplicationStatusModel
*
* Useful on models that have a related Ethos job application status model. The relation is connected via the `ethos_job_application_status_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosJobApplicationStatusModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosJobApplicationStatusAttribute()
    {
        return $this->ethosJobApplicationStatus();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_job_application_status_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosJobApplicationStatus()
    {
        if (!empty($this->ethos_job_application_status_id)) {
            $client = new JobApplicationStatusesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_job_application_status_id;
            $cacheKey = 'ms.ethos-php.laravel.job-application-status.' . $ethosId;

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
            return (object)$client->readById($this->ethos_job_application_status_id)->data();
        }
        return (object)[];
    }
}