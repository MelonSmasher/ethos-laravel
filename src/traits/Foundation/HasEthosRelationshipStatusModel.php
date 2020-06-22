<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\RelationshipStatusesClient;

/**
* Trait HasEthosRelationshipStatusModel
*
* Useful on models that have a related Ethos relationship status model. The relation is connected via the `ethos_relationship_status_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosRelationshipStatusModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosRelationshipStatus']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_relationship_status_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosRelationshipStatus()
    {
        if (!empty($this->ethos_relationship_status_id)) {
            $client = new RelationshipStatusesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_relationship_status_id;
            $cacheKey = 'ms.ethos-php.laravel.relationship-status.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                $model = Cache::get($cacheKey, null);
                if (empty($model)) {
                    $model = serialize($client->readById($ethosId)->data());
                    Cache::put($cacheKey, $cacheTTL, $model);
                }
                return (object)unserialize($model);
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_relationship_status_id)->data();
        }
        return (object)[];
    }
}