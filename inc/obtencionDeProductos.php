<?php
function obtenerProductos() {
    global $wpdb;

    // Consultar los productos en wp_posts
    $query_posts = "SELECT ID, post_title FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'publish'";
    $productos = $wpdb->get_results($query_posts);

    // Preparar la respuesta
    $lista_productos = array();

    foreach ($productos as $producto) {
        // Obtener metadatos del producto
        $precio = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = '_price'", $producto->ID));
        $galeria = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = 'product_image_gallery'", $producto->ID));
        $estado_stock = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = '_stock_status'", $producto->ID));

        // Agregar al array de respuesta
        $lista_productos[] = array(
            'ID' => $producto->ID,
            'nombre' => $producto->post_title,
            'precio' => $precio,
            'galeria' => explode(',', $galeria), // Suponiendo que las imágenes están separadas por comas
            'estado_stock' => $estado_stock
        );
    }

    return $lista_productos;
}
