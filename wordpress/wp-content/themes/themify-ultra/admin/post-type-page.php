<?php
/**
 * Page Meta Box Options
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function themify_theme_page_meta_box( $args = array() ){
	extract( $args );
	return array(
		// Page Layout
		array(
			'name' 		=> 'page_layout',
			'title'		=> __('Sidebar Option', 'themify'),
			'description'	=> '',
			'type'		=> 'layout',
			'show_title' => true,
			'meta'		=> array(
				array('value' => 'default', 'img' => 'images/layout-icons/default.png', 'selected' => true, 'title' => __('Default', 'themify')),
				array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
				array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
				array('value' => 'sidebar2', 'img' => 'images/layout-icons/sidebar2.png', 'title' => __('Left and Right', 'themify')),
				array('value' => 'sidebar2 content-left', 	'img' => 'images/layout-icons/sidebar2-content-left.png', 'title' => __('2 Right Sidebars', 'themify')),
				array('value' => 'sidebar2 content-right', 	'img' => 'images/layout-icons/sidebar2-content-right.png', 'title' => __('2 Left Sidebars', 'themify')),
				array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar ', 'themify'))
			),
			'default' => 'default'
		),

		// Content Width
		array(
			'name'=> 'content_width',
			'title' => __('Content Width', 'themify'),
			'description' => '',
			'type' => 'layout',
			'show_title' => true,
			'meta' => array(
				array(
					'value' => 'default_width',
					'img' => 'themify/img/default.png',
					'selected' => true,
					'title' => __( 'Default', 'themify' )
				),
				array(
					'value' => 'full_width',
					'img' => 'themify/img/fullwidth.png',
					'title' => __( 'Fullwidth', 'themify' )
				)
			),
			'default' => 'default_width'
		),		
		// Hide page title
		array(
			'name' 		=> 'hide_page_title',
			'title'		=> __('Hide Page Title', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default'
		),
		// Custom menu
		array(
			'name'        => 'custom_menu',
			'title'       => __( 'Custom Menu', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			// extracted from $args
			'meta'        => themify_get_available_menus(),
		),
		// Full section scrolling
		array(
			'name' 		=> 'section_full_scrolling',
			'title'		=> __('Builder Section Scrolling', 'themify'),
			'description'	=> __( 'When it is enabled, each Builder row will have full height and perform full section scrolling on page scroll.', 'themify' ),
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'), 'selected' => true)
			),
			'default' => 'no',
			'hide'		=> 'no section-scrolling-mobile'
		),
		// Scroll Section on mobile devices
		array(
			'name' 		=> 'section_scrolling_mobile',
			'title'		=> '',
			'after'=> __('Keep section scrolling enabled on tablet/mobile', 'themify'),
			'type' 		=> 'checkbox',
			'default' 	=> 'default',
			'class'		=> 'section-scrolling-mobile'
		)
	);
}

/**
 * Query Posts Options
 * @param array $args
 * @return array
 * @since 1.0.0
 */
