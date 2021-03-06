<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\StudentGradePointAveragesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosStudentGradePointAverageModel
*
* Useful on models that have a related Ethos student grade point average model. The relation is connected via the `ethos_student_grade_point_average_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosStudentGradePointAverageModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosStudentGradePointAverageAttribute()
    {
        return $this->ethosStudentGradePointAverage();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_student_grade_point_average_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosStudentGradePointAverage()
    {
        if (!empty($this->ethos_student_grade_point_average_id)) {
            $client = new StudentGradePointAveragesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_student_grade_point_average_id;
            $cacheKey = 'ms.ethos-php.laravel.student-grade-point-average.' . $ethosId;

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
            return (object)$client->readById($this->ethos_student_grade_point_average_id)->data();
        }
        return (object)[];
    }
}