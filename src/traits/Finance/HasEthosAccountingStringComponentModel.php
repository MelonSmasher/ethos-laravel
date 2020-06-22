<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\AccountingStringComponentsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosAccountingStringComponentModel
*
* Useful on models that have a related Ethos accounting string component model. The relation is connected via the `ethos_accounting_string_component_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAccountingStringComponentModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosAccountingStringComponent']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_accounting_string_component_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAccountingStringComponent()
    {
        if (!empty($this->ethos_accounting_string_component_id)) {
            $client = new AccountingStringComponentsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_accounting_string_component_id;
            $cacheKey = 'ms.ethos-php.laravel.accounting-string-component.' . $ethosId;

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
            return (object)$client->readById($this->ethos_accounting_string_component_id)->data();
        }
        return (object)[];
    }
}