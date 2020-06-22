<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\StudentAcademicProgramsSubmissionsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosStudentAcademicProgramsSubmissionModel
*
* Useful on models that have a related Ethos student academic programs submission model. The relation is connected via the `ethos_student_academic_programs_submission_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosStudentAcademicProgramsSubmissionModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosStudentAcademicProgramsSubmission']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_student_academic_programs_submission_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosStudentAcademicProgramsSubmission()
    {
        if (!empty($this->ethos_student_academic_programs_submission_id)) {
            $client = new StudentAcademicProgramsSubmissionsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_student_academic_programs_submission_id;
            $cacheKey = 'ms.ethos-php.laravel.student-academic-programs-submission.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                $model = Cache::get($cacheKey, null);
                if (empty($model)) {
                    $model = serialize($client->readById($ethosId)->data());
                    Cache::put($cacheKey, $cacheTTL, $model);
                }
                return (object)unserialize($model);
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_student_academic_programs_submission_id)->data();
        }
        return (object)[];
    }
}