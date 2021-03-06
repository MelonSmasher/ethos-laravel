<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\InstitutionEmployersClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosInstitutionEmployerModel
*
* Useful on models that have a related Ethos institution employer model. The relation is connected via the `ethos_institution_employer_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosInstitutionEmployerModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosInstitutionEmployerAttribute()
    {
        return $this->ethosInstitutionEmployer();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_institution_employer_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosInstitutionEmployer()
    {
        if (!empty($this->ethos_institution_employer_id)) {
            $client = new InstitutionEmployersClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_institution_employer_id;
            $cacheKey = 'ms.ethos-php.laravel.institution-employer.' . $ethosId;

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
            return (object)$client->readById($this->ethos_institution_employer_id)->data();
        }
        return (object)[];
    }
}