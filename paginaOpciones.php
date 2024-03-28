<?php
$img_url = plugin_dir_url(__FILE__) . 'img/';

// Verifico si se ha enviado el formulario.
if (isset($_POST['aai_guardar'])) {
    // Guardo la clave y el nombre del negocio en las opciones de WordPress para su uso posterior.
    // Utilizo la función sanitize_text_field para asegurarme de que el texto ingresado sea seguro.
    update_option('aai_clave', sanitize_text_field($_POST['aai_clave']));
    update_option('aai_assistant_id', sanitize_text_field($_POST['aai_assistant_id']));
}

// Recupero la clave y el nombre del negocio guardados en las opciones de WordPress.
$clave_guardada = get_option('aai_clave', '');
$assistant_id_guardado = get_option('aai_assistant_id', '');
?>

<!-- Parte HTML del código para mostrar la interfaz de usuario en la página de administración de WordPress -->

<div class="wrap">
    <!-- Título de la página -->
    <h1>Divinity IA Assistant</h1>
    <!-- Descripción de la página -->
    <p>Bienvenido al administrador de Divinity IA Assistant, en esta pantalla puedes modificar todos los parámetros necesarios.</p>
    <!-- Contenedor de imágenes -->
    <div style="display: flex; justify-content: start; margin-bottom: 20px;">
        <!-- Primera imagen y su descripción -->
        <div style="text-align: left;">
            <?php echo '<img src="' . esc_url($img_url . 'diagramaID.png') . '" alt="Diagrama del ID" style="width: 95%;">'; ?>
                <h3>¿Como obtener ID del Assistant?</h3>
                <p>Paso 1: Pulsar en la barra lateral la opción de Assistants</p>
                <p>Paso 2: Pulsa en el botón de crear Assistant</p>
                <p>Paso 3: Copia la ID de tu Assistant y pegala en en el campo "Introduce la ID de tu Assistant de OpenAI"</p>
                <p>Paso 4: Introduce un nombre para tu Assistant</p>
                <p>Paso 5: Configura la instrucciones para tu negocio</p>
                <p>Paso 6: Selecciona un modelo, para que se suban los productos de tu tienda selecciona el modelo GPT-4 o superior</p>
                <p>Paso 7: Selleciona la opción de "Retrieval" para poder subir los productos a tu Assistant</p>
        </div>
        <!-- Segunda imagen y su descripción -->
        <div style="text-align: left;">
            <?php echo '<img src="' . esc_url($img_url . 'diagramaAPIkey.png') . '" alt="Diagrama de la API Key" style="width: 95%;">'; ?>
                <h3>¿Como obtener la API key?</h3>
                <p>Primero ha de registrarse en: https://platform.openai.com/docs/overview </p>
                <p>Paso 1: Pulsar en la barra lateral la opción de API keys</p>
                <p>Paso 2: Crear una nueva clave, posteriormete pegala en el campo "Introduce la API key de OpenAI"</p>
        </div>
        
    </div>
    <!-- Formulario para ingresar la clave de la API y el nombre del negocio -->
    <form method="post">
        <table class="form-table">
            <!-- Fila para ingresar el nombre del negocio -->
            <tr valign="top">
                <th scope="row">Introduce la ID de tu Assistant de OpenAI</th>
                <td><input type="text" name="aai_assistant_id" value="<?php echo esc_attr($assistant_id_guardado); ?>" /></td>
            </tr>
            <!-- Fila para ingresar la clave de la API -->
            <tr valign="top">
                <th scope="row">Introduce la API Key de OpenAI</th>
                <td><input type="text" name="aai_clave" value="<?php echo esc_attr($clave_guardada); ?>" /></td>
            </tr>
        </table>
        <!-- Botón para guardar los cambios -->
        <?php submit_button('Guardar', 'primary', 'aai_guardar'); ?>
        
    </form>
</div>
<!-- Fin del código HTML -->

