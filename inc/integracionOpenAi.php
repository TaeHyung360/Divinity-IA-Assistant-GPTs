<?php
//===================================================================================================================================================
// modificar_asistente_openai()
//===================================================================================================================================================
function modificar_asistente_openai($assistant_id, $file_ids){
    $api_key = get_option('aai_clave');
    $url = "https://api.openai.com/v1/assistants/$assistant_id";

    $body = [
        'file_ids' => [$file_ids]  // Los IDs de los archivos aquí
        // Se pueden agregar campos adicionales como 'model', 'name', 'description', etc., si es necesario
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
    //$body = wp_remote_retrieve_body($response);
    //return json_decode($body);
    $response_body = wp_remote_retrieve_body($response);
    $decoded_response = json_decode($response_body, true);

    // Verificar si hay un mensaje de error en la respuesta
    if (!empty($decoded_response['error'])) {
        return 'Error en la respuesta de OpenAI: ' . $decoded_response['error']['message'];
    }

    return $decoded_response;
}
//===================================================================================================================================================
// crear_thread_openai()
//===================================================================================================================================================
function crear_thread_openai() {
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
// subir_un_archivo()
//===================================================================================================================================================
function subir_un_archivo(){

    $api_key = get_option('aai_clave');
    $url = "https://api.openai.com/v1/files";
    $filePath = plugin_dir_path(__FILE__) . 'productos.txt';
    $fileName = basename($filePath);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: multipart/form-data'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'purpose' => 'assistants',
        'file' => new CURLFile($filePath, 'text/plain', $fileName)
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_message = curl_error($ch);
        echo "Error al conectar con OpenAI: $error_message";
    } else {
        // Decodifica la respuesta JSON
        $decodedResponse = json_decode($response, true);
        // Verifica si la respuesta contiene el ID del archivo
        if (isset($decodedResponse['id'])) {
            // Devuelve solo el ID del archivo
            return $decodedResponse['id'];
        } else {
            // En caso de que no se encuentre el ID, devuelve un mensaje de error
            return json_encode(["error" => "No se pudo obtener el ID del archivo"]);
        }
    }
    curl_close($ch);
}
//===================================================================================================================================================
// borrar_thread_openai()
//===================================================================================================================================================
function borrar_thread_openai($thread_id) {
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




