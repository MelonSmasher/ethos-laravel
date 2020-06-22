<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\CountriesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosCountryModel
*
* Useful on models that have a related Ethos country model. The relation is connected via the `ethos_country_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosCountryModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosCountry']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_country_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosCountry()
    {
        if (!empty($this->ethos_country_id)) {
            $client = new CountriesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_country_id;
            $cacheKey = 'ms.ethos-php.laravel.country.' . $ethosId;

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
            return (object)$client->readById($this->ethos_country_id)->data();
        }
        return (object)[];
    }
}