function themify_theme_query_post_meta_box($args = array()) {
	extract( $args );
	return array(
		// Notice
		array(
			'name' => '_query_posts_notice',
			'title' => '',
			'description' => '',
			'type' => 'separator',
			'meta' => array(
				'html' => '<div class="themify-info-link">' . sprintf( __( '<a href="%s">Query Posts</a> allows you to query WordPress posts from any category on the page. To use it, select a Query Category.', 'themify' ), 'http://themify.me/docs/query-posts' ) . '</div>'
			),
		),
		// Query Category
		array(
			'name' 		=> 'query_category',
			'title'		=> __('Query Category', 'themify'),
			'description'	=> __('Select a category or enter multiple category IDs (eg. 2,5,6). Enter 0 to display all category.', 'themify'),
			'type'		=> 'query_category',
			'meta'		=> array()
		),
		// Query All Post Types
		array(
			'name' => 'query_all_post_types',
			'type' => 'dropdown',
			'title' => __( 'Query All Post Types', 'themify'),
			'meta' =>array(
				array(
				'value' => '',
				'name' => '',
				),
				array(
				'value' => 'yes',
				'name' => 'Yes',
				),
				array(
				'value' => 'no',
				'name' => 'No',
				),
			)
		),
		// Descending or Ascending Order for Posts
		array(
			'name' 		=> 'order',
			'title'		=> __('Order', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Descending', 'themify'), 'value' => 'desc', 'selected' => true),
				array('name' => __('Ascending', 'themify'), 'value' => 'asc')
			),
			'default' => 'desc'
		),
		// Criteria to Order By
		array(
			'name' 		=> 'orderby',
			'title'		=> __('Order By', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Date', 'themify'), 'value' => 'date', 'selected' => true),
				array('name' => __('Random', 'themify'), 'value' => 'rand'),
				array('name' => __('Author', 'themify'), 'value' => 'author'),
				array('name' => __('Post Title', 'themify'), 'value' => 'title'),
				array('name' => __('Comments Number', 'themify'), 'value' => 'comment_count'),
				array('name' => __('Modified Date', 'themify'), 'value' => 'modified'),
				array('name' => __('Post Slug', 'themify'), 'value' => 'name'),
				array('name' => __('Post ID', 'themify'), 'value' => 'ID')
			),
			'default' => 'date'
		),
		// Post Layout
		array(
			'name' 		=> 'layout',
			'title'		=> __('Query Post Layout', 'themify'),
			'description'	=> '',
			'type'		=> 'layout',
			'show_title' => true,
			'meta'		=> array(
				array(
					'value' => 'list-post',
					'img' => 'images/layout-icons/list-post.png',
					'selected' => true
				),
				array(
					'value' => 'grid4',
					'img'   => 'images/layout-icons/grid4.png',
					'title' => __( 'Grid 4', 'themify' )
				),
				array(
					'value' => 'grid3',
					'img'   => 'images/layout-icons/grid3.png',
					'title' => __( 'Grid 3', 'themify' )
				),
				array(
					'value' => 'grid2',
					'img'   => 'images/layout-icons/grid2.png',
					'title' => __( 'Grid 2', 'themify' )
				),
				array(
					'value' => 'list-large-image',
					'img'   => 'images/layout-icons/list-large-image.png',
					'title' => __( 'List Large Image', 'themify' )
				),
				array(
					'value' => 'list-thumb-image',
					'img'   => 'images/layout-icons/list-thumb-image.png',
					'title' => __( 'List Thumb Image', 'themify' )
				),
				array(
					'value' => 'grid2-thumb',
					'img'   => 'images/layout-icons/grid2-thumb.png',
					'title' => __( 'Grid 2 Thumb', 'themify' )
				),
                                array(  'value' => 'auto_tiles', 
                                        'img' => 'images/layout-icons/auto-tiles.png', 
                                        'title' => __('Auto Tiles', 'themify')
                                    )
			),
			'default' => 'list-post',
		),
		// Post Content Layout
		array(
			'name' 		=> 'post_content_layout',
			'title'		=> __( 'Post Content Layout', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'overlay',
					'name' => __( 'Overlay', 'themify' ),
				),
				array(
					'value' => 'polaroid',
					'name' => __( 'Polaroid', 'themify' ),
				),
				array(
					'value' => 'boxed',
					'name' => __( 'Boxed', 'themify' ),
				)
			)
		),
		// Post Masonry
		array(
			'name' 		=> 'disable_masonry',
			'title'		=> __( 'Post Masonry', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'yes',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'no',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Post Gutter
		array(
			'name' 		=> 'post_gutter',
			'title'		=> __( 'Post Gutter', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'no-gutter',
					'name' => __( 'No gutter', 'themify' ),
				)
			)
		),
		// Infinite Scroll
		array(
			'name' 		=> 'more_posts',
			'title'		=> __( 'Infinite Scroll', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'infinite',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'pagination',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Posts Per Page
		array(
			'name' 		=> 'posts_per_page',
			'title'		=> __('Posts Per Page', 'themify'),
			'description'	=> '',
			'type'		=> 'textbox',
			'meta'		=> array('size' => 'small')
		),
		// Display Content
		array(
			'name' 		=> 'display_content',
			'title'		=> __('Display Content', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array( 'name' => __('Full Content', 'themify'), 'value' => 'content' ),
				array( 'name' => __('Excerpt', 'themify'), 'value' => 'excerpt', 'selected' => true ),
				array( 'name' => __('None', 'themify'), 'value' => 'none' )
			),
			'default' => 'excerpt',
		),
		// Post filter 
		array(
			'name' => 'post_filter',
			'type' => 'dropdown',
			'title' => __('Post Filter', 'themify'),
			'meta' =>array(
				array(
				'value' => '',
				'name' => '',
				),
				array(
				'value' => 'yes',
				'name' => 'Yes',
				),
				array(
				'value' => 'no',
				'name' => 'No',
				),
			)
		),
		// Featured Image Size
		array(
			'name'	=>	'feature_size_page',
			'title'	=>	__('Image Size', 'themify'),
			'description' => sprintf(__('Image sizes can be set at <a href="%s">Media Settings</a> and <a href="%s" target="_blank">Regenerated</a>', 'themify'), 'options-media.php', 'https://wordpress.org/plugins/regenerate-thumbnails/'),
			'type'		 =>	'featimgdropdown',
			'display_callback' => 'themify_is_image_script_disabled'
		),
		// Multi field: Image Dimension
		themify_image_dimensions_field(),
		// Hide Title
		array(
			'name' 		=> 'hide_title',
			'title'		=> __('Hide Post Title', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Unlink Post Title
		array(
			'name' 		=> 'unlink_title',
			'title' 		=> __('Unlink Post Title', 'themify'),
			'description' => __('Unlink post title (it will display the post title without link)', 'themify'),
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Post Date
		array(
			'name' 		=> 'hide_date',
			'title'		=> __('Hide Post Date', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Post Meta
		themify_multi_meta_field(),
		// Hide Post Image
		array(
			'name' 		=> 'hide_image',
			'title' 		=> __('Hide Featured Image', 'themify'),
			'description' => '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Unlink Post Image
		array(
			'name' 		=> 'unlink_image',
			'title' 		=> __('Unlink Featured Image', 'themify'),
			'description' => __('Display the Featured Image without link', 'themify'),
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Page Navigation Visibility
		array(
			'name' 		=> 'hide_navigation',
			'title'		=> __('Hide Page Navigation', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
	);
}

/**
 * Portfolio Meta Box Options
 * @param array $args
 * @return array
 * @since 1.0.7
 */
function themify_theme_query_portfolio_meta_box($args = array()){
	extract( $args );
	return array(
		// Query Category
		array(
			'name' 		=> 'portfolio_query_category',
			'title'		=> __('Portfolio Category', 'themify'),
			'description'	=> __('Select a portfolio category or enter multiple portfolio category IDs (eg. 2,5,6). Enter 0 to display all portfolio categories.', 'themify'),
			'type'		=> 'query_category',
			'meta'		=> array('taxonomy' => 'portfolio-category')
		),
		// Descending or Ascending Order for Portfolios
		array(
			'name' 		=> 'portfolio_order',
			'title'		=> __('Order', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Descending', 'themify'), 'value' => 'desc', 'selected' => true),
				array('name' => __('Ascending', 'themify'), 'value' => 'asc')
			),
			'default' => 'desc',
		),
		// Criteria to Order By
		array(
			'name' 		=> 'portfolio_orderby',
			'title'		=> __('Order By', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('name' => __('Date', 'themify'), 'value' => 'date', 'selected' => true),
				array('name' => __('Random', 'themify'), 'value' => 'rand'),
				array('name' => __('Author', 'themify'), 'value' => 'author'),
				array('name' => __('Post Title', 'themify'), 'value' => 'title'),
				array('name' => __('Comments Number', 'themify'), 'value' => 'comment_count'),
				array('name' => __('Modified Date', 'themify'), 'value' => 'modified'),
				array('name' => __('Post Slug', 'themify'), 'value' => 'name'),
				array('name' => __('Post ID', 'themify'), 'value' => 'ID')
			),
			'default' => 'date',
		),
		// Post Layout
		array(
			'name' 		=> 'portfolio_layout',
			'title'		=> __('Portfolio Layout', 'themify'),
			'description'	=> '',
			'type'		=> 'layout',
			'show_title' => true,
			'meta'		=> array(
				array(
					'value' => 'list-post',
					'img' => 'images/layout-icons/list-post.png',
					'selected' => true
				),
				array(
					'value' => 'grid4',
					'img' => 'images/layout-icons/grid4.png',
					'title' => __( 'Grid 4', 'themify' )
				),
				array(
					'value' => 'grid3',
					'img' => 'images/layout-icons/grid3.png',
					'title' => __( 'Grid 3', 'themify' )
				),
				array(
					'value' => 'grid2',
					'img' => 'images/layout-icons/grid2.png',
					'title' => __( 'Grid 2', 'themify' )
				),
				array(
					'value' => 'slider',
					'img'   => 'images/layout-icons/slider-default.png',
					'title' => __( 'Slider', 'themify' )
				),
				array( 'value' => 'auto_tiles', 
					'img' => 'images/layout-icons/auto-tiles.png', 
					'title' => __('Auto Tiles', 'themify')
				)
			),
			'default' => 'list-post',
		),
		// Post Content Layout
		array(
			'name' 		=> 'portfolio_content_layout',
			'title'		=> __( 'Post Content Layout', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'overlay',
					'name' => __( 'Overlay', 'themify' ),
				),
				array(
					'value' => 'polaroid',
					'name' => __( 'Polaroid', 'themify' ),
				),
				array(
					'value' => 'boxed',
					'name' => __( 'Boxed', 'themify' ),
				),
				
			)
		),
		// Post Masonry
		array(
			'name' 		=> 'portfolio_disable_masonry',
			'title'		=> __( 'Post Masonry', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'yes',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'no',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Post Gutter
		array(
			'name' 		=> 'portfolio_post_gutter',
			'title'		=> __( 'Post Gutter', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'no-gutter',
					'name' => __( 'No gutter', 'themify' ),
				)
			)
		),
		// Infinite Scroll
		array(
			'name' 		=> 'portfolio_more_posts',
			'title'		=> __( 'Infinite Scroll', 'themify' ),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => '',
					'name' => '',
					'selected' => true,
				),
				array(
					'value' => 'infinite',
					'name' => __( 'Enable', 'themify' ),
				),
				array(
					'value' => 'pagination',
					'name' => __( 'Disable', 'themify' ),
				)
			)
		),
		// Posts Per Page
		array(
			  'name' 		=> 'portfolio_posts_per_page',
			  'title'		=> __('Portfolios Per Page', 'themify'),
			  'description'	=> '',
			  'type'		=> 'textbox',
			  'meta'		=> array('size' => 'small')
			),

		// Display Content
		array(
			'name' 		=> 'portfolio_display_content',
			'title'		=> __('Display Content', 'themify'),
			'description'	=> '',
			'type'		=> 'dropdown',
			'meta'		=> array(
				array( 'name' => __('Full Content', 'themify'), 'value' => 'content' ),
				array( 'name' => __('Excerpt', 'themify'), 'value' => 'excerpt', 'selected' => true ),
				array( 'name' => __('None', 'themify'), 'value' => 'none' )
			),
			'default' => 'excerpt',
		),
		// Post filter 
		array(
			'name' => 'portfolio_post_filter',
			'type' => 'dropdown',
			'title' => __('Post Filter', 'themify'),
			'meta' =>array(
				array(
				'value' => '',
				'name' => '',
				),
				array(
				'value' => 'yes',
				'name' => 'Yes',
				),
				array(
				'value' => 'no',
				'name' => 'No',
				),
			)
		),
		// Featured Image Size
		array(
			'name'	=>	'portfolio_feature_size_page',
			'title'	=>	__('Image Size', 'themify'),
			'description' => __('Image sizes can be set at <a href="options-media.php">Media Settings</a> and <a href="https://wordpress.org/plugins/regenerate-thumbnails/" target="_blank">Regenerated</a>', 'themify'),
			'type'		 =>	'featimgdropdown',
			'display_callback' => 'themify_is_image_script_disabled'
			),

		// Multi field: Image Dimension
		array(
			'type' => 'multi',
			'name' => '_portfolio_image_dimensions',
			'title' => __('Image Dimensions', 'themify'),
			'meta' => array(
				'fields' => array(
					// Image Width
					array(
					  'name' 		=> 'portfolio_image_width',
					  'label' => __('width', 'themify'),
					  'description' => '',
					  'type' 		=> 'textbox',
					  'meta'		=> array('size'=>'small')
					),
					// Image Height
					array(
					  'name' 		=> 'portfolio_image_height',
					  'label' => __('height', 'themify'),
					  'description' => '',
					  'type' 		=> 'textbox',
					  'meta'		=> array('size'=>'small')
					),
				),
				'description' => __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify'),
				'before' => '',
				'after' => '',
				'separator' => ''
			)
		),
		// Hide Title
		array(
			  'name' 		=> 'portfolio_hide_title',
			  'title'		=> __('Hide Portfolio Title', 'themify'),
			  'description'	=> '',
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Unlink Post Title
		array(
			  'name' 		=> 'portfolio_unlink_title',
			  'title' 		=> __('Unlink Portfolio Title', 'themify'),
			  'description' => __('Unlink portfolio title (it will display the post title without link)', 'themify'),
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Post Meta
		array(
			'name' 		=> 'portfolio_hide_meta_all',
			'title' 	=> __('Hide Portfolio Meta', 'themify'),
			'description' => '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Hide Post Image
		array(
			  'name' 		=> 'portfolio_hide_image',
			  'title' 		=> __('Hide Featured Image', 'themify'),
			  'description' => '',
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Unlink Post Image
		array(
			  'name' 		=> 'portfolio_unlink_image',
			  'title' 		=> __('Unlink Featured Image', 'themify'),
			  'description' => __('Display the Featured Image Without Link', 'themify'),
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		),
		// Page Navigation Visibility
		array(
			  'name' 		=> 'portfolio_hide_navigation',
			  'title'		=> __('Hide Page Navigation', 'themify'),
			  'description'	=> '',
			  'type' 		=> 'dropdown',
			  'meta'		=> array(
				array('value' => 'default', 'name' => '', 'selected' => true),
				array('value' => 'yes', 'name' => __('Yes', 'themify')),
				array('value' => 'no',	'name' => __('No', 'themify'))
			),
			'default' => 'default',
		)
	);
}

/**
 * Theme Appearance Tab for Themify Custom Panel
 *
 * @since 1.0.0
 *
 * @param array $args
 *
 * @return array
 */
function themify_theme_page_theme_design_meta_box( $args = array() ) {
	return array(
		// Notice
		array(
			'name' => '_theme_appearance_notice',
			'title' => '',
			'description' => '',
			'type' => 'separator',
			'meta' => array(
				'html' => '<div class="themify-info-link">' . __( 'The settings here apply on this page only. Leave everything as default will use the site-wide Theme Appearance from the Themify panel > Settings > Theme Settings.', 'themify' ) . '</div>'
			),
		),
		// Background Color
		array(
			'name'        => 'body_background_color',
			'title'       => __( 'Body Background', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'format'      => 'rgba',
		),
		// Background image
		array(
			'name'        => 'body_background_image',
			'title'       => '',
			'type'        => 'image',
			'description' => '',
			'meta'        => array(),
			'before'      => '',
			'after'       => '',
		),
		// Background repeat
		array(
			'name'        => 'body_background_repeat',
			'title'       => '',
			'description' => __( 'Background Repeat', 'themify' ),
			'type'        => 'dropdown',
			'meta'        => array(
				array(
					'value' => '',
					'name'  => ''
				),
				array(
					'value' => 'fullcover',
					'name'  => __( 'Fullcover', 'themify' )
				),
				array(
					'value' => 'repeat',
					'name'  => __( 'Repeat', 'themify' )
				),
				array(
					'value' => 'no-repeat',
					'name'  => __( 'No Repeat', 'themify' )
				),
				array(
					'value' => 'repeat-x',
					'name'  => __( 'Repeat horizontally', 'themify' )
				),
				array(
					'value' => 'repeat-y',
					'name'  => __( 'Repeat vertically', 'themify' )
				),
			),
		),
		// Accent Color Mode, Presets or Custom
		array(
			'name'          => 'color_scheme_mode',
			'title'         => __( 'Accent Color', 'themify' ),
			'description'   => '',
			'type'          => 'radio',
			'show_title'    => true,
			'meta'          => array(
				array(
					'value'    => 'color-presets',
					'name'     => __( 'Presets', 'themify' ),
					'selected' => true
				),
				array(
					'value' => 'color-custom',
					'name' => __( 'Custom', 'themify' ),
				),
			),
			'default' => 'color-presets',
			'enable_toggle' => true,
		),
		// Theme Color
		array(
			'name'        => 'color_design',
			'title'       => '',
			'description' => '',
			'type'        => 'layout',
			'show_title'  => true,
			'meta'        => $args['color_design_options'],
			'toggle'	=> 'color-presets-toggle',
			'default' => 'default',
		),
		// Accent Color
		array(
			'name'        => 'scheme_color',
			'title'       => '',
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'after'		  => __( 'Accent Font Color', 'themify' ),
			'toggle'	=> 'color-custom-toggle',
			'format'      => 'rgba',
		),
		array(
			'name'        => 'scheme_link',
			'title'       => '',
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'after'		  => __( 'Accent Link Color', 'themify' ),
			'toggle'	=> 'color-custom-toggle',
			'format'      => 'rgba',
		),
		array(
			'name'        => 'scheme_background',
			'title'       => '',
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'after'		  => __( 'Accent Background Color', 'themify' ),
			'toggle'	=> 'color-custom-toggle',
			'format'      => 'rgba',
		),

		// Typography Mode, Presets or Custom
		array(
			'name'          => 'typography_mode',
			'title'         => __( 'Typography', 'themify' ),
			'description'   => '',
			'type'          => 'radio',
			'show_title'    => true,
			'meta'          => array(
				array(
					'value'    => 'typography-presets',
					'name'     => __( 'Presets', 'themify' ),
					'selected' => true
				),
				array(
					'value' => 'typography-custom',
					'name' => __( 'Custom', 'themify' ),
				),
			),
			'default' => 'typography-presets',
			'enable_toggle' => true,
		),
		// Typography
		array(
			'name'        => 'font_design',
			'title'       => '',
			'description' => '',
			'type'        => 'layout',
			'show_title'  => true,
			'meta'        => $args['font_design_options'],
			'toggle'	  => 'typography-presets-toggle',
			'default' => 'default',
		),
		// Body font
		array(
			'name'        => 'body_font',
			'title'       => '',
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array_merge( themify_get_web_safe_font_list(), themify_get_google_web_fonts_list() ),
			'after'		  => ' ' . __( 'Body Font', 'themify' ),
			'toggle'	  => 'typography-custom-toggle',
			'default' => 'default',
		),
		// Body wrap text color
		array(
			'name'        => 'body_text_color',
			'title'       => '',
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'after'		  => __( 'Body Font Color', 'themify' ),
			'toggle'	  => 'typography-custom-toggle',
			'format'      => 'rgba',
		),
		// Body wrap link color
		array(
			'name'        => 'body_link_color',
			'title'       => '',
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'after'		  => __( 'Body Link Color', 'themify' ),
			'toggle'	  => 'typography-custom-toggle',
			'format'      => 'rgba',
		),
		// Heading font
		array(
			'name'        => 'heading_font',
			'title'       => '',
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array_merge( themify_get_web_safe_font_list(), themify_get_google_web_fonts_list() ),
			'after'		  => ' ' . __( 'Heading (h1 to h6)', 'themify' ),
			'toggle'	  => 'typography-custom-toggle',
			'default' => 'default',
		),
		// Heading color
		array(
			'name'        => 'heading_color',
			'title'       => '',
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'after'		  => __( 'Heading Font Color', 'themify' ),
			'toggle'	  => 'typography-custom-toggle',
			'format'      => 'rgba',
		),

		// Header Separator
		array(
			'name' => 'header_separator',
			'type' => 'separator',
			'meta' => array('html'=>'<hr />')
		),
		// Header Design
		array(
			'name'        => 'header_design',
			'title'       => __( 'Header Design', 'themify' ),
			'description' => '',
			'type'        => 'layout',
			'show_title'  => true,
			'meta'        => $args['header_design_options'],
			'hide'		  => 'none header-leftpane header-minbar boxed-content header-rightpane',
			'default' => 'default',
		),
		// Sticky Header
		array(
			'name'        => 'fixed_header',
			'title'       => __( 'Sticky Header', 'themify' ),
			'description' => '',
			'type'		  => 'radio',
			'meta'		  => themify_ternary_options(),
			'class'		  => 'hide-if none header-leftpane header-minbar boxed-content header-rightpane header-slide-out',
			'default' => 'default',
		),
		// Shrinking Logo
		array(
			'name'        => 'shrinking_logo',
			'title'       => __( 'Shrinking logo', 'themify' ),
			'description' => '',
			'type'		  => 'radio',
			'meta'		  => themify_ternary_options(),
			'class'		  => 'hide-if none header-leftpane header-minbar boxed-content header-rightpane header-slide-out',
			'default' => 'default',
		),
		// Full Height Header
		array(
			'name'        => 'full_height_header',
			'title'       => __( 'Full Height Header', 'themify' ),
			'description' => __( 'Full height will display the container in 100% viewport height', 'themify' ),
			'type'		  => 'radio',
			'meta'		  => themify_ternary_options(),
			'class'		  => 'hide-if default none header-horizontal header-leftpane header-minbar boxed-content header-top-bar header-slide-out boxed-layout boxed-compact header-rightpane header-stripe header-menu-split header-overlay',
			'default' => 'default',
		),
		// Header Elements
		array(
			'name' 	=> '_multi_header_elements',	
			'title' => __('Header Elements', 'themify'), 	
			'description' => '',	
			'type' 	=> 'multi',
			'class' => 'hide-if none',		
			'meta'	=> array(
				'fields' => array(
					// Show Site Logo
					array(
						'name' 	=> 'exclude_site_logo',
						'description' => '',
						'title' => __( 'Site Logo', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none header-menu-split',
						'after' => '<div class="clear"></div>',
					),
					// Show Site Tagline
					array(
						'name' 	=> 'exclude_site_tagline',
						'description' => '',
						'title' => __( 'Site Tagline', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Search Form
					array(
						'name' 	=> 'exclude_search_form',
						'description' => '',
						'title' => __( 'Search Form', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show RSS Link
					array(
						'name' 	=> 'exclude_rss',
						'description' => '',
						'title' => __( 'RSS Link', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Header Widgets
					array(
						'name' 	=> 'exclude_header_widgets',
						'description' => '',
						'title' => __( 'Header Widgets', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Social Widget
					array(
						'name' 	=> 'exclude_social_widget',
						'description' => '',
						'title' => __( 'Social Widget', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Menu Navigation
					array(
						'name' 	=> 'exclude_menu_navigation',
						'description' => '',
						'title' => __( 'Menu Navigation', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none header-menu-split',
						'after' => '<div class="clear"></div>',
					),
					array(
						'name' 	=> 'exclude_cart_icon',
						'description' => '',
						'title' => __( 'Cart Icon', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => '',
						'after' => '<div class="clear"></div>',
						'display_callback' => 'themify_is_woocommerce_active'
					),
				),
				'description' => '',
				'before' => '',
				'after' => '<div class="clear"></div>',
				'separator' => ''
			)
		),
		// Header Wrap
		array(
			'name'          => 'header_wrap',
			'title'         => __( 'Header Background Type', 'themify' ),
			'description'   => '',
			'type'          => 'radio',
			'show_title'    => true,
			'meta'          => array(
				array(
					'value'    => 'solid',
					'name'     => __( 'Image Background', 'themify' ),
					'selected' => true
				),
				array(
					'value' => 'transparent',
					'name'  => __( 'Transparent Header', 'themify' ),
				),
				array(
					'value' => 'slider',
					'name' => __( 'Image Slider', 'themify' ),
				),
				array(
					'value' => 'video',
					'name' => __( 'Video Background', 'themify' ),
				),
				array(
					'value' => 'colors',
					'name' => __( 'Animating Colors', 'themify' ),
				),
			),
			'enable_toggle' => true,
			'class'     => 'hide-if none clear',
			'default' => 'solid',
		),
		// Animated Colors
		array(
			'name' 		=> '_animated_colors',
			'title'		=> __('Animating Colors', 'themify'),
			'description' => sprintf( __('Animating Colors can be configured at <a href="%s">Themify > Settings > Theme Settings</a>', 'themify'), esc_url( add_query_arg( 'page', 'themify', admin_url( 'admin.php' ) ) ) ),
			'type' 		=> 'post_id_info',
			'toggle'	=> 'colors-toggle',
		),
		// Select Background Gallery
		array(
			'name' 		=> 'background_gallery',
			'title'		=> __('Header Slider', 'themify'),
			'description' => '',
			'type' 		=> 'gallery_shortcode',
			'toggle'	=> 'slider-toggle',
			'class'     => 'hide-if none',
		),
		// Background Mode
		array(
			'name'		=> 'background_mode',
			'title'		=> __('Slider Mode', 'themify'),
			'type'		=> 'radio',
			'meta'		=> array(
				array('value' => 'fullcover', 'selected' => $args['background_mode']=='fullcover', 'name' => __('Full Cover', 'themify')),
				array('value' => 'best-fit', 'selected' => $args['background_mode']=='best-fit', 'name' => __('Best Fit', 'themify'))
			),
			'enable_toggle'	=> true,
			'toggle'	=> 'enable_toggle_child slider-toggle',
			'class'     => 'hide-if none',
			'default' => 'fullcover',
		),
		// Background Position
		array(
			'name'		=> 'background_position',
			'title'		=> __('Slider Image Position', 'themify'),
			'type'		=> 'dropdown',
			'meta'		=> array(
				array('value' => '', 'name' => '', 'selected' => true),
				array('value' => 'left-top', 'name' => __('Left Top', 'themify')),
				array('value' => 'left-center', 'name' => __('Left Center', 'themify')),
				array('value' => 'left-bottom', 'name' => __('Left Bottom', 'themify')),
				array('value' => 'right-top', 'name' => __('Right Top', 'themify')),
				array('value' => 'right-center', 'name' => __('Right Center', 'themify')),
				array('value' => 'right-bottom', 'name' => __('Right Bottom', 'themify')),
				array('value' => 'center-top', 'name' => __('Center Top', 'themify')),
				array('value' => 'center-center', 'name' => __('Center Center', 'themify')),
				array('value' => 'center-bottom', 'name' => __('Center Bottom', 'themify'))
			),
			'toggle'	=> 'slider-toggle fullcover-toggle',
			'class'     => 'hide-if none',
		),
		array(
			'type' => 'multi',
			'name' => '_video_select',
			'title' => __('Header Video', 'themify'),
			'meta' => array(
				'fields' => array(
					// Video File
					array(
						'name' 		=> 'video_file',
						'title' 	=> __('Video File', 'themify'),
						'description' => '',
						'type' 		=> 'video',
						'meta'		=> array(),
					),
				),
				'description' => __('Video format: mp4. Note: video background does not play on mobile, background image will be used as fallback.', 'themify'),
				'before' => '',
				'after' => '',
				'separator' => ''
			),
			'toggle'	=> 'video-toggle',
			'class'     => 'hide-if none',
		),
		// Background Color
		array(
			'name'        => 'background_color',
			'title'       => __( 'Header Background', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'toggle'      => array( 'solid-toggle', 'slider-toggle', 'video-toggle' ),
			'class'     => 'hide-if none',
			'format'      => 'rgba',
		),
		// Background image
		array(
			'name'        => 'background_image',
			'title'       => '',
			'type'        => 'image',
			'description' => '',
			'meta'        => array(),
			'before'      => '',
			'after'       => '',
			'toggle'      => array( 'solid-toggle', 'video-toggle' ),
			'class'     => 'hide-if none',
		),
		// Background repeat
		array(
			'name'        => 'background_repeat',
			'title'       => '',
			'description' => __( 'Background Image Mode', 'themify' ),
			'type'        => 'dropdown',
			'meta'        => array(
				array(
					'value' => '',
					'name'  => ''
				),
				array(
					'value' => 'fullcover',
					'name'  => __( 'Fullcover', 'themify' )
				),
				array(
					'value' => 'repeat',
					'name'  => __( 'Repeat all', 'themify' )
				),
				array(
					'value' => 'no-repeat',
					'name'  => __( 'No repeat', 'themify' )
				),
				array(
					'value' => 'repeat-x',
					'name'  => __( 'Repeat horizontally', 'themify' )
				),
				array(
					'value' => 'repeat-y',
					'name'  => __( 'Repeat vertically', 'themify' )
				),
			),
			'toggle'      => array( 'solid-toggle', 'video-toggle' ),
			'class'     => 'hide-if none',
		),
		// Header Slider Auto
		array(
			'name' 		=> 'background_auto',
			'title'		=> __('Autoplay', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'yes', 'name' => __('Yes', 'themify'), 'selected' => true),
				array('value' => 'no', 'name' => __('No', 'themify'))
			),
			'toggle'	=> 'slider-toggle',
			'default' => 'yes',
		),
		// Header Slider Auto Timeout
		array(
			'name' 		=> 'background_autotimeout',
			'title'		=> __('Autoplay Timeout', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 1, 'name' => __('1 Secs', 'themify')),
				array('value' => 2, 'name' => __('2 Secs', 'themify')),
				array('value' => 3, 'name' => __('3 Secs', 'themify')),
				array('value' => 4, 'name' => __('4 Secs', 'themify')),
				array('value' => 5, 'name' => __('5 Secs', 'themify'), 'selected' => true),
				array('value' => 6, 'name' => __('6 Secs', 'themify')),
				array('value' => 7, 'name' => __('7 Secs', 'themify')),
				array('value' => 8, 'name' => __('8 Secs', 'themify')),
				array('value' => 9, 'name' => __('9 Secs', 'themify')),
				array('value' => 10, 'name' => __('10 Secs', 'themify'))
			),
			'toggle'	=> 'slider-toggle',
			'default' => 5,
		),
		// Header Slider Transition Speed
		array(
			'name' 		=> 'background_speed',
			'title'		=> __('Transition Speed', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 1500, 'name' => __('Slow', 'themify')),
				array('value' => 500, 'name' => __('Normal', 'themify'), 'selected' => true),
				array('value' => 300, 'name' => __('Fast', 'themify'))
			),
			'toggle'	=> 'slider-toggle',
			'default' => 500,
		),
		// Header Slider Wrap
		array(
			'name' 		=> 'background_wrap',
			'title'		=> __('Wrap Slides', 'themify'),
			'description'	=> '',
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array('value' => 'yes', 'name' => __('Yes', 'themify'), 'selected' => true),
				array('value' => 'no', 'name' => __('No', 'themify'))
			),
			'toggle'	=> 'slider-toggle',
			'default' => 'yes',
		),
		// Hide Slider Controlls
		array(
			'name'        => 'header_hide_controlls',
			'title'       => __( 'Hide Slider Controlls', 'themify' ),
			'description' => '',
			'type'        => 'checkbox',
			'toggle'	=> 'slider-toggle'
		),
		// Header wrap text color
		array(
			'name'        => 'headerwrap_text_color',
			'title'       => __( 'Header Text Color', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'class'     => 'hide-if none',
			'format'      => 'rgba',
		),
		// Header wrap link color
		array(
			'name'        => 'headerwrap_link_color',
			'title'       => __( 'Header Link Color', 'themify' ),
			'description' => '',
			'type'        => 'color',
			'meta'        => array( 'default' => null ),
			'class'     => 'hide-if none',
			'format'      => 'rgba',
		),
		// Footer Separator
		array(
			'name' => 'footer_separator',
			'type' => 'separator',
			'meta' => array('html'=>'<hr />')
		),
		// Footer Design
		array(
			'name'        => 'footer_design',
			'title'       => __( 'Footer Design', 'themify' ),
			'description' => '',
			'type'        => 'layout',
			'show_title'  => true,
			'meta'        => $args['footer_design_options'],
			'hide'		  => 'none',
			'default' => 'default',
		),  
		// Footer Elements
		array(
			'name' 	=> '_multi_footer_elements',	
			'title' => __('Footer Elements', 'themify'), 	
			'description' => '',	
			'type' 	=> 'multi',
			'class' => 'hide-if none',		
			'meta'	=> array(
				'fields' => array(
					// Show Site Logo
					array(
						'name' 	=> 'exclude_footer_site_logo',
						'description' => '',
						'title' => __( 'Site Logo', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Footer Widgets
					array(
						'name' 	=> 'exclude_footer_widgets',
						'description' => '',
						'title' => __( 'Footer Widgets', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Menu Navigation
					array(
						'name' 	=> 'exclude_footer_menu_navigation',
						'description' => '',
						'title' => __( 'Menu Navigation', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Texts
					array(
						'name' 	=> 'exclude_footer_texts',
						'description' => '',
						'title' => __( 'Footer Text', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
					// Show Back to Top
					array(
						'name' 	=> 'exclude_footer_back',
						'description' => '',
						'title' => __( 'Back to Top Arrow', 'themify' ),
						'type' 	=> 'dropdownbutton',
						'states' => themify_ternary_states( array(
							'icon_no' => THEMIFY_URI . '/img/ddbtn-check.png',
							'icon_yes' => THEMIFY_URI . '/img/ddbtn-cross.png', 
							) ),
						'class' => 'hide-if none',
						'after' => '<div class="clear"></div>',
					),
				),
				'description' => '',
				'before' => '',
				'after' => '<div class="clear"></div>',
				'separator' => ''
			)
		),
		
		// Footer widget position
		array(
			'name'        => 'footer_widget_position',
			'title'       => __( 'Footer Widgets Position', 'themify' ),
			'class' => 'hide-if none',
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array(
					'value' => '',
					'name'  => __( 'Default', 'themify' )
				),
				array(
					'value' => 'bottom',
					'name'  => __( 'After Footer Text', 'themify' )
				),
				array(
					'value' => 'top',
					'name'  => __( 'Before Footer Text', 'themify' )
				)
			),
		),
		// Image Filter Separator
		array(
			'name' => 'image_filter_separator',
			'type' => 'separator',
			'meta' => array('html'=>'<hr />')
		),
		// Image Filter
		array(
			'name'        => 'imagefilter_options',
			'title'       => __( 'Image Filter', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array( 'name' => '', 'value' => 'initial' ),
				array( 'name' => __( 'None', 'themify' ), 'value' => 'none' ),
				array( 'name' => __( 'Grayscale', 'themify' ), 'value' => 'grayscale' ),
				array( 'name' => __( 'Sepia', 'themify' ), 'value' => 'sepia' ),
				array( 'name' => __( 'Blur', 'themify' ), 'value' => 'blur' ),
			),
			'default' => 'initial',
		),
		// Image Hover Filter
		array(
			'name'        => 'imagefilter_options_hover',
			'title'       => __( 'Image Hover Filter', 'themify' ),
			'description' => '',
			'type'        => 'dropdown',
			'meta'        => array(
				array( 'name' => '', 'value' => 'initial' ),
				array( 'name' => __( 'None', 'themify' ), 'value' => 'none' ),
				array( 'name' => __( 'Grayscale', 'themify' ), 'value' => 'grayscale' ),
				array( 'name' => __( 'Sepia', 'themify' ), 'value' => 'sepia' ),
				array( 'name' => __( 'Blur', 'themify' ), 'value' => 'blur' ),
			),
			'default' => 'initial',
		),
		// Image Filter Apply To
		array(
			'name'        => 'imagefilter_applyto',
			'title'       => __( 'Apply Filter To', 'themify' ),
			'description' => sprintf( __( 'Image filters can be set site-wide at <a href="%s" target="_blank">Themify > Settings > Theme Settings</a>', 'themify' ), admin_url( 'admin.php?page=themify#setting-theme_settings' ) ),
			'type'        => 'radio',
			'meta'        => array(
				array( 'value' => 'initial', 'name' => __( 'Theme Default', 'themify' )),
				array( 'value' => 'all', 'name' => __( 'All Images', 'themify' ) ),
				array( 'value' => 'featured-only', 'name' => __( 'Featured Images Only', 'themify' ), )
			),
			'default' => 'initial',
		),

	);
}

/**
 * Default Page Layout Module
 * @param array $data Theme settings data
 * @return string Markup for module.
 * @since 1.0.0
 */
function themify_default_page_layout($data = array()){
	$data = themify_get_data();

	/**
	 * Theme Settings Option Key Prefix
	 * @var string
	 */
	$prefix = 'setting-default_page_';

	/**
	 * Sidebar placement options
	 * @var array
	 */
	$sidebar_location_options = array(
		array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'selected' => true, 'title' => __('Sidebar Right', 'themify')),
		array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
		array('value' => 'sidebar2', 'img' => 'images/layout-icons/sidebar2.png', 'title' => __('Left and Right', 'themify')),
		array('value' => 'sidebar2 content-left', 	'img' => 'images/layout-icons/sidebar2-content-left.png', 'title' => __('2 Right Sidebars', 'themify')),
		array('value' => 'sidebar2 content-right', 	'img' => 'images/layout-icons/sidebar2-content-right.png', 'title' => __('2 Left Sidebars', 'themify')),
		array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'title' => __('No Sidebar', 'themify'))
	);

	/**
	 * Tertiary options <blank>|yes|no
	 * @var array
	 */
	$default_options = array(
		array('name' => '', 'value' => ''),
		array('name' => __('Yes', 'themify'), 'value' => 'yes'),
		array('name' => __('No', 'themify'), 'value' => 'no')
	);

	/**
	 * Module markup
	 * @var string
	 */
	$output = '';

	/**
	 * Page sidebar placement
	 */
	$output .= '<p>
					<span class="label">' . __('Page Sidebar Option', 'themify') . '</span>';
	$val = isset( $data[$prefix.'layout'] ) ? $data[$prefix.'layout'] : '';
	foreach ( $sidebar_location_options as $option ) {
		if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
			$val = $option['value'];
		}
		if ( $val == $option['value'] ) {
			$class = "selected";
		} else {
			$class = "";
		}
		$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
	}
	$output .= '<input type="hidden" name="'.$prefix.'layout" class="val" value="'.$val.'" /></p>';

	/**
	 * Hide Title in All Pages
	 */
	$output .= '<p>
					<span class="label">' . __('Hide Title in All Pages', 'themify') . '</span>
					<select name="setting-hide_page_title">'.
						themify_options_module($default_options, 'setting-hide_page_title') . '
					</select>
				</p>';

	/**
	 * Hide Feauted images in All Pages
	 */
	$output .= '<p>
					<span class="label">' . __('Hide Featured Image', 'themify') . '</span>
					<select name="setting-hide_page_image">' .
						themify_options_module($default_options, 'setting-hide_page_image') . '
					</select>
				</p>';

	/**
	 * Featured Image dimensions
	 */
	$output .= '<p>
				<span class="label">' . __('Image Size', 'themify') . '</span>
				<input type="text" class="width2" name="setting-page_featured_image_width" value="' . themify_get( 'setting-page_featured_image_width' ) . '" /> ' . __('width', 'themify') . ' <small>(px)</small>
				<input type="text" class="width2 show_if_enabled_img_php" name="setting-page_featured_image_height" value="' . themify_get( 'setting-page_featured_image_height' ) . '" /> <span class="show_if_enabled_img_php">' . __('height', 'themify') . ' <small>(px)</small></span>
				<br /><span class="pushlabel show_if_enabled_img_php"><small>' . __('Enter height = 0 to disable vertical cropping with img.php enabled', 'themify') . '</small></span>
			</p>';

	/**
	 * Page Comments
	 */
	$pre = 'setting-comments_pages';
	$output .= '<p><span class="label">' . __('Page Comments', 'themify') . '</span><label for="'.$pre.'"><input type="checkbox" id="'.$pre.'" name="'.$pre.'" ' . checked( themify_get( $pre ), 'on', false ) . ' /> ' . __('Disable comments in all Pages', 'themify') . '</label></p>';

	return $output;
}