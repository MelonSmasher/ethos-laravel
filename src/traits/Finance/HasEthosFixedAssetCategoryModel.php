<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\FixedAssetCategoriesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosFixedAssetCategoryModel
*
* Useful on models that have a related Ethos fixed asset category model. The relation is connected via the `ethos_fixed_asset_category_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosFixedAssetCategoryModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosFixedAssetCategoryAttribute()
    {
        return $this->ethosFixedAssetCategory();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_fixed_asset_category_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosFixedAssetCategory()
    {
        if (!empty($this->ethos_fixed_asset_category_id)) {
            $client = new FixedAssetCategoriesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_fixed_asset_category_id;
            $cacheKey = 'ms.ethos-php.laravel.fixed-asset-category.' . $ethosId;

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
            return (object)$client->readById($this->ethos_fixed_asset_category_id)->data();
        }
        return (object)[];
    }
}