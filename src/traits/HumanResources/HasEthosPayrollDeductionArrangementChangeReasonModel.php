<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\PayrollDeductionArrangementChangeReasonsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosPayrollDeductionArrangementChangeReasonModel
*
* Useful on models that have a related Ethos payroll deduction arrangement change reason model. The relation is connected via the `ethos_payroll_deduction_arrangement_change_reason_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosPayrollDeductionArrangementChangeReasonModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosPayrollDeductionArrangementChangeReasonAttribute()
    {
        return $this->ethosPayrollDeductionArrangementChangeReason();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_payroll_deduction_arrangement_change_reason_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosPayrollDeductionArrangementChangeReason()
    {
        if (!empty($this->ethos_payroll_deduction_arrangement_change_reason_id)) {
            $client = new PayrollDeductionArrangementChangeReasonsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_payroll_deduction_arrangement_change_reason_id;
            $cacheKey = 'ms.ethos-php.laravel.payroll-deduction-arrangement-change-reason.' . $ethosId;

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
            return (object)$client->readById($this->ethos_payroll_deduction_arrangement_change_reason_id)->data();
        }
        return (object)[];
    }
}