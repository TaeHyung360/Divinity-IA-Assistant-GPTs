<?php
/**
 * Plugin Name: Divinity IA Assistant
 * Description: Un plugin para recomendar configuraciones de ordenadores personalizados mediante un chat interactivo con IA.
 * Version: 1.0
 * Author: Juan Ferrera Sala
 */

// Verificar si WordPress ha sido cargado correctamente para evitar accesos directos no autorizados.
if (!defined('ABSPATH')) {
    exit; // Si no se ha definido ABSPATH, detiene la ejecución del script.
}

// Iniciar una nueva sesión PHP o continuar la sesión existente.
if (!session_id()) {
    session_start();
}

//===========================================================
//Añado un nuevo menú al Panel de Control de Administrador
//===========================================================

add_action('admin_menu', 'aai_Add_My_Admin_Link');
add_action('admin_init', 'aai_register_settings');

// Registro la configuración de la opción
function aai_register_settings() {
    register_setting('aai_settings_group', 'aai_clave');
    register_setting('aai_settings_group', 'aai_assistant_id');
}

// Agrego un nuevo enlace de menú de nivel superior
function aai_Add_My_Admin_Link() {
    add_menu_page(
        'Divinity IA Assistant', // Titulo de la pagina
        'Divinity IA Assistant', // Texto para mostrar en el enlace del menu
        'manage_options', // Requisito de capacidad para ver el enlace.
        'aai_admin_page_slug', 
        'aai_admin_page' //  Función de devolución de llamada para mostrar el contenido de la página.
    );
}

// Función para generar el contenido de la página.
function aai_admin_page() {
    include(plugin_dir_path(__FILE__) . 'paginaOpciones.php');
}

//===========================================================
// Llamadas e inicializacion de archivos
//===========================================================
require_once plugin_dir_path(__FILE__) . 'chatShortcode.php';

require_once plugin_dir_path(__FILE__) . 'inc/integracionOpenAi.php';

require_once plugin_dir_path(__FILE__) . 'inc/manejadorAjaxOpenIa.php';

