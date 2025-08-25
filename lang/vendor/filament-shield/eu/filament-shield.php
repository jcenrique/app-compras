<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Izena',
    'column.guard_name' => 'Guard',
    'column.roles' => 'Rolak',
    'column.permissions' => 'Baimenak',
    'column.updated_at' => 'Eguneratua',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Izena',
    'field.guard_name' => 'Guard',
    'field.permissions' => 'Baimenak',
    'field.select_all.name' => 'Hautatu guztiak',
    'field.select_all.message' => 'Gaitu rol honetarako une honetan <span class="text-primary font-medium">gaituta</span> dauden baimen guztiak',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Erabiltzaileen kudeaketa',
    'nav.role.label' => 'Rola',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Rola',
    'resource.label.roles' => 'Rolak',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entitateak',
    'resources' => 'Baliabideak',
    'widgets' => 'Widgetak',
    'pages' => 'Orrialdeak',
    'custom' => 'Baimen pertsonalizatuak',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Ez duzu sarbide baimenik',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Ikusi erregistro jakin bat',
        'view_any' => 'Ikusi erregistroen zerrenda',
        'create' => 'Sortu',
        'update' => 'Eguneratu',
        'delete' => 'Ezabatu erregistro jakin bat',
        'delete_any' => 'Ezabatu hainbat erregistro aldi berean',
        'force_delete' => 'Bultzatu ezabatzea erregistro jakin bat',
        'force_delete_any' => 'Bultzatu ezabatzea hainbat erregistro',
        'restore' => 'Berreskuratu erregistro jakin bat',
        'reorder' => 'Reordenatu',
        'restore_any' => 'Berreskuratu hainbat erregistro',
        'replicate' => 'Errepikatu',
    ],
];
