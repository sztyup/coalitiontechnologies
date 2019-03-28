<?php

return [
    'default_provider' => 'bmejegy',

    'providers' => [
        'bmejegy' => [
            'config' => [
                'username' => env('BMEJEGY_USERNAME'),
                'password' => env('BMEJEGY_PASSWORD')
            ]
        ]
    ]
];
