<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\FinancialAid;


use MelonSmasher\EthosPHP\FinancialAid\StudentFinancialAidAcademicProgressStatusesClient;

/**
* Trait HasEthosStudentFinancialAidAcademicProgressStatusModel
*
* Useful on models that have a related Ethos student financial aid academic progress status model. The relation is connected via the `ethos_student_financial_aid_academic_progress_status_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\FinancialAid
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosStudentFinancialAidAcademicProgressStatusModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosStudentFinancialAidAcademicProgressStatus']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_student_financial_aid_academic_progress_status_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosStudentFinancialAidAcademicProgressStatus()
    {
        if (!empty($this->ethos_student_financial_aid_academic_progress_status_id)) {
            $client = new StudentFinancialAidAcademicProgressStatusesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_student_financial_aid_academic_progress_status_id;
            $cacheKey = 'ms.ethos-php.laravel.student-financial-aid-academic-progress-status.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                return (object)unserialize(
                    Cache::remember($cacheKey, $cacheTTL, function () use ($client, $ethosId) {
                        return serialize($client->readById($ethosId)->data());
                    })
                );
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_student_financial_aid_academic_progress_status_id)->data();
        }
        return (object)[];
    }
}