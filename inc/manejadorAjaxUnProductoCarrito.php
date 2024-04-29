<?php
//===================================================================================================================================================
// añadir_un_producto_al_carrito()
//===================================================================================================================================================
// Añade un producto al carrito de Woocommerce
//===================================================================================================================================================
add_action('wp_ajax_anadir_un_producto_al_carrito', 'añadir_un_producto_al_carrito');
add_action('wp_ajax_nopriv_anadir_un_producto_al_carrito', 'añadir_un_producto_al_carrito');

function añadir_un_producto_al_carrito() {
    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    $cart = WC()->cart;

    $resultado = [
        'success' => false,
        'data' => null
    ];

    if ($producto_id && $cart) {
        $cart->add_to_cart($producto_id);
        $resultado['success'] = true;
        $resultado['data'] = ['message' => 'Producto añadido al carrito'];
    } else {
        $resultado['data'] = ['message' => 'Error al añadir el producto al carrito'];
    }

    wp_send_json($resultado);
    wp_die();
}