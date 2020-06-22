<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\HumanResources;


use MelonSmasher\EthosPHP\HumanResources\EmployeeLeaveTransactionsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosEmployeeLeaveTransactionModel
*
* Useful on models that have a related Ethos employee leave transaction model. The relation is connected via the `ethos_employee_leave_transaction_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\HumanResources
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosEmployeeLeaveTransactionModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosEmployeeLeaveTransaction']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_employee_leave_transaction_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosEmployeeLeaveTransaction()
    {
        if (!empty($this->ethos_employee_leave_transaction_id)) {
            $client = new EmployeeLeaveTransactionsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_employee_leave_transaction_id;
            $cacheKey = 'ms.ethos-php.laravel.employee-leave-transaction.' . $ethosId;

            // If we are caching the result attempt to pull from the cache
            // If its not in the cache store it for next time
            // Expiry is controlled by $cacheTTL
            if ($cacheTTL) {
                $model = Cache::get($cacheKey, null);
                if (empty($model)) {
                    $model = serialize($client->readById($ethosId)->data());
                    Cache::put($cacheKey, $cacheTTL, $model);
                }
                return (object)unserialize($model);
            }
            // If the cache $cacheTTL is 0 or false just pull the object
            return (object)$client->readById($this->ethos_employee_leave_transaction_id)->data();
        }
        return (object)[];
    }
}