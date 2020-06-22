<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


    use MelonSmasher\EthosPHP\HumanResources\InstitutionJobSupervisorsClient;

    /**
    * Trait HasEthosInstitutionJobSupervisorModel
    *
    * Useful on models that have a related Ethos institution job supervisor model. The relation is connected via the `ethos_institution_job_supervisor_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\HumanResources
    */
    trait HasEthosInstitutionJobSupervisorModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosInstitutionJobSupervisor']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_institution_job_supervisor_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosInstitutionJobSupervisor()
      {
          if (!empty($this->ethos_institution_job_supervisor_id)) {
              $client = new InstitutionJobSupervisorsClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_institution_job_supervisor_id;
              $cacheKey = 'ms.ethos-php.laravel.institution-job-supervisor.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_institution_job_supervisor_id)->data();
              }
          }

          return (object)[];
      }

    }