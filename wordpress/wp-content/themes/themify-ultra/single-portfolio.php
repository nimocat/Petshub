<?php
/**
 * Template for single portfolio view
 * @package themify
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<?php if (have_posts()) while (have_posts()) : the_post(); ?>

	<div class="featured-area fullcover">
		<?php if ( $themify->post_layout_type == 'slider' || $themify->post_layout_type == 'gallery' ) : ?>
			<?php if($themify->hide_image != 'yes'):?>
                            <?php get_template_part( 'includes/single-' . $themify->post_layout_type, 'portfolio' ); ?>
			<?php endif;?>
		<?php else : ?>
			<?php get_template_part( 'includes/post-media', 'portfolio' ); ?>
		<?php endif; ?> 

		<?php get_template_part( 'includes/post-meta', 'portfolio' ); ?>
	</div><!-- .featured-area -->

	<!-- layout-container -->
	<div id="layout" class="pagewidth clearfix">

		<?php themify_content_before(); // hook  ?>

		<!-- content -->
		<div id="content" class="list-post">
			<?php themify_content_start(); // hook ?>

			<article id="post-<?php the_id(); ?>" <?php post_class('post clearfix'); ?>>
				<?php themify_post_start(); // hook   ?>
				<div class="post-content">
					<div class="entry-content" itemprop="articleBody">
						<?php get_template_part( 'includes/portfolio-meta', 'portfolio' ); ?>
						<?php the_content(); ?>
					</div>
				</div>
				<?php themify_post_end(); // hook   ?>
			</article>

			<?php wp_link_pages(array('before' => '<p class="post-pagination"><strong>' . __('Pages:', 'themify') . ' </strong>', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			<?php get_template_part('includes/author-box', 'single'); ?>

			<?php get_template_part('includes/post-nav', 'portfolio'); ?>

			<?php if ( ! themify_check('setting-comments_posts') || themify_check( 'setting-portfolio_comments' ) ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>

			<?php themify_content_end(); // hook  ?>
		</div>
		<!-- /content -->

		<?php themify_content_after(); // hook  ?>

	<?php endwhile; ?>

	<?php
	/////////////////////////////////////////////
	// Sidebar							
	/////////////////////////////////////////////
	if ( $themify->layout != "sidebar-none" ) get_sidebar();
	?>

	</div><!-- /layout-container -->

<?php get_footer(); ?>