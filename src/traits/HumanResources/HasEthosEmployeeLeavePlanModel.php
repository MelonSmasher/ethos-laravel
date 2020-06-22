<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


    use MelonSmasher\EthosPHP\HumanResources\EmployeeLeavePlansClient;

    /**
    * Trait HasEthosEmployeeLeavePlanModel
    *
    * Useful on models that have a related Ethos employee leave plan model. The relation is connected via the `ethos_employee_leave_plan_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\HumanResources
    */
    trait HasEthosEmployeeLeavePlanModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosEmployeeLeavePlan']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_employee_leave_plan_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosEmployeeLeavePlan()
      {
          if (!empty($this->ethos_employee_leave_plan_id)) {
              $client = new EmployeeLeavePlansClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_employee_leave_plan_id;
              $cacheKey = 'ms.ethos-php.laravel.employee-leave-plan.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_employee_leave_plan_id)->data();
              }
          }

          return (object)[];
      }

    }