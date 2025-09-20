<?php

declare(strict_types=1);

return [
    'name' => 'Non',
    'email' => 'Email',
    'password' => 'Mot de passe',
    'email_verified_at' => 'Email vérifié',
    'created_at' => 'Date de création',
    'updated_at' => 'Date de mise à jour',
    'deleted_at' => 'Date de suppression',

    'market_management_nav_group' => 'Gestion du marché',

    'export_excel' => 'Exporter vers Excel',

    /*|--------------------------------------------------------------------------
    | User Resource
    |--------------------------------------------------------------------------
    */

    'user_resource_label' => 'Utilisateur',
    'user_resource_plural_label' => 'Utilisateurs',




    'user_management_nav_group' => 'Gestion des utilisateurs',

    'panel_avalaible_titles' => [
        'title' => 'Panneaux disponibles',
        'description' => 'électionnez le panneau auquel vous souhaitez passer',
        'admin' => 'Panneau d\'administration',
        'app' => 'Panneau d\'application',
    ],

    'latest_users' => 'Derniers utilisateurs',
    'latest_users_description' => 'Liste des derniers utilisateurs enregistrés dans le système',
    'latest_users_empty' => 'Aucun utilisateur enregistré.',


    /*|--------------------------------------------------------------------------
    | Category Resource
    |--------------------------------------------------------------------------
    */
    'category_resource_label' => 'Catégorie',
    'category_resource_plural_label' => 'Catégories',
    'image' => 'Image',
    'image_helper_text' => 'L\'image doit être au format PNG, JPG ou JPEG et ne doit pas dépasser 2 Mo.',
    'active' => 'Actif',
    'description' => 'Description',
    'slug' => 'Slug',

    /*|--------------------------------------------------------------------------
    | Market Resource
    |--------------------------------------------------------------------------
    */
    'market_resource_label' => 'Marché',
    'market_resource_plural_label' => 'Marchés',
    'logo' => 'Logo',

    /*|--------------------------------------------------------------------------
    | Product Resource
    |--------------------------------------------------------------------------
    */
    'product_resource_label' => 'Produit',
    'product_resource_plural_label' => 'Produits',
    'price' => 'Prix',
    'brand' => 'Marque',
    'is_active' => 'Est actif',
    'category' => 'Catégorie',
    'market' => 'Marché',
    'is_unique_market' => 'Produit unique par marché',
    'product_already_exists' => 'Le produit existe déjà dans le supermarché sélectionné.',
    'product_not_found' => 'Le produit n\'a pas été trouvé.',
    'product' => 'Produit',
    'products' => 'Produits',
    'product_created' => 'Produit créé avec succès.',
    'is_favorite' => 'Favori',
    'show_favorites' => 'Afficher les favoris',
    'inactive' => 'Inactif',
    'update_price' => 'Mettre à jour les produits',



   /*
    |--------------------------------------------------------------------------
    | Section Resource
    |--------------------------------------------------------------------------
    */
    'section_resource_label' => 'Section',
    'section_resource_plural_label' => 'Sections',


    /*|--------------------------------------------------------------------------
    | Client Resource
    |--------------------------------------------------------------------------
    */
    'clients_nav_group' => 'Gestion des clients',

    'client_resource_label' => 'Client',
    'client_resource_plural_label' => 'Clients',
    'verification_email_sent' => 'Email de vérification envoyé',
    'resend_verification_email' => 'Renvoyer l\'email de vérification',
    'verification_email_resent' => 'Email de vérification renvoyé',


    /*|--------------------------------------------------------------------------
    | Order Resource
    |--------------------------------------------------------------------------
    */
    'order_resource_label' => 'Commande',
    'order_resource_plural_label' => 'Commandes',
    'notes' => 'Notes',
    'order_date' => 'Date de la commande',
    'total_price' => 'Prix total',
    'order_status' => 'État de la commande',
    'items_count' => 'Nombre d\'articles',
    'items_count_prefix' => 'Nº d\'articles: ',
    'order_statuses' => [
        'pending' => 'En attente',
        'processing' => 'En cours de traitement',
        'completed' => 'Terminé',
        'canceled' => 'Annulé',
    ],
    'items' => 'Articles',
    'product' => 'Product',
    'quantity' => 'Quantité',
    'total' => 'Total',
    'order_items_label' => 'Article de commande',
    'order_items_plural_label' => 'Articles de commande',
    'subtotal' => 'Subtotal',
    'comprar' => 'Acheter',
    'copy' => 'Copier',
    'is_basket' => 'Dans le chariot',
    'finalize_order' => 'Finaliser la commande',
    'save_pending' => 'Enregistrer en attente',
    'save_pending_tooltip' => 'Enregistrer les produits en attente de la commande dans une nouvelle commande',
    'basket' => 'Chariot',
    'items_added_to_basket' => 'Articles ajoutés au chariot',
    'basket_empty' => 'Le panier est vide',
    'open_order' => 'Rouvrir la commande',
    'change_status' => 'Changer le statut',
    'add_to_basket' => 'Ajouter au panier',
    'pending_items_saved' => 'Produits en attente enregistrés dans une nouvelle commande',
    'change_quantity' => 'Changer la quantité',
    'change_quantity_tooltip' => 'Modifier la quantité du produit dans la commande',

    'copy_order' => 'Copier la commande',
    'copy_order_tooltip' => 'Copier la commande actuelle vers une nouvelle commande',
    'order_copied_successfully' => 'Commande copiée avec succès.',
    'copy_order_modal_heading' => 'Copier la commande',
    'copy_order_modal_submit_action_label' => 'Copier la commande',

    'new_order' => 'Nouvelle commande',
    'order_copied' => 'Commande copiée avec succès',

    'order_copied_to' => 'Commande copiée vers :market',
    'order_copied_to_helper_text' => 'La commande a été copiée vers le supermarché sélectionné.',
    'order_copied_to_required' => 'Le supermarché est obligatoire',
    'order_copied_to_placeholder' => 'Sélectionnez un supermarché',
    'copy_order_description' => 'Sélectionnez la nouvelle date pour la commande. La commande sera copiée avec tous ses produits et quantités.',
    'copy_order' => 'Copier la commande',

    'all_items_in_basket' => 'Tous les articles sont dans le chariot',
    'finalize_order_confirmation' => 'La commande a été finalisée',
    'order_finalized' => 'Commande finalisée avec succès',
];
