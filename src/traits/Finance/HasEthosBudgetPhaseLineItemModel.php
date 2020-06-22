<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\BudgetPhaseLineItemsClient;

/**
* Trait HasEthosBudgetPhaseLineItemModel
*
* Useful on models that have a related Ethos budget phase line item model. The relation is connected via the `ethos_budget_phase_line_item_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosBudgetPhaseLineItemModel
{
    /**
    * @return mixed
    */
    protected function getArrayableAppends()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosBudgetPhaseLineItem']));
        return parent::getArrayableAppends();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_budget_phase_line_item_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosBudgetPhaseLineItem()
    {
        if (!empty($this->ethos_budget_phase_line_item_id)) {
            $client = new BudgetPhaseLineItemsClient(getEthosSession());
            $cacheTTL = config('trait_response_cache_time', 0);
            $ethosId = $this->ethos_budget_phase_line_item_id;
            $cacheKey = 'ms.ethos-php.laravel.budget-phase-line-item.' . $ethosId;

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
                return (object)$client->readById($this->ethos_budget_phase_line_item_id)->data();
            }
        }
        return (object)[];
    }
}