<?php
//===================================================================================================================================================
// verificarIntegridadDelTxt()
//===================================================================================================================================================
// Devuelve T,F si el archivo productos.txt contiene o no todos los productos de la base datos
//===================================================================================================================================================
require_once('obtencionDeProductos.php');
require_once('guardadoDeProductos.php');

function verificarIntegridadDelTxt() {
    
    // Obtiene todos los productos de la base de datos
    $productosActuales = obtenerProductos();
    // Obtiene el contedido del archivo actual productos.txt
    $productosTxt = file_get_contents(plugin_dir_path(__FILE__) . 'productos.txt');

    // Convierte los productos actuales de la base de datos a un formato de cadena para comparar
    $contenidoActual = "";
    foreach ($productosActuales as $producto) {
        $contenidoActual .= "ID: {$producto['ID']}\n";
        $contenidoActual .= "Nombre: {$producto['nombre']}\n";
        $contenidoActual .= "Precio: {$producto['precio']}\n";
        $contenidoActual .= "Galería: {$producto['galeria']}\n";
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
