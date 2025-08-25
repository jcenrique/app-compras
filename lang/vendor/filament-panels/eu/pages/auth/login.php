<?php

return [

    'title' => 'Aksesua',

    'heading' => 'Hasi saioa zure kontuan',

    'actions' => [

        'register' => [
            'before' => 'edo',
            'label' => 'Kontu bat sortu',
        ],

        'request_password_reset' => [
            'label' => 'Pasahitza berreskuratzeko eskaera?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Posta elektronikoa',
        ],

        'password' => [
            'label' => 'Pasahitza',
        ],

        'remember' => [
            'label' => 'Gogoratu',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Sartu',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Estas kredentzialak ez dira gure erregistroekin bat etortzen.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Saiakera gehiegi. Mesedez, saiatu berriro :seconds segundo igaro ondoren.',
            'body' => 'Mesedez, saiatu berriro :seconds segundo igaro ondoren.',
        ],

    ],

];
