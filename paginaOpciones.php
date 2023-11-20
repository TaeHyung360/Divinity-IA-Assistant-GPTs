<?php
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
    
    <!-- Formulario para ingresar la clave de la API y el nombre del negocio -->
    <form method="post">
        <table class="form-table">
            <!-- Fila para ingresar el nombre del negocio -->
            <tr valign="top">
                <th scope="row">Introduce la id de tu assistant de OpenIA</th>
                <td><input type="text" name="aai_assistant_id" value="<?php echo esc_attr($assistant_id_guardado); ?>" /></td>
            </tr>
            <!-- Fila para ingresar la clave de la API -->
            <tr valign="top">
                <th scope="row">Introduce la api key de openai</th>
                <td><input type="text" name="aai_clave" value="<?php echo esc_attr($clave_guardada); ?>" /></td>
            </tr>
        </table>
        <!-- Botón para guardar los cambios -->
        <?php submit_button('Guardar', 'primary', 'aai_guardar'); ?>
        
    </form>
</div>
<!-- Fin del código HTML -->

