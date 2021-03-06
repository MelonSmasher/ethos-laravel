<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\SectionInstructorsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosSectionInstructorModel
*
* Useful on models that have a related Ethos section instructor model. The relation is connected via the `ethos_section_instructor_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosSectionInstructorModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosSectionInstructorAttribute()
    {
        return $this->ethosSectionInstructor();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_section_instructor_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosSectionInstructor()
    {
        if (!empty($this->ethos_section_instructor_id)) {
            $client = new SectionInstructorsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_section_instructor_id;
            $cacheKey = 'ms.ethos-php.laravel.section-instructor.' . $ethosId;

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
            return (object)$client->readById($this->ethos_section_instructor_id)->data();
        }
        return (object)[];
    }
}