<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\FixedAssetsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosFixedAssetModel
*
* Useful on models that have a related Ethos fixed asset model. The relation is connected via the `ethos_fixed_asset_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosFixedAssetModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosFixedAsset']));
        return parent::__construct();
    }

    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosFixedAssetAttribute() {
        return $this->ethosFixedAsset();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_fixed_asset_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosFixedAsset()
    {
        if (!empty($this->ethos_fixed_asset_id)) {
            $client = new FixedAssetsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_fixed_asset_id;
            $cacheKey = 'ms.ethos-php.laravel.fixed-asset.' . $ethosId;

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
            return (object)$client->readById($this->ethos_fixed_asset_id)->data();
        }
        return (object)[];
    }
}