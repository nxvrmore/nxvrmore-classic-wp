<?php
/*
Plugin Name: Nxvrmore Classic WP
Plugin URI: https://github.com/nxvrmore/nxvrmore-classic-wp
Description: Este plugin activa el editor clásico de WordPress, desactiva el editor de bloques para widgets, establece los enlaces permanentes como 'Nombre de la entrada', deshabilita la barra de herramientas para todos los usuarios (incluido el administrador), elimina plugins y contenido por defecto de WordPress.
Version: 1.2
Author: Nxvrmore
Author URI: https://github.com/nxvrmore
License: GPL2
*/

// Activar el editor clásico de WordPress
add_filter('use_block_editor_for_post', '__return_false');
add_filter('use_block_editor_for_post_type', '__return_false');

// Activar el diseño clásico de la pantalla de edición con TinyMCE
add_filter('use_block_editor_for_post_type', function($enabled, $post_type) {
    return false;
}, 10, 2);

// Activar las pantallas clásicas de ajustes de widgets
add_filter('gutenberg_use_widgets_block_editor', '__return_false');
add_filter('use_widgets_block_editor', '__return_false');

// Establecer los enlaces permanentes como 'Nombre de la entrada'
function nxvrmore_set_permalink_structure() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure('/%postname%/');
    $wp_rewrite->flush_rules();
}
register_activation_hook(__FILE__, 'nxvrmore_set_permalink_structure');

// Deshabilitar la barra de herramientas para todos los usuarios, incluido el administrador
function nxvrmore_disable_admin_bar() {
    // Deshabilitar la barra de herramientas para todos los usuarios
    add_filter('show_admin_bar', '__return_false');

    // Deshabilitar la barra de herramientas en el frontend para el administrador
    if (current_user_can('administrator')) {
        show_admin_bar(false);
    }
}
add_action('init', 'nxvrmore_disable_admin_bar', 9); // Prioridad 9 para asegurarse de que se ejecute antes que otros hooks

// Eliminar los plugins por defecto de WordPress (Hello Dolly y Akismet)
function nxvrmore_remove_default_plugins() {
    // Desactivar y eliminar Akismet
    if (is_plugin_active('akismet/akismet.php')) {
        deactivate_plugins('akismet/akismet.php');
        delete_plugins(array('akismet/akismet.php'));
    }
    // Desactivar y eliminar Hello Dolly
    if (is_plugin_active('hello.php')) {
        deactivate_plugins('hello.php');
        delete_plugins(array('hello.php'));
    }
}
register_activation_hook(__FILE__, 'nxvrmore_remove_default_plugins');

// Eliminar el comentario por defecto de WordPress
function nxvrmore_remove_default_comment() {
    wp_delete_comment(1, true);
}
register_activation_hook(__FILE__, 'nxvrmore_remove_default_comment');

// Eliminar las páginas por defecto de WordPress (Página de ejemplo y Política de privacidad)
function nxvrmore_remove_default_pages() {
    // Títulos de las páginas por defecto en español
    $default_page_titles = array(
        'Página de ejemplo', // Título de la página de ejemplo en español
        'Política de privacidad', // Título de la política de privacidad en español
        'Sample Page', // Título en inglés por si acaso
        'Privacy Policy' // Título en inglés por si acaso
    );

    // Buscar y eliminar las páginas por su título
    foreach ($default_page_titles as $title) {
        $page = get_page_by_title($title);
        if ($page) {
            wp_delete_post($page->ID, true);
        }
    }
}
register_activation_hook(__FILE__, 'nxvrmore_remove_default_pages');

// Eliminar la entrada por defecto de WordPress
function nxvrmore_remove_default_post() {
    $default_post = get_post(1);
    if ($default_post) {
        wp_delete_post(1, true);
    }
}
register_activation_hook(__FILE__, 'nxvrmore_remove_default_post');