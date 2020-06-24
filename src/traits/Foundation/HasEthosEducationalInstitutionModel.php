<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\EducationalInstitutionsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosEducationalInstitutionModel
*
* Useful on models that have a related Ethos educational institution model. The relation is connected via the `ethos_educational_institution_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosEducationalInstitutionModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosEducationalInstitutionAttribute()
    {
        return $this->ethosEducationalInstitution();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_educational_institution_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosEducationalInstitution()
    {
        if (!empty($this->ethos_educational_institution_id)) {
            $client = new EducationalInstitutionsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_educational_institution_id;
            $cacheKey = 'ms.ethos-php.laravel.educational-institution.' . $ethosId;

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
            return (object)$client->readById($this->ethos_educational_institution_id)->data();
        }
        return (object)[];
    }
}