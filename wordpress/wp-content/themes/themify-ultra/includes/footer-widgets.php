<?php
/**
 * Template to load footer widgets.
 * @package themify
 * @since 1.0.0
 */
 
$footer_widget_option = '' == themify_get( 'setting-footer_widgets' ) ? 'footerwidget-3col' : themify_get( 'setting-footer_widgets' );

if ( $footer_widget_option != 'none' ) : 

	$columns = array(
		'footerwidget-4col' => array( 'col4-1', 'col4-1', 'col4-1', 'col4-1' ),
		'footerwidget-3col' => array( 'col3-1', 'col3-1', 'col3-1' ),
		'footerwidget-2col' => array( 'col4-2', 'col4-2', ),
		'footerwidget-1col' => array( '', )
	);

	if ( themify_theme_has_widgets( 'footer-widget-', $columns[$footer_widget_option] ) ) : ?>

		<div class="footer-widgets clearfix">
			<?php
			$x = 0;
			foreach ( $columns[$footer_widget_option] as $col ) :
				$x++;
				$class = ( 1 == $x ) ? 'first' : ''; ?>
				<div class="<?php echo esc_attr( $col . ' ' . $class ); ?>">
					<?php dynamic_sidebar( 'footer-widget-' . $x ); ?>
				</div>
			<?php endforeach; ?>
		</div>
		<!-- /.footer-widgets -->

	<?php
	endif; // end has widgets

endif; // end footer widget option ?>