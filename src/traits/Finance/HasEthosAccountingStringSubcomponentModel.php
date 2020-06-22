<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\AccountingStringSubcomponentsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosAccountingStringSubcomponentModel
*
* Useful on models that have a related Ethos accounting string subcomponent model. The relation is connected via the `ethos_accounting_string_subcomponent_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAccountingStringSubcomponentModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosAccountingStringSubcomponent']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_accounting_string_subcomponent_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAccountingStringSubcomponent()
    {
        if (!empty($this->ethos_accounting_string_subcomponent_id)) {
            $client = new AccountingStringSubcomponentsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_accounting_string_subcomponent_id;
            $cacheKey = 'ms.ethos-php.laravel.accounting-string-subcomponent.' . $ethosId;

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
            return (object)$client->readById($this->ethos_accounting_string_subcomponent_id)->data();
        }
        return (object)[];
    }
}