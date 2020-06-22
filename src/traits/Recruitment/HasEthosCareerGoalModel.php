<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


    use MelonSmasher\EthosPHP\Recruitment\CareerGoalsClient;

    /**
    * Trait HasEthosCareerGoalModel
    *
    * Useful on models that have a related Ethos career goal model. The relation is connected via the `ethos_career_goal_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Recruitment
    */
    trait HasEthosCareerGoalModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosCareerGoal']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_career_goal_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosCareerGoal()
      {
          if (!empty($this->ethos_career_goal_id)) {
              $client = new CareerGoalsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_career_goal_id;
              $cacheKey = 'ms.ethos-php.laravel.career-goal.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_career_goal_id)->data();
              }
          }

          return (object)[];
      }

    }