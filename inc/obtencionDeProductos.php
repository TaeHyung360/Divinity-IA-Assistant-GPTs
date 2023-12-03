<?php
//===================================================================================================================================================
// obtenerProductos()
//===================================================================================================================================================
// Devuelve una lista de todos los productos de la base datos
//===================================================================================================================================================
function obtenerProductos() {
    global $wpdb;

    // Consultar los productos en wp_posts, que tengan la propiedad 'product' y 'publish'
    $query_posts = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'publish'";
    $productos = $wpdb->get_results($query_posts);

    // Creamos un array donde introducimos los productos
    $lista_productos = array();

    foreach ($productos as $producto) {
        // Obtener metadatos del producto, en la tabla postmeta y rescatamos los campos precio, imagen, estado de stock
        $precio = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = '_price'", $producto->ID));
        $galeria = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = 'product_image_gallery'", $producto->ID));
        $estado_stock = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = '_stock_status'", $producto->ID));

        // Agregar al array de respuesta con los valores de los productos
        $lista_productos[] = array(
            'ID' => $producto->ID,
            'nombre' => $producto->post_title,
            'precio' => $precio,
            'galeria' => explode(',', $galeria), 
            'estado_stock' => $estado_stock
        );
    }
    return $lista_productos;
}
