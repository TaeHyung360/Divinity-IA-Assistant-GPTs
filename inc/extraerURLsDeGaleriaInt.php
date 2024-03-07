<?php

add_action('wp_ajax_extraer_urls_de_galeria_int', 'funcion_para_extraer_urls_de_galeria_int');
add_action('wp_ajax_nopriv_extraer_urls_de_galeria_int', 'funcion_para_extraer_urls_de_galeria_int'); // Si necesitas que usuarios no logueados puedan hacer esta solicitud.

function funcion_para_extraer_urls_de_galeria_int() {
    // Aquí accedes a la información enviada a través de $_POST['respuesta']
    $respuesta = isset($_POST['respuesta']) ? json_decode(stripslashes($_POST['respuesta']), true) : null;

    $productos_path = __DIR__ . '/productos.txt';
    $contenido = file_get_contents($productos_path);
    $productos = explode("-----------------", $contenido);

    $urls = [];

    // Asumiendo que cada nombre de componente es único
    foreach ($respuesta['listadoConLosComponentes'] as $componente) {
        foreach ($productos as $producto) {
            if (strpos($producto, $componente['nombre']) !== false) {
                preg_match("/Galería: (.+)/", $producto, $matches);
                if (!empty($matches[1])) {
                    // Usa el nombre o ID del componente como clave
                    $urls[$componente['nombre']] = $matches[1];
                    break; // Rompe el ciclo interno una vez que encuentres la coincidencia
                }
            }
        }
    }

    // Ordenar el arreglo de URLs según el arreglo de componentes
    $urlsOrdenadas = [];
    foreach ($respuesta['listadoConLosComponentes'] as $componente) {
        if (array_key_exists($componente['nombre'], $urls)) {
            $urlsOrdenadas[] = $urls[$componente['nombre']];
        }
    }

    echo json_encode($urlsOrdenadas);
    wp_die(); // Esto es importante para terminar correctamente la ejecución en un manejador AJAX de WordPress
}

