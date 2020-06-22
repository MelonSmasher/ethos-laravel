<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\CommerceTaxCodeRatesClient;

/**
* Trait HasEthosCommerceTaxCodeRateModel
*
* Useful on models that have a related Ethos commerce tax code rate model. The relation is connected via the `ethos_commerce_tax_code_rate_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosCommerceTaxCodeRateModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosCommerceTaxCodeRate']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_commerce_tax_code_rate_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosCommerceTaxCodeRate()
    {
        if (!empty($this->ethos_commerce_tax_code_rate_id)) {
            $client = new CommerceTaxCodeRatesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_commerce_tax_code_rate_id;
            $cacheKey = 'ms.ethos-php.laravel.commerce-tax-code-rate.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                return (object)unserialize(
                    Cache::remember($cacheKey, $cacheTTL, function () use ($client, $ethosId) {
                        return serialize($client->readById($ethosId)->data());
                    })
                );
            } else {
                // If the cache $cacheTTL is 0 or false just pull the object
                return (object)$client->readById($this->ethos_commerce_tax_code_rate_id)->data();
            }
        }
        return (object)[];
    }
}