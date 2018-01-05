<?php

class Themify_Portfolio_Posts_Admin {

	var $options;
	var $post_type = 'portfolio';
	var $tax = 'portfolio-category';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'manage_and_filter' ) );
		add_filter( 'manage_edit-'.$this->tax.'_columns', array( $this, 'taxonomy_header' ), 10, 2 );
		add_filter( 'manage_'.$this->tax.'_custom_column', array( $this, 'taxonomy_column_id' ), 10, 3 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 10, 2 );
		add_action( 'edit_attachment', array($this, 'attachment_fields_to_save'), 10, 2 );

		// Compatibility mode: do not setup metabox or options page
		if( THEMIFY_PORTFOLIO_POSTS_COMPAT_MODE == true ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'init', array( $this, 'setup_portfolio_metabox' ) );
		add_action( 'admin_menu', array( $this, 'setup_options_page' ), 100 );
	}

	public function setup_options_page() {
		add_submenu_page( 'edit.php?post_type=portfolio', __( 'Portfolio Options', 'themify-portfolio-posts' ), __( 'Portfolio Options', 'themify-portfolio-posts' ), 'manage_options', 'themify-portfolio-posts', array( $this, 'create_admin_page' ) );
	}

	public function setup_portfolio_metabox() {
		add_filter( 'themify_do_metaboxes', array( $this, 'themify_do_metaboxes' ) );
	}

	public function themify_do_metaboxes( $metaboxes ) {
		global $themify_portfolio_posts, $pagenow;

		$portfolio_options = array(
			array(
				'name'		=> __( 'Portfolio Options', 'themify-portfolio-posts' ),
				'id' 		=> 'portfolio-options',
				'options' 	=> include $themify_portfolio_posts->dir . 'includes/config.php',
				'pages'		=> 'portfolio'
			)
		);
		if( file_exists( $themify_portfolio_posts->locate_template( 'config' ) ) ) {
			$portfolio_options[] = include( $themify_portfolio_posts->locate_template( 'config' ) );
		}
		$portfolio_options = apply_filters( "themify_portfolio_post_options", $portfolio_options );

		return array_merge( $portfolio_options, $metaboxes );
	}

    public function create_admin_page() {
		global $themify_portfolio_posts;

		$this->options = $themify_portfolio_posts->get_options();
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e( 'Themify Portfolio Posts', 'themify-portfolio-posts' ); ?></h2>           
			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'themify_portfolio_posts' );   
				do_settings_sections( 'themify-portfolio-posts' );
				submit_button(); 
				?>
			</form>
		</div>
		<?php
    }

	/**
	 * Register and add settings
	 */
	public function page_init() {        
		register_setting(
			'themify_portfolio_posts', // Option group
			'themify_portfolio_posts' // Option name
		);

		add_settings_section(
			'tpp-portfolio-general', // ID
			__( 'General Settings', 'themify-portfolio-posts' ), // Title
			null, // Callback
			'themify-portfolio-posts' // Page
		);

		add_settings_section(
			'tpp-portfolio-index', // ID
			__( 'Portfolio Index Options', 'themify-portfolio-posts' ), // Title
			null, // Callback
			'themify-portfolio-posts' // Page
		);

		add_settings_section(
			'tpp-portfolio-single', // ID
			__( 'Portfolio Single Options', 'themify-portfolio-posts' ), // Title
			null, // Callback
			'themify-portfolio-posts' // Page
		);

		add_settings_section(
			'tpp-portfolio-permalink', // ID
			__( 'Portfolio Permalink', 'themify-portfolio-posts' ), // Title
			null, // Callback
			'themify-portfolio-posts' // Page
		);

		add_settings_field(
			'theme', // ID
			__( 'Theme', 'themify-portfolio-posts' ), // Title 
			array( $this, 'theme_callback' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-general' // Section
		);

		add_settings_field(
			'index_page_template', // ID
			__( 'Page Template', 'themify-portfolio-posts' ), // Title 
			array( $this, 'page_select_field' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_page_template' )
		);

		add_settings_field(
			'enable_masonry', // ID
			__( 'Enable Masonry', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'enable_masonry' )
		);

		add_settings_field(
			'layout', // ID
			__( 'Portfolio Layout', 'themify-portfolio-posts' ), // Title 
			array( $this, 'layout_callback' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index' // Section
		);

		add_settings_field(
			'single_page_template', // ID
			__( 'Page Template', 'themify-portfolio-posts' ), // Title 
			array( $this, 'page_select_field' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-single', // Section
			array( 'id' => 'single_page_template' )
		);

		add_settings_field(
			'index_display', // ID
			__( 'Display Content', 'themify-portfolio-posts' ), // Title 
			array( $this, 'display_callback' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_display' )
		);

		add_settings_field(
			'index_hide_title', // ID
			__( 'Hide Title', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_hide_title' )
		);

		add_settings_field(
			'index_unlink_title', // ID
			__( 'Unlink Portfolio Title', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_unlink_title' )
		);

		add_settings_field(
			'index_hide_meta', // ID
			__( 'Hide Portfolio Meta', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_hide_meta' )
		);

		add_settings_field(
			'index_hide_date', // ID
			__( 'Hide Portfolio Date', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_hide_date' )
		);

		add_settings_field(
			'index_hide_image', // ID
			__( 'Hide Portfolio Image', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_hide_image' )
		);

		add_settings_field(
			'index_unlink_image', // ID
			__( 'Unlink Portfolio Image', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_unlink_image' )
		);

		add_settings_field(
			'index_image_size', // ID
			__( 'Image Size', 'themify-portfolio-posts' ), // Title 
			array( $this, 'image_size_callback' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-index', // Section
			array( 'id' => 'index_image_size' )
		);

		add_settings_field(
			'single_hide_title', // ID
			__( 'Hide Portfolio Title', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-single', // Section
			array( 'id' => 'single_hide_title' )
		);

		add_settings_field(
			'single_hide_meta', // ID
			__( 'Hide Portfolio Meta', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-single', // Section
			array( 'id' => 'single_hide_meta' )
		);

		add_settings_field(
			'single_hide_date', // ID
			__( 'Hide Portfolio Date', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-single', // Section
			array( 'id' => 'single_hide_date' )
		);

		add_settings_field(
			'single_hide_image', // ID
			__( 'Hide Portfolio Image', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-single', // Section
			array( 'id' => 'single_hide_image' )
		);

		add_settings_field(
			'single_unlink_image', // ID
			__( 'Unlink Portfolio Image', 'themify-portfolio-posts' ), // Title 
			array( $this, 'yes_no_select' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-single', // Section
			array( 'id' => 'single_unlink_image' )
		);

		add_settings_field(
			'single_image_size', // ID
			__( 'Image Size', 'themify-portfolio-posts' ), // Title 
			array( $this, 'image_size_callback' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-single', // Section
			array( 'id' => 'single_image_size' )
		);

		add_settings_field(
			'portfolio_permalink', // ID
			__( 'Portfolio Base Slug', 'themify-portfolio-posts' ), // Title 
			array( $this, 'portfolio_permalink_callback' ), // Callback
			'themify-portfolio-posts', // Page
			'tpp-portfolio-permalink' // Section
		);
    }

	public function yes_no_select( $args ) {
		?>
		<label>
			<input type="radio" id="" name="themify_portfolio_posts[<?php echo $args['id']; ?>]" value="yes" <?php checked( 'yes', $this->options[$args['id']] ); ?>><?php _e( 'Yes', 'themify-portfolio-posts' ); ?>
		</label>
		<label>
			<input type="radio" id="" name="themify_portfolio_posts[<?php echo $args['id']; ?>]" value="no" <?php checked( 'no', $this->options[$args['id']] ); ?>><?php _e( 'No', 'themify-portfolio-posts' ); ?>
		</label>
		<?php
	}

	public function page_select_field( $args ) {
		wp_dropdown_pages( array(
			'selected' => $this->options[$args['id']],
			'name' => 'themify_portfolio_posts[' . $args['id'] .']',
			'show_option_none' => '&nbsp;'
		) );
	}

	public function theme_callback( $args ) {
		global $themify_portfolio_posts;
		?><select class="" id="" name="themify_portfolio_posts[theme]">
			<?php foreach( $themify_portfolio_posts->get_themes() as $theme ) : ?>
				<option value="<?php echo $theme['id']; ?>" <?php selected( $this->options['theme'], $theme['id'] ); ?>> <?php echo $theme['label']; ?></option>
			<?php endforeach; ?>
		</select><?php
	}

	public function display_callback( $args ) {
		?><select class="" id="" name="themify_portfolio_posts[<?php echo $args['id']; ?>]">
			<option value="none" <?php selected( $this->options[$args['id']], 'none' ); ?>> <?php _e( 'None', 'themify-portfolio-posts' ); ?></option>
			<option value="excerpt" <?php selected( $this->options[$args['id']], 'excerpt' ); ?>> <?php _e( 'Excerpt', 'themify-portfolio-posts' ); ?></option>
			<option value="content" <?php selected( $this->options[$args['id']], 'content' ); ?>> <?php _e( 'Content', 'themify-portfolio-posts' ); ?></option>
		</select><?php
	}

	public function layout_callback( $args ) {
		global $themify_portfolio_posts;
		$base_url = $themify_portfolio_posts->url . 'images/layout-icons';
		?>
		<div class="themify-radio-image">
			<label>
				<input type="radio" name="themify_portfolio_posts[layout]" value="masonry" <?php checked( $this->options['layout'], 'masonry' ); ?>>
				<img src="<?php echo $base_url ?>/masonry-view.png" alt="masonry" />
			</label>
			<label>
				<input type="radio" name="themify_portfolio_posts[layout]" value="list-post" <?php checked( $this->options['layout'], 'list-post' ); ?>>
				<img src="<?php echo $base_url ?>/list-post.png" alt="list-post" />
			</label>
			<label>
				<input type="radio" name="themify_portfolio_posts[layout]" value="grid2" <?php checked( $this->options['layout'], 'grid2' ); ?>>
				<img src="<?php echo $base_url ?>/grid2.png" alt="grid2" />
			</label>
			<label>
				<input type="radio" name="themify_portfolio_posts[layout]" value="grid3" <?php checked( $this->options['layout'], 'grid3' ); ?>>
				<img src="<?php echo $base_url ?>/grid3.png" alt="grid3" />
			</label>
			<label>
				<input type="radio" name="themify_portfolio_posts[layout]" value="grid4" <?php checked( $this->options['layout'], 'grid4' ); ?>>
				<img src="<?php echo $base_url ?>/grid4.png" alt="grid4" />
			</label>
		</div>
		<style>
			.themify-radio-image img { display: block; }
			.themify-radio-image label { float: left; margin-right: 10px; }
		</style>
		<?php
	}

	public function image_size_callback( $args ) {
		?>
		<input class="small-text" id="" name="themify_portfolio_posts[<?php echo $args['id']; ?>][width]" value="<?php echo esc_attr( $this->options[$args['id']]['width'] ) ?>"> <?php _e( 'Width (px)', 'themify-portfolio-posts' ); ?>
		<input class="small-text" id="" name="themify_portfolio_posts[<?php echo $args['id']; ?>][height]" value="<?php echo esc_attr( $this->options[$args['id']]['height'] ) ?>"> <?php _e( 'Height', 'themify-portfolio-posts' ); ?>
		<?php
	}

	public function portfolio_permalink_callback( $args ) {
		?>
		<input class="medium-text" id="" name="themify_portfolio_posts[portfolio_permalink]" value="<?php echo esc_attr( $this->options['portfolio_permalink'] ) ?>">
		<div class="description">
			<?php _e( 'Use only lowercase letters, numbers, underscores and dashes. After changing this, go to permalinks and click "Save changes" to refresh them.', 'themify-portfolio-posts' ); ?>
		</div>
		<?php
	}

	/**
	 * Trigger at the end of __construct of this shortcode
	 */
	function manage_and_filter() {
		add_filter( "manage_edit-{$this->post_type}_columns", array( $this, 'type_column_header' ), 10, 2 );
		add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'type_column' ), 10, 3 );
		global $typenow;
		if ( empty( $typenow ) && isset( $_GET['post'] ) && !empty( $_GET['post'] ) ) {
			$post = get_post( $_GET['post'] );
			$typenow = $post->post_type;
		}
		if( $typenow == $this->post_type ) {
			add_action( 'load-edit.php', array( $this, 'filter_load' ) );
			add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 1 );
		}
	}

	/**
	 * Filter request to sort
	 */
	function filter_load() {
		add_action( current_filter(), array( $this, 'setup_vars' ), 20 );
		add_action( 'restrict_manage_posts', array( $this, 'get_select' ) );
		add_filter( "manage_taxonomies_for_{$this->post_type}_columns", array( $this, 'add_columns' ) );
	}

	/**
	 * Setup vars when filtering posts in edit.php
	 */
	function setup_vars() {
		$this->post_type =  get_current_screen()->post_type;
		$this->taxonomies = array_diff(get_object_taxonomies($this->post_type), get_taxonomies(array('show_admin_column' => 'false')));
	}

	/**
	 * Select form element to filter the post list
	 * @return string HTML
	 */
	public function get_select() {
		$html = '';
		foreach ( $this->taxonomies as $tax ) {
			$options = sprintf( '<option value="">%s %s</option>', __('View All', 'themify-portfolio-posts'),
			get_taxonomy($tax)->label );
			$class = is_taxonomy_hierarchical( $tax ) ? ' class="level-0"' : '';
			foreach ( get_terms( $tax ) as $taxon ) {
				$options .= sprintf( '<option %s%s value="%s">%s%s</option>', isset( $_GET[$tax] ) ? selected( $taxon->slug, $_GET[$tax], false ) : '', '0' !== $taxon->parent ? ' class="level-1"' : $class, $taxon->slug, '0' !== $taxon->parent ? str_repeat( '&nbsp;', 3 ) : '', "{$taxon->name} ({$taxon->count})" );
			}
			$html .= sprintf( '<select name="%s" id="%s" class="postform">%s</select>', esc_attr( $tax ), esc_attr( $tax ), $options );
		}
		echo $html;
	}

	/**
	 * Add columns when filtering posts in edit.php
	 */
	public function add_columns( $taxonomies ) {
		return array_merge( $taxonomies, $this->taxonomies );
	}

	/**
	 * Display an additional column in list
	 * @param array
	 * @return array
	 */
	function type_column_header( $columns ) {
		unset( $columns['date'] );
		return $columns;
	}

	/**
	 * Display shortcode, type, size and color in columns in tiles list
	 * @param string $column key
	 * @param number $post_id
	 * @return string
	 */
	function type_column( $column, $post_id ) {
		switch( $column ) {
			case 'shortcode' :
				$shortcode = '[' . $this->post_type . ' id="' . esc_attr( $post_id ) . '"]';
				echo '<input type="text" value="' . esc_attr( $shortcode ) . '" readonly="readonly" class="widefat" onclick="this.select()" />';
				break;
		}
	}

	/**
	 * Remove quick edit action from entries list in admin
	 * @param $actions
	 * @return mixed
	 */
	function remove_quick_edit( $actions ) {
		unset($actions['inline hide-if-no-js']);
		return $actions;
	}

	function attachment_fields_to_edit( $form_fields, $post ) {
		if ( ! preg_match( '!^image/!', get_post_mime_type( $post->ID ) ) ) {
			return $form_fields;
		}

		$include = get_post_meta( $post->ID, 'themify_gallery_featured', true );

		$name = 'attachments[' . $post->ID . '][themify_gallery_featured]';

		$form_fields['themify_gallery_featured'] = array(
			'label' => __( 'Larger', 'themify-portfolio-posts' ),
			'input' => 'html',
			'helps' => __('Show larger image in the gallery.', 'themify-portfolio-posts'),
			'html'  => '<span class="setting"><label for="' . esc_attr( $name ) . '" class="setting"><input type="checkbox" name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" value="featured" ' . checked( $include, 'featured', false ) . ' />' . '</label></span>',
		);

		return $form_fields;
	}

	function attachment_fields_to_save( $attachment_id ) {
		if( isset( $_REQUEST['attachments'][$attachment_id]['themify_gallery_featured'] ) && preg_match( '!^image/!', get_post_mime_type( $attachment_id ) ) ) {
			update_post_meta($attachment_id, 'themify_gallery_featured', 'featured');
		} else {
			update_post_meta($attachment_id, 'themify_gallery_featured', '');
		}
	}

	/**
	 * Display an additional column in categories list
	 * @since 1.0.0
	 */
	function taxonomy_header($cat_columns) {
		$cat_columns['cat_id'] = 'ID';
		return $cat_columns;
	}

	/**
	 * Display ID in additional column in categories list
	 * @since 1.0.0
	 */
	function taxonomy_column_id($null, $column, $termid) {
		return $termid;
	}
}
