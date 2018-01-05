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
function themify_theme_accountant_google_fonts( $fonts ) {
	$fonts = array();
	/* translators: If there are characters in your language that are not supported by this font, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Playfair Display font: on or off', 'themify' ) ) {
		$fonts['playfair-display'] = 'Playfair+Display:400,400italic,700,700italic,900,900italic';
	}
	/* translators: If there are characters in your language that are not supported by this font, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Montserrat font: on or off', 'themify' ) ) {
		$fonts['montserrat'] = 'Montserrat:400,700';
	}
	/* translators: If there are characters in your language that are not supported by this font, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Open Sans: on or off', 'themify' ) ) {
		$fonts['open-sans'] = 'Open+Sans:400,700';
	}

	return $fonts;
}
add_filter( 'themify_theme_google_fonts', 'themify_theme_accountant_google_fonts' );