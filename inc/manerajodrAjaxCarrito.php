<?php
//===================================================================================================================================================
// divinity_añadir_al_carrito()
//===================================================================================================================================================
add_action('wp_ajax_añadir_al_carrito', 'divinity_añadir_al_carrito');
add_action('wp_ajax_nopriv_añadir_al_carrito', 'divinity_añadir_al_carrito'); // Para usuarios no autenticados

function divinity_añadir_al_carrito() {
    if (!isset($_POST['productos']) || !is_array($_POST['productos'])) {
        wp_send_json_error('Datos inválidos');
        wp_die();
    }

    $producto_ids = $_POST['productos'];
    foreach ($producto_ids as $producto_id) {
        $producto_id = intval($producto_id); // La función intval() en PHP se utiliza para obtener el valor entero de una variable. 
        $cantidad = 1; // Definimos la cantidad
        WC()->cart->add_to_cart($producto_id, $cantidad);
    }

    wp_send_json_success('Productos añadidos al carrito');
    wp_die();
}
