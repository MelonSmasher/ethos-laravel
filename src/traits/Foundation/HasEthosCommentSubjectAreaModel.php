<?php


    namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


    use MelonSmasher\EthosPHP\Foundation\CommentSubjectAreaClient;

    /**
    * Trait HasEthosCommentSubjectAreaModel
    *
    * Useful on models that have a related Ethos comment subject area model. The relation is connected via the `ethos_comment_subject_area_id` attribute.
    *
    * @package MelonSmasher\EthosLaravel\Traits\Foundation
    */
    trait HasEthosCommentSubjectAreaModel
    {

      /**
       * @return mixed
       */
      protected function getArrayableAppends()
      {
          $this->appends = array_unique(array_merge($this->appends, ['ethosCommentSubjectArea']));
          return parent::getArrayableAppends();
      }

      /**
       * Ethos Model
       *
       * The Ethos Model related by the `ethos_comment_subject_area_id`.
       *
       * @return object
       * @throws \GuzzleHttp\Exception\GuzzleException
       */
      public function ethosCommentSubjectArea()
      {
          if (!empty($this->ethos_comment_subject_area_id)) {
              $client = new CommentSubjectAreaClient(getEthosSession());
              $cacheTTL = config('trait_response_cache_time', 0);
              $ethosId = $this->ethos_comment_subject_area_id;
              $cacheKey = 'ms.ethos-php.laravel.comment-subject-area.' . $ethosId;

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
                  return (object)$client->readById($this->ethos_comment_subject_area_id)->data();
              }
          }

          return (object)[];
      }

    }