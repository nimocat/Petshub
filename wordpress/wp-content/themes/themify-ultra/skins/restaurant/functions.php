<?php
/**
 * Custom functions specific to the Restaurant skin
 *
 * @package Themify Ultra
 */

/**
 * Load Google web fonts required for the Restaurant skin
 *
 * @since 1.4.9
 * @return array
 */
function themify_theme_restaurant_google_fonts( $fonts ) {
	$fonts = array();
	/* translators: If there are characters in your language that are not supported by Cabin, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Cabin font: on or off', 'themify' ) ) {
		$fonts['cabin'] = 'Cabin:400,400italic,600,600italic,700,700italic';
	}
	/* translators: If there are characters in your language that are not supported by Source Sans, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Source Sans font: on or off', 'themify' ) ) {
		$fonts['source-sans'] = 'Source+Sans+Pro:400,700,900';
	}
	/* translators: If there are characters in your language that are not supported by Playfair Display, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Playfair Display font: on or off', 'themify' ) ) {
		$fonts['playfair-display'] = 'Playfair+Display:400,400italic,700,700italic,900,900italic';
	}

	return $fonts;
}
add_filter( 'themify_theme_google_fonts', 'themify_theme_restaurant_google_fonts' );

/**
 * Register custom template for the Fancy Heading module
 *
 * @since 1.1
 */
function themify_theme_restaurant_custom_modules() {
	Themify_Builder_Model::register_directory( 'templates', dirname( __FILE__ ) . '/templates' );
}
add_action( 'themify_builder_setup_modules', 'themify_theme_restaurant_custom_modules' );

/**
 * Add fancy heading styles to page titles
 *
 * @since 1.1
 */
function themify_theme_restaurant_page_title( $args ) {
	$args['class'] .= ' fancy-heading';
	$args['before_title'] = '
		<span class="maketable">
			<span class="addBorder"></span>
			<span class="fork-icon"></span>
			<span class="addBorder"></span>
		</span>';
	$args['after_title'] = '<span class="bottomBorder"></span>';

	return $args;
}
add_filter( 'themify_after_page_title_parse_args', 'themify_theme_restaurant_page_title' );

/**
 * Add fancy heading styles to post titles
 *
 * @since 1.1
 */
function themify_theme_restaurant_post_title( $args ) {
	global $themify;

	if ( ( isset( $themify->post_layout ) && $themify->post_layout == 'list-post' ) || ( is_singular() && $args['tag'] == 'h1' ) ) {
		$args['class'] .= ' fancy-heading';
		$args['before_title'] = '
			<span class="maketable">
				<span class="addBorder"></span>
				<span class="fork-icon"></span>
				<span class="addBorder"></span>
			</span>';
		$args['after_title'] = '<span class="bottomBorder"></span>';
	}

	return $args;
}
add_filter( 'themify_after_post_title_parse_args', 'themify_theme_restaurant_post_title' );