<?php 
/**
 * Media Template.
 * If there's a Video URL in Themify Custom Panel it will show it, otherwise shows the featured image.
 * @package themify
 * @since 1.0.0
 */
?>

<?php if ( $image == 'yes' ) : ?>
		
	<?php do_action( 'themify_portfolio_post_before_image' ); ?>

	<?php
	if ( tpp_get( 'video_url' ) != '' ) : ?>

		<figure class="post-image">
			<?php
				global $wp_embed;
				echo $wp_embed->run_shortcode('[embed]' . esc_url( tpp_get( 'video_url' ) ) . '[/embed]');
			?>
		</figure>

	<?php else: ?>

		<?php
		if ( 'yes' == $use_original_dimensions ) {
			$image_w = tpp_get( 'image_width' );
			$image_h = tpp_get( 'image_height' );
		}

		if ( ! wp_script_is( 'themify-backstretch' ) ) {
			// Enqueue Backstretch
			wp_enqueue_script( 'themify-backstretch' );
		}
		?>

		<?php if( has_post_thumbnail() && $post_image = themify_do_img( wp_get_attachment_url( get_post_thumbnail_id() ), $image_w, $image_h ) ) : ?>
		
			<figure class="post-image">

				<?php if( $unlink_image == 'no' ) : ?><a href="<?php echo get_permalink(); ?>" class="themify-lightbox"><?php endif; ?>
				<img src="<?php echo $post_image['url'] ?>" width="<?php echo $post_image['width'] ?>" height="<?php echo $post_image['height'] ?>" alt="" />
				<?php if( $unlink_image == 'no' ) : ?></a><?php endif; ?>

			</figure>
	
		<?php endif; // if there's a featured image?>

	<?php endif; // video else image ?>

	<?php do_action( 'themify_portfolio_post_after_image' ); ?>

<?php endif; // hide image ?>