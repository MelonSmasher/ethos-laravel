<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\FinancialAid;


use MelonSmasher\EthosPHP\FinancialAid\FinancialAidAwardPeriodsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosFinancialAidAwardPeriodModel
*
* Useful on models that have a related Ethos financial aid award period model. The relation is connected via the `ethos_financial_aid_award_period_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\FinancialAid
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosFinancialAidAwardPeriodModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosFinancialAidAwardPeriod']));
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
    public function getEthosFinancialAidAwardPeriodAttribute() {
        return $this->ethosFinancialAidAwardPeriod();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_financial_aid_award_period_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosFinancialAidAwardPeriod()
    {
        if (!empty($this->ethos_financial_aid_award_period_id)) {
            $client = new FinancialAidAwardPeriodsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_financial_aid_award_period_id;
            $cacheKey = 'ms.ethos-php.laravel.financial-aid-award-period.' . $ethosId;

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
            return (object)$client->readById($this->ethos_financial_aid_award_period_id)->data();
        }
        return (object)[];
    }
}