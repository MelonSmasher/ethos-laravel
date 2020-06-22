<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\AccountingCodeCategoriesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosAccountingCodeCategoryModel
*
* Useful on models that have a related Ethos accounting code category model. The relation is connected via the `ethos_accounting_code_category_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAccountingCodeCategoryModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosAccountingCodeCategory']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_accounting_code_category_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAccountingCodeCategory()
    {
        if (!empty($this->ethos_accounting_code_category_id)) {
            $client = new AccountingCodeCategoriesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_accounting_code_category_id;
            $cacheKey = 'ms.ethos-php.laravel.accounting-code-category.' . $ethosId;

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
            return (object)$client->readById($this->ethos_accounting_code_category_id)->data();
        }
        return (object)[];
    }
}