<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\InstructionalDeliveryMethodsClient;

/**
* Trait HasEthosInstructionalDeliveryMethodModel
*
* Useful on models that have a related Ethos instructional delivery method model. The relation is connected via the `ethos_instructional_delivery_method_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosInstructionalDeliveryMethodModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosInstructionalDeliveryMethod']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_instructional_delivery_method_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosInstructionalDeliveryMethod()
    {
        if (!empty($this->ethos_instructional_delivery_method_id)) {
            $client = new InstructionalDeliveryMethodsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_instructional_delivery_method_id;
            $cacheKey = 'ms.ethos-php.laravel.instructional-delivery-method.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                return (object)unserialize(
                    Cache::remember($cacheKey, $cacheTTL, function () use ($client, $ethosId) {
                        return serialize($client->readById($ethosId)->data());
                    })
                );
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_instructional_delivery_method_id)->data();
        }
        return (object)[];
    }
}