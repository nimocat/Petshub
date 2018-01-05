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
function themify_theme_games_google_fonts( $fonts ) {
	$fonts = array();
	/* translators: If there are characters in your language that are not supported by this font, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Lato font: on or off', 'themify' ) ) {
		$fonts['lato'] = 'Lato:300i,400,400i,700';
	}
	/* translators: If there are characters in your language that are not supported by this font, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Montserrat font: on or off', 'themify' ) ) {
		$fonts['montserrat'] = 'Montserrat:400,700';
	}	

	return $fonts;
}
add_filter( 'themify_theme_google_fonts', 'themify_theme_games_google_fonts' );

function new_excerpt_more( $more ) {
    return '';
}
add_filter('excerpt_more', 'new_excerpt_more');


function themify_theme_custom_excerpt_length( $length ) {
    return 12;
}
add_filter( 'excerpt_length', 'themify_theme_custom_excerpt_length', 999 );