<?php
/**
 * Template to load header widgets.
 * @package themify
 * @since 1.0.0
 */
 
$header_widget_option = '' == themify_get( 'setting-header_widgets' ) ? 'headerwidget-3col' : themify_get( 'setting-header_widgets' );

if ( $header_widget_option != 'none' ) : 

	$columns = array(
		'headerwidget-4col' => array( 'col4-1', 'col4-1', 'col4-1', 'col4-1' ),
		'headerwidget-3col' => array( 'col3-1', 'col3-1', 'col3-1' ),
		'headerwidget-2col' => array( 'col4-2', 'col4-2', ),
		'headerwidget-1col' => array( '', )
	);

	if ( themify_theme_has_widgets( 'header-widget-', $columns[$header_widget_option] ) ) : ?>

		<div class="header-widget clearfix">
			<div class="header-widget-inner">
				<?php
				$x = 0;
				foreach ( $columns[$header_widget_option] as $col ) :
					$x++;
					$class = ( 1 == $x ) ? 'first' : ''; ?>
					<div class="<?php echo esc_attr( $col . ' ' . $class ); ?>">
						<?php dynamic_sidebar( 'header-widget-' . $x ); ?>
					</div>
				<?php endforeach; ?>
			</div>
			<!-- /.header-widget-inner -->
		</div>
		<!-- /.header-widget -->

	<?php
	endif; // end has widgets

endif; // end header widget option ?>