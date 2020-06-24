<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


use MelonSmasher\EthosPHP\Recruitment\AdmissionApplicationsSubmissionsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosAdmissionApplicationsSubmissionModel
*
* Useful on models that have a related Ethos admission applications submission model. The relation is connected via the `ethos_admission_applications_submission_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Recruitment
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAdmissionApplicationsSubmissionModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosAdmissionApplicationsSubmissionAttribute()
    {
        return $this->ethosAdmissionApplicationsSubmission();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_admission_applications_submission_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAdmissionApplicationsSubmission()
    {
        if (!empty($this->ethos_admission_applications_submission_id)) {
            $client = new AdmissionApplicationsSubmissionsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_admission_applications_submission_id;
            $cacheKey = 'ms.ethos-php.laravel.admission-applications-submission.' . $ethosId;

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
            return (object)$client->readById($this->ethos_admission_applications_submission_id)->data();
        }
        return (object)[];
    }
}