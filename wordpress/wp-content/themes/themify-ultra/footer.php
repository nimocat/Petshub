<?php
/**
 * Template for site footer
 * @package themify
 * @since 1.0.0
 */
?>
<?php
/** Themify Default Variables
 *  @var object */
	global $themify;
        if(themify_theme_show_area( 'footer_widgets' )){
            $footer_position = themify_get('footer_widget_position');
            if(!$footer_position){
                $footer_position = themify_get('setting-footer_widget_position');
            }
        }
        else{
            $footer_position = false;
        }
        ?>

        <?php themify_layout_after(); // hook ?>
			</div>
			<!-- /body -->

			<?php if ( themify_theme_show_area( 'footer' ) && themify_theme_do_not_exclude_all( 'footer' ) ) : ?>
				<div id="footerwrap" <?php themify_theme_header_background( 'footer' ); ?>>

					<?php themify_footer_before(); // hook ?>

					<footer id="footer" class="pagewidth clearfix" itemscope="itemscope" itemtype="https://schema.org/WPFooter">

						<?php themify_footer_start(); // hook ?>

						<?php if ( themify_theme_show_area( 'footer_back' ) ) :?>
							<?php
							$float = '';
							if ( themify_check( 'setting-use_float_back' ) ){
								$float = 'back-top-float back-top-hide';
							}
							?>
                            <div class="back-top clearfix <?php echo $float;?>">
                                <div class="arrow-up">
                                    <a href="#header"></a>
                                </div>
                            </div>
                        <?php endif; // exclude footer widgets ?>

						<div class="main-col first clearfix">
							<div class="footer-left-wrap first">
								<?php if ( themify_theme_show_area( 'footer_site_logo' ) ) : ?>
									<div class="footer-logo-wrapper clearfix">
										<?php echo themify_logo_image( 'footer_logo', 'footer-logo' ); ?>
										<!-- /footer-logo -->
									</div>
								<?php endif; ?>
							
								<?php if ( is_active_sidebar( 'footer-social-widget' ) ) : ?>
									<div class="social-widget">
										<?php dynamic_sidebar( 'footer-social-widget' ); ?>
									</div>
									<!-- /.social-widget -->
								<?php endif; ?>
							</div>
							
							<div class="footer-right-wrap">

								<?php if ( themify_theme_show_area( 'footer_menu_navigation' ) ) : ?>
									<div class="footer-nav-wrap">
										<?php wp_nav_menu( array(
											'theme_location' => 'footer-nav',
											'fallback_cb' => '',
											'container'  => '',
											'menu_id' => 'footer-nav',
											'menu_class' => 'footer-nav',
										)); ?>
									</div>
									<!-- /.footer-nav-wrap -->
								<?php endif; // exclude menu navigation ?>
								<?php if ($footer_position!=='top') : ?>
									<div class="footer-text clearfix">
										<div class="footer-text-inner">
											<?php if ( themify_theme_show_area( 'footer_texts' ) ) : ?>
												<?php themify_the_footer_text(); ?>
												<?php themify_the_footer_text('right'); ?>
											<?php endif; ?>
										</div>
									</div>
									<!-- /.footer-text -->
								<?php endif;?>
							</div>
						</div>

						<?php if (themify_theme_show_area( 'footer_widgets' )) : ?>
							<?php if($footer_position==='top'):?>
								<div class="section-col clearfix">
									<div class="footer-widgets-wrap">
										<?php get_template_part( 'includes/footer-widgets'); ?>
										<!-- /footer-widgets -->
									</div>
								</div>
								<div class="footer-text clearfix">
									<div class="footer-text-inner">
										<?php if ( themify_theme_show_area( 'footer_texts' ) ) : ?>
											<?php themify_the_footer_text(); ?>
											<?php themify_the_footer_text('right'); ?>
										<?php endif; ?>
									</div>
								</div>
								<!-- /.footer-text -->
							<?php else:?>
								<div class="section-col clearfix">
									<div class="footer-widgets-wrap">
										<?php get_template_part( 'includes/footer-widgets'); ?>
										<!-- /footer-widgets -->
									</div>
								</div>
							<?php endif;?>
						<?php endif;?>

						<?php themify_footer_end(); // hook ?>

					</footer>
					<!-- /#footer -->

					<?php themify_footer_after(); // hook ?>

				</div>
				<!-- /#footerwrap -->
			<?php endif; // exclude footer ?>

		</div>
		<!-- /#pagewrap -->

		<?php
		/**
		 * Stylesheets and Javascript files are enqueued in theme-functions.php
		 */
		?>

		<?php themify_body_end(); // hook ?>
		<!-- wp_footer -->
		<?php wp_footer(); ?>
	</body>
</html>