<?php

return [

    'label' => 'Inportatu :label',

    'modal' => [

        'heading' => 'Inportatu :label',

        'form' => [

            'file' => [

                'label' => 'Fitxategia',

                'placeholder' => 'Kargatu CSV fitxategi bat',

                'rules' => [
                    'duplicate_columns' => '{0} Fitxategiak ez du hutsik dagoen zutabe baten goibururik izan behar.|{1,*} Fitxategiak ez du zutabe bikoitzik izan behar: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Zutabeak',
                'placeholder' => 'Hautatu zutabe bat',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Deskargatu adibideko CSV fitxategia',
            ],

            'import' => [
                'label' => 'Inportatu',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Inportazioa burutu da',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Deskargatu huts egin duten errenkaden informazioa|Deskargatu huts egin duten errenkaden informazioa',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Kargatutako CSV fitxategia gehiegi handia da',
            'body' => 'Ez da posible 1 fila baino gehiago inportatzea.|Ez da posible :count fila baino gehiago inportatzea.',
        ],

        'started' => [
            'title' => 'Inportazioa hasi da',
            'body' => 'Zure inportazioa hasi da eta 1 fila prozesatuko da atzean.|Zure inportazioa hasi da eta :count fila prozesatuko da atzean.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'errore',
        'system_error' => 'Sistema errorea, jarri harremanetan laguntza zerbitzuarekin.',
        'column_mapping_required_for_new_record' => 'Zutabea :attribute ez da fitxategiko zutabe bati atxiki, baina hori beharrezkoa da erregistro berri bat sortzeko.',
    ],

];
