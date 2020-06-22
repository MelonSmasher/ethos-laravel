<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\VeteranStatusesClient;

/**
* Trait HasEthosVeteranStatusModel
*
* Useful on models that have a related Ethos veteran status model. The relation is connected via the `ethos_veteran_status_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosVeteranStatusModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosVeteranStatus']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_veteran_status_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosVeteranStatus()
    {
        if (!empty($this->ethos_veteran_status_id)) {
            $client = new VeteranStatusesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_veteran_status_id;
            $cacheKey = 'ms.ethos-php.laravel.veteran-status.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                return (object)unserialize(
                    Cache::remember($cacheKey, $cacheTTL, function () use ($client, $ethosId) {
                        return serialize($client->readById($ethosId)->data());
                    })
                );
            } else {
                // If the cache $cacheTTL is 0 or false just pull the object
                return (object)$client->readById($this->ethos_veteran_status_id)->data();
            }
        }
        return (object)[];
    }
}