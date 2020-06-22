<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\CommerceTaxCodeRatesClient;
use Illuminate\Support\Facades\Cache;

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
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosCommerceTaxCodeRate']));
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
    public function getEthosCommerceTaxCodeRateAttribute() {
        return $this->ethosCommerceTaxCodeRate();
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
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_commerce_tax_code_rate_id;
            $cacheKey = 'ms.ethos-php.laravel.commerce-tax-code-rate.' . $ethosId;

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
            return (object)$client->readById($this->ethos_commerce_tax_code_rate_id)->data();
        }
        return (object)[];
    }
}