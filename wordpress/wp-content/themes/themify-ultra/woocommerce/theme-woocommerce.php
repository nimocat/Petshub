<?php
/*-----------------------------------------------------------------------------------*/
/* Any WooCommerce overrides and functions can be found here
/*-----------------------------------------------------------------------------------*/

/**
 * Hide certain shop features based on user choice
 */
function themify_hide_shop_features() {
	if(themify_check('setting-hide_shop_count')) {
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	}
}

/**
 * Display sorting bar only in shop and category pages
 * @since 1.0.0
 */
function themify_catalog_ordering(){
	if( !is_search() ){
		// Get user choice
		if(!themify_check('setting-hide_shop_sorting'))
			woocommerce_catalog_ordering();
	}
}

/**
 * Hide related products based in user choice
 */
function themify_single_product_related_products(){
	if( is_product() ) {
		if( ! themify_check('setting-related_products') ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			add_action( 'woocommerce_after_single_product_summary', 'themify_related_products_limit', 20 );
		} else {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
	}
}

/**
 * Display a specific number of related products
 * @since 1.3.1
 */
function themify_related_products_limit() {
	$related_products_limit = themify_check('setting-related_products_limit')? themify_get('setting-related_products_limit'): 3;
	woocommerce_related_products(array(
		  'posts_per_page' => $related_products_limit,
		  'columns'        => 3,
	));
}

/**
 * Hide reviews based in user choice
 * @param array $tabs Default tabs shown
 * @return array Filtered tabs
 */
function themify_single_product_reviews($tabs){
	if(is_product()) {
		if(themify_check('setting-product_reviews')) {
			unset($tabs['reviews']);
		}
	}
	return $tabs;
}

/**
 * Get sidebar layout
 */
function themify_woocommerce_sidebar_layout(){
	/** Themify Default Variables
	 *  @var object */
	global $themify;
	if(themify_is_shop()) {
		$themify->layout = themify_check('setting-shop_layout')? themify_get('setting-shop_layout'): 'sidebar1';
	}
	if(is_product()) {
		$themify->layout = themify_check('setting-single_product_layout')? themify_get('setting-single_product_layout'): 'sidebar1';
	}
	if( is_product_category() || is_product_tag() ) {
		$themify->layout = themify_check('setting-shop_archive_layout') ? themify_get('setting-shop_archive_layout') : 'sidebar1';
	}
	if('sidebar-none' == $themify->layout)
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
}

/**
 * Disables price output or not following the setting applied in shop settings panel
 * @param string $price
 * @return string
 */
function themify_no_price($price){
	if( in_the_loop() && (themify_is_shop() || is_product_category() || is_product_tag() ) && themify_get('setting-product_archive_hide_price') == 'yes' )
		return '';
	else
		return $price;
}

/**
 * Disables title output or not following the setting applied in shop settings panel
 * @param $title String
 * @return String
 */
function themify_no_product_title($title){
	if( in_the_loop() && (themify_is_shop() || is_product_category() || is_product_tag() ) && themify_get('setting-product_archive_hide_title') == 'yes' )
		return '';
	else
		return $title;
}

/**
 * Outputs product short description or full content depending on the setting.
 */
function themify_after_shop_loop_item() {
	if ('excerpt' == themify_get('setting-product_archive_show_short')) {
		the_excerpt();
	} elseif ('content' == themify_get('setting-product_archive_show_short')) {
		the_content();
	}
};

/**
 * Include post type product in WordPress' search
 * @param array
 * @return array
 * @since 1.0.0 
 */
function woocommerceframework_add_search_fragment ( $settings ) {
	$settings['add_fragment'] = '?post_type=product';
	return $settings;
}

/**
 * Set number of products shown in shop
 * @param int $products Default number of products shown
 * @return int Number of products based on user choice
 */
function themify_products_per_page($products){
	return themify_get('setting-shop_products_per_page');
}

//////////////////////////////////////////////////////////////
// Update catalog images
// Hooks:
// 		switch_theme - themify_theme_delete_image_sizes_flag
// 		wp_loaded - themify_set_wc_image_sizes
//////////////////////////////////////////////////////////////

/**
 * Delete flag option to set up new image sizes the next time
 */
function themify_theme_delete_image_sizes_flag() {
	delete_option( 'themify_set_wc_images' );
}
  
/** gets the url to remove an item from dock cart */
function themify_get_remove_url( $cart_item_key ) {
	global $woocommerce;

	$cart_page_id = version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' )
		? wc_get_page_id( 'cart' )
		: woocommerce_get_page_id( 'cart' );
		
	if ($cart_page_id)
		return apply_filters('woocommerce_get_remove_url', $woocommerce->nonce_url( 'cart', add_query_arg('update_cart', $cart_item_key, get_permalink($cart_page_id))));
}

/**
 * Remove from cart/update
 **/
function themify_update_cart_action() {
	global $woocommerce;
	
	// Update Cart
	if (isset($_GET['update_cart']) && $_GET['update_cart']  && $woocommerce->verify_nonce('cart')) :
		
		$cart_totals = $_GET['update_cart'];
		
		if (sizeof($woocommerce->cart->get_cart())>0) : 
			foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) :
				
        $update = $values['quantity'] - 1;
        
				if ($cart_totals == $cart_item_key) 
          $woocommerce->cart->set_quantity( $cart_item_key, $update);
				
			endforeach;
		endif;
		
		echo json_encode(array('deleted' => 'deleted'));
    die();
		
	endif;
}

/**
 * Add product variation value to callback lightbox
 **/
function themify_product_variation_vars(){
  global $available_variations, $woocommerce, $product, $post;
  echo '<div class="hide" id="themify_product_vars">'.json_encode($available_variations).'</div>';
}

/**
 * Add cart total and shopdock cart to the WC Fragments
 * @param array $fragments 
 * @return array
 */
function themify_theme_add_to_cart_fragments( $fragments ) {
	// cart list
	ob_start();
	get_template_part( 'includes/shopdock' );
	$shopdock = ob_get_clean();

	global $woocommerce;

	$fragments['#shopdock-ultra'] = $shopdock;
	$fragments['.check-cart'] = sprintf( '<span class="%s"></span>'
			, WC()->cart->get_cart_contents_count() > 0 ? 'check-cart show-count' : 'check-cart' );
	$fragments['#cart-icon span'] = sprintf( '<span>%s</span>', WC()->cart->get_cart_contents_count() );
	return $fragments;
}

/**
 * Delete cart
 * @return json
 */
function themify_theme_woocommerce_delete_cart() {
	global $woocommerce;
	if ( isset($_POST['remove_item']) && $_POST['remove_item'] ) {
		$woocommerce->cart->set_quantity( $_POST['remove_item'], 0 );
		WC_AJAX::get_refreshed_fragments();
		die();
	}
}

/**
 * Add to cart ajax on single product page
 * @return json
 */
function themify_theme_woocommerce_add_to_cart() {
	ob_start();
	WC_AJAX::get_refreshed_fragments();
	die();	
}

/**
 * Remove (unnecessary) success message after a product was added to cart through theme's AJAX method.
 * 
 * @since 1.8.0
 * 
 * @param array|string $message
 *
 * @return string
 */
function themify_theme_wc_add_to_cart_message( $message ) {
	if ( is_array( $message ) ) {
		if ( ( $key = array_search( 'success', $message ) ) !== false ) {
			unset( $message[$key] );
		}
	} else {
		if ( isset( $_REQUEST['action'] ) && 'theme_add_to_cart' == $_REQUEST['action'] ) {
			$message = '';
		}
	}
	return $message;
}