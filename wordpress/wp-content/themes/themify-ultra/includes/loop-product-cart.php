<?php
/**
 * Template to display products in Themify cart
 * @package themify
 * @since 1.0.0
 */

global $woocommerce;
$carts = array_reverse( $woocommerce->cart->get_cart() );

foreach ( $carts as $cart_item_key => $values ) :
	$_product = $values['data'];

	if ( $_product->exists() && $values['quantity'] > 0 ): ?>

		<div class="product">

			<a href="<?php echo esc_url( $woocommerce->cart->get_remove_url($cart_item_key) ); ?>" data-product-key="<?php echo $cart_item_key; ?>" class="remove-item remove-item-js">
				<i class="icon-flatshop-close"></i>
			</a>

			<figure class="product-image">
				<?php themify_product_cart_image_start(); // hook ?>
				<a href="<?php echo esc_url( get_permalink(apply_filters('woocommerce_in_cart_product_id', $values['product_id'])) ); ?>">
					<?php
						$product_thumbnail = $_product->get_image('cart_thumbnail');
						if ( ! empty( $product_thumbnail ) ) {
							echo $product_thumbnail;
						} else {
							?>
							<img src="http://placehold.it/40x40">
							<?php
						}
					?>
				</a>
				<?php themify_product_cart_image_end(); // hook ?>
			</figure>

			<div class="product-details">
				<h3 class="product-title">
					<a href="<?php echo esc_url( get_permalink(apply_filters('woocommerce_in_cart_product_id', $values['product_id'])) );?>">
						<?php echo apply_filters( 'woocommerce_in_cart_product_title', $_product->get_title(), $values, $cart_item_key ); ?>
					</a>
				</h3>
				<p class="quantity-count"><?php echo sprintf(__('x %d', 'themify'), $values['quantity']); ?></p>
			</div>

		</div>
		<!--/product -->

	<?php endif; ?>

<?php endforeach; ?>
