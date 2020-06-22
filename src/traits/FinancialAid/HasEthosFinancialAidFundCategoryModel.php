<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\FinancialAid;


use MelonSmasher\EthosPHP\FinancialAid\FinancialAidFundCategoriesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosFinancialAidFundCategoryModel
*
* Useful on models that have a related Ethos financial aid fund category model. The relation is connected via the `ethos_financial_aid_fund_category_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\FinancialAid
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosFinancialAidFundCategoryModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosFinancialAidFundCategory']));
        return parent::__construct();
    }

    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosFinancialAidFundCategoryAttribute() {
        return $this->ethosFinancialAidFundCategory();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_financial_aid_fund_category_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosFinancialAidFundCategory()
    {
        if (!empty($this->ethos_financial_aid_fund_category_id)) {
            $client = new FinancialAidFundCategoriesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_financial_aid_fund_category_id;
            $cacheKey = 'ms.ethos-php.laravel.financial-aid-fund-category.' . $ethosId;

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
            return (object)$client->readById($this->ethos_financial_aid_fund_category_id)->data();
        }
        return (object)[];
    }
}