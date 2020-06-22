<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


use MelonSmasher\EthosPHP\Recruitment\AssessmentSpecialCircumstancesClient;

/**
* Trait HasEthosAssessmentSpecialCircumstanceModel
*
* Useful on models that have a related Ethos assessment special circumstance model. The relation is connected via the `ethos_assessment_special_circumstance_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Recruitment
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAssessmentSpecialCircumstanceModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosAssessmentSpecialCircumstance']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_assessment_special_circumstance_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAssessmentSpecialCircumstance()
    {
        if (!empty($this->ethos_assessment_special_circumstance_id)) {
            $client = new AssessmentSpecialCircumstancesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_assessment_special_circumstance_id;
            $cacheKey = 'ms.ethos-php.laravel.assessment-special-circumstance.' . $ethosId;

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
            return (object)$client->readById($this->ethos_assessment_special_circumstance_id)->data();
        }
        return (object)[];
    }
}