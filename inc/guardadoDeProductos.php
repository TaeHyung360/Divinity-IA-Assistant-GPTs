<?php
//===================================================================================================================================================
// guardarProductosEnTxt()
//===================================================================================================================================================
// Dada una lista de productos, los guardamos en un txt
//===================================================================================================================================================
function guardarProductosEnTxt($productos) {
    $contenido = "";

    foreach ($productos as $producto) {
        $contenido .= "ID: {$producto['ID']}\n";
        $contenido .= "Nombre: {$producto['nombre']}\n";
        $contenido .= "Precio: {$producto['precio']}\n";
        $contenido .= "Galería: {$producto['galeria']}\n";
        $contenido .= "Estado de stock: {$producto['estado_stock']}\n";
        $contenido .= "-----------------\n";
    }

    file_put_contents(plugin_dir_path(__FILE__) . 'productos.txt', $contenido);
    return;
}


