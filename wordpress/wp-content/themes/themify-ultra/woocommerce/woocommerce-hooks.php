<?php
/**
 * WooCommerce Custom Hook
 * woocommerce-hooks.php
 */

// include plugin functions
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/* Single product actions */
add_action( 'themify_single_product_price', 'woocommerce_template_single_price', 10);
add_action( 'template_redirect', 'themify_single_product_related_products', 12);
add_action( 'template_redirect', 'themify_hide_shop_features', 12);
add_filter( 'woocommerce_product_tabs', 'themify_single_product_reviews' );

/* Single product on lightbox actions */
add_action( 'themify_single_product_image_ajax', 'woocommerce_show_product_sale_flash', 20);
add_action( 'themify_single_product_image_ajax', 'woocommerce_show_product_images', 20);
add_action( 'themify_single_product_ajax_content', 'woocommerce_template_single_add_to_cart', 10);
if(isset($_GET['ajax']) && $_GET['ajax']) {
	add_filter('woocommerce_single_product_image_html', 'themify_product_image_ajax', 10, 2);
	add_filter('woocommerce_single_product_image_thumbnail_html', create_function('', "return '';"));
}
add_filter('woocommerce_single_product_image_html', 'themify_product_image_single', 10, 2);

/* Sorting menu */
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
add_action( 'woocommerce_before_shop_loop', 'themify_catalog_ordering', 8 );

// Remove breadcrumb for later insertion within Themify wrapper
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// Remove dock item hooks
add_action( 'init', 'themify_update_cart_action');

/* Content Wrapper */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

add_action( 'woocommerce_before_main_content', 'themify_before_shop_content', 20);
add_action( 'woocommerce_after_main_content', 'themify_after_shop_content', 20);

/* Sidebar */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
add_action('template_redirect', 'themify_woocommerce_sidebar_layout', 12);

// Show excerpt or content in product archive pages
add_action('woocommerce_after_shop_loop_item', 'themify_after_shop_loop_item');
// Set WC image sizes
add_action( 'switch_theme', 'themify_theme_delete_image_sizes_flag' );

// Add to cart link
add_filter('woocommerce_loop_add_to_cart_link', 'themify_loop_add_to_cart_link', 10, 3);
// No product title in product archive pages
add_filter('the_title', 'themify_no_product_title');
// No product price in product archive pages
add_filter('woocommerce_get_price_html', 'themify_no_price');
// Set number of products shown in product archive pages
add_filter('loop_shop_per_page', 'themify_products_per_page');
// Alter or remove success message after adding to cart with ajax.
$cart_message_hook = 'wc_add_to_cart_message';
$cart_message_hook .= version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' ) ? '_html' : '';
add_filter( $cart_message_hook, 'themify_theme_wc_add_to_cart_message' );

// Hide Add to Cart Button
if( themify_get('setting-product_archive_hide_cart_button') == 'yes' ) {
	add_filter( 'woocommerce_loop_add_to_cart_link', '__return_false' );
}

/**
 * Fragments
 * Adding cart total and shopdock markup to the fragments
 */
$cart_fragments_hook = version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' )
	? 'woocommerce_add_to_cart_fragments' : 'add_to_cart_fragments';
add_filter( $cart_fragments_hook, 'themify_theme_add_to_cart_fragments' );

/**
 * Theme delete cart hook
 * Note: for Add to cart using default WC function
 */
add_action( 'wp_ajax_theme_delete_cart', 'themify_theme_woocommerce_delete_cart' );
add_action( 'wp_ajax_nopriv_theme_delete_cart', 'themify_theme_woocommerce_delete_cart' );

/**
 * Theme adding cart hook
 * Adding cart ajax on single product page
 */
add_action( 'wp_ajax_theme_add_to_cart', 'themify_theme_woocommerce_add_to_cart' );
add_action( 'wp_ajax_nopriv_theme_add_to_cart', 'themify_theme_woocommerce_add_to_cart' );

/**
 * WC Plugins compliance 
 */
// Dynamic Gallery Plugin
if ( is_plugin_active( 'woocommerce-dynamic-gallery/wc_dynamic_gallery_woocommerce.php' ) ) {
	remove_action( 'themify_single_product_image', 'woocommerce_show_product_images', 20);
	remove_action( 'themify_single_product_image', 'woocommerce_show_product_thumbnails', 20);
}

/**
 * Specific for infinite scroll themes
 */

if( 'infinite' == themify_get('setting-more_posts') ){
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
	remove_action( 'woocommerce_after_shop_loop', 'themify_theme_shop_pagination', 10 );
	function themify_shop_infinite_scroll() {
		global $wp_query;
		$total_pages = (int)$wp_query->max_num_pages;
		$current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
		if( $total_pages > $current_page ){
			//If it's a Query Category page, set the number of total pages
			echo '<p id="load-more"><a href="' . next_posts( $total_pages, false ) . '">' . __('Load More', 'themify') . '</a></p>';
			echo '<script type="text/javascript">var qp_max_pages = ' . $total_pages . ';</script>';
		}
	};
	add_action('woocommerce_after_shop_loop', 'themify_shop_infinite_scroll', 9 );
}