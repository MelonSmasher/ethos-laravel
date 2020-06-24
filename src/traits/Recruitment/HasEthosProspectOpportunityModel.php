<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


use MelonSmasher\EthosPHP\Recruitment\ProspectOpportunitiesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosProspectOpportunityModel
*
* Useful on models that have a related Ethos prospect opportunity model. The relation is connected via the `ethos_prospect_opportunity_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Recruitment
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosProspectOpportunityModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosProspectOpportunityAttribute()
    {
        return $this->ethosProspectOpportunity();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_prospect_opportunity_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosProspectOpportunity()
    {
        if (!empty($this->ethos_prospect_opportunity_id)) {
            $client = new ProspectOpportunitiesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_prospect_opportunity_id;
            $cacheKey = 'ms.ethos-php.laravel.prospect-opportunity.' . $ethosId;

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
            return (object)$client->readById($this->ethos_prospect_opportunity_id)->data();
        }
        return (object)[];
    }
}