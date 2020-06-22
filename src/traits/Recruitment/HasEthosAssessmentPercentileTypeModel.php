<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


    use MelonSmasher\EthosPHP\Recruitment\AssessmentPercentileTypesClient;

    /**
    * Trait HasEthosAssessmentPercentileTypeModel
    *
    * Useful on models that have a related Ethos assessment percentile type model. The relation is connected via the `ethos_assessment_percentile_type_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Recruitment
    */
    trait HasEthosAssessmentPercentileTypeModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosAssessmentPercentileType']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_assessment_percentile_type_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosAssessmentPercentileType()
      {
          if (!empty($this->ethos_assessment_percentile_type_id)) {
              $client = new AssessmentPercentileTypesClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_assessment_percentile_type_id;
              $cacheKey = 'ms.ethos-php.laravel.assessment-percentile-type.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_assessment_percentile_type_id)->data();
              }
          }

          return (object)[];
      }

    }