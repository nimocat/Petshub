<?php

class Themify_Portfolio_Post {

	/**
	 * Path to the plugin's system directory */
	var $dir;
	var $url;
	var $version;

	/*
	 * Iterator for portfolio instances on a given page
	 */
	var $instance;
	var $pid = 'themify-portfolio-posts';

	/**
	 * Multi-d array containing information about available portfolio themes
	 */
	var $themes = array();
	/**
	 * Currently active portfolio theme */
	var $active_theme;

	/**
	 * Array of plugin's settings saved in DB */
	var $options = null;

	/**
	 * Used internally for storing a copy of $wp_query on portfolio archive pages */
	var $original_query;

	public function __construct( $args ) {
		$this->dir = $args['dir'];
		$this->url = $args['url'];
		$this->version = $args['version'];
		$this->actions();
	}

	public function actions() {
		add_action( 'init', array( $this, 'register' ) );
		include( $this->dir . 'includes/functions.php' );

		// compatibility mode: let the theme handle everything
		if( THEMIFY_PORTFOLIO_POSTS_COMPAT_MODE == true ) {
			add_filter( 'builder_is_portfolio_active', '__return_true' );
			return;
		}

		add_action( 'after_setup_theme', array( $this, 'admin' ), 100 );
		add_action( 'after_setup_theme', array( $this, 'load_dependencies' ), 15 );
		add_action( 'template_redirect', array( $this, 'template_redirect' ), 1 );
		add_shortcode( 'themify_portfolio_posts', array( $this, 'shortcode' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );

		$this->register_theme( array(
			'id' => 'stack',
			'label' => __( 'Stack', 'themify-portfolio-posts' ),
			'url' => $this->url . 'themes/stack',
			'dir' => $this->dir . 'themes/stack',
		) );
		do_action( 'themify_portfolio_posts_themes', $this );
		$this->active_theme = $this->themes[ $this->get_option( 'theme' ) ];

		// load custom functions.php from the active portfolio theme
		if( file_exists( $this->get_theme_dir() . '/functions.php' ) ) {
			include $this->get_theme_dir() . '/functions.php';
		}
	}

	public function admin() {
		if( is_admin() ) {
			include $this->dir . 'includes/admin.php';
			new Themify_Portfolio_Posts_Admin();
		}
	}

	public function template_redirect() {
		global $wp_query, $wp_the_query, $post;

		if( is_tax( 'portfolio-category' ) && '' != $this->get_option( 'index_page_template' ) ) {

			$this->original_query = $wp_query; // save a copy of original page query
			query_posts( 'page_id=' . $this->get_option( 'index_page_template' ) ); // change $wp_query to page query
			$wp_the_query = $wp_query; // destroy "the main query" as well, is_main_query() will now points to the page query
			$post = get_post( $this->get_option( 'index_page_template' ) ); // modify the global $post object
			add_filter( 'the_content', array( $this, 'index_portfolio_template' ) );

		} elseif( is_singular( 'portfolio' ) && $this->get_option( 'single_page_template' ) ) {

			$this->original_query = $wp_query; // save a copy of original page query
			query_posts( 'page_id=' . $this->get_option( 'single_page_template' ) ); // change $wp_query to page query
			$wp_the_query = $wp_query; // destroy "the main query" as well, is_main_query() will now points to the page query
			$post = get_post( $this->get_option( 'single_page_template' ) ); // modify the global $post object
			add_filter( 'the_content', array( $this, 'single_portfolio_template' ) );
			add_filter( 'the_title', array( $this, 'single_portfolio_title' ), 10, 2 );

		}
	}

	public function index_portfolio_template( $content ) {
		global $post;

		if( $post->post_type == 'page' && is_main_query() ) {
			 // load configuration variables (for the theme)
			$args = $this->parse_atts();

			// set the query object
			$args['query'] = $this->original_query;

			// render the portfolio items and append the result to the_content
			$content .= $this->get_template( 'portfolio-loop', $args );

			// to be sure the output is not modified again, remove the filter
			remove_filter( 'the_content', array( $this, 'output_portfolio_template' ) );
		}

		return $content;
	}

	public function single_portfolio_template( $content ) {
		global $post;

		if( $post->post_type == 'page' && is_main_query() ) {
			 // load configuration variables (for the theme)
			$args = $this->parse_atts();

			// set the query object
			$args['query'] = $this->original_query;

			// render the portfolio item and append the result to the_content
			$content .= $this->get_template( 'single-portfolio', $args );

			// to be sure the output is not modified again, remove the filter
			remove_filter( 'the_content', array( $this, 'output_portfolio_template' ) );
		}

		return $content;
	}

	/**
	 * Fix page titles for the page selected as single template for portfolios
	 *
	 * @return string
	 */
	public function single_portfolio_title( $title, $id ) {
		global $post;
		if( $id == $this->get_option( 'single_page_template' ) ) {
			if( $this->get_option( 'single_hide_title' ) == 'yes' ) {
				$title = '';
			} else {
				$title = $this->original_query->queried_object->post_title;
			}
		}

		return $title;
	}

	/**
	 * Load external libraries required by the plugin
	 *
	 * @since 1.0
	 */
	public function load_dependencies() {
		defined( 'THEMIFY_METABOX_DIR' ) || define( 'THEMIFY_METABOX_DIR', $this->dir . '/includes/themify-metabox/' );
		defined( 'THEMIFY_METABOX_URI' ) || define( 'THEMIFY_METABOX_URI', $this->url . '/includes/themify-metabox/' );
		include_once( $this->dir . 'includes/themify-metabox/themify-metabox.php' );
		require_once( $this->dir . 'includes/themify/img.php' );
	}

	/**
	 * Register post type and taxonomy
	 */
	function register() {
		$cpt = array(
			'plural' => __( 'Portfolios', 'themify-portfolio-posts' ),
			'singular' => __( 'Portfolio', 'themify-portfolio-posts' ),
			'rewrite' => apply_filters( 'themify_portfolio_post_rewrite', $this->get_option( 'portfolio_permalink' ) )
		);
		$post_type = array(
			'labels' => array(
				'name' => $cpt['plural'],
				'singular_name' => $cpt['singular']
			),
			'supports' => isset( $cpt['supports'] )? $cpt['supports'] : array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt','author' ),
			'hierarchical' => false,
			'has_archive' => true,
			'public' => true,
			'rewrite' => array( 'slug' => $cpt['rewrite'] ),
			'query_var' => true,
			'can_export' => true,
			'capability_type' => 'post',
			'menu_icon' => 'dashicons-portfolio',
		);
		/**
		 * Filter post type parameters sent to register_post_type()
		 *
		 * @param $post_type array
		 */
		$post_type = apply_filters( 'themify_portfolio_post_args', $post_type );
		register_post_type( 'portfolio', $post_type );

		register_taxonomy( 'portfolio-category', array( 'portfolio' ), array(
			'labels' => array(
				'name' => sprintf( __( '%s Categories', 'themify-portfolio-posts' ), $cpt['singular'] ),
				'singular_name' => sprintf( __( '%s Category', 'themify-portfolio-posts' ), $cpt['singular'] )
			),
			'public' => true,
			'show_in_nav_menus' => true,
			'show_ui' => true,
			'show_tagcloud' => true,
			'hierarchical' => true,
			'rewrite' => true,
			'query_var' => true
		));
	}

	public function shortcode( $atts, $content = '' ) {
		$this->instance++;

		extract( $this->parse_atts( $atts ) );

		// Pagination
		global $paged;
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		// Parameters to get posts
		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => $limit,
			'order' => $order,
			'orderby' => $orderby,
			'suppress_filters' => false,
			'paged' => $paged
		);
		// Category parameters
		$args['tax_query'] = $this->parse_category_args( $category, $post_type );

		$multiple = true;

		// Single post type or many single post types
		if( '' != $id ){
			if( strpos( $id, ',' ) ) {
				$ids = explode(',', str_replace(' ', '', $id));
				foreach ($ids as $string_id) {
					$int_ids[] = intval($string_id);
				}
				$args['post__in'] = $int_ids;
				$args['orderby'] = 'post__in';
			} else {
				$args['p'] = intval($id);
				$multiple = false;
			}
		}

		// Get posts according to parameters
		$query = new WP_Query( $args );

		if( $query ) {
			if(!$multiple) {
				if( '' == $image_w || get_post_meta($args['p'], 'image_width', true ) ){
					$image_w = get_post_meta($args['p'], 'image_width', true );
				}
				if( '' == $image_h || get_post_meta($args['p'], 'image_height', true ) ){
					$image_h = get_post_meta($args['p'], 'image_height', true );
				}
			}

			ob_start();
			include $this->locate_template( 'portfolio-loop' );
			return ob_get_clean();
		}

		return '';
	}

