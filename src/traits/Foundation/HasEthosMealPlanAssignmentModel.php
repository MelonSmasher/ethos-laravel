<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\MealPlanAssignmentsClient;

    /**
    * Trait HasEthosMealPlanAssignmentModel
    *
    * Useful on models that have a related Ethos meal plan assignment model. The relation is connected via the `ethos_meal_plan_assignment_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosMealPlanAssignmentModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosMealPlanAssignment']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_meal_plan_assignment_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosMealPlanAssignment()
      {
          if (!empty($this->ethos_meal_plan_assignment_id)) {
              $client = new MealPlanAssignmentsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_meal_plan_assignment_id;
              $cacheKey = 'ms.ethos-php.laravel.meal-plan-assignment.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_meal_plan_assignment_id)->data();
              }
          }

          return (object)[];
      }

    }