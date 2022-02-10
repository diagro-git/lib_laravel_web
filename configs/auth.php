<?php
return [

    'defaults' => [
        'guard' => 'diagro',
    ],


    'guards' => [
        'diagro' => [
            'driver' => 'diagro-aat',
            'provider' => 'diagro-token'
        ]
    ],


    'providers' => [
        'diagro-token' => [
            'driver' => 'token',
            'token_class_name' => Diagro\Token\ApplicationAuthenticationToken::class,
            'model' => Diagro\Token\Model\User::class,
        ],
    ],

];