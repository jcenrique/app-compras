<?php

declare(strict_types=1);

return [
    'name' => 'Name',
    'email' => 'Email',
    'password' => 'Password',
    'email_verified_at' => 'Email verified',
    'created_at' => 'Creation date',
    'updated_at' => 'Update date',
    'deleted_at' => 'Deletion date',

    /*
    |--------------------------------------------------------------------------
    | Filament Resource
    |--------------------------------------------------------------------------
    */

    'panel_avalaible_titles' => [
        'title'=> 'Panels available',
        'description' => 'Select the panel you want to switch to',
        'admin' => 'Admin Panel',
        'app' => 'Application Panel',
    ],

    'user_resource_label' => 'User',
    'user_resource_plural_label' => 'Users',



    'user_management_nav_group' => 'User Management',

    'latest_users' => 'Latest Users',
    'latest_users_description' => 'List of the latest registered users in the system',
    'latest_users_empty' => 'No users registered yet.',

      /*|--------------------------------------------------------------------------
    | Category Resource
    |--------------------------------------------------------------------------
    */
    'category_resource_label' => 'Category',
    'category_resource_plural_label' => 'Categories',
    'image' => 'Image',
    'image_helper_text' => 'The image must be in PNG, JPG, or JPEG format and must not exceed 2MB in size.',
    'active' => 'Active',
    'description' => 'Description',
    'slug' => 'Slug',

    /*|--------------------------------------------------------------------------
    | Market Resource
    |--------------------------------------------------------------------------
    */
    'market_resource_label' => 'Market',
    'market_resource_plural_label' => 'Markets',
    'logo' => 'Logo',

    /*|--------------------------------------------------------------------------
    | Product Resource
    |--------------------------------------------------------------------------
    */
    'product_resource_label' => 'Product',
    'product_resource_plural_label' => 'Products',
    'price' => 'Price',

    'brand' => 'Brand',
    'is_active' => 'Is Active',
    'category' => 'Category',
    'market' => 'Market',
    'is_unique_market' => 'Unique product per market',
    'product_already_exists' => 'The product already exists in the selected supermarket.',
    'product_not_found' => 'The product was not found.',
    'product' => 'Product',
    'products' => 'Products',
    'product_created' => 'Product created successfully.',
    'is_favorite' => 'Favorite',
    'show_favorites' => 'Show favorites',

          /*|--------------------------------------------------------------------------
    | Section Resource
    |--------------------------------------------------------------------------
    */
    'section_resource_label' => 'Section',
    'section_resource_plural_label' => 'Sections',

    /*|--------------------------------------------------------------------------
    | Client Resource
    |--------------------------------------------------------------------------
    */
    'clients_nav_group' => 'Client Management',

    'client_resource_label' => 'Client',
    'client_resource_plural_label' => 'Clients',
    'verification_email_sent' => 'Verification email sent',
    'resend_verification_email' => 'Resend verification email',
    'verification_email_resent' => 'Verification email resent',

    /*|--------------------------------------------------------------------------
    | Order Resource
    |--------------------------------------------------------------------------
    */
    'order_resource_label' => 'Order',
    'order_resource_plural_label' => 'Orders',
    'notes' => 'Notes',
    'order_date' => 'Order date',
    'total_price' => 'Total price',
    'order_status' => 'Order status',
    'items_count' => 'Items count',
    'items_count_prefix' => 'Items count: ',
    'order_statuses' => [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'canceled' => 'Canceled',
    ],
    'items' => 'Items',
    'product' => 'Product',
    'quantity' => 'Quantity',
    'total' => 'Total',
    'order_items_label' => 'Order Item',
    'order_items_plural_label' => 'Order Items',
    'subtotal' => 'Subtotal',
    'comprar' => 'Buy',
    'copy' => 'Copy',
    'is_basket' => 'In the basket',
    'finalize_order' => 'Finalize Order',
    'save_pending' => 'Save Pending Items',
    'save_pending_tooltip' => 'Save pending items in a new order',
    'basket' => 'Basket',
    'items_added_to_basket' => 'Items added to the basket',
    'basket_empty' => 'The basket is empty',
    'open_order' => 'Reopen order',
    'change_status' => 'Change status',
    'add_to_basket' => 'Add to basket',
    'pending_items_saved' => 'Pending items saved in a new order',
    'change_quantity' => 'Change quantity',
    'change_quantity_tooltip' => 'Change the quantity of the product in the order',

    'copy_order' => 'Copy order',
    'copy_order_tooltip' => 'Copy the current order to a new order',
    'order_copied_successfully' => 'Order copied successfully.',
    'copy_order_modal_heading' => 'Copy order',
    'copy_order_modal_submit_action_label' => 'Copy order',

    'new_order' => 'New order',
    'order_copied' => 'Order copied successfully',

    'order_copied_to' => 'Order copied to :market',
    'order_copied_to_helper_text' => 'The order has been copied to the selected supermarket.',
    'order_copied_to_required' => 'Supermarket is required',
    'order_copied_to_placeholder' => 'Select a supermarket',
    'copy_order_description' => 'Select the new date for the order. The order will be copied with all its products and quantities.',
    'copy_order' => 'Copy order',

    'all_items_in_basket' => 'All items are in the basket',
    'finalize_order_confirmation' => 'The order has been finalized',
    'order_finalized' => 'Order finalized successfully',

];