	public function get_template( $name, $args = array() ) {
		extract( $args );
		if( $path = $this->locate_template( $name ) ) {
			ob_start();
			include $path;
			return ob_get_clean();
		}

		return false;
	}

	public function locate_template( $name ) {
		if( is_child_theme() && file_exists( trailingslashit( get_stylesheet_directory() ) . trailingslashit( $this->pid ) . trailingslashit( $this->get_active_theme() ) . "{$name}.php" ) ) {
			return trailingslashit( get_stylesheet_directory() ) . trailingslashit( $pid ) . trailingslashit( $this->get_active_theme() ) . "{$name}.php";
		} else if( file_exists( trailingslashit( get_template_directory() ) . trailingslashit( $this->pid ) . trailingslashit( $this->get_active_theme() ) . "{$name}.php" ) ) {
			return trailingslashit( get_template_directory() ) . trailingslashit( $pid ) . trailingslashit( $this->get_active_theme() ) . "{$name}.php";
		} else if( file_exists( $this->get_theme_dir() . "/{$name}.php" ) ) {
			return $this->get_theme_dir() . "/{$name}.php";
		} else {
			return false;
		}
	}

	public function register_theme( $args ) {
		$this->themes[$args['id']] = array(
			'id' => $args['id'],
			'label' => $args['label'],
			'dir' => $args['dir'],
			'url' => $args['url'],
		);
	}

