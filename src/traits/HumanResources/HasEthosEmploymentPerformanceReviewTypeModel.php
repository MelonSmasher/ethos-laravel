<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\EmploymentPerformanceReviewTypesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosEmploymentPerformanceReviewTypeModel
*
* Useful on models that have a related Ethos employment performance review type model. The relation is connected via the `ethos_employment_performance_review_type_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosEmploymentPerformanceReviewTypeModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosEmploymentPerformanceReviewTypeAttribute()
    {
        return $this->ethosEmploymentPerformanceReviewType();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_employment_performance_review_type_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosEmploymentPerformanceReviewType()
    {
        if (!empty($this->ethos_employment_performance_review_type_id)) {
            $client = new EmploymentPerformanceReviewTypesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_employment_performance_review_type_id;
            $cacheKey = 'ms.ethos-php.laravel.employment-performance-review-type.' . $ethosId;

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
            return (object)$client->readById($this->ethos_employment_performance_review_type_id)->data();
        }
        return (object)[];
    }
}