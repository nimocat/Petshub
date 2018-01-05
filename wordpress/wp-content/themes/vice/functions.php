<?php
add_action( 'wp_enqueue_scripts', 'vice_theme_css',999);
function vice_theme_css() {
    wp_enqueue_style('appointment-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style('bootstrap-style', get_template_directory_uri() . '/css/bootstrap.css' );
	wp_enqueue_style('theme-menu', get_template_directory_uri() . '/css/theme-menu.css' );
	wp_enqueue_style('default-css', get_stylesheet_directory_uri()."/css/default.css" );
	wp_enqueue_style('element-style', get_template_directory_uri() . '/css/element.css' );
	wp_enqueue_style('media-responsive', get_template_directory_uri(). '/css/media-responsive.css');
	wp_dequeue_style('appointment-default',get_template_directory_uri() .'/css/default.css');
}
require( get_stylesheet_directory() . '/functions/customizer/customizer_import_data.php' );

add_action('admin_init', 'vice_update_theme');
function vice_update_theme()
{
update_option('template', 'appointment');
}

//Load text domain for translation-ready
load_theme_textdomain( 'vice', get_stylesheet_directory() . '/languages' );
?>