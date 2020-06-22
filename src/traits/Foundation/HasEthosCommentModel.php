<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\CommentsClient;

/**
* Trait HasEthosCommentModel
*
* Useful on models that have a related Ethos comment model. The relation is connected via the `ethos_comment_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosCommentModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosComment']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_comment_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosComment()
    {
        if (!empty($this->ethos_comment_id)) {
            $client = new CommentsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_comment_id;
            $cacheKey = 'ms.ethos-php.laravel.comment.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                $model = Cache::get($cacheKey, null);
                if (empty($model)) {
                    $model = serialize($client->readById($ethosId)->data());
                    Cache::put($cacheKey, $cacheTTL, $model);
                }
                return (object)unserialize($model);
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_comment_id)->data();
        }
        return (object)[];
    }
}