	/**
	 * Get a list of available themes for portfolios
	 *
	 * @return array
	 * @since 1.0
	 */
	public function get_themes() {
		return $this->themes;
	}

	/**
	 * Returns name of the currently active theme
	 *
	 * @return string
	 * @since 1.0
	 */
	public function get_active_theme() {
		return $this->active_theme['id'];
	}

	/**
	 * Return system path to the active theme
	 *
	 * @return string
	 * @since 1.0
	 */
	public function get_theme_dir() {
		return $this->active_theme['dir'];
	}

	/**
	 * Return URL path to the active theme
	 *
	 * @return string
	 * @since 1.0
	 */
	public function get_theme_url() {
		return $this->active_theme['url'];
	}

	/**
	 * Parses the arguments given as category to see if they are category IDs or slugs and returns a proper tax_query
	 * @param $category
	 * @param $post_type
	 * @return array
	 */
	function parse_category_args( $category, $post_type ) {
		$tax_query = array();
		if ( 'all' != $category ) {
			$terms = explode(',', $category);
			if( preg_match( '#[a-z]#', $category ) ) {
				$include = array_filter( $terms, 'themify_is_positive_string' );
				$exclude = array_filter( $terms, 'themify_is_negative_string' );
				$field = 'slug';
			} else {
				$include = array_filter( $terms, 'themify_is_positive_number' );
				$exclude = array_map( 'themify_make_absolute_number', array_filter( $terms, 'themify_is_negative_number' ) );
				$field = 'id';
			}

			if ( !empty( $include ) && !empty( $exclude ) ) {
				$tax_query = array(
					'relation' => 'AND'
				);
			}
			if ( !empty( $include ) ) {
				$tax_query[] = array(
					'taxonomy' => $post_type . '-category',
					'field'    => $field,
					'terms'    => $include,
				);
			}
			if ( !empty( $exclude ) ) {
				$tax_query[] = array(
					'taxonomy' => $post_type . '-category',
					'field'    => $field,
					'terms'    => $exclude,
					'operator' => 'NOT IN',
				);
			}
		}
		return $tax_query;
	}

	public function get_post_category_classes( $post_id = null ) {
		if( $post_id == null ) {
			$post_id = get_the_ID();
		}

		$categories = wp_get_object_terms( $post_id, 'portfolio-category' );
		$class      = '';
		foreach ( $categories as $cat ) {
			$class .= ' cat-' . $cat->term_id;
		}
		return $class;
	}

