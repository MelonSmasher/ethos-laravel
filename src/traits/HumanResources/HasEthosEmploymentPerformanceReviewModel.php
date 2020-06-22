<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


    use MelonSmasher\EthosPHP\HumanResources\EmploymentPerformanceReviewsClient;

    /**
    * Trait HasEthosEmploymentPerformanceReviewModel
    *
    * Useful on models that have a related Ethos employment performance review model. The relation is connected via the `ethos_employment_performance_review_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\HumanResources
    */
    trait HasEthosEmploymentPerformanceReviewModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosEmploymentPerformanceReview']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_employment_performance_review_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosEmploymentPerformanceReview()
      {
          if (!empty($this->ethos_employment_performance_review_id)) {
              $client = new EmploymentPerformanceReviewsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_employment_performance_review_id;
              $cacheKey = 'ms.ethos-php.laravel.employment-performance-review.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_employment_performance_review_id)->data();
              }
          }

          return (object)[];
      }

    }