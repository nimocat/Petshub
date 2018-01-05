<?php do_action( 'themify_portfolio_post_before' ); ?>

<article itemscope itemtype="http://schema.org/Article" id="portfolio-<?php the_ID(); ?>" class="<?php echo esc_attr( implode( ' ', get_post_class( 'post clearfix portfolio-post ' . $this->get_post_category_classes() ) ) ); ?>">

	<?php do_action( 'themify_portfolio_post_start' ); ?>

	<?php if ( ( ! tpp_is_portfolio_single() && $image == 'yes' ) ) : ?>

		<?php include( $this->locate_template( 'portfolio-media' ) ); ?>

	<?php endif //hide image ?>

	<div class="post-content">
		<div class="disp-table">
			<div class="disp-row">
				<div class="disp-cell valignmid">

					<?php if ( tpp_is_portfolio_single() ) : ?>
						<div class="project-meta">
							<?php if ( $date = get_post_meta( get_the_ID(), 'project_date', true ) ): ?>
								<div class="project-date">
									<strong><?php _e( 'Date', 'themify-portfolio-post' ); ?></strong>
									<?php echo wp_kses_post( $date ); ?>
								</div>
							<?php endif; //post date ?>

							<?php if ( $client = get_post_meta( get_the_ID(), 'project_client', true ) ) : ?>
								<div class="project-client">
									<strong><?php _e( 'Client', 'themify-portfolio-post' ); ?></strong>
									<?php echo wp_kses_post( $client ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $services = get_post_meta( get_the_ID(), 'project_services', true ) ) : ?>
								<div class="project-services">
									<strong><?php _e( 'Services', 'themify-portfolio-post' ); ?></strong>
									<?php echo wp_kses_post( $services ); ?>
								</div>
							<?php endif; ?>

							<?php if ( $launch = get_post_meta( get_the_ID(), 'project_launch', true ) ) : ?>
								<div class="project-launch">
									<a href="<?php echo esc_attr( $launch ); ?>"><?php _e( 'Launch Project', 'themify-portfolio-post' ); ?></a>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $post_meta == 'yes' ): ?>
						<div class="post-meta-top entry-meta">
							<?php the_terms( get_the_ID(), get_post_type() . '-category', '<span class="post-category">', ' <span class="separator">/</span> ', ' </span>' ) ?>
						</div>
					<?php endif; //post meta ?>

					<?php if ( $title == 'yes' ): ?>
						<?php do_action( 'themify_portfolio_post_before_title' ); ?>
						<h2 class="post-title entry-title" itemprop="name">
							<?php if ( $unlink_title == 'yes' ): ?>
								<?php the_title(); ?>
							<?php else: ?>
								<a href="<?php echo get_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
							<?php endif; //unlink post title ?>
						</h2>
						<?php do_action( 'themify_portfolio_post_after_title' ); ?>
					<?php endif; //post title ?>

					<div class="entry-content" itemprop="articleBody">

						<?php if ( 'excerpt' == $display && ! is_attachment() ) : ?>

							<?php the_excerpt(); ?>

						<?php elseif ( 'none' == $display && ! is_attachment() ) : ?>

						<?php else: ?>

							<?php the_content(  ); ?>

						<?php endif; //display content ?>

					</div>
					<!-- /.entry-content -->

					<?php edit_post_link( __( 'Edit', 'themify-portfolio-post' ), '<span class="edit-button">[', ']</span>' ); ?>

				</div><!-- /.disp-cell -->
			</div><!-- /.disp-row -->
		</div><!-- /.disp-table -->
	</div><!-- /.post-content -->

	<?php do_action( 'themify_portfolio_post_end' ); ?>

</article><!-- /.post -->

<?php do_action( 'themify_portfolio_post_after' ); ?>