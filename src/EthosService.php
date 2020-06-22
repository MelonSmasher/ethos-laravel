<?php


namespace MelonSmasher\EthosPHP\Laravel;


use MelonSmasher\EthosPHP\ErpBackend;
use MelonSmasher\EthosPHP\Ethos;
use Illuminate\Support\Facades\Cache;

/**
 * Class EthosService
 *
 * The base Ethos service.
 *
 * @package MelonSmasher\EthosLaravel
 * @license https://raw.githubusercontent.com/MelonSmasher/ethos-laravel/master/LICENSE MIT
 * @author Alex Markessinis
 */
class EthosService
{
    /**
     * Ethos
     *
     * The Ethos session.
     *
     * @var Ethos
     */
    private $ethos;

    /**
     * EthosService constructor.
     *
     * Constructs the Ethos session.
     * Utilizes the cache to store active Ethos sessions.
     * This reduces API calls for authentication.
     *
     * @param $secret
     * @param $baseURL
     * @param $erpBackend
     */
    public function __construct($secret, $baseURL, $erpBackend)
    {
        $secret = $this->validate($secret, '');
        $baseURL = $this->validate($baseURL, 'https://integrate.elluciancloud.com');
        $erpBackend = $this->validate($erpBackend, ErpBackend::COLLEAGUE);

        $ethos = Cache::get('melonsmasher_ethos_session', null);
        if (empty($ethos)) {
            $ethos = serialize(new Ethos($secret, $baseURL, $erpBackend));
            Cache::put('melonsmasher_ethos_session', 240, $ethos);
        }
        $this->ethos = unserialize($ethos);
    }

    /**
     * Validate
     *
     * Validates a value. Returns a default value if nothing is passed in. Decodes JSON if needed.
     *
     * @param $val
     * @param $default
     * @param bool $json
     *
     * @return string|ErpBackend
     */
    private function validate($val, $default, $json = false)
    {
        if (!is_null($val)) {
            if ($json) {
                return json_decode($val, true);
            }
            return $val;
        }
        return $default;
    }

    /**
     * Get
     *
     * Returns the Ethos session instance.
     *
     * @return Ethos
     */
    public function get()
    {
        return $this->ethos;
    }

}