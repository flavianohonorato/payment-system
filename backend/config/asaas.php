<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações de API do Asaas
    |--------------------------------------------------------------------------
    */
    'api_key' => env('ASAAS_API_KEY', ''),
    'api_url' => env('ASAAS_API_URL', 'https://api.asaas.com'),
    'timeout' => env('ASAAS_TIMEOUT', 30),
    'sandbox' => env('ASAAS_SANDBOX', true),
    'webhook_token' => env('ASAAS_WEBHOOK_TOKEN', ''),
];
