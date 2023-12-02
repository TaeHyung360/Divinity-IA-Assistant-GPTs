<?php

require_once('obtencionDeProductos.php');
require_once('guardadoDeProductos.php');

function verificarIntegridadDelTxt() {
    $productosActuales = obtenerProductos();
    $productosTxt = file_get_contents(plugin_dir_path(__FILE__) . 'productos.txt');

    // Convierte los productos actuales en un formato de cadena para comparar
    $contenidoActual = "";
    foreach ($productosActuales as $producto) {
        $contenidoActual .= "ID: {$producto['ID']}\n";
        $contenidoActual .= "Nombre: {$producto['nombre']}\n";
        $contenidoActual .= "Precio: {$producto['precio']}\n";
        $contenidoActual .= "Galería: " . implode(', ', $producto['galeria']) . "\n";
        $contenidoActual .= "Estado de stock: {$producto['estado_stock']}\n";
        $contenidoActual .= "-----------------\n";
    }

    // Compara el contenido del archivo con los productos actuales
    if ($contenidoActual !== $productosTxt) {
        guardarProductosEnTxt($productosActuales);
        return true; // Hubo cambios y se actualizaron
    }

    return false; // No hubo cambios
}
