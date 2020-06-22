<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\AccountsPayableInvoicesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosAccountsPayableInvoiceModel
*
* Useful on models that have a related Ethos accounts payable invoice model. The relation is connected via the `ethos_accounts_payable_invoice_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosAccountsPayableInvoiceModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosAccountsPayableInvoice']));
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
    public function getEthosAccountsPayableInvoiceAttribute() {
        return $this->ethosAccountsPayableInvoice();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_accounts_payable_invoice_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosAccountsPayableInvoice()
    {
        if (!empty($this->ethos_accounts_payable_invoice_id)) {
            $client = new AccountsPayableInvoicesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_accounts_payable_invoice_id;
            $cacheKey = 'ms.ethos-php.laravel.accounts-payable-invoice.' . $ethosId;

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
            return (object)$client->readById($this->ethos_accounts_payable_invoice_id)->data();
        }
        return (object)[];
    }
}