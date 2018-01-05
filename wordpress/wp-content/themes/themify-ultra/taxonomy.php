<?php get_header(); ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<!-- layout -->
<div id="layout" class="pagewidth clearfix">

	<?php themify_content_before(); //hook ?>
	<!-- content -->
	<div id="content">
    	<?php themify_content_start(); //hook ?>
	
		<?php 
		/////////////////////////////////////////////
		// Category Title	 							
		/////////////////////////////////////////////
		?>
		
		<h1 class="page-title"><?php single_cat_title(); ?></h1>
		<?php echo themify_get_term_description( $wp_query->query_vars['taxonomy'] ); ?>
		
		<?php
		global $query_string;
		// If it's a taxonomy, set the related post type
		$set_post_type = str_replace( '-category', '', $wp_query->query_vars['taxonomy'] );
		if ( in_array( $wp_query->query_vars['taxonomy'], get_object_taxonomies( $set_post_type ) ) ) {
			$themify_query = $query_string . '&post_type=' . $set_post_type . '&paged=' . urlencode( $paged );
			query_posts( $themify_query );
		}
		?>

		<?php 
		/////////////////////////////////////////////
		// Loop	 							
		/////////////////////////////////////////////
		?>
		<?php if (have_posts()) : ?>

			<!-- loops-wrapper -->
			<div id="loops-wrapper" class="loops-wrapper <?php echo esc_attr( themify_theme_query_classes() ); ?>">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'includes/loop', $set_post_type ); ?>

				<?php endwhile; ?>

			</div>
			<!-- /loops-wrapper -->

			<?php get_template_part( 'includes/pagination' ); ?>

		<?php 
		/////////////////////////////////////////////
		// Error - No Page Found	 							
		/////////////////////////////////////////////
		?>
	
		<?php else : ?>

			<p><?php _e( 'Sorry, nothing found.', 'themify' ); ?></p>
	
		<?php endif; ?>			
	
    	<?php themify_content_end(); //hook ?>
	</div>
	<!-- /#content -->
    <?php themify_content_after() //hook; ?>

	<?php 
	/////////////////////////////////////////////
	// Sidebar							
	/////////////////////////////////////////////
	 if ( $themify->layout != 'sidebar-none' ): get_sidebar(); endif; ?>

</div>
<!-- /#layout -->

<?php get_footer(); ?>