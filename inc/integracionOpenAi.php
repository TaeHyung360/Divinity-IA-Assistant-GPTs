<?php
//$api_key = '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'; // Reemplaza esto con tu clave API real
//$assistant_id = 'asst_wE2Ka8DbcS2kclxMUffSFWaV'; // Reemplaza con tu ID del asistente
//$model = 'gpt-4-1106-preview'; // Modelo a utilizar
//===================================================================================================================================================
// crear_thread_openai()
//===================================================================================================================================================
function crear_thread_openai() {
    //$api_key = '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'; // Reemplaza esto con tu clave API real
    $api_key = get_option('aai_clave');
    $url = "https://api.openai.com/v1/threads";
    $args = [
        // Definición de encabezados para la solicitud API, incluyendo autorización y tipo de contenido
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v1'  // Agregar el encabezado de seguridad
        ],
        'method' => 'POST', // Método HTTP POST para crear un recurso
        'data_format' => 'body' // Formato de datos como cuerpo de la solicitud
    ];
    $response = wp_remote_post($url, $args); // Envío de la solicitud HTTP POST
    // Manejo de errores en la respuesta
    if (is_wp_error($response)) {
        return 'Error al conectar con OpenAI: ' . $response->get_error_message();
    }
    $body = wp_remote_retrieve_body($response); // Recuperación del cuerpo de la respuesta
    $thread = json_decode($body); // Decodificación de la respuesta JSON
    return $thread->id; // Devuelve el ID del hilo creado
}
//===================================================================================================================================================
// borrar_thread_openai()
//===================================================================================================================================================
function borrar_thread_openai($thread_id) {
    //$api_key = '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';
    $api_key = get_option('aai_clave');
    $url = "https://api.openai.com/v1/threads/$thread_id";
    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v1'  
        ],
        'method' => 'DELETE',
        'data_format' => 'body'
    ];
    $response = wp_remote_post($url, $args);
    if (is_wp_error($response)) {
        return 'Error al conectar con OpenAI: ' . $response->get_error_message();
    }
    $body = wp_remote_retrieve_body($response);
    return json_decode($body);
}
//===================================================================================================================================================
// crear_mensaje_en_thread_openai()
//===================================================================================================================================================
function crear_mensaje_en_thread_openai($thread_id, $mensaje) {
    //$api_key = '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'; 
    $api_key = get_option('aai_clave');
    $url = "https://api.openai.com/v1/threads/$thread_id/messages";
    $body = [
        'role' => 'user',
        'content' => $mensaje,
        //Se puede agregar 'file_ids' y 'metadata' si son necesarios
    ];
    $args = [
        'body' => json_encode($body),
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v1'  
        ],
        'method' => 'POST',
        'data_format' => 'body'
    ];
    $response = wp_remote_post($url, $args);
    if (is_wp_error($response)) {
        return 'Error al conectar con OpenAI: ' . $response->get_error_message();
    }
    $body = wp_remote_retrieve_body($response);
    return json_decode($body);
}
//===================================================================================================================================================
// crear_run_en_thread_openai()
//===================================================================================================================================================
function crear_run_en_thread_openai($thread_id, $assistant_id) {
    //$api_key = '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'; 
    $api_key = get_option('aai_clave');
    $url = "https://api.openai.com/v1/threads/$thread_id/runs";
    $body = [
        'assistant_id' => $assistant_id
    ];
    $args = [
        'body' => json_encode($body),
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v1'  
        ],
        'method' => 'POST',
        'data_format' => 'body'
    ];
    $response = wp_remote_post($url, $args);
    if (is_wp_error($response)) {
        return 'Error al conectar con OpenAI: ' . $response->get_error_message();
    }
    $body = wp_remote_retrieve_body($response);
    $run = json_decode($body);
    // Verifica si la propiedad 'id' existe en el objeto thread
    if (isset($run->id)) {
        return $run->id; // Devolver el ID del thread
    } else {
        return 'El campo "id" no se encontró en la respuesta.';
    }
}
//===================================================================================================================================================
// listar_mensajes_de_thread_openai()
//===================================================================================================================================================
function listar_mensajes_de_thread_openai($thread_id) {
    //$api_key = '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'; 
    $api_key = get_option('aai_clave');
    $url = "https://api.openai.com/v1/threads/$thread_id/messages";
    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v1'
        ],
        'method' => 'GET'
    ];
    $response = wp_remote_get($url, $args);
    if (is_wp_error($response)) {
        return 'Error al conectar con OpenAI: ' . $response->get_error_message();
    }
    $body = json_decode(wp_remote_retrieve_body($response), true);
    $primerMensaje = null;
    // Recorrer la matriz desde el principio
    foreach ($body['data'] as $message) {
        // Verifica si el mensaje tiene el rol "assistant"
        if ($message['role'] === 'assistant') {
            // Verifica si el mensaje tiene un valor "value"
            if (isset($message['content'][0]['text']['value'])) {
                // Guarda el primer valor encontrado
                $primerMensaje = $message['content'][0]['text']['value'];
                break; // Salir del bucle una vez que encuentres el primer mensaje
            }
        }
    }
    return $primerMensaje;
}
//===================================================================================================================================================
// recuperar_run_openai()
//===================================================================================================================================================
function recuperar_run_openai($thread_id, $run_id) {
    //$api_key = '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'; 
    $api_key = get_option('aai_clave');
    $url = "https://api.openai.com/v1/threads/$thread_id/runs/$run_id";
    $args = [
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v1'
        ],
        'method' => 'GET'
    ]; 
    $response = wp_remote_get($url, $args);
    if (is_wp_error($response)) {
        return 'Error al conectar con OpenAI: ' . $response->get_error_message();
    }
    $body = json_decode(wp_remote_retrieve_body($response), true); 
    // Devuelve true si la 'run' ha completado su ejecución.  
    return isset($body['status']) && $body['status'] === 'completed';
}




