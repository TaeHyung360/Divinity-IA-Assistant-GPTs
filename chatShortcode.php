<?php
function divinity_ia_chat_shortcode() {
    // Registrar y cargar la hoja de estilo para el chat
    wp_enqueue_style('divinity-ia-chat-style', plugins_url('css/styleChatShortcode.css', __FILE__));
    //Llamada al archivo js divinity-convertirTextoAIaHTML
    wp_enqueue_script('divinity-convertirTextoAIaHTML', plugins_url('js/convertirTextoAIaHTML.js', __FILE__), array('jquery'), null, true);
    //Llamada al archivo js toggleMenu
    wp_enqueue_script('divinity-toggleMenu', plugins_url('js/toggleMenu.js', __FILE__), array('jquery'), null, true);
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
                            <li>
                                <h7>Gigabyte Z790 GAMING X AX</h7>
                                <p>Precio: 272.99€</p>
                            </li>
                            <li>
                                <h7>Intel Core i7-13700F 2.1 GHz/5.2 GHz</h7>
                                <p>Precio: 410.99€</p>
                            </li>
                            <li>
                                <h7>Corsair Dominator Platinum RGB DDR5 6000MHz 64GB (2x32GB) CL40 Negro</h7>
                                <p>Precio: 272.99€</p>
                            </li>
                            <li>
                                <h7>Samsung 980 Pro SSD 1TB PCIe NVMe M.2</h7>
                                <p>Precio: 90.87€</p>
                            </li>
                            <li>
                                <h7>Gigabyte GeForce RTX 4070 GAMING OC 12 GB GDDR6X DLSS3</h7>
                                <p>Precio: 709.89€</p>
                            </li>
                            <li>
                                <h7>Lian Li PC-O11 Dynamic Cristal Templado USB 3.1 Negra</h7>
                                <p>Precio: 128.93€</p>
                            </li>
                            <li>
                                <h7>Gigabyte Aorus WaterForce X 360 Kit de Refrigeración Líquida</h7>
                                <p>Precio: 268.11€</p>
                            </li>
                            <li>
                                <h7>Gigabyte UD850GM PG5 850W 80 Plus Gold Full Modular</h7>
                                <p>Precio: 115.99€</p>
                            </li>
                            <li>
                                <h7>Thermal Grizzly Kryonaut Pasta Térmica 1gr</h7>
                                <p>Precio: 10.07€</p>
                            </li>
                        
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
                            console.log("Respuesta del servidor antes de JSON.parse:", response);

                            // Analizar la respuesta JSON para obtener la cadena real
                            let textoRespuesta = JSON.parse(response);
                            console.log("Respuesta después de JSON.parse:", textoRespuesta);


                            let textoHTML = "";
                            // Convierte el texto decodificado a HTML
                            if (typeof textoRespuesta === 'string') {
                                textoHTML = convertirTextoAIaHTML(textoRespuesta);
                                console.log("Texto convertido a HTML:", textoHTML);
                            } else {
                                console.error("textoRespuesta no es una cadena:", textoRespuesta);
                                textoHTML = "Error: la respuesta no es una cadena.";
                            }
                            //var textoRespuesta = JSON.parse(textoHTML);
                            //$('.divinity-ia-chat-messages').append('<div>RA: ' + textoRespuesta + '</div>');
                            $('.divinity-ia-chat-messages').append('<div class="respuesta-ra"><span class="icono-ra"></span><span class="nombre-ra">RA:</span><br>' + textoHTML + '<br><br><br></div>');
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

    </script>

    <?php
    // Devolver el contenido generado
    return ob_get_clean();
}
// Registrar el shortcode para su uso en WordPress
add_shortcode('asistente_chat', 'divinity_ia_chat_shortcode');
