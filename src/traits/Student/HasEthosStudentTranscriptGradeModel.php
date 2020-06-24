<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\StudentTranscriptGradesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosStudentTranscriptGradeModel
*
* Useful on models that have a related Ethos student transcript grade model. The relation is connected via the `ethos_student_transcript_grade_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosStudentTranscriptGradeModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosStudentTranscriptGradeAttribute()
    {
        return $this->ethosStudentTranscriptGrade();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_student_transcript_grade_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosStudentTranscriptGrade()
    {
        if (!empty($this->ethos_student_transcript_grade_id)) {
            $client = new StudentTranscriptGradesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_student_transcript_grade_id;
            $cacheKey = 'ms.ethos-php.laravel.student-transcript-grade.' . $ethosId;

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
            return (object)$client->readById($this->ethos_student_transcript_grade_id)->data();
        }
        return (object)[];
    }
}