<?php
$slider = false;
$shortcode = themify_get( 'post_layout_slider' );
if ( $shortcode ) {
	$slider = themify_get_images_from_gallery_shortcode($shortcode);
}
if ( ! $slider ) {
	return;
}
$img_width = themify_get('image_width');
$img_height = themify_get('image_height');
$image_size = !$img_width ? themify_get_gallery_param_option( $shortcode, 'size' ) : 'full';

$speed = themify_get( 'setting-single_slider_speed', 'normal' );
if( $speed == 'slow' ) {
	$speed = 4;
} elseif( $speed == 'fast' ) {
	$speed = 0.5;
} else {
	$speed = 1.25;
}
$config = apply_filters( 'themify_single_post_slider_args', array(
	'height'      => themify_get( 'setting-single_slider_height', 'auto' ),
	'slider_nav'  => 1,
	'pager'       => 1,
	'wrapvar'     => 1,
	'auto'        => themify_get( 'setting-single_slider_autoplay', 'off' ),
	'pause_hover' => 1,
	'speed'       => $speed,
	'scroll'      => 1,
	'effect'      => themify_get( 'setting-single_slider_effect', 'scroll' ),
	'visible'     => 1,
	'numsldr'     => rand( 0, 1011 ).uniqid()
) );
?>

<div id="slider-<?php echo $config['numsldr'] ?>" class="shortcode clearfix slider single-slider">
	<ul data-slider="<?php echo esc_attr( base64_encode( json_encode( $config ) ) ); ?>" class="slides">
		<?php foreach ( $slider as $image ) : ?>
			<?php
			$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
			$caption = $image->post_excerpt ? $image->post_excerpt : $image->post_content;
			if ( ! $alt ) {
				$alt = get_post_meta($image->ID, '_wp_attachment_image_title', true);
			}
			if ( ! $caption ) {
				$caption = $alt;
			}
			$img = wp_get_attachment_image_src($image->ID, $image_size);
			$img = $img[0];
			if( $img_width > 0 ) {
				$img = themify_get_image( array( 'w' => $img_width, 'h' => $img_height, 'urlonly' => true, 'ignore' => true, 'src' => $img, 'crop'=> true ) );
			}
			?>
			<li>
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
				<?php if ($caption): ?>
					<div class="slide-caption">
						<?php echo esc_html( $caption ); ?>
					</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div><!-- .shortcode.slider -->