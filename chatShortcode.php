<?php
function divinity_ia_chat_shortcode() {
    // Registrar y cargar la hoja de estilo para el chat
    wp_enqueue_style('divinity-ia-chat-style', plugins_url('css/styleChatShortcode.css', __FILE__));
    //Llamada al archivo js divinity-convertirTextoAIaHTML
    wp_enqueue_script('divinity-convertirTextoAIaHTML', plugins_url('js/convertirTextoAIaHTML.js', __FILE__), array('jquery'), null, true);
    //Llamada al archivo js toggleMenu
    wp_enqueue_script('divinity-toggleMenu', plugins_url('js/toggleMenu.js', __FILE__), array('jquery'), null, true);
    //Llamada al archivo js procesadoDeRespuesta
    wp_enqueue_script('divinity-procesadoDeRespuesta', plugins_url('js/procesadoDeRespuesta.js', __FILE__), array('jquery'), null, true);
    //Llamada al archivo js extraerYParsearYReemplazarJSON
    wp_enqueue_script('divinity-extraerYParsearYReemplazarJSON', plugins_url('js/extraerYParsearYReemplazarJSON.js', __FILE__), array('jquery'), null, true);
    // Iniciar almacenamiento en búfer de salida 
    ob_start();
    //==================================================================================================
    // Estructura HTML del chat
    //==================================================================================================
    ?>
    <meta charset="UTF-8">
    <div class="main-chat-shortcode">
        <button class="menu-hamburguesa" onclick="toggleMenu()">☰ Menú</button> <!-- Botón menú hamburguesa -->
        <div class="container-main">
            <div class="divinity-ia-products-column">
                    <h3>Productos Seleccionados</h3>
                    <div class="lista-de-productos-container" style="flex-grow: 1; overflow-y: auto;">
                        <ul class = "lista-de-productos">
                        </ul>
                    </div>
                <div class="divinity-ia-btn-carrito-container">
                    <button id="add-to-cart-btn">Añadir al carrito</button>
                </div>
            </div>
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
                    $('.divinity-ia-chat-messages').append('<div class="mensaje-usuario"><span class="icono-usuario"></span><span class="nombre-usuario">Usuario:</span><br><br>' + mensaje + '<br><br><br></div>');
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
                            console.log("Respuesta del servidor antes de JSON.parse, como string:", response);
                            // Analizar la respuesta JSON para obtener la cadena real
                                let textoRespuesta = JSON.parse(response);
                                //console.log("Respuesta después de JSON.parse:", textoRespuesta);
                                //console.log("Tipo de textoRespuesta después de JSON.parse:", typeof textoRespuesta);
                                // Procesar la respuesta para extraer el mensaje y los productos
                                let resultadoProcesado = procesadoDeRespuesta(textoRespuesta);
                                if (resultadoProcesado) {
                                    console.log("JSON extraído y parseado:", resultadoProcesado);
                                } else {
                                    console.log("No fue posible extraer o parsear el JSON.");
                                }
                                //Sustituimos el JSON incrustado en medio de la respuesta response e incrustamos los elementos que hay en medio en formato Markdown
                                salidaMarkdown = extraerYParsearYReemplazarJSON(textoRespuesta)
                                let textoHTML = "";
                                // Convierte el texto decodificado a HTML
                                if (typeof salidaMarkdown === 'string') {
                                    textoHTML = convertirTextoAIaHTML(salidaMarkdown);
                                    console.log("Texto convertido a HTML:", textoHTML);
                                } else {
                                    console.error("resultadoProcesado no es una cadena:", salidaMarkdown);
                                    textoHTML = "Error: la respuesta no es una cadena.";
                                }
                                //var textoRespuesta = JSON.parse(textoHTML);
                                //$('.divinity-ia-chat-messages').append('<div>RA: ' + textoRespuesta + '</div>');
                                $('.divinity-ia-chat-messages').append('<div class="respuesta-ra"><span class="icono-ra"></span><span class="nombre-ra">RA:</span><br>' + textoHTML + '<br><br><br></div>');
                                //$('.divinity-ia-chat-messages').append('<div> RA:' + decodeURIComponent(escape(response)) + '</div>');
                                // Procesar y mostrar los productos en el panel de la izquierda
                                if (resultadoProcesado && resultadoProcesado.listadoConLosComponentes && resultadoProcesado.listadoConLosComponentes.length > 0) {
                                    let productosHTML = '<ul class="lista-de-productos">';
                                    resultadoProcesado.listadoConLosComponentes.forEach(function(producto) {
                                        productosHTML += '<li><h7>' + producto.nombre + '</h7><p>Precio: ' + producto.precio + '</p></li>'; // Corregido para adecuarse a la estructura
                                    });
                                    productosHTML += '</ul>';
                                    // Reemplazar el contenido de la lista de productos con los nuevos productos
                                    $('.lista-de-productos-container').html(productosHTML);
                                } else {
                                    // Mostrar un mensaje si no hay productos
                                    $('.lista-de-productos-container').html('<p>No se encontraron productos.</p>');
                                    // Restaurar el estado de la interfaz
                                    document.getElementById('loading').style.display = 'none';
                                    document.getElementById('divinity-ia-chat-submit').style.display = 'block';
                                }
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
    </script>
    <?php
    // Devolver el contenido generado
    return ob_get_clean();
}
// Registrar el shortcode para su uso en WordPress
add_shortcode('asistente_chat', 'divinity_ia_chat_shortcode');
