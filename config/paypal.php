<?php

return [

    # Account credentials from developer portal
    'account' => [
        // sandbox
        'sandbox_client_id' => env('PAYPAL_CLIENT_ID', ''),
        'sandbox_client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
        // live
        'live_client_id' => env('PAYPAL_LIVE_ID', ''),
        'live_client_secret' => env('PAYPAL_LIVE_SECRET', ''),
    ],

    'settings'  => [
        # Define your application mode here (LIVE or SANDBOX)
        'mode' => env('PAYPAL_MODE', 'sandbox'),
        // Connection TimeOut
        'http.ConnectionTimeOut'    =>  3000,
        // Loging information
        'log.LogEnabled'      =>  true,
        'log.FileName'         =>   storage_path()."/logs/paypal.log",
        // Level can accept (Debug, Fine, Info, Warning, Error )
        'log.LogLevel'         =>   "DEBUG"
    ],
];
