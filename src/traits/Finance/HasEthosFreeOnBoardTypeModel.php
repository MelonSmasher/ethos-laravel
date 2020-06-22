<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Finance;


use MelonSmasher\EthosPHP\Finance\FreeOnBoardTypesClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosFreeOnBoardTypeModel
*
* Useful on models that have a related Ethos free on board type model. The relation is connected via the `ethos_free_on_board_type_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Finance
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosFreeOnBoardTypeModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosFreeOnBoardType']));
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
    public function getEthosFreeOnBoardTypeAttribute() {
        return $this->ethosFreeOnBoardType();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_free_on_board_type_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosFreeOnBoardType()
    {
        if (!empty($this->ethos_free_on_board_type_id)) {
            $client = new FreeOnBoardTypesClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_free_on_board_type_id;
            $cacheKey = 'ms.ethos-php.laravel.free-on-board-type.' . $ethosId;

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
            return (object)$client->readById($this->ethos_free_on_board_type_id)->data();
        }
        return (object)[];
    }
}