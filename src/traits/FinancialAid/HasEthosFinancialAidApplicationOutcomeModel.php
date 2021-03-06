<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\FinancialAid;


use MelonSmasher\EthosPHP\FinancialAid\FinancialAidApplicationOutcomesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosFinancialAidApplicationOutcomeModel
*
* Useful on models that have a related Ethos financial aid application outcome model. The relation is connected via the `ethos_financial_aid_application_outcome_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\FinancialAid
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosFinancialAidApplicationOutcomeModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosFinancialAidApplicationOutcomeAttribute()
    {
        return $this->ethosFinancialAidApplicationOutcome();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_financial_aid_application_outcome_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosFinancialAidApplicationOutcome()
    {
        if (!empty($this->ethos_financial_aid_application_outcome_id)) {
            $client = new FinancialAidApplicationOutcomesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_financial_aid_application_outcome_id;
            $cacheKey = 'ms.ethos-php.laravel.financial-aid-application-outcome.' . $ethosId;

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
            return (object)$client->readById($this->ethos_financial_aid_application_outcome_id)->data();
        }
        return (object)[];
    }
}