	/**
	 * Extract image IDs from gallery shortcode and try to return them as entries list
	 * @param string $field
	 * @return array|bool
	 * @since 1.0.0
	 */
	function get_gallery_images( $field = 'gallery_shortcode' ) {
		$gallery_shortcode = tpp_get( $field );
		$image_ids = preg_replace( '#\[gallery(.*)ids="([0-9|,]*)"(.*)\]#i', '$2', $gallery_shortcode );

		$query_args = array(
			'post__in' => explode( ',', str_replace( ' ', '', $image_ids ) ),
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'numberposts' => -1,
			'orderby' => stripos( $gallery_shortcode, 'rand' ) ? 'rand': 'post__in',
			'order' => 'ASC'
		);
		$entries = get_posts( apply_filters( 'themify_theme_get_gallery_images', $query_args ) );

		if ( $entries ) {
			return $entries;
		}

		return false;
	}

	/**
	 * Get columns specified in gallery shortcode. Defaults to 3.
	 *
	 * @since 1.0.0
	 *
	 * @param string $field Custom field where the gallery shortcode is saved.
	 *
	 * @return string Number of columns.
	 */
	function get_gallery_columns( $field = 'gallery_shortcode' ) {
		$gallery_shortcode = tpp_get( $field );
		preg_match( '#\[gallery(.*?)columns="([0-9])"(.*?)\]#i', $gallery_shortcode, $matches );

		if ( isset( $matches[2] ) && ctype_digit( $matches[2] ) ) {
			return (int) $matches[2];
		}
		return 3;
	}

	/**
	 * Get size specified in gallery shortcode. Defaults to large.
	 *
	 * @since 1.1.9
	 *
	 * @param string $field Custom field where the gallery shortcode is saved.
	 *
	 * @return string Number of columns.
	 */
	function get_gallery_size( $field = 'gallery_shortcode' ) {
		$gallery_shortcode = tpp_get( $field );
		preg_match( '#\[gallery(.*?)size="([a-z])"(.*?)\]#i', $gallery_shortcode, $matches );

		if ( isset( $matches[2] ) ) {
			return $matches[2];
		}
		return 'large';
	}

	/**
	 * Checks if there's a caption and returns it, otherwise returns description
	 * @param $image
	 * @return mixed
	 */
	function get_caption( $image ) {
		if ( '' != $image->post_excerpt ) {
			return $image->post_excerpt;
		}
		return $image->post_content;
	}

	public function parse_atts( $atts = array() ) {
		$defaults = array(
			'id' => '',
			'title' => 'yes',
			'unlink_title' => 'no',
			'image' => 'yes', // no
			'unlink_image' => 'no',
			'image_w' => 290,
			'image_h' => 290,
			'display' => 'none', // excerpt, content
			'post_meta' => 'yes', // no
			'post_date' => 'yes', // no
			'more_link' => false, // true goes to post type archive, and admits custom link
			'more_text' => __( 'More &rarr;', 'themify-portfolio-posts' ),
			'limit' => 4,
			'category' => 'all', // integer category ID
			'order' => 'DESC', // ASC
			'orderby' => 'date', // title, rand
			'style' => 'grid4', // grid4, grid3, grid2
			'sorting' => 'no', // yes
			'paged' => '0', // internal use for pagination, dev: previously was 1
			'use_original_dimensions' => 'no', // yes
			'filter' => 'no', // entry filter
			'post_type' => 'portfolio'
		);
		if( tpp_is_portfolio_category() ) {
			$image_size = $this->get_option( 'index_image_size' );
			$defaults['layout'] = $this->get_option( 'layout' );
			$defaults['display'] = $this->get_option( 'index_display' );
			$defaults['title'] = ( $this->get_option( 'index_hide_title' ) == 'yes' ) ? 'no' : 'yes';
			$defaults['unlink_title'] = $this->get_option( 'index_unlink_title' );
			$defaults['post_meta'] = ( $this->get_option( 'index_hide_meta' ) == 'yes' ) ? 'no' : 'yes';
			$defaults['post_date'] = ( $this->get_option( 'index_hide_date' ) == 'yes' ) ? 'no' : 'yes';
			$defaults['image'] = ( $this->get_option( 'index_hide_image' ) == 'yes' ) ? 'no' : 'yes';
			$defaults['unlink_image'] = $this->get_option( 'index_unlink_image' );
			if( isset( $image_size['width'] ) ) {
				$defaults['image_w'] = $image_size['width'];
				$defaults['image_h'] = $image_size['height'];
			}
			$defaults['masonry'] = $this->get_option( 'enable_masonry' ) == 'yes' ? 'yes' : 'no';
		} elseif( tpp_is_portfolio_single() ) {
			$image_size = $this->get_option( 'single_image_size' );
			$defaults['display'] = 'content';
			$defaults['title'] = ( $this->get_option( 'single_hide_title' ) == 'yes' ) ? 'no' : 'yes';
			$defaults['unlink_title'] = $this->get_option( 'single_unlink_title' );
			$defaults['post_meta'] = ( $this->get_option( 'single_hide_meta' ) == 'yes' ) ? 'no' : 'yes';
			$defaults['post_date'] = ( $this->get_option( 'single_hide_date' ) == 'yes' ) ? 'no' : 'yes';
			$defaults['image'] = ( $this->get_option( 'single_hide_image' ) == 'yes' ) ? 'no' : 'yes';
			$defaults['unlink_image'] = $this->get_option( 'single_unlink_image' );
			if( isset( $image_size['width'] ) ) {
				$defaults['image_w'] = $image_size['width'];
				$defaults['image_h'] = $image_size['height'];
			}
		}

		$defaults = apply_filters( 'themify_portfolio_posts_default_atts', $defaults );
		return apply_filters( "themify_portfolio_atts", shortcode_atts( $defaults, $atts ) );
	}

