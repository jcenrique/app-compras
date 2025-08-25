<?php

return [

    'title' => 'Erregistratu',

    'heading' => 'Kontu bat sortu',

    'actions' => [

        'login' => [
            'before' => 'edo',
            'label' => 'zure kontuan saioa hasi',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Posta elektronikoa',
        ],

        'name' => [
            'label' => 'Izena',
        ],

        'password' => [
            'label' => 'Pasahitza',
            'validation_attribute' => 'pasahitza',
        ],

        'password_confirmation' => [
            'label' => 'Pasahitzaren berrespena',
        ],

        'actions' => [

            'register' => [
                'label' => 'Erregistratu',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Erregistratzeko saiakera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundo igaro ondoren.',
        ],

    ],

];
