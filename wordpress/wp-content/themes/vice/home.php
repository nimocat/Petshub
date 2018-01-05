<?php
get_header();
get_template_part('index','banner'); ?>
<div class="page-builder">
<div class="clearfix"></div>
<!-- Blog Masonry Section -->
<section class="blog-section-lg">
	<div class="container">
		<div class="row masonry-4">
			<?php
			if ( have_posts() ) :
					// Start the Loop.
						while ( have_posts() ) : the_post();
			?>
			<div class="masonry-item">
				<div id="post-<?php the_ID(); ?>" <?php post_class('blog-lg-area-left'); ?>>
					<div class="media">
						<div class="media-body">
						<?php appointment_post_thumbnail('','img-responsive'); ?>
						<div class="blog-post-sm">
						
						<?php _e('By','appointment');?><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) );?>"><?php echo get_the_author();?></a>
						<?php _e('Posted','appointment');?>
						<a href="<?php echo get_month_link(get_post_time('Y'),get_post_time('m')); ?>">
						<?php echo get_the_date('M j, Y'); ?></a>
						<?php 	$tag_list = get_the_tag_list();
						if(!empty($tag_list)) { ?>
						<div class="blog-tags-sm"><?php _e('In','appointment');?><?php the_tags('', ', ', ''); ?></div>
						<?php } ?>
						</div>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php the_content( __('Read More', 'appointment' ) ); 
						wp_link_pages( ); ?>
					</div>
				</div>
			</div>
		</div>
		<?php endwhile; endif; ?>
		</div>	
		<!-- Blog Pagination -->
				<?php 				
					the_posts_pagination( array(
					'prev_text'          => '<i class="fa fa-angle-double-left"></i>',
					'next_text'          => '<i class="fa fa-angle-double-right"></i>',
					'screen_reader_text' => ' ',
					) ); 
				?>
		<!-- /Blog Pagination -->					
</section>
<!-- /Blog Masonry Section -->
<div class="clearfix"></div>
<?php get_footer(); ?>