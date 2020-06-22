<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


use MelonSmasher\EthosPHP\Recruitment\ProspectOpportunitiesSubmissionsClient;

/**
* Trait HasEthosProspectOpportunitiesSubmissionModel
*
* Useful on models that have a related Ethos prospect opportunities submission model. The relation is connected via the `ethos_prospect_opportunities_submission_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Recruitment
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosProspectOpportunitiesSubmissionModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosProspectOpportunitiesSubmission']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_prospect_opportunities_submission_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosProspectOpportunitiesSubmission()
    {
        if (!empty($this->ethos_prospect_opportunities_submission_id)) {
            $client = new ProspectOpportunitiesSubmissionsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_prospect_opportunities_submission_id;
            $cacheKey = 'ms.ethos-php.laravel.prospect-opportunities-submission.' . $ethosId;

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
                return (object)$client->readById($this->ethos_prospect_opportunities_submission_id)->data();
            }
        }
        return (object)[];
    }
}