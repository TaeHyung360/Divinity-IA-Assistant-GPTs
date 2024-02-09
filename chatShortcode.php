<?php
function divinity_ia_chat_shortcode() {
    // Registrar y cargar la hoja de estilo para el chat
    wp_enqueue_style('divinity-ia-chat-style', plugins_url('css/styleChatShortcode.css', __FILE__));
    // Iniciar almacenamiento en búfer de salida
    ob_start();
    //==================================================================================================
    // Estructura HTML del chat
    //==================================================================================================
    ?>
    <meta charset="UTF-8">
    <div class="divinity-ia-chat-container">
        <div class="divinity-ia-chat-messages"></div>
        <div class="divinity-ia-chat-input-container">
            <textarea id="divinity-ia-chat-input" placeholder="Escribe tu mensaje aquí..."></textarea>
            <button id="divinity-ia-chat-submit">Enviar</button>
            <!-- Ícono de carga que se muestra durante las peticiones AJAX -->
            <div id="loading" style="display: none;">
                <div class="loader"></div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Evento de clic en el botón de enviar
            $('#divinity-ia-chat-submit').on('click', function() {
            var mensaje = $('#divinity-ia-chat-input').val().trim();
            
                if(mensaje) {
                    // Añadir el mensaje del usuario al contenedor de mensajes
                    //$('.divinity-ia-chat-messages').append('<div>Usuario: ' + mensaje + '</div>');
                    $('.divinity-ia-chat-messages').append('<div class="mensaje-usuario">Usuario: ' + mensaje + '</div>');
                    // Limpia el campo de entrada
                    $('#divinity-ia-chat-input').val(''); 
                    // Mostrar ícono de carga y ocultar botón de enviar
                    document.getElementById('loading').style.display = 'block';
                    document.getElementById('divinity-ia-chat-submit').style.display = 'none';
                    // Petición AJAX para enviar el mensaje al servidor
                    $.ajax({
                        url : '<?php echo admin_url('admin-ajax.php'); ?>',
                        type : 'POST',
                        data : {
                            action : 'enviar_mensaje_a_openai',
                            mensaje : mensaje
                        },
                        success: function(response) {
                            console.log("Respuesta del servidor antes de JSON.parse:", response);

                            // Analizar la respuesta JSON para obtener la cadena real
                            let textoRespuesta = JSON.parse(response);
                            console.log("Respuesta después de JSON.parse:", textoRespuesta);

                            // Convierte el texto decodificado a HTML
                            let textoHTML = convertirTextoAIaHTML(textoRespuesta);
                            console.log("Texto convertido a HTML:", textoHTML);
                            
                            //var textoRespuesta = JSON.parse(textoHTML);
                            //$('.divinity-ia-chat-messages').append('<div>RA: ' + textoRespuesta + '</div>');
                            $('.divinity-ia-chat-messages').append('<div class="respuesta-ra">RA: ' + textoHTML + '</div>');
                            //$('.divinity-ia-chat-messages').append('<div> RA:' + decodeURIComponent(escape(response)) + '</div>');
                            // Restaurar el estado de la interfaz
                            document.getElementById('loading').style.display = 'none';
                            document.getElementById('divinity-ia-chat-submit').style.display = 'block';
                        },
                        error : function(jqXHR, textStatus, errorThrown) {
                            // Manejar errores en la petición AJAX
                            console.log('Error en la solicitud AJAX:', textStatus, errorThrown);
                            //$('#divinity-ia-chat-messages').append('<div>Error al procesar la solicitud.</div>');
                            $('.divinity-ia-chat-messages').append('<div class="respuesta-ra">Error al procesar la solicitud.</div>');
                            // Restaurar el estado de la interfaz
                            document.getElementById('loading').style.display = 'none';
                            document.getElementById('divinity-ia-chat-submit').style.display = 'block';
                        }
                    });
                }
            });
        });
        // Ajustar la altura del textarea automáticamente según su contenido
        document.getElementById('divinity-ia-chat-input').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });


        function convertirTextoAIaHTML(texto){
            // Convertir negritas: **texto** a <strong>texto</strong>
            let html = texto.replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>");

            // Dividir el texto en líneas
            let lineas = html.split('\n');
            let htmlFinal = '';
            let enLista = false;

            lineas.forEach((linea) => {
                if (linea.startsWith('- ')) {
                // Comprobar si empezamos una nueva lista
                if (!enLista) {
                    htmlFinal += '<ul>';
                    enLista = true;
                }
                // Agregar elemento de lista
                htmlFinal += `<li>${linea.slice(2)}</li>`;
                } else {
                // Si no es parte de una lista y hay una lista abierta, cerrarla
                if (enLista) {
                    htmlFinal += '</ul>';
                    enLista = false;
                }
                // Agregar párrafo si la línea no está vacía
                if (linea.trim() !== '') {
                    htmlFinal += `<p>${linea}</p>`;
                }
                }
            });

            // Cerrar la lista si el texto termina en una lista
            if (enLista) {
                htmlFinal += '</ul>';
            }

            return htmlFinal;
        }
    </script>

    <?php
    // Devolver el contenido generado
    return ob_get_clean();
}
// Registrar el shortcode para su uso en WordPress
add_shortcode('asistente_chat', 'divinity_ia_chat_shortcode');
