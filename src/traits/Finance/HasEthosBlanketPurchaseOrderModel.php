<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\BlanketPurchaseOrdersClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosBlanketPurchaseOrderModel
*
* Useful on models that have a related Ethos blanket purchase order model. The relation is connected via the `ethos_blanket_purchase_order_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosBlanketPurchaseOrderModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosBlanketPurchaseOrderAttribute()
    {
        return $this->ethosBlanketPurchaseOrder();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_blanket_purchase_order_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosBlanketPurchaseOrder()
    {
        if (!empty($this->ethos_blanket_purchase_order_id)) {
            $client = new BlanketPurchaseOrdersClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_blanket_purchase_order_id;
            $cacheKey = 'ms.ethos-php.laravel.blanket-purchase-order.' . $ethosId;

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
            return (object)$client->readById($this->ethos_blanket_purchase_order_id)->data();
        }
        return (object)[];
    }
}