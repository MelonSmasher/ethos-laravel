<?php


use MelonSmasher\EthosPHP\Laravel\Facade\Ethos;

/**
 * Get an Ethos session
 *
 * Reads env vars and returns an Ethos session.
 *
 * @return \MelonSmasher\EthosPHP\Ethos
 */
function getEthosSession()
{
    return Ethos::get();
}