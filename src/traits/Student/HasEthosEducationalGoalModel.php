<?php


namespace MelonSmasher\EthosPHP\Laravel\Traits\Student;


use MelonSmasher\EthosPHP\Student\EducationalGoalsClient;
use Illuminate\Support\Facades\Cache;

/**
* Trait HasEthosEducationalGoalModel
*
* Useful on models that have a related Ethos educational goal model. The relation is connected via the `ethos_educational_goal_id` attribute.
*
* @package MelonSmasher\EthosLaravel\Traits\Student
* @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
* @author Alex Markessinis
*/
trait HasEthosEducationalGoalModel
{
    /**
     * HasEthosAccountFundsAvailableModel constructor.
     */
    public function __construct()
    {
        $this->appends = array_unique(array_merge($this->appends, ['ethosEducationalGoal']));
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
    public function getEthosEducationalGoalAttribute() {
        return $this->ethosEducationalGoal();
    }

    /**
    * Ethos Model
    *
    * The Ethos Model related by the `ethos_educational_goal_id`.
    *
    * @return object
    * @throws \GuzzleHttp\Exception\GuzzleException
    */
    public function ethosEducationalGoal()
    {
        if (!empty($this->ethos_educational_goal_id)) {
            $client = new EducationalGoalsClient(getEthosSession());
            $cacheTTL = config('ethos.trait_response_cache_time', 0);
            $ethosId = $this->ethos_educational_goal_id;
            $cacheKey = 'ms.ethos-php.laravel.educational-goal.' . $ethosId;

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
            return (object)$client->readById($this->ethos_educational_goal_id)->data();
        }
        return (object)[];
    }
}