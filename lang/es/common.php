<?php

declare(strict_types=1);

return [
    'name' => 'Nombre',
    'email' => 'Email',
    'password' => 'Contraseña  ',
    'email_verified_at' => 'Email verificado',
    'created_at' => 'Fecha de creación',
    'updated_at' => 'Fecha de actualización',
    'deleted_at' => 'Fecha de eliminación',
    'all' => 'Todos',
    'search' => 'Buscar',
    'actions' => 'Acciones',
    'save' => 'Guardar',
    'cancel' => 'Cancelar',
    'edit' => 'Editar',
    'delete' => 'Eliminar',
    'view' => 'Ver',


    'market_management_nav_group' => 'Gestión del supermercado',

    /*
    |--------------------------------------------------------------------------
    | Filament Resource
    |--------------------------------------------------------------------------
    */
    'export_excel' => 'Exportar a Excel',
    /*|--------------------------------------------------------------------------
    | User Resource
    |--------------------------------------------------------------------------
    */

    'user_resource_label' => 'Usuario',
    'user_resource_plural_label' => 'Usuarios',



    'user_management_nav_group' => 'Gestión de Usuarios',

    'panel_avalaible_titles' => [
        'title' => 'Paneles disponibles',
        'description' => 'Seleccione el panel al que desea cambiar',
        'admin' => 'Panel de Administración',
        'app' => 'Panel de Aplicación',
    ],

    'latest_users' => 'Últimos Usuarios',
    'latest_users_description' => 'Lista de los últimos usuarios registrados en el sistema',
    'latest_users_empty' => 'No hay usuarios registrados aún.',


    /*|--------------------------------------------------------------------------
    | Category Resource
    |--------------------------------------------------------------------------
    */
    'category_resource_label' => 'Categoría',
    'category_resource_plural_label' => 'Categorías',
    'image' => 'Imagen',
    'image_helper_text' => 'La imagen debe ser en formato PNG, JPG o JPEG y no debe exceder los 2MB de tamaño.',
    'active' => 'Activo',
    'description' => 'Descripción',
    'slug' => 'Slug',

    /*|--------------------------------------------------------------------------
    | Market Resource
    |--------------------------------------------------------------------------
    */
    'market_resource_label' => 'Supermercado',
    'market_resource_plural_label' => 'Supermercados',
    'logo' => 'Logo',

    /*|--------------------------------------------------------------------------
    | Product Resource
    |--------------------------------------------------------------------------
    */
    'product_resource_label' => 'Producto',
    'product_resource_plural_label' => 'Productos',

    'brand' => 'Marca',
    'price' => 'Precio',
    'is_active' => 'Activo',
    'category' => 'Categoría',
    'market' => 'Supermercado',
    'is_unique_market' => 'Producto único por supermercado',
    'product_already_exists' => 'El producto ya existe en el supermercado seleccionado.',
    'product_not_found' => 'El producto no fue encontrado.',
    'product' => 'Producto',
    'products' => 'Productos',
    'product_created' => 'Producto creado correctamente.',
    'is_favorite' => 'Favorito',
    'show_favorites' => 'Mostrar favoritos',
    'inactive' => 'Inactivo',
    'update_price' => 'Actualizar Productos',
    'format' => 'Formato',
    'Registro duplicado, no se puede crear' => 'Registro duplicado, no se puede crear',

    /*|--------------------------------------------------------------------------
    | Client Resource
    |--------------------------------------------------------------------------
    */
    'clients_nav_group' => 'Gestión de Clientes',

    'client_resource_label' => 'Cliente',
    'client_resource_plural_label' => 'Clientes',

    'verification_email_sent' => 'Email de verificación enviado',
    'resend_verification_email' => 'Reenviar email de verificación',
    'verification_email_resent' => 'Email de verificación reenviado',


    /*|--------------------------------------------------------------------------
    | Section Resource
    |--------------------------------------------------------------------------
    */
    'section_resource_label' => 'Sección',
    'section_resource_plural_label' => 'Secciones',


    /*|--------------------------------------------------------------------------
    | Order Resource
    |--------------------------------------------------------------------------
    */
    'order_resource_label' => 'Pedido',
    'order_resource_plural_label' => 'Pedidos',
    'notes' => 'Notas',
    'order_date' => 'Fecha del pedido',
    'total_price' => 'Precio total',
    'order_status' => 'Estado del pedido',
    'items_count' => 'Numero de productos',
    'items_count_prefix' => 'Nº de productos: ',
    'order_statuses' => [
        'pending' => 'Pendiente',
        'processing' => 'Procesando',
        'completed' => 'Completado',
        'canceled' => 'Cancelado',
    ],
    'items' => 'Productos',
    'product' => 'Producto',
    'quantity' => 'Cantidad',
    'total' => 'Total',
    'order_items_label' => 'Producto del pedido',
    'order_items_plural_label' => 'Productos del pedido',
    'subtotal' => 'Subtotal',
    'comprar' => 'Comprar',
    'copy' => 'Copiar',
    'is_basket' => 'En la cesta',
    'finalize_order' => 'Finalizar pedido',
    'save_pending' => 'Guardar pendientes',
    'save_pending_tooltip' => 'Guarda los productos pendientes del pedido en un nuevo pedido',
    'basket' => 'Cesta',


    'basket_empty' => 'La cesta está vacía',
    'open_order' => 'Reabrir pedido',
    'change_status' => 'Cambiar estado',
    'add_to_basket' => 'Añadir a la cesta',
    'items_added_to_basket' => 'Productos añadidos a la cesta',
    'pending_items_saved' => 'Productos pendientes guardados en un nuevo pedido',
    'change_quantity' => 'Cambiar cantidad',
    'change_quantity_tooltip' => 'Cambiar la cantidad del producto en el pedido',

    'copy_order' => 'Copiar pedido',
    'copy_order_tooltip' => 'Copiar el pedido actual a un nuevo pedido',
    'order_copied_successfully' => 'Pedido copiado correctamente.',
    'copy_order_modal_heading' => 'Copiar pedido',
    'copy_order_modal_submit_action_label' => 'Copiar pedido',

    'new_order' => 'Nuevo pedido',
    'order_copied' => 'Pedido copiado correctamente',


    'order_copied_to' => 'Pedido copiado a :market',
    'order_copied_to_helper_text' => 'El pedido ha sido copiado al supermercado seleccionado.',
    'order_copied_to_required' => 'El supermercado es obligatorio',
    'order_copied_to_placeholder' => 'Seleccione un supermercado',
    'copy_order_description' => 'Seleccione la nueva fecha para el pedido. El pedido se copiará con todos sus productos y cantidades.',
    'copy_order' => 'Copiar pedido',

    'all_items_in_basket' => 'Todos los productos están en la cesta',
    'finalize_order_confirmation' => 'El pedido se ha finalizado',
    'order_finalized' => 'Pedido finalizado correctamente',

// Cart Page
    'shopping_cart' => 'Carrito de Compras',
    'add_to_cart' => 'Añadir al carrito',
    'items_in_cart' => 'Productos en el carrito',
    'clear_cart' => 'Vaciar carrito',
    'cart_cleared' => 'Carrito vaciado correctamente',
    'order_now' => 'Hacer pedido',
    'order_placed_successfully' => 'Pedido realizado correctamente',
    'cart' => 'Carrito',
    'cart_empty' => 'El carrito está vacío',
    'item_removed_from_cart' => 'Producto eliminado del carrito',
    'item_quantity_increased' => 'Aumentada la cantidad del producto',
    'item_quantity_decreased' => 'Disminuida la cantidad del producto',
      'uncategorized' => 'Sin categoría',
    'increase_quantity' => 'Aumentar cantidad',
    'decrease_quantity' => 'Disminuir cantidad',
    'diferent_market_product' => 'Producto de supermercado diferente.',
    'product_not_add_to_cart' => 'El producto no se añadirá al carrito.'
];
