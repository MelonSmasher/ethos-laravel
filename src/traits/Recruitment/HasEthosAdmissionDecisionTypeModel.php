<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Recruitment;


use MelonSmasher\EthosPHP\Recruitment\AdmissionDecisionTypesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosAdmissionDecisionTypeModel
*
* Useful on models that have a related Ethos admission decision type model. The relation is connected via the `ethos_admission_decision_type_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Recruitment
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAdmissionDecisionTypeModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosAdmissionDecisionType']));
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
    public function getEthosAdmissionDecisionTypeAttribute() {
        return $this->ethosAdmissionDecisionType();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_admission_decision_type_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAdmissionDecisionType()
    {
        if (!empty($this->ethos_admission_decision_type_id)) {
            $client = new AdmissionDecisionTypesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_admission_decision_type_id;
            $cacheKey = 'ms.ethos-php.laravel.admission-decision-type.' . $ethosId;

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
            return (object)$client->readById($this->ethos_admission_decision_type_id)->data();
        }
        return (object)[];
    }
}