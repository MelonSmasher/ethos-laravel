<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


    use MelonSmasher\EthosPHP\HumanResources\ContributionPayrollDeductionsClient;

    /**
    * Trait HasEthosContributionPayrollDeductionModel
    *
    * Useful on models that have a related Ethos contribution payroll deduction model. The relation is connected via the `ethos_contribution_payroll_deduction_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\HumanResources
    */
    trait HasEthosContributionPayrollDeductionModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosContributionPayrollDeduction']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_contribution_payroll_deduction_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosContributionPayrollDeduction()
      {
          if (!empty($this->ethos_contribution_payroll_deduction_id)) {
              $client = new ContributionPayrollDeductionsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_contribution_payroll_deduction_id;
              $cacheKey = 'ms.ethos-php.laravel.contribution-payroll-deduction.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_contribution_payroll_deduction_id)->data();
              }
          }

          return (object)[];
      }

    }