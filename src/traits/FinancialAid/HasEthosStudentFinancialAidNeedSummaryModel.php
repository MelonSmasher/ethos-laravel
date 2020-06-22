<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\FinancialAid;


use MelonSmasher\EthosPHP\FinancialAid\StudentFinancialAidNeedSummariesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosStudentFinancialAidNeedSummaryModel
*
* Useful on models that have a related Ethos student financial aid need summary model. The relation is connected via the `ethos_student_financial_aid_need_summary_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\FinancialAid
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosStudentFinancialAidNeedSummaryModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosStudentFinancialAidNeedSummary']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_student_financial_aid_need_summary_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosStudentFinancialAidNeedSummary()
    {
        if (!empty($this->ethos_student_financial_aid_need_summary_id)) {
            $client = new StudentFinancialAidNeedSummariesClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_student_financial_aid_need_summary_id;
            $cacheKey = 'ms.ethos-php.laravel.student-financial-aid-need-summary.' . $ethosId;

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
            return (object)$client->readById($this->ethos_student_financial_aid_need_summary_id)->data();
        }
        return (object)[];
    }
}