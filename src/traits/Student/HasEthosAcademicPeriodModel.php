<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\AcademicPeriodsClient;

/**
* Trait HasEthosAcademicPeriodModel
*
* Useful on models that have a related Ethos academic period model. The relation is connected via the `ethos_academic_period_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAcademicPeriodModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosAcademicPeriod']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_academic_period_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAcademicPeriod()
    {
        if (!empty($this->ethos_academic_period_id)) {
            $client = new AcademicPeriodsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_academic_period_id;
            $cacheKey = 'ms.ethos-php.laravel.academic-period.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                $model = Cache::get($cacheKey, null);
                if (empty($model)) {
                    $model = serialize($client->readById($ethosId)->data());
                    Cache::put($cacheKey, $cacheTTL, $model);
                }
                return (object)unserialize($model);
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_academic_period_id)->data();
        }
        return (object)[];
    }
}