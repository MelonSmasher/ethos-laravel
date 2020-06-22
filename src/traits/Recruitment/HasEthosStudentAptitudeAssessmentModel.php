<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


    use MelonSmasher\EthosPHP\Recruitment\StudentAptitudeAssessmentsClient;

    /**
    * Trait HasEthosStudentAptitudeAssessmentModel
    *
    * Useful on models that have a related Ethos student aptitude assessment model. The relation is connected via the `ethos_student_aptitude_assessment_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Recruitment
    */
    trait HasEthosStudentAptitudeAssessmentModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosStudentAptitudeAssessment']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_student_aptitude_assessment_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosStudentAptitudeAssessment()
      {
          if (!empty($this->ethos_student_aptitude_assessment_id)) {
              $client = new StudentAptitudeAssessmentsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_student_aptitude_assessment_id;
              $cacheKey = 'ms.ethos-php.laravel.student-aptitude-assessment.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_student_aptitude_assessment_id)->data();
              }
          }

          return (object)[];
      }

    }