<?php do_action( 'themify_portfolio_single_before_loop' ); ?>

<div class="themify-portfolio-single">

	<?php while( $query->have_posts() ) : $query->the_post(); ?>

	<div class="featured-area">

	<?php if ( $post_meta == 'yes' ) : ?>

		<?php do_action( 'themify_portfolio_post_before_image' ); ?>

		<?php
		///////////// GALLERY //////////////////////
		if ( $images = $this->get_gallery_images() ) : ?>
			<?php
			// Find out the number of columns in shortcode
			$columns = $this->get_gallery_columns();

			// Find out the size specified in shortcode
			$thumb_size = $this->get_gallery_size();

			// Count how many images we really have
			$n_images = count( $images );
			if ( $columns > $n_images ) {
				$columns = $n_images;
			}
			?>
			<div class="tpp-gallery-wrapper clearfix <?php echo esc_attr( "gallery-columns-$columns" ); ?>">
				<div class="grid-sizer"></div>
				<?php
				$counter = 0; ?>

				<?php foreach ( $images as $image ) :
					$counter++;

					$caption = $this->get_caption( $image );
					$description = $this->get_description( $image );
					if ( $caption ) {
						$alt = $caption;
					} elseif ( $description ) {
						$alt = $description;
					} else {
						$alt = the_title_attribute('echo=0');
					}
					$featured = get_post_meta( $image->ID, 'themify_gallery_featured', true );
					if ( $featured && '' != $featured ) {
						$img_size = array(
							'width' => 350,
							'height' => 400,
						);
					} else {
						$img_size = array(
							'width' => 350,
							'height' => 200,
						);
					}

					$size = $featured && '' != $featured ? 'large' : 'medium';
					$img = wp_get_attachment_image_src( $image->ID, apply_filters( 'themify_gallery_post_type_single', $size ) );
					$out_image = '<img src="' . esc_url( $img[0] ) . '" alt="' . esc_attr( $alt ) . '" width="' . esc_attr( $img_size['width'] ) . '" height="' . esc_attr( $img_size['height'] ) . '" />';

					?>
					<div class="item <?php if( $caption ) echo 'has-caption'; ?> <?php echo esc_attr( $featured ); ?>">
						<a href="<?php echo esc_url( $img[0] ); ?>" class="" data-image="<?php echo esc_url( $img[0] ); ?>" data-caption="<?php echo esc_attr( $caption ); ?>" data-description="<?php echo esc_attr( $description ); ?>">
							<span class="gallery-item-wrapper">
								<?php echo wp_kses_post( $out_image ); ?>
								<?php if ( $caption ) : ?>
									<span class="gallery-caption"><?php echo esc_html( $caption ); ?></span>
								<?php endif; // caption ?>
							</span>
						</a>
					</div>
				<?php endforeach; // images as image ?>
			</div>

		<?php
		///////////// SINGLE IMAGE //////////////////////
		elseif( has_post_thumbnail() && $post_image = themify_do_img( wp_get_attachment_url( get_post_thumbnail_id() ), $image_w, $image_h ) ) : ?>

			<figure class="post-image">
				<?php if( 'yes' == $unlink_image ): ?>
					<img src="<?php echo $post_image['url'] ?>" width="<?php echo $post_image['width'] ?>" height="<?php echo $post_image['height'] ?>" alt="" />
				<?php else: ?>
					<a href="<?php echo get_permalink(); ?>">
						<img src="<?php echo $post_image['url'] ?>" width="<?php echo $post_image['width'] ?>" height="<?php echo $post_image['height'] ?>" alt="" />
					</a>
				<?php endif; // unlink image ?>
			</figure>

		<?php endif; // video else image ?>

		<?php do_action( 'themify_portfolio_post_after_image' ); ?>

	<?php endif; // hide image ?>

	</div>

	<?php include $this->locate_template( 'portfolio' ); ?>

	<?php include $this->locate_template( 'post-nav' ); ?>

	<?php endwhile; wp_reset_postdata(); ?>

</div><!-- .themify-portfolio-single -->

<?php do_action( 'themify_portfolio_single_after_loop' ); ?>