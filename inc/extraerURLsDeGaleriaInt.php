<?php
//===================================================================================================================================================
// funcion_para_extraer_urls_de_galeria_int()
//===================================================================================================================================================
// Devuelve una lista de las URLs de todos los productos seleccionados por el asistente
//===================================================================================================================================================
require_once('obtencionDeProductos.php');
add_action('wp_ajax_extraer_urls_de_galeria_int', 'funcion_para_extraer_urls_de_galeria_int');
add_action('wp_ajax_nopriv_extraer_urls_de_galeria_int', 'funcion_para_extraer_urls_de_galeria_int'); // Usuarios no logueados puedan hacer esta solicitud.

function funcion_para_extraer_urls_de_galeria_int() {

    // Acceder a la información enviada a través de $_POST['respuesta']
    $respuesta = isset($_POST['respuesta']) ? json_decode(stripslashes($_POST['respuesta']), true) : null;

    // Obtener todos los productos de la base de datos
    $productos = obtenerProductos();

    $urls = [];

    foreach ($respuesta['listadoConLosComponentes'] as $componente) {
        foreach ($productos as $producto) {
            // Comparar el nombre del componente con el nombre del producto
            if (strcasecmp($producto['nombre'], $componente['nombre']) == 0) {
                if (!empty($producto['galeria'])) {
                    // Usa el nombre o ID del componente como clave
                    $urls[$componente['nombre']] = $producto['galeria'];
                    break; // Rompe el ciclo interno una vez que encuentra la coincidencia
                }
            }
        }
    }

    // Ordenar el array de URLs según el array de componentes
    $urlsOrdenadas = [];
    foreach ($respuesta['listadoConLosComponentes'] as $componente) {
        if (array_key_exists($componente['nombre'], $urls)) {
            $urlsOrdenadas[] = $urls[$componente['nombre']];
        }
    }

    echo json_encode($urlsOrdenadas);
    wp_die(); 
}
