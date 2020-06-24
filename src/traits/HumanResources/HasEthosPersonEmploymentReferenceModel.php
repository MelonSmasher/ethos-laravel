<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\PersonEmploymentReferencesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosPersonEmploymentReferenceModel
*
* Useful on models that have a related Ethos person employment reference model. The relation is connected via the `ethos_person_employment_reference_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosPersonEmploymentReferenceModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosPersonEmploymentReferenceAttribute()
    {
        return $this->ethosPersonEmploymentReference();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_person_employment_reference_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosPersonEmploymentReference()
    {
        if (!empty($this->ethos_person_employment_reference_id)) {
            $client = new PersonEmploymentReferencesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_person_employment_reference_id;
            $cacheKey = 'ms.ethos-php.laravel.person-employment-reference.' . $ethosId;

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
            return (object)$client->readById($this->ethos_person_employment_reference_id)->data();
        }
        return (object)[];
    }
}