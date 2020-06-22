<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\EmploymentLeaveOfAbsenceReasonsClient;

/**
* Trait HasEthosEmploymentLeaveOfAbsenceReasonModel
*
* Useful on models that have a related Ethos employment leave of absence reason model. The relation is connected via the `ethos_employment_leave_of_absence_reason_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosEmploymentLeaveOfAbsenceReasonModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosEmploymentLeaveOfAbsenceReason']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_employment_leave_of_absence_reason_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosEmploymentLeaveOfAbsenceReason()
    {
        if (!empty($this->ethos_employment_leave_of_absence_reason_id)) {
            $client = new EmploymentLeaveOfAbsenceReasonsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_employment_leave_of_absence_reason_id;
            $cacheKey = 'ms.ethos-php.laravel.employment-leave-of-absence-reason.' . $ethosId;

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
            return (object)$client->readById($this->ethos_employment_leave_of_absence_reason_id)->data();
        }
        return (object)[];
    }
}