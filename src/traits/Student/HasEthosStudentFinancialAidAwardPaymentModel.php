<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\StudentFinancialAidAwardPaymentsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosStudentFinancialAidAwardPaymentModel
*
* Useful on models that have a related Ethos student financial aid award payment model. The relation is connected via the `ethos_student_financial_aid_award_payment_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosStudentFinancialAidAwardPaymentModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosStudentFinancialAidAwardPayment']));
        return parent::__construct();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_student_financial_aid_award_payment_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosStudentFinancialAidAwardPayment()
    {
        if (!empty($this->ethos_student_financial_aid_award_payment_id)) {
            $client = new StudentFinancialAidAwardPaymentsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_student_financial_aid_award_payment_id;
            $cacheKey = 'ms.ethos-php.laravel.student-financial-aid-award-payment.' . $ethosId;

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
            return (object)$client->readById($this->ethos_student_financial_aid_award_payment_id)->data();
        }
        return (object)[];
    }
}