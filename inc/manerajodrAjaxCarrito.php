<?php
//===================================================================================================================================================
// divinity_añadir_al_carrito()
//===================================================================================================================================================
// Añade la lsita de productos seleccionados al carrito de Woocommerce
//===================================================================================================================================================
add_action('wp_ajax_añadir_al_carrito', 'divinity_añadir_al_carrito');
add_action('wp_ajax_nopriv_añadir_al_carrito', 'divinity_añadir_al_carrito'); // Para usuarios no autenticados

function divinity_añadir_al_carrito() {
    error_log('AJAX Request Received');
    if (!isset($_POST['productos']) || !is_array($_POST['productos'])) {
        error_log('Datos inválidos: ' . print_r($_POST, true));
        wp_send_json_error('Datos inválidos');
        wp_die();
    }

    $producto_ids = $_POST['productos'];
    error_log('Producto IDs: ' . implode(', ', $producto_ids));
    foreach ($producto_ids as $producto_id) {
        error_log('Intentando añadir al carrito: Producto ID ' . $producto_id);
        $producto_id = intval($producto_id); // La función intval() en PHP se utiliza para obtener el valor entero de una variable. 
        $cantidad = 1; // Definimos la cantidad
        $result = WC()->cart->add_to_cart($producto_id, $cantidad);
        if (!$result) {
            error_log('Error añadiendo al carrito: Producto ID ' . $producto_id);
            wp_send_json_error('No se pudo añadir el producto al carrito');
            wp_die();
        }
    }
    wp_send_json_success('Productos añadidos al carrito');
    wp_die();
}
