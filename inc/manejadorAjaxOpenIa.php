<?php
require_once('obtencionDeProductos.php');
require_once('guardadoDeProductos.php');
require_once('verificarCreacionDelTxt.php');
require_once('verificarIntegridadDelTxt.php');
//===================================================================================================================================================
// manejar_mensaje_ajax()
//===================================================================================================================================================
function manejar_mensaje_ajax() {

    $assistant_id = get_option('aai_assistant_id');

    // Obtiene el mensaje recibido a través de POST
    $mensaje = sanitize_text_field($_POST['mensaje']);

    if (verificarCreacionDelTxt()) {
        // Verifica la integridad del archivo existente
        $huboCambios = verificarIntegridadDelTxt();
        if ($huboCambios) {
            //echo json_encode("Cambios detectados y archivo actualizado.");
        } else {
            //echo json_encode("No se detectaron cambios. El archivo está actualizado.");
        }
    } else {
        // Si no existe el archivo, obtiene los productos y los guarda
        $productos = obtenerProductos();
        guardarProductosEnTxt($productos);
        $file_id = subir_un_archivo();
        $res = modificar_asistente_openai($assistant_id, $file_id);
        //echo json_encode("Guardados los cambios. Y subido el archivo". $res);
    }

   // Verificar si ya existe un ID de thread en la sesión
   if (!isset($_SESSION['openai_thread_id'])) {
        // Si no existe, crea un nuevo hilo en OpenAI y lo almacena en la sesión
        $thread_id = crear_thread_openai();
        $_SESSION['openai_thread_id'] = $thread_id;
    } else {
        // Si ya existe, usa el ID del hilo existente
        $thread_id = $_SESSION['openai_thread_id'];
        //echo json_encode($thread_id);
    }

    // Verifica si se pudo obtener un ID de hilo válido
    if (!$thread_id) {
        echo 'Error al gestionar el thread en OpenAI.';
        wp_die(); // Termina la ejecución del script
    }

    // Crea un mensaje en el hilo de OpenAI
    $respuesta_mensaje = crear_mensaje_en_thread_openai($thread_id, $mensaje);
    // Contiene posibles errores al conectar con OpenAI
    if (is_wp_error($respuesta_mensaje)) {
        echo 'Error al conectar con OpenAI: ' . $respuesta_mensaje->get_error_message();
    } else {
        // Crea una ejecución ('run') en el hilo de OpenAI
        $run_id = crear_run_en_thread_openai($thread_id, $assistant_id);

        if (is_wp_error($run_id)) {
            echo 'Error al conectar con OpenAI: ' . $respuesta->get_error_message();
        } else {
            // Inicia un ciclo para intentar recuperar la respuesta
            $intentos = 0;
            $maxIntentos = 10; // Número máximo de intentos
            $espera = 5; // Tiempo de espera en segundos  

            while ($intentos < $maxIntentos) { 
                $esCompletada = recuperar_run_openai($thread_id, $run_id);
                if ($esCompletada) {
                    // Si la ejecución está completada, recupera los mensajes del hilo
                    $mensajes = listar_mensajes_de_thread_openai($thread_id);
                    if (is_wp_error($mensajes)) {
                        echo 'Error al recuperar mensajes: ' . $mensajes->get_error_message();
                    } else {
                        echo json_encode($mensajes); // Devuelve los mensajes en formato JSON
                    }
                    wp_die(); // Termina la ejecución del script adecuadamente
                }
                sleep($espera); // Espera antes de reintentar
                $intentos++;
            }
            // Si se supera el número máximo de intentos, se devuelve un error
            echo json_encode(['success' => false, 'message' => 'Tiempo de espera excedido.']);
        }
    }
    wp_die(); // Esto es requerido para terminar adecuadamente la ejecución del script
}
/*
function manejar_mensaje_ajax() {

    // Obtiene el mensaje recibido a través de POST
    $mensaje = sanitize_text_field($_POST['mensaje']);

    // Si el mensaje es "obtener productos", devuelve la lista de productos
    if ($mensaje === 'obtener productos') {
        $productos = obtenerProductos();
        echo json_encode($productos);
        wp_die();
    }
}
*/
// Registra la función como un 'action' para manejar mensajes AJAX en WordPress
add_action('wp_ajax_enviar_mensaje_a_openai', 'manejar_mensaje_ajax');
// Registra para usuarios no autenticados
add_action('wp_ajax_nopriv_enviar_mensaje_a_openai', 'manejar_mensaje_ajax');
