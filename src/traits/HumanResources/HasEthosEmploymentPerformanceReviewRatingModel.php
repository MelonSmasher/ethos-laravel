<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\EmploymentPerformanceReviewRatingsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosEmploymentPerformanceReviewRatingModel
*
* Useful on models that have a related Ethos employment performance review rating model. The relation is connected via the `ethos_employment_performance_review_rating_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosEmploymentPerformanceReviewRatingModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosEmploymentPerformanceReviewRating']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_employment_performance_review_rating_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosEmploymentPerformanceReviewRating()
    {
        if (!empty($this->ethos_employment_performance_review_rating_id)) {
            $client = new EmploymentPerformanceReviewRatingsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_employment_performance_review_rating_id;
            $cacheKey = 'ms.ethos-php.laravel.employment-performance-review-rating.' . $ethosId;

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
            return (object)$client->readById($this->ethos_employment_performance_review_rating_id)->data();
        }
        return (object)[];
    }
}