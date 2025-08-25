<?php

return [

    'title' => 'Berrezarri zure pasahitza',

    'heading' => 'Berrezarri zure pasahitza',

    'form' => [

        'email' => [
            'label' => 'Posta elektronikoa',
        ],

        'password' => [
            'label' => 'Pasahitza',
            'validation_attribute' => 'pasahitza',
        ],

        'password_confirmation' => [
            'label' => 'Pasahitzaren berrespena',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Pasahitza berrezarri',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Eskaera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundo igaro ondoren.',
        ],

    ],

];
