<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\ExternalEmploymentPositionsClient;

/**
* Trait HasEthosExternalEmploymentPositionModel
*
* Useful on models that have a related Ethos external employment position model. The relation is connected via the `ethos_external_employment_position_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosExternalEmploymentPositionModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosExternalEmploymentPosition']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_external_employment_position_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosExternalEmploymentPosition()
    {
        if (!empty($this->ethos_external_employment_position_id)) {
            $client = new ExternalEmploymentPositionsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_external_employment_position_id;
            $cacheKey = 'ms.ethos-php.laravel.external-employment-position.' . $ethosId;

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
            return (object)$client->readById($this->ethos_external_employment_position_id)->data();
        }
        return (object)[];
    }
}