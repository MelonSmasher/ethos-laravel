<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\EmploymentFrequenciesClient;

/**
* Trait HasEthosEmploymentFrequencyModel
*
* Useful on models that have a related Ethos employment frequency model. The relation is connected via the `ethos_employment_frequency_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosEmploymentFrequencyModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosEmploymentFrequency']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_employment_frequency_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosEmploymentFrequency()
    {
        if (!empty($this->ethos_employment_frequency_id)) {
            $client = new EmploymentFrequenciesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_employment_frequency_id;
            $cacheKey = 'ms.ethos-php.laravel.employment-frequency.' . $ethosId;

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
            return (object)$client->readById($this->ethos_employment_frequency_id)->data();
        }
        return (object)[];
    }
}