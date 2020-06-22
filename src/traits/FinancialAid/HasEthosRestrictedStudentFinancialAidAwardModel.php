<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\FinancialAid;


use MelonSmasher\EthosPHP\FinancialAid\RestrictedStudentFinancialAidAwardsClient;

/**
* Trait HasEthosRestrictedStudentFinancialAidAwardModel
*
* Useful on models that have a related Ethos restricted student financial aid award model. The relation is connected via the `ethos_restricted_student_financial_aid_award_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\FinancialAid
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosRestrictedStudentFinancialAidAwardModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosRestrictedStudentFinancialAidAward']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_restricted_student_financial_aid_award_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosRestrictedStudentFinancialAidAward()
    {
        if (!empty($this->ethos_restricted_student_financial_aid_award_id)) {
            $client = new RestrictedStudentFinancialAidAwardsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_restricted_student_financial_aid_award_id;
            $cacheKey = 'ms.ethos-php.laravel.restricted-student-financial-aid-award.' . $ethosId;

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
                return (object)$client->readById($this->ethos_restricted_student_financial_aid_award_id)->data();
            }
        }
        return (object)[];
    }
}