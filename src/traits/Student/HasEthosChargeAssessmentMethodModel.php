<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\ChargeAssessmentMethodsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosChargeAssessmentMethodModel
*
* Useful on models that have a related Ethos charge assessment method model. The relation is connected via the `ethos_charge_assessment_method_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosChargeAssessmentMethodModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosChargeAssessmentMethodAttribute()
    {
        return $this->ethosChargeAssessmentMethod();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_charge_assessment_method_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosChargeAssessmentMethod()
    {
        if (!empty($this->ethos_charge_assessment_method_id)) {
            $client = new ChargeAssessmentMethodsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_charge_assessment_method_id;
            $cacheKey = 'ms.ethos-php.laravel.charge-assessment-method.' . $ethosId;

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
            return (object)$client->readById($this->ethos_charge_assessment_method_id)->data();
        }
        return (object)[];
    }
}