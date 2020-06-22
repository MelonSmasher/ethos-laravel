<?php


namespace MelonSmasher\EthosPHP\Laravel\Facade;


use Illuminate\Support\Facades\Facade;

/**
 * Class Ethos
 *
 * The Ethos facade.
 *
 * @package MelonSmasher\EthosLaravel
 * @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
 * @author Alex Markessinis
 */
class Ethos extends Facade
{
    /**
     * Get Facade
     *
     * Returns the facade
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ethos';
    }
}