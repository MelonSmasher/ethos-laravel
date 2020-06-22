<?php

namespace MelonSmasher\EthosPHP\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

class Ethos extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'ethos';
    }

}