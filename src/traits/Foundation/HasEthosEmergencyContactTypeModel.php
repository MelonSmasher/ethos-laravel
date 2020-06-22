<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Foundation;


use MelonSmasher\EthosPHP\Foundation\EmergencyContactTypesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosEmergencyContactTypeModel
*
* Useful on models that have a related Ethos emergency contact type model. The relation is connected via the `ethos_emergency_contact_type_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Foundation
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosEmergencyContactTypeModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosEmergencyContactType']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_emergency_contact_type_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosEmergencyContactType()
    {
        if (!empty($this->ethos_emergency_contact_type_id)) {
            $client = new EmergencyContactTypesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_emergency_contact_type_id;
            $cacheKey = 'ms.ethos-php.laravel.emergency-contact-type.' . $ethosId;

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
            return (object)$client->readById($this->ethos_emergency_contact_type_id)->data();
        }
        return (object)[];
    }
}