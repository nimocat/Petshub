<?php
/**
 * Custom functions specific to the skin
 *
 * @package Themify Ultra
 */

/**
 * Load Google web fonts required for the skin
 *
 * @since 1.4.9
 * @return array
 */
function themify_theme_ecommerce_google_fonts( $fonts ) {
	$fonts = array();
	/* translators: If there are characters in your language that are not supported by this font, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Poppins font: on or off', 'themify' ) ) {
		$fonts['poppins'] = 'Poppins:400,500,700';
	}
	return $fonts;
}
add_filter( 'themify_theme_google_fonts', 'themify_theme_ecommerce_google_fonts' );

/**
 * Register custom script for the ecommerce skin
 *
 * @since 1.1
 */
function themify_theme_ecommerce_custom_script() {
	wp_enqueue_script( 'themify-ecommerce-script', themify_enque(THEME_URI . '/skins/ecommerce/js/script.js'), array( 'jquery' ), wp_get_theme()->display( 'Version' ), true );	
}
add_action( 'wp_enqueue_scripts', 'themify_theme_ecommerce_custom_script' );
