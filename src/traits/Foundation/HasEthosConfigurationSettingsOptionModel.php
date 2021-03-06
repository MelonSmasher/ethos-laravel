<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\ConfigurationSettingsOptionsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosConfigurationSettingsOptionModel
*
* Useful on models that have a related Ethos configuration settings option model. The relation is connected via the `ethos_configuration_settings_option_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosConfigurationSettingsOptionModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosConfigurationSettingsOptionAttribute()
    {
        return $this->ethosConfigurationSettingsOption();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_configuration_settings_option_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosConfigurationSettingsOption()
    {
        if (!empty($this->ethos_configuration_settings_option_id)) {
            $client = new ConfigurationSettingsOptionsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_configuration_settings_option_id;
            $cacheKey = 'ms.ethos-php.laravel.configuration-settings-option.' . $ethosId;

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
            return (object)$client->readById($this->ethos_configuration_settings_option_id)->data();
        }
        return (object)[];
    }
}