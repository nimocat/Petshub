<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<?php
	/** Themify Default Variables
	 *  @var object */
	global $themify; ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<!-- wp_head -->
	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php themify_body_start(); // hook ?>

<div id="pagewrap" class="hfeed site">

	<?php if ( themify_theme_show_area( 'header' ) && themify_theme_do_not_exclude_all( 'header' ) ) : ?>
		<div id="headerwrap" <?php themify_theme_header_background() ?>>
                        <?php   
                        $show_mobile_menu = themify_theme_do_not_exclude_all( 'mobile-menu' );
                        $show_menu_navigation = $show_mobile_menu && themify_theme_show_area( 'menu_navigation' );
                      ?>
			<?php themify_header_before(); // hook ?>
                        <?php if($show_menu_navigation):?>
                            <div class="header-icons">
                                <a id="menu-icon" href="#mobile-menu"></a>
                            </div>
                        <?php endif;?>

			<header id="header" class="pagewidth clearfix" itemscope="itemscope" itemtype="https://schema.org/WPHeader">

	            <?php themify_header_start(); // hook ?>

	            <div class="header-bar">
		            <?php if ( themify_theme_show_area( 'site_logo' ) ) : ?>
						<?php echo themify_logo_image(); ?>
					<?php endif; ?>

					<?php if ( themify_theme_show_area( 'site_tagline' ) ) : ?>
						<?php echo themify_site_description(); ?>
					<?php endif; ?>
				</div>
				<!-- /.header-bar -->

				<?php if ( $show_mobile_menu ) : ?>
					<div id="mobile-menu" class="sidemenu sidemenu-off">

						<div class="navbar-wrapper clearfix">

							<?php if ( themify_theme_show_area( 'social_widget' ) || themify_theme_show_area( 'rss' ) ) : ?>
								<div class="social-widget">
									<?php if ( themify_theme_show_area( 'social_widget' ) ) : ?>
										<?php dynamic_sidebar( 'social-widget' ); ?>
									<?php endif; // exclude social widget ?>

									<?php if ( themify_theme_show_area( 'rss' ) ) : ?>
										<div class="rss">
											<a href="<?php echo esc_url( themify_get( 'setting-custom_feed_url' ) != '' ? themify_get( 'setting-custom_feed_url' ) : get_bloginfo( 'rss2_url' ) ); ?>"></a>
										</div>
									<?php endif; // exclude RSS ?>
								</div>
								<!-- /.social-widget -->
							<?php endif; // exclude social widget or RSS icon ?>

							<?php if ( themify_theme_show_area( 'search_form' ) ) : ?>
								<div id="searchform-wrap">
									<?php get_search_form(); ?>
								</div>
								<!-- /searchform-wrap -->
							<?php endif; // exclude search form ?>

							<nav id="main-nav-wrap" itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement">
								<?php if ( $show_menu_navigation ) : ?>
									<?php themify_menu_nav( array( 'walker' => new Themify_Mega_Menu_Walker ) ); ?>
									<!-- /#main-nav -->
									
									<?php if ( themify_is_woocommerce_active() && themify_theme_show_area( 'cart_icon' ) ) : global $woocommerce; ?>
										<div class="cart-icon">
											<span class="check-cart"></span>
											<div class="cart-wrap">
												<a id="cart-icon" href="#slide-cart">
													<i class="fa fa-shopping-cart"></i>
													<span>
														<?php echo $woocommerce->cart->get_cart_contents_count(); ?>
													</span>
												</a>
											<!-- /.cart-wrap -->
											</div>
										</div>
									<?php endif; ?>
									
								<?php else: echo '<span id="main-nav"></span>'; ?>
								<?php endif; // exclude menu navigation ?>
							</nav>
							<!-- /#main-nav-wrap -->
							
						</div>

						<?php if ( themify_theme_show_area( 'header_widgets' ) ) : ?>
							<?php get_template_part( 'includes/header-widgets'); ?>
							<!-- /header-widgets -->
						<?php endif; // exclude header widgets ?>

						<a id="menu-icon-close" href="#"></a>
					</div>
					<!-- /#mobile-menu -->
				<?php endif; // do not exclude all this ?>

				<?php
					// If there's a header background slider, show it.
					global $themify_bg_gallery;
					$themify_bg_gallery->create_controller();
				?>

				<?php if ( themify_is_woocommerce_active() ) : ?>
					<div id="slide-cart" class="sidemenu sidemenu-off">
						<a id="cart-icon-close"></a>
						<?php themify_get_ecommerce_template( 'includes/shopdock' ); ?>
					</div>
				<?php endif; ?>

				<?php themify_header_end(); // hook ?>

			</header>
			<!-- /#header -->

	        <?php themify_header_after(); // hook ?>

		</div>
		<!-- /#headerwrap -->
	<?php endif; // exclude header ?>

	<div id="body" class="clearfix">

		<?php themify_layout_before(); //hook ?>
