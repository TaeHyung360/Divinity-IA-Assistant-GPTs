<?php
// Agrega este código a tu functions.php o a un plugin específico
/*
add_action('wp_ajax_nopriv_obtener_urls_galeria', 'obtener_urls_galeria');
add_action('wp_ajax_obtener_urls_galeria', 'obtener_urls_galeria');

function obtener_urls_galeria() {
    $json_data = json_decode(stripslashes($_POST['jsonData']), true);
    $productos_path = get_template_directory() . 'productos.txt'; // Ajusta la ruta según donde hayas almacenado el archivo
    $contenido = file_get_contents($productos_path);
    $productos = explode("-----------------", $contenido);

    $urls = [];

    foreach ($productos as $producto) {
        foreach ($json_data['listadoConLosComponentes'] as $componente) {
            if (strpos($producto, $componente['nombre']) !== false) {
                preg_match("/Galería: (.+)/", $producto, $matches);
                if (!empty($matches[1])) {
                    $urls[] = $matches[1];
                }
            }
        }
    }

    wp_send_json_success($urls);
}
*/
add_action('wp_ajax_extraer_urls_de_galeria_int', 'funcion_para_extraer_urls_de_galeria_int');
add_action('wp_ajax_nopriv_extraer_urls_de_galeria_int', 'funcion_para_extraer_urls_de_galeria_int'); // Si necesitas que usuarios no logueados puedan hacer esta solicitud.

function funcion_para_extraer_urls_de_galeria_int() {
    // Aquí accedes a la información enviada a través de $_POST['respuesta']
    $respuesta = isset($_POST['respuesta']) ? json_decode(stripslashes($_POST['respuesta']), true) : null;

    $productos_path = __DIR__ . '/productos.txt';
    $contenido = file_get_contents($productos_path);
    $productos = explode("-----------------", $contenido);

    $urls = [];

    foreach ($productos as $producto) {
        foreach ($respuesta['listadoConLosComponentes'] as $componente) {
            if (strpos($producto, $componente['nombre']) !== false) {
                preg_match("/Galería: (.+)/", $producto, $matches);
                if (!empty($matches[1])) {
                    $urls[] = $matches[1];
                }
            }
        }
    }

    echo json_encode($urls);
    wp_die(); // Esto es importante para terminar correctamente la ejecución en un manejador AJAX de WordPress
}
