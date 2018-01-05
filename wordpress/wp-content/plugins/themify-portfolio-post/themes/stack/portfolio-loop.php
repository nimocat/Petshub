<?php do_action( 'themify_portfolio_posts_before_loop' ); ?>

<div class="themify-portfolio-posts">

	<?php
	if ( 'yes' == $filter ) {
			echo $this->get_template( 'filter-portfolio', array(
				'cats' => $category,
				'taxo' => 'portfolio-category'
			) );
		}
	?>

	<div class="loops-wrapper shortcode <?php echo $post_type; ?> <?php echo $layout ?> <?php echo ( $query->post_count > 1 ) ? 'portfolio-multiple clearfix type-multiple' : 'portfolio-single' ?> <?php echo $masonry == 'yes' ? 'masonry-layout' : 'masonry-disabled'; ?>">

		<?php while( $query->have_posts() ) : $query->the_post(); ?>

			<?php include $this->locate_template( 'portfolio' ); ?>

		<?php endwhile; wp_reset_postdata(); ?>

		<?php if( $more_link ) include $this->locate_template( 'more-link' ); ?>

	</div>

</div><!-- .themify-portfolio-posts -->

<?php do_action( 'themify_portfolio_posts_after_loop' ); ?>