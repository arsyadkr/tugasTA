<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    | Didapat dari: Midtrans Dashboard → Settings → Access Keys
    | Sandbox : SB-Mid-server-xxxx
    | Production: Mid-server-xxxx
    */
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    | Dipakai di frontend (Snap.js).
    | Sandbox : SB-Mid-client-xxxx
    | Production: Mid-client-xxxx
    */
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    | false = sandbox (testing), true = production (live payment)
    */
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
];
