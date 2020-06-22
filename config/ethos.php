<?php

return [

    /*
     * Your Ethos API key / refresh token.
     */
    'secret' => env('ETHOS_SECRET'),

    /*
     * The base url that should be used to connect to Ethos. If omitted https://integrate.elluciancloud.com is used.
     *
     * eg: https://integrate.elluciancloud.com
     * eg: http://localhost
     *
     */
    'base_url' => env('ETHOS_BASE_URL', 'https://integrate.elluciancloud.com'),

    /*
     * The ERP backend that is connected to Ethos. Must be either 'banner' or 'colleague'. If nothing is supplied 'colleague' is used.
     */
    'erp_backend' => (strtolower(env('ETHOS_ERP_BACKEND', 'colleague')) === 'banner') ? \MelonSmasher\EthosPHP\ErpBackend::BANNER : \MelonSmasher\EthosPHP\ErpBackend::COLLEAGUE,

    /*
     * How long trait responses should remain in the cache in seconds. False or 0 to disable.
     */
    'trait_response_cache_time' => env('ETHOS_TRAIT_CACHE_TTL', false)
];