<?php
/**
 * WooCommerce Template Override
 * woocommerce-template.php
 */

if (!function_exists('woocommerce_get_product_thumbnail')) {
	/**
	 * WooCommerce Product Thumbnail
	 * @param string $size Image size
	 * @param int $placeholder_width Width of image placeholder
	 * @param int $placeholder_height Height of image placeholder
	 * @return string Markup including image
	 */
	function woocommerce_get_product_thumbnail( $size = 'shop_catalog', $placeholder_width = 0, $placeholder_height = 0 ) {
		global $post;
		$html = '<figure class="product-image">';
		
			if ( has_post_thumbnail() ) {
				$html .= get_the_post_thumbnail($post->ID, $size);
			} else {
                            $shop_catalog = wc_get_image_size( $size );
                            if (!$placeholder_width) $placeholder_width = $shop_catalog['width'];
                            if (!$placeholder_height) $placeholder_height = $shop_catalog['height'];

				$html .= '<img src="http://placehold.it/'.$placeholder_width.'x'.$placeholder_height.'" alt="Placeholder" />'; 
			}

			$html .= '<span class="loading-product"></span>';
		$html .= '</figure>';
		
		return $html;
	  }
}

if(!function_exists('themify_before_shop_content')) {
	/**
	 * Add initial portion of wrapper
	 */
	function themify_before_shop_content() { ?>
		<!-- layout -->
		<div id="layout" class="pagewidth clearfix">
			
			<?php themify_content_before(); //hook ?>
			<!-- content -->
			<div id="content" class="<?php echo (is_product() || themify_is_shop()) ? 'list-post':''; ?>">
				
				<?php if( ! ( themify_check( 'setting-hide_shop_breadcrumbs' ) 
					|| ( themify_check( 'setting-hide_shop_single_breadcrumbs' ) && is_product() ) ) ) { ?>
				
					<?php themify_breadcrumb_before(); ?>
					
					<?php woocommerce_breadcrumb(); ?>
					
					<?php themify_breadcrumb_after(); ?>
					
				<?php } ?>
				
				<?php themify_content_start(); //hook ?>
				
				<?php
	}
}

if(!function_exists('themify_after_shop_content')) {
	/**
	 * Add end portion of wrapper
	 */
	function themify_after_shop_content() {
				if (is_search() && is_post_type_archive() ) {
					add_filter( 'woo_pagination_args', 'woocommerceframework_add_search_fragment', 10 );
				} ?>
				<?php themify_content_end(); //hook ?>
			</div>
			<!-- /#content -->
			 <?php themify_content_after() //hook; ?>

			<?php
			if(themify_is_shop() || is_product_category()) {
				$layout = themify_get('setting-shop_layout');
			} else {
				$layout = themify_get('setting-single_product_layout');
			}
			if ($layout != 'sidebar-none') get_sidebar();
		?>
		</div><!-- /#layout -->
		<?php
	}
}

if(!function_exists('themify_product_image_ajax')){
	/**
	 * Filter image of product loaded in lightbox to remove link and wrap in figure.product-image. Implements filter themify_product_image_ajax for external usage
	 * @param string $html Original markup
	 * @param int $post_id Post ID
	 * @return string Image markup without link
	 */
	function themify_product_image_ajax($html, $post_id) {
		$image = get_the_post_thumbnail( $post_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) );
		return apply_filters( 'themify_product_image_ajax', sprintf( '<figure class="product-image">%s<span class="loading-product"></span></figure>', $image ) );
	};
}

if(!function_exists('themify_product_image_single')){
	/**
	 * Filter image of product loaded in lightbox to remove link and wrap in figure.product-image. Implements filter themify_product_image_ajax for external usage
	 * @param string $html Original markup
	 * @param int $post_id Post ID
	 * @return string Image markup without link
	 */
	function themify_product_image_single($html, $post_id) {
		//$html = str_replace('</a>', '<span class="loading-product"></span></a>', $html);
		//<figure class="product-image">%s</figure>
		$pattern = '/(<img(.*)>)<\/a>/i';
		$replacement = '<figure class="product-image">${1}<span class="loading-product"></span></figure></a>';
		$html = preg_replace($pattern, $replacement, $html);
		return $html;
	};
}

if(!function_exists('themify_loop_add_to_cart_link')) {
	/**
	 * Filter link to setup lightbox capabilities
	 * @param string $format Original markup
	 * @param object $product WC Product Object
	 * @param array $link Array of link parameters
	 * @return string Markup for link
	 */
	function themify_loop_add_to_cart_link( $format = '', $product = null, $link = array() ) {		
		if ( $product->is_purchasable() ) {
			$format = preg_replace( '/add_to_cart_button/', 'add_to_cart_button theme_add_to_cart_button', $format, 1 );
		}
		return $format;
	}
}

if(!function_exists('themify_product_description')){
	/**
	 * WooCommerce Single Product description
	 */
	function themify_product_description(){
		the_content();
	}
}

if(!function_exists('themify_shopdock_bar')){
	/**
	 * Load dock bar in footer
	 */
	function themify_shopdock_bar(){
		get_template_part('includes/shopdock');
	}
}