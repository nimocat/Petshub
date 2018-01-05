<?php

/**
 * Load assets needed for Stack theme on front end
 *
 * @since 1.0
 */
function themify_portfolio_posts_stack_enqueue() {
	global $themify_portfolio_posts;

	// Backstretch
	if( ! wp_script_is( 'themify-backstretch', 'registered' ) ) {
		wp_register_script( 'themify-backstretch', $themify_portfolio_posts->get_theme_url() . '/js/backstretch.min.js', array( 'jquery' ), $themify_portfolio_posts->version, true );
	}

	wp_enqueue_script( 'themify-portfolio-posts-stack', $themify_portfolio_posts->get_theme_url() . '/js/scripts.js', array( 'jquery', 'jquery-masonry' ), $themify_portfolio_posts->version, true );

	wp_enqueue_style( $themify_portfolio_posts->pid, $themify_portfolio_posts->get_theme_url() . '/style.css' );
	// wp_enqueue_style( 'themify-font-icons-css', THEMIFY_URI . '/fontawesome/css/font-awesome.min.css', array(), THEMIFY_VERSION );
}
add_action( 'wp_enqueue_scripts', 'themify_portfolio_posts_stack_enqueue', 20 );

if ( ! function_exists( 'themify_portfolio_posts_stack_custom_post_css' ) ) {
	/**
	 * Outputs custom post CSS at the end of a post
	 * @since 1.0.0
	 */
	function themify_portfolio_posts_stack_custom_post_css() {
		global $themify;

		if( tpp_is_portfolio_single() ) {
			return;
		}

		$post_id = get_the_ID();
		if ( in_array( get_post_type( $post_id ), array( 'portfolio' ) ) ) {
			$css = array();
			$style = '';
			$rules = array();

			if ( ! is_single() ) {
				$entry_id = '.post-' . $post_id;
				$entry = $entry_id . '.post';
				$rules = array(
					$entry => array(
						array(
							'prop' => 'background-color',
							'key'  => 'background_color'
						),
						array(
							'prop' => 'background-image',
							'key'  => 'background_image'
						),
						array(
							'prop' => 'background-repeat',
							'key'  => 'background_repeat',
							'dependson' => array(
								'prop' => 'background-image',
								'key'  => 'background_image'
							),
						),
						array(
							'prop' => 'color',
							'key'  => 'text_color'
						),
					),
					"$entry a" => array(
						array(
							'prop' => 'color',
							'key'  => 'link_color'
						),
					),
				);
			}

			foreach ( $rules as $selector => $property ) {
				foreach ( $property as $val ) {
					$prop = $val['prop'];
					$key = $val['key'];
					if ( is_array( $key ) ) {
						if ( $prop == 'font-size' && tpp_check( $key[0] ) ) {
							$css[$selector][$prop] = $prop . ': ' . tpp_get( $key[0] ) . tpp_get( $key[1] );
						}
					} elseif ( tpp_check( $key ) && 'default' != tpp_get( $key ) ) {
						if ( $prop == 'color' || stripos( $prop, 'color' ) ) {
							$css[$selector][$prop] = $prop . ': #' . tpp_get( $key );
						}
						elseif ( $prop == 'background-image' && 'default' != tpp_get( $key ) ) {
							$css[$selector][$prop] = $prop .': url(' . tpp_get( $key ) . ')';
						}
						elseif ( $prop == 'background-repeat' && 'fullcover' == tpp_get( $key ) ) {
							if ( isset( $val['dependson'] ) ) {
								if ( $val['dependson']['prop'] == 'background-image' && ( tpp_check( $val['dependson']['key'] ) && 'default' != tpp_get( $val['dependson']['key'] ) ) ) {
									$css[$selector]['background-size'] = 'background-size: cover';
								}
							} else {
								$css[$selector]['background-size'] = 'background-size: cover';
							}
						}
						elseif ( $prop == 'font-family' ) {
							$font = tpp_get( $key );
							$css[$selector][$prop] = $prop .': '. $font;
							if ( ! in_array( $font, themify_get_web_safe_font_list( true ) ) ) {
								$themify->google_fonts .= str_replace( ' ', '+', $font.'|' );
							}
						}
						else {
							$css[$selector][$prop] = $prop .': '. tpp_get( $key );
						}
					}
				}
				if ( ! empty( $css[$selector] ) ) {
					$style .= "$selector {\n\t" . implode( ";\n\t", $css[$selector] ) . "\n}\n";
				}
			}

			if ( '' != $style ) {
				echo "\n<!-- Entry Style -->\n<style>\n$style</style>\n<!-- End Entry Style -->\n";
			}
		}
	}
}
add_action( 'themify_portfolio_post_end', 'themify_portfolio_posts_stack_custom_post_css' );

function themify_portfolio_posts_stack_post_class( $classes ) {
	global $post;

	if( $post->post_type == 'portfolio' ) {
		if ( $size = get_post_meta( $post->ID, 'tile_layout', true ) ) {
			$classes[] = $size;
		} else {
			$classes[] = 'size-large image-left';
		}
	}

	return $classes;
}
add_filter( 'post_class', 'themify_portfolio_posts_stack_post_class' );