<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\StudentTranscriptGradesAdjustmentsClient;

/**
* Trait HasEthosStudentTranscriptGradesAdjustmentModel
*
* Useful on models that have a related Ethos student transcript grades adjustment model. The relation is connected via the `ethos_student_transcript_grades_adjustment_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosStudentTranscriptGradesAdjustmentModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosStudentTranscriptGradesAdjustment']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_student_transcript_grades_adjustment_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosStudentTranscriptGradesAdjustment()
    {
        if (!empty($this->ethos_student_transcript_grades_adjustment_id)) {
            $client = new StudentTranscriptGradesAdjustmentsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_student_transcript_grades_adjustment_id;
            $cacheKey = 'ms.ethos-php.laravel.student-transcript-grades-adjustment.' . $ethosId;

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
            return (object)$client->readById($this->ethos_student_transcript_grades_adjustment_id)->data();
        }
        return (object)[];
    }
}