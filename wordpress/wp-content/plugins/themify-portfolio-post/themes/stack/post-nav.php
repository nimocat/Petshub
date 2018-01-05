<?php 
/**
 * Post Navigation Template
 * @since 1.0.0
 */

$in_same_cat = false;
$previous = get_previous_post_link( '<span class="themify-portfolio-prev">%link</span>', '<span class="arrow" title="%title"></span>', $in_same_cat, '', 'portfolio-category' );
$next = get_next_post_link( '<span class="themify-portfolio-next">%link</span>', '<span class="arrow" title="%title"></span>', $in_same_cat, '', 'portfolio-category' );

	if ( ! empty( $previous ) || ! empty( $next ) ) : ?>

		<div class="themify-portfolio-post-nav clearfix">
			<?php echo "$previous $next"; ?>
		</div>
		<!-- /.post-nav -->

	<?php endif; // empty previous or next