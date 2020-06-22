<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


    use MelonSmasher\EthosPHP\Recruitment\AptitudeAssessmentsClient;

    /**
    * Trait HasEthosAptitudeAssessmentModel
    *
    * Useful on models that have a related Ethos aptitude assessment model. The relation is connected via the `ethos_aptitude_assessment_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Recruitment
    */
    trait HasEthosAptitudeAssessmentModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosAptitudeAssessment']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_aptitude_assessment_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosAptitudeAssessment()
      {
          if (!empty($this->ethos_aptitude_assessment_id)) {
              $client = new AptitudeAssessmentsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_aptitude_assessment_id;
              $cacheKey = 'ms.ethos-php.laravel.aptitude-assessment.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_aptitude_assessment_id)->data();
              }
          }

          return (object)[];
      }

    }