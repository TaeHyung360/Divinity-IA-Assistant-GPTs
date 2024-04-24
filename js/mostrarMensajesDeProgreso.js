//===================================================================================================================
// Mientras se consulta a la API generamos frases de carga
//===================================================================================================================
function mostrarMensajesDeProgreso() {
    var mensajes = [
        "¡Krzzzt! ¡Desempolvando las GPU de alta gama solo para ti!",
        "¡Beep-boop! ¿Quién necesita café? ¡Estoy sobrealimentando tu PC!",
        "¡Beep-buzz! Instalando un turbo al procesador... ¡No intentes esto en casa!",
        "¡Bip-bzzt! Dándole un poco de amor overclock a tu futuro PC...",
        "¡Bloop-bleep! ¡Prepárate! Estoy poniendo secretos ninja en tu configuración...",
        "¡Krzzzt! ¡Ssssh! Estoy colando componentes premium en tu presupuesto...",
        "¡Beep-buzz! Estoy poniendo RGB para que tengas más FPS...",
        "¡Bloop-bleep! Calibrando los rayos láser... Sí, tu PC los va a necesitar.",
        "¡Beep-boop! Configurando la máquina para que puedas ganar a tus amigos... siempre.",
        "¡Bip-bzzt! ¡Poniendo el modo bestia en tu tarjeta gráfica!",
        "¡Tzzt-click! Estoy recuperando todos los componentes de tu PC...",
        "¡Waa-Waaaa! Casi termino, ajustando los últimos detalles..."
    ];
    // Comprobar si ya existe el contenedor, si no, crearlo
    if (jQuery('#mensaje-progreso').length === 0) {
        var progressContainer = jQuery('<div id="mensaje-progreso" class="respuesta-ra"><span class="icono-ra"></span><span class="nombre-ra">RA: </span><br><span id="texto-progreso"></span><br><br><br></div>');
        jQuery('.divinity-ia-chat-messages').append(progressContainer);
    }

    // Función para actualizar el mensaje de forma aleatoria
    function actualizarMensaje() {
        var indiceAleatorio = Math.floor(Math.random() * mensajes.length); // Genera un índice aleatorio
        jQuery('#texto-progreso').text(mensajes[indiceAleatorio]); // Actualiza el mensaje con un índice aleatorio
    }

    // Muestra un mensaje aleatorio de inmediato al inicio
    actualizarMensaje();

    // Continúa actualizando los mensajes a intervalos
    var intervalId = setInterval(actualizarMensaje, 5000); // Cambia el mensaje cada 5 segundos
    return intervalId; // Devuelve el ID del intervalo para poder detenerlo más tarde
}