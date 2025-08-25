<?php

return [

    'title' => 'Egiaztatu zure helbide elektronikoa',

    'heading' => 'Egiaztatu zure helbide elektronikoa',

    'actions' => [

        'resend_notification' => [
            'label' => 'Berriro bidali',
        ],

    ],

    'messages' => [
        'notification_not_received' => '¿Ez al duzu jaso dugun posta elektronikoa?',
        'notification_sent' => 'Mezu elektronikoa bidali dugu :email helbidera, zure helbide elektronikoa egiaztatzeko argibidekin.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Mezu elektronikoa birbidali dugu.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Eskaera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundo igaro ondoren.',
        ],

    ],

];
