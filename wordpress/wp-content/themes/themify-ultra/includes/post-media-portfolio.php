<?php
/**
 * Media Template.
 * If there's a Video URL in Themify Custom Panel it will show it, otherwise shows the featured image.
 * @package themify
 * @since 1.0.0
 */

/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php if ( $themify->hide_image != 'yes' ) : ?>
	<?php themify_before_post_image(); // Hook ?>

	<?php
	if ( themify_has_post_video() ) : ?>

		<figure class="post-image">
			<?php echo themify_post_video(); ?>
		</figure>

	<?php else: ?>

		<?php
		if(!$themify->is_shortcode && $themify->query_category == ''){
			if ( themify_get( 'image_width' ) !== NULL || themify_get( 'image_height' ) !== NULL ) {
				$themify->width = themify_get( 'image_width' );
				$themify->height = themify_get( 'image_height' );
			}
		}
		?>

		<?php if( $post_image = themify_get_image( 'ignore=true&w='.$themify->width.'&h='.$themify->height  ) ) : ?>

			<figure class="post-image <?php echo $themify->image_align; ?>">

				<?php if ( $themify->unlink_image != 'yes' ) : ?>
					<a href="<?php echo themify_get_featured_image_link(); ?>">
				<?php endif; ?>
					<?php echo wp_kses_post( $post_image ); ?>
				<?php if ( $themify->unlink_image != 'yes' ) : ?>
					<?php themify_zoom_icon(); ?>
				</a>
				<?php endif; ?>

			</figure>

		<?php endif; // if there's a featured image?>

	<?php endif; // video else image ?>

	<?php themify_after_post_image(); // Hook ?>
<?php endif; // hide image ?>
