<?php

return [

    'label' => 'Esportatu :label',

    'modal' => [

        'heading' => 'Esportatu :label',

        'form' => [

            'columns' => [

                'label' => 'Columnak',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column gaituta',
                    ],

                    'label' => [
                        'label' => 'etiketa  :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Esportatu',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Esportazioa burutu da',

            'actions' => [

                'download_csv' => [
                    'label' => 'Deskargatu .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Deskargatu .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Esportazioa gehiegi handia da',
            'body' => 'Ez da posible 1 fila baino gehiago esportatzea.|Ez da posible :count fila baino gehiago esportatzea.',
        ],

        'started' => [
            'title' => 'Esportazioa hasi da',
            'body' => 'Zure esportazioa hasi da eta 1 fila prozesatuko da atzean.|Zure esportazioa hasi da eta :count fila prozesatuko da atzean.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