	/**
	 * Checks if there's a description and returns it, otherwise returns caption
	 * @param $image
	 * @return mixed
	 */
	function get_description( $image ) {
		if ( '' != $image->post_content ) {
			return $image->post_content;
		}
		return $image->post_excerpt;
	}

	/**
	 * Return all options
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function get_options() {
		if( null == $this->options ) {
			$this->options = wp_parse_args( get_option( 'themify_portfolio_posts', array() ), $this->get_default_options() );
		}

		return $this->options;
	}

	/**
	 * Return an option by it's name
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function get_option( $name, $default = null ) {
		$options = $this->get_options();
		if( isset( $options[$name] ) ) {
			return $options[$name];
		} else {
			return $default;
		}
	}

	/**
	 * Return default options of the plugin
	 *
	 * @return mixed
	 * @since 1.0
	 */
	public function get_default_options() {
		return apply_filters( 'themify_portfolio_posts_default_options', array(
			'theme' => 'stack',
			'layout' => 'masonry',
			'enable_masonry' => 'yes',
			'index_display' => 'none',
			'index_hide_title' => 'no',
			'index_unlink_title' => 'no',
			'index_hide_meta' => 'no',
			'index_hide_date' => 'no',
			'index_hide_image' => 'no',
			'index_unlink_image' => 'no',
			'index_image_size' => array( 'width' => '', 'height' => '' ),
			'single_hide_title' => 'no',
			'single_unlink_title' => 'no',
			'single_hide_meta' => 'no',
			'single_hide_date' => 'no',
			'single_hide_image' => 'no',
			'single_unlink_image' => 'no',
			'single_image_size' => array( 'width' => '', 'height' => '' ),
			'portfolio_permalink' => 'project',
			'index_page_template' => '',
			'single_page_template' => '',
		) );
	}

	/**
	 * Conditional tag to check if we're on a portfolio category archive page
	 * Can optionally check for specific portfolio category terms
	 * Should only be used after template_redirect
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_portfolio_category( $term = null ) {
		return isset( $this->original_query ) && $this->original_query->is_tax( 'portfolio-category', $term );
	}

	/**
	 * Conditional tag to check if we're on a single portfolio page
	 * Can optionally check for specific portfolio slug
	 * Should only be used after template_redirect
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public function is_portfolio_single( $slug = null ) {
		if( isset( $this->original_query ) && $this->original_query->is_singular( 'portfolio' ) ) {
			if( $slug == null ) {
				return true;
			} else {
				if( in_array( $this->original_query->queried_object->post_name, (array) $slug ) ) {
					return true;
				}
			}
		}
		return false;
	}

	function body_class( $classes ) {
		if( $this->get_option( 'enable_masonry' ) == 'yes' ) {
			$classes[] = 'tpp-masonry-enabled';
		}

		return $classes;
	}
}