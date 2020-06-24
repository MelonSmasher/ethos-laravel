<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\SectionGradeTypesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosSectionGradeTypeModel
*
* Useful on models that have a related Ethos section grade type model. The relation is connected via the `ethos_section_grade_type_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosSectionGradeTypeModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosSectionGradeTypeAttribute()
    {
        return $this->ethosSectionGradeType();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_section_grade_type_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosSectionGradeType()
    {
        if (!empty($this->ethos_section_grade_type_id)) {
            $client = new SectionGradeTypesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_section_grade_type_id;
            $cacheKey = 'ms.ethos-php.laravel.section-grade-type.' . $ethosId;

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
            return (object)$client->readById($this->ethos_section_grade_type_id)->data();
        }
        return (object)[];
    }
}