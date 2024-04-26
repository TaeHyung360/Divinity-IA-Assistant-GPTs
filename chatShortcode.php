<?php
function divinity_ia_chat_shortcode() {
    // Registrar y cargar la hoja de estilo para el chat
    wp_enqueue_style('divinity-ia-chat-style', plugins_url('css/styleChatShortcode.css', __FILE__));
    //Llamada al archivo mostrarMensajesDeProgreso.js
    wp_enqueue_script('divinity-progress-messages', plugins_url('js/mostrarMensajesDeProgreso.js', __FILE__), array('jquery'), null, true);
    // Iniciar almacenamiento en búfer de salida 
    ob_start();
    //==================================================================================================
    // Estructura HTML del chat
    //==================================================================================================
    ?>
    <meta charset="UTF-8">
    <div class="main-chat-shortcode">
        <div class="center-btn-container">
            <button id="toggle-products-btn">Mostrar Productos</button>
        </div>
        <div class="container-main">
            <div class="divinity-ia-products-column">
                    <h3>Productos Seleccionados</h3>
                    <div class="lista-de-productos-container" style="flex-grow: 1; overflow-y: auto;">
                        <ul class = "lista-de-productos">
                        </ul>
                    </div>
                <div class="divinity-ia-btn-carrito-container">
                    <button id="add-to-cart-btn">Añadir todos</button>
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

        let productosConfiguracionPC = {};

        jQuery(document).ready(function($) {
            // Evento de clic en el botón de enviar
            $('#divinity-ia-chat-submit').on('click', function() {
                var mensaje = $('#divinity-ia-chat-input').val().trim();
                    if(mensaje) {
                        // Añadir el mensaje del usuario al contenedor de mensajes
                        $('.divinity-ia-chat-messages').append('<div class="mensaje-usuario"><span class="icono-usuario"></span><span class="nombre-usuario">Usuario:</span><br><br>' + mensaje + '<br><br><br></div>');
                        // Limpia el campo de entrada
                        $('#divinity-ia-chat-input').val(''); 
                        // Mostrar ícono de carga y ocultar botón de enviar
                        document.getElementById('loading').style.display = 'block';
                        document.getElementById('divinity-ia-chat-submit').style.display = 'none';
                        // Iniciamos los mensajes de progreso
                        var intervalId = mostrarMensajesDeProgreso();

                        // Asegurarse de que el nuevo mensaje sea visible
                        scrollToBottom();
                        // Petición AJAX para enviar el mensaje al servidor
                        $.ajax({
                            url : '<?php echo admin_url('admin-ajax.php'); ?>',
                            type : 'POST',
                            data : {
                                action : 'enviar_mensaje_a_openai',
                                mensaje : mensaje
                            },
                            success: function(response) {
                                clearInterval(intervalId);  // Detiene los mensajes de progreso
                                jQuery('#mensaje-progreso').remove();  // Elimina el contenedor de mensajes de progreso
                                // Manejar la respuesta
                                console.log("Respuesta del servidor antes de JSON.parse, como string:", response);
                                try {
                                    // Intenta analizar la respuesta JSON para obtener la cadena real
                                    let resultadoProcesado = JSON.parse(JSON.parse(response));

                                    if (resultadoProcesado.listadoConLosComponentes && resultadoProcesado.listadoConLosComponentes.length > 0) {
                                        console.log("JSON extraído y parseado:", resultadoProcesado.listadoConLosComponentes );
                                        productosConfiguracionPC = resultadoProcesado.listadoConLosComponentes;
                                        console.log("productosConfiguracionPC:", productosConfiguracionPC);
                                    } else {
                                        console.log("No fue posible extraer o parsear el JSON.");
                                    }

                                    if (resultadoProcesado.listadoConLosComponentes && resultadoProcesado.listadoConLosComponentes.length > 0) {

                                        $.ajax({
                                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                            type: 'POST',
                                            data: {
                                                action: 'extraer_urls_de_galeria_int', // Esta es la acción que manejará la solicitud en WordPress
                                                respuesta: JSON.stringify(resultadoProcesado) // Aquí envío el textoRespuesta como parte de la data
                                            },
                                            success: function(responseGaleria) {
                                                // Manejo de la respuesta de tu segunda solicitud AJAX
                                                try {
                                                    let urlsGaleria = JSON.parse(responseGaleria);
                                                    console.log("Las URL de la galeria:",urlsGaleria)
                                                    // Procesamiento de las URLs de la galería
                                                    if (resultadoProcesado && resultadoProcesado.listadoConLosComponentes && resultadoProcesado.listadoConLosComponentes.length > 0) {
                                                        let productosHTML = '<ul class="lista-de-productos">';
                                                        resultadoProcesado.listadoConLosComponentes.forEach(function(producto, index) {
                                                            // Asume que `urlsGaleria` es un array con las URLs en el mismo orden que los productos
                                                            let urlImagen = urlsGaleria[index]; // Acceder a la URL de la imagen usando el índice

                                                            // Agregar la imagen al HTML del producto
                                                            productosHTML += `<li>
                                                                <img src="${urlImagen}" alt="${producto.nombre}" style="width: 70%; height: auto;">
                                                                <h5>${producto.nombre}</h5>
                                                                <p>Precio: ${producto.precio}</p>
                                                                <button style="background: none; border: none; color: #683475; cursor: pointer; font-size: 1rem; transition: color 0.3s;" id="add-to-cart-button" data-producto-id="${producto.ID}">Añadir al carrito</button>
                                                            </li>`; 
                                                        });
                                                        productosHTML += '</ul>';
                                                        // Reemplazar el contenido de la lista de productos con los nuevos productos
                                                        $('.lista-de-productos-container').html(productosHTML);
                                                    }else {
                                                        // Mostrar un mensaje si no hay productos
                                                        $('.lista-de-productos-container').html('<p>No se encontraron productos.</p>');
                                                        // Restaurar el estado de la interfaz
                                                        document.getElementById('loading').style.display = 'none';
                                                        document.getElementById('divinity-ia-chat-submit').style.display = 'block';
                                                    }
                                                } catch(e) {
                                                    console.error('Error al parsear las URLs de la galería: ', e);
                                                }
                                            },
                                            error: function(jqXHR, textStatus, errorGaleria) {
                                                console.log('Error en la solicitud AJAX de la galería:', textStatus, errorGaleria);
                                            }
                                        });
                                    } else {
                                        // Ccaso donde no hay componentes nuevos para actualizar
                                        console.log("No hay nuevos componentes para actualizar.");
                                    }

                                    // Toma el texto de la respuesta
                                    const textoHTML = resultadoProcesado.respuesta;

                                    const textoConvertidoHTML = simpleMarkdownToHTML(textoHTML);
                                    
                                    // Comienza a construir la salida
                                    let htmlOutput = '<div class="respuesta-ra"><span class="icono-ra"></span><span class="nombre-ra">RA:</span><br>' + textoConvertidoHTML + '<br>';

                                    // Si el listado de componentes existe, añádelo al HTML como lista
                                    if (resultadoProcesado.listadoConLosComponentes && resultadoProcesado.listadoConLosComponentes.length > 0) {
                                        htmlOutput += '<ul>'; // comienzo de la lista

                                        resultadoProcesado.listadoConLosComponentes.forEach((componente) => {
                                            htmlOutput += '<li>' + 
                                                componente.nombre + ' (' + componente.modelo + ') - ' + componente.precio + 
                                                '</li>'; // cada componente en formato lista
                                        });

                                        htmlOutput += '</ul>'; // fin de la lista
                                    }

                                    // Cierre del bloque HTML
                                    htmlOutput += '<br><br></div>';
                                    // Agrega el contenido HTML a la página
                                    $('.divinity-ia-chat-messages').append(htmlOutput);

                                    document.getElementById('loading').style.display = 'none';
                                    document.getElementById('divinity-ia-chat-submit').style.display = 'block';

                                } catch (error) {
                                    // Manejar el error, por ejemplo, si el JSON es inválido o no hay texto
                                    console.error("Error al parsear la respuesta:", error.message);
                                    //$('#divinity-ia-chat-messages').append('<div>Error al procesar la solicitud.</div>');
                                    $('.divinity-ia-chat-messages').append('<div class="respuesta-ra">Error al parsear la respuesta</div>');
                                    // Restaurar el estado de la interfaz
                                    document.getElementById('loading').style.display = 'none';
                                    document.getElementById('divinity-ia-chat-submit').style.display = 'block';
                                    // Aquí podrías manejar diferentes tipos de errores o realizar acciones específicas
                                    // Por ejemplo, puedes decidir loggear el error, enviarlo a un sistema de monitoreo, etc.
                                }                                
                            },
                            error : function(jqXHR, textStatus, errorThrown) {
                                clearInterval(intervalId);  // Detiene los mensajes de progreso
                                jQuery('#mensaje-progreso').remove();  // Elimina el contenedor de mensajes de progreso
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

        function scrollToBottom() {
            var messagesContainer = jQuery('.divinity-ia-chat-messages');
            messagesContainer.scrollTop(messagesContainer.prop("scrollHeight"));
        }

        function simpleMarkdownToHTML(text) {
            // Convertir encabezados
            text = text.replace(/^### (.*$)/gim, '<h3>$1</h3>');
            text = text.replace(/^## (.*$)/gim, '<h2>$1</h2>');
            text = text.replace(/^# (.*$)/gim, '<h1>$1</h1>');

            // Convertir negritas e itálicas
            text = text.replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>');
            text = text.replace(/\*(.*)\*/gim, '<em>$1</em>');

            // Convertir enlaces
            text = text.replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href="$2">$1</a>');

            // Convertir saltos de línea a etiquetas <br>
            text = text.replace(/\n/gim, '<br>');

            return text;
        }
        
        // Ajustar la altura del textarea automáticamente según su contenido
        document.getElementById('divinity-ia-chat-input').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        jQuery(document).ready(function($) {
            // Manejar el clic del botón para mostrar/ocultar productos y el chat
            $('#toggle-products-btn').click(function() {
                $('.divinity-ia-products-column').toggle(); // Alterna la visibilidad del div de productos
                $('.divinity-ia-chat-container').toggle(); // Alterna la visibilidad del div del chat
            });
        });

        jQuery(document).ready(function($) {
            $('#add-to-cart-btn').on('click', function() {
                // Código que se ejecuta cuando el botón sea pulsado
                console.log("productosConfiguracionPC:", productosConfiguracionPC);
                if (!productosConfiguracionPC || productosConfiguracionPC.length === 0) {
                    alert("No hay productos seleccionados para añadir al carrito.");
                    return; // Detiene la ejecución de la función aquí
                }
                // Comprobar si productoIds está vacío
                let productoIds = productosConfiguracionPC.map(producto => producto.ID);  
                console.log('El producto ha sido añadido al carrito');
                console.log('Añadiendo al carrito el producto con ID:', productoIds);
                console.log(typeof productoIds);
                var homeUrl = '<?php echo get_site_url(); ?>';
            
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'añadir_al_carrito',
                            productos: productoIds
                        },
                        success: function(response) {
                            console.log('Respuesta del servidor:', response);
                            if (response.success) {
                                //alert("Productos añadidos al carrito correctamente.");
                                window.location.href = homeUrl + '/carrito/';
                            } else {
                                alert("Hubo un error al añadir los productos al carrito.");
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Error al añadir productos al carrito:', textStatus, errorThrown);
                        alert("Error al procesar la solicitud.");
                    }
                });
            });
        });

        jQuery(document).ready(function($) {
            $('.lista-de-productos-container').on('click', '.btn-add-to-cart', function() {
                let productoId = $(this).data('producto-id');
                console.log('Añadiendo al carrito el producto con ID:', productoId);

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'anadir_un_producto_al_carrito',
                        producto_id: productoId
                    },
                    success: function(response) {
                        console.log('Respuesta del servidor:', response);
                        if (response.success) {
                            alert("Producto añadido al carrito correctamente.");
                        } else {
                            alert("Hubo un error al añadir el producto al carrito.");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log('Error al añadir producto al carrito:', textStatus, errorThrown);
                        alert("Error al procesar la solicitud.");
                    }
                });
            });
        });
    </script>
    <?php
    // Devolver el contenido generado
    return ob_get_clean();
}
// Registrar el shortcode para su uso en WordPress
add_shortcode('asistente_chat', 'divinity_ia_chat_shortcode');
