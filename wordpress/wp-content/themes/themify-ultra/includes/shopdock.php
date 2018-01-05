<?php
/**
 * Template for cart
 * @package themify
 * @since 1.0.0
 */
?>
<?php themify_shopdock_before(); //hook ?>
<div id="shopdock-ultra">

	<?php themify_shopdock_start(); //hook ?>

	<?php
	// check whether cart is not empty
	if ( sizeof( WC()->cart->get_cart() ) > 0 ):
	?>
		<div id="cart-wrap">

			<div id="cart-list">
				<div class="jspContainer">
					<div class="jspPane">
						<?php get_template_part( 'includes/loop-product', 'cart' ); ?>
					</div>
					<!-- /.jspPane -->
				</div>
				<!-- /.jspContainer -->
			</div>
			<!-- /cart-list -->

			<p class="cart-total">
				<?php echo WC()->cart->get_cart_subtotal(); ?>
				<a id="view-cart" href="<?php echo esc_url( wc_get_cart_url() ) ?>">
					<?php _e('(view cart)', 'themify') ?>
				</a>
			</p>

			<?php themify_checkout_start(); //hook ?>

			<p class="checkout-button">
				<button type="submit" class="button checkout white flat" onClick="document.location.href='<?php echo esc_url( WC()->cart->get_checkout_url() ); ?>'; return false;"><?php _e('Checkout', 'themify')?></button>
			</p>
			<!-- /checkout-botton -->

			<?php themify_checkout_end(); //hook ?>

		</div>
		<!-- /#cart-wrap -->
	<?php else: ?>
		<?php printf( __( 'Your cart is empty. Go to <a href="%s">Shop</a>.', 'themify' )
			, get_permalink( version_compare( WOOCOMMERCE_VERSION, '3.0.0', '>=' )
				? wc_get_page_id( 'shop' )
				: woocommerce_get_page_id( 'shop' ) ) ); ?>
	<?php endif; // cart whether is not empty?>

	<?php themify_shopdock_end(); //hook ?>

</div>
<!-- /#shopdock -->

<?php themify_shopdock_after(); //hook ?>