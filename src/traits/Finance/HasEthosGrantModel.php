<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\GrantsClient;

/**
* Trait HasEthosGrantModel
*
* Useful on models that have a related Ethos grant model. The relation is connected via the `ethos_grant_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosGrantModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosGrant']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_grant_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosGrant()
    {
        if (!empty($this->ethos_grant_id)) {
            $client = new GrantsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_grant_id;
            $cacheKey = 'ms.ethos-php.laravel.grant.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                return (object)unserialize(
                    Cache::remember($cacheKey, $cacheTTL, function () use ($client, $ethosId) {
                        return serialize($client->readById($ethosId)->data());
                    })
                );
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_grant_id)->data();
        }
        return (object)[];
    }
}