<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\PurchaseClassificationsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosPurchaseClassificationModel
*
* Useful on models that have a related Ethos purchase classification model. The relation is connected via the `ethos_purchase_classification_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosPurchaseClassificationModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosPurchaseClassificationAttribute()
    {
        return $this->ethosPurchaseClassification();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_purchase_classification_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosPurchaseClassification()
    {
        if (!empty($this->ethos_purchase_classification_id)) {
            $client = new PurchaseClassificationsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_purchase_classification_id;
            $cacheKey = 'ms.ethos-php.laravel.purchase-classification.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                $model = Cache::get($cacheKey, null);
                if (empty($model)) {
                    $model = serialize($client->readById($ethosId)->data());
                    Cache::put($cacheKey, $model, $cacheTTL);
                }
                return (object)unserialize($model);
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_purchase_classification_id)->data();
        }
        return (object)[];
    }
}