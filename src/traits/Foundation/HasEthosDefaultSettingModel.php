<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\DefaultSettingsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosDefaultSettingModel
*
* Useful on models that have a related Ethos default setting model. The relation is connected via the `ethos_default_setting_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosDefaultSettingModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosDefaultSetting']));
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
    public function getEthosDefaultSettingAttribute() {
        return $this->ethosDefaultSetting();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_default_setting_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosDefaultSetting()
    {
        if (!empty($this->ethos_default_setting_id)) {
            $client = new DefaultSettingsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_default_setting_id;
            $cacheKey = 'ms.ethos-php.laravel.default-setting.' . $ethosId;

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
            return (object)$client->readById($this->ethos_default_setting_id)->data();
        }
        return (object)[];
    }
}