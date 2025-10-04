<?php

declare(strict_types=1);

return [
    'name' => 'Izena',
    'email' => 'Emaila',
    'password' => 'Pasahitza',
    'email_verified_at' => 'Emaila egiaztatuta',
    'created_at' => 'Sortu denbora',
    'updated_at' => 'Eguneratu denbora',
    'deleted_at' => 'Ezabatutako denbora',

    'market_management_nav_group' => 'Merkatuaren kudeaketa',

    /*
    |--------------------------------------------------------------------------
    | Filament Resource
    |--------------------------------------------------------------------------
    */
    'export_excel' => 'Esportatu Excelera',

    'panel_avalaible_titles' => [
        'title' => 'Eskuragarri dauden panelak',
        'description' => 'Aldatu nahi duzun panelaren hautaketa',
        'admin' => 'Administrazio Panel',
        'app' => 'Aplikazio Panel',
    ],
    'user_resource_label' => 'Erabiltzaileak',
    'user_resource_plural_label' => 'Erabiltzaileak',



    'user_management_nav_group' => 'Erabiltzaileen kudeaketa',

    'latest_users' => 'Azken Erabiltzaileak',
    'latest_users_description' => 'Sistemara azken erregistratutako erabiltzaileen zerrenda',
    'latest_users_empty' => 'Oraindik ez dago erabiltzaile erregistraturik.',


    /*|--------------------------------------------------------------------------
    | Category Resource
    |--------------------------------------------------------------------------
    */
    'category_resource_label' => 'Kategoria',
    'category_resource_plural_label' => 'Kategoriek',
    'image' => 'Irudia',
    'image_helper_text' => 'Irudia PNG, JPG edo JPEG formatuan izan behar da eta 2MB-ko tamaina gainditu behar ez du.',
    'active' => 'Aktiboa',
    'description' => 'Deskribapena',
    'slug' => 'Slug',

    /*|--------------------------------------------------------------------------
    | Market Resource
    |--------------------------------------------------------------------------
    */
    'market_resource_label' => 'Merkatua',
    'market_resource_plural_label' => 'Merkatuak',
    'logo' => 'Logo',

    /*|--------------------------------------------------------------------------
    | Product Resource
    |--------------------------------------------------------------------------
    */
    'product_resource_label' => 'Produktu',
    'product_resource_plural_label' => 'Produktuak',

    'brand' => 'Marka',
    'price' => 'Prezioa',
    'is_active' => 'Aktiboa',
    'category' => 'Kategoria',
    'market' => 'Merkatua',
    'is_unique_market' => 'Produktu bakarra merkatuan',
    'product_already_exists' => 'Produktua dagoeneko hautatutako supermerkatuan existitzen da.',
    'product_not_found' => 'Produktua ez da aurkitu.',
    'product' => 'Produktua',
    'products' => 'Produktuak',
    'product_created' => 'Produktua ondo sortu da.',
    'is_favorite' => 'Gogokoena',
    'show_favorites' => 'Gogokoenak erakutsi',
    'inactive' => 'Inaktibo',
    'update_price' => 'Produktuak eguneratu',
    'add_to_cart' => 'Gehitu saskira',

    /*|--------------------------------------------------------------------------
    | Section Resource
    |--------------------------------------------------------------------------
    */
    'section_resource_label' => 'Atala',
    'section_resource_plural_label' => 'Atalak',


    /*|--------------------------------------------------------------------------
    | Client Resource
    |--------------------------------------------------------------------------
    */
    'clients_nav_group' => 'Bezeroen kudeaketa',

    'client_resource_label' => 'Bezero',
    'client_resource_plural_label' => 'Bezeroak',
    'verification_email_sent' => 'Berrespen-emaila bidali da',
    'resend_verification_email' => 'Berrespen-emaila berriro bidali',
    'verification_email_resent' => 'Berrespen-emaila berriro bidali da',

     /*|--------------------------------------------------------------------------
    | Order Resource
    |--------------------------------------------------------------------------
    */
    'order_resource_label' => 'Eskaria',
    'order_resource_plural_label' => 'Eskariak',
    'notes' => 'Oharra',
    'order_date' => 'Eskariaren data',
    'total_price' => 'Prezio osoa',
    'order_status' => 'Eskariaren egoera',
    'items_count' => 'Produktu kopurua',
    'items_count_prefix' => 'Produktu kopurua: ',
    'order_statuses' => [
        'pending' => 'Zain',
        'processing' => 'Prozesatzen',
        'completed' => 'Osatua',
        'canceled' => 'Ezeztatuta',
    ],

    'items' => 'Produktuak',
    'product' => 'Produktu',
    'quantity' => 'Kantitatea',
    'total' => 'Total',
    'order_items_label' => 'Produktuaren eskaria',
    'order_items_plural_label' => 'Produktuaren eskariak',
    'subtotal' => 'Subtotal',
    'comprar' => 'Erosi',
    'copy' => 'Kopiatu',
    'is_basket' => 'Saskian',
    'finalize_order' => 'Eskaria amaitu',
    'save_pending' => 'Gorde zain',
    'save_pending_tooltip' => 'Gorde eskariaren zain dauden produktuak beste eskari batean',
    'basket' => 'Saskia',
    'items_added_to_basket' => 'Produktuak saskira gehitu dira',

    'basket_empty' => 'Saskia hutsik dago',
    'open_order' => 'Eskaria berriro ireki',
    'change_status' => 'Egoera aldatu',
    'add_to_basket' => 'Saskira gehitu',
    'pending_items_saved' => 'Zain dauden produktuak eskaera berrian gorde dira',
    'change_quantity' => 'Kantitatea aldatu',
    'change_quantity_tooltip' => 'Aldatu produktuen kantitatea eskaeran',

    'copy_order' => 'Eskaria kopiatu',
    'copy_order_tooltip' => 'Uneko eskaria beste eskaera batean kopiatu',
    'order_copied_successfully' => 'Eskaria ondo kopiatu da.',
    'copy_order_modal_heading' => 'Eskaria kopiatu',
    'copy_order_modal_submit_action_label' => 'Eskaria kopiatu',


    'new_order' => 'Eskaera berria',
    'order_copied' => 'Eskaria ondo kopiatu da',


    'order_copied_to' => 'Eskaria kopiatu da :market-(e)ra',
    'order_copied_to_helper_text' => 'Eskaria hautatutako supermerkatura kopiatu da.',
    'order_copied_to_required' => 'Supermerkatua beharrezkoa da',
    'order_copied_to_placeholder' => 'Hautatu supermerkatu bat',
    'copy_order_description' => 'Hautatu eskaeraren data berria. Eskaera produktu eta kantitate guztiekin kopiatu egingo da.',
    'copy_order' => 'Eskaria kopiatu',

    'all_items_in_basket' => 'Produktu guztiak saskian daude',
    'finalize_order_confirmation' => 'Eskaria amaitu da',
    'order_finalized' => 'Eskaria ondo amaitu da',
];
