<?php

return [
    'driver' => [
        'base' => 'https://auth.sch.bme.hu',
        'client_id' => env('AUTHSCH_CLIENT_ID'),
        'client_secret' => env('AUTHSCH_CLIENT_SECRET'),
        'redirect' => ['main.auth.callback', 'authsch'] // Route where a callback route is found
    ],

    'scopes' => [
        'displayName', // Teljes név
        'sn', // Vezetéknév
        'givenName', // Keresztnév
        'mail', // E-mail cím
        'eduPersonEntitlement', // Körtagságok
        'bmeunitscope', // Egyetemi státusz
        'linkedAccounts', // SCH account, VIR account és Címtár account
        'mobile', // Telefonszám
        'niifPersonOrgID' // Neptun kód
//        'entrants', // Színes belépők
//        'niifEduPersonAttendedCourse', // Hallgatott tárgyak
//        'admembership', // KSZK-s AD tagságok
    ],

    'redirect_route' => 'home'
];
