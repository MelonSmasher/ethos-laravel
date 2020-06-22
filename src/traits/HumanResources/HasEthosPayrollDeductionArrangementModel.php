<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\PayrollDeductionArrangementsClient;

/**
* Trait HasEthosPayrollDeductionArrangementModel
*
* Useful on models that have a related Ethos payroll deduction arrangement model. The relation is connected via the `ethos_payroll_deduction_arrangement_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosPayrollDeductionArrangementModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosPayrollDeductionArrangement']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_payroll_deduction_arrangement_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosPayrollDeductionArrangement()
    {
        if (!empty($this->ethos_payroll_deduction_arrangement_id)) {
            $client = new PayrollDeductionArrangementsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_payroll_deduction_arrangement_id;
            $cacheKey = 'ms.ethos-php.laravel.payroll-deduction-arrangement.' . $ethosId;

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
                return (object)$client->readById($this->ethos_payroll_deduction_arrangement_id)->data();
            }
        }
        return (object)[];
    }
}