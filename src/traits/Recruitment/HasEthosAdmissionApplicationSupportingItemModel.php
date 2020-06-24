<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


use MelonSmasher\EthosPHP\Recruitment\AdmissionApplicationSupportingItemsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosAdmissionApplicationSupportingItemModel
*
* Useful on models that have a related Ethos admission application supporting item model. The relation is connected via the `ethos_admission_application_supporting_item_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Recruitment
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAdmissionApplicationSupportingItemModel
{
    /**
     * Get Attribute
     *
     * Returns the attribute object.
     *
     * @return object
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getEthosAdmissionApplicationSupportingItemAttribute()
    {
        return $this->ethosAdmissionApplicationSupportingItem();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_admission_application_supporting_item_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAdmissionApplicationSupportingItem()
    {
        if (!empty($this->ethos_admission_application_supporting_item_id)) {
            $client = new AdmissionApplicationSupportingItemsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_admission_application_supporting_item_id;
            $cacheKey = 'ms.ethos-php.laravel.admission-application-supporting-item.' . $ethosId;

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
            return (object)$client->readById($this->ethos_admission_application_supporting_item_id)->data();
        }
        return (object)[];
    }
}