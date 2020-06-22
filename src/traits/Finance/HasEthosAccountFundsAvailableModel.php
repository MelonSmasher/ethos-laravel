<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\AccountFundsAvailableClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosAccountFundsAvailableModel
*
* Useful on models that have a related Ethos account funds available model. The relation is connected via the `ethos_account_funds_available_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAccountFundsAvailableModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosAccountFundsAvailable']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_account_funds_available_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAccountFundsAvailable()
    {
        if (!empty($this->ethos_account_funds_available_id)) {
            $client = new AccountFundsAvailableClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_account_funds_available_id;
            $cacheKey = 'ms.ethos-php.laravel.account-funds-available.' . $ethosId;

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
            return (object)$client->readById($this->ethos_account_funds_available_id)->data();
        }
        return (object)[];
    }
}