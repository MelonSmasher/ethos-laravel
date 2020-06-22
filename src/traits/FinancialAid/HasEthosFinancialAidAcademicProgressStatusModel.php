<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\FinancialAid;


use MelonSmasher\EthosPHP\FinancialAid\FinancialAidAcademicProgressStatusesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosFinancialAidAcademicProgressStatusModel
*
* Useful on models that have a related Ethos financial aid academic progress status model. The relation is connected via the `ethos_financial_aid_academic_progress_status_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\FinancialAid
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosFinancialAidAcademicProgressStatusModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosFinancialAidAcademicProgressStatus']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_financial_aid_academic_progress_status_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosFinancialAidAcademicProgressStatus()
    {
        if (!empty($this->ethos_financial_aid_academic_progress_status_id)) {
            $client = new FinancialAidAcademicProgressStatusesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_financial_aid_academic_progress_status_id;
            $cacheKey = 'ms.ethos-php.laravel.financial-aid-academic-progress-status.' . $ethosId;

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
            return (object)$client->readById($this->ethos_financial_aid_academic_progress_status_id)->data();
        }
        return (object)[];
    }
}