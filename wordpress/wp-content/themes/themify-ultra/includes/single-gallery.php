<?php
$gallery = false;
$shortcode = themify_get('post_layout_gallery');
if ($shortcode) {
	$gallery = themify_get_images_from_gallery_shortcode($shortcode);
}
if (!$gallery) {
	return;
}
$thumb_size = themify_get_gallery_param_option($shortcode, 'size');
$columns = themify_get_gallery_param_option($shortcode, 'columns');
$columns = ( $columns == '' ) ? 3 : $columns;
$columns = intval($columns);
$use = themify_check('setting-img_settings_use');
if (!$thumb_size) {
	$thumb_size = 'thumbnail';
}
if ($thumb_size !== 'full') {
	$size['width'] = get_option("{$thumb_size}_size_w");
	$size['height'] = get_option("{$thumb_size}_size_h");
}
?>
<div class="gallery-wrapper packery-gallery clearfix gallery-columns-<?php echo $columns ?>">
	<?php foreach ($gallery as $image): ?>
		<?php
		$caption = $image->post_excerpt ? $image->post_excerpt : $image->post_content;
		$description = $image->post_content ? $image->post_excerpt : $image->post_excerpt;
		$alt = get_post_meta($image->ID, '_wp_attachment_image_alt', true);
		if (!$alt) {
			$alt = $caption ? $caption : ($description ? $description : the_title_attribute('echo=0'));
		}
		$featured = get_post_meta($image->ID, 'themify_gallery_featured', true);
		$img_size = $thumb_size !== 'full' ? $size : ( $featured ? array('width' => 474, 'height' => 542) : array('width' => 474, 'height' => 271));
		$img_size = apply_filters('themify_single_gallery_image_size', $img_size, $featured);
		$height = $thumb_size !== 'full' && $featured ? 2 * $size['height'] : $size['height'];
		$thumb = $featured ? 'large' : $thumb_size;
		$img = wp_get_attachment_image_src($image->ID, apply_filters('themify_gallery_post_type_single', $thumb));
		$url = !$featured || $use ? $img[0]:themify_get_image("src={$img[0]}&w={$img_size['width']}&h={$height}&ignore=true&urlonly=true");
		$lightbox_url = $thumb_size!=='large'?wp_get_attachment_image_src($image->ID, 'large'):$img;
		?>
		<div class="item gallery-icon <?php echo esc_attr( $featured ); ?>">
			<a href="<?php echo esc_url( $lightbox_url[0] ) ?>" title="<?php esc_attr_e($image->post_title) ?>" data-caption="<?php esc_attr_e($caption); ?>" data-description="<?php esc_attr_e( $description ); ?>">
				<span class="gallery-item-wrapper">
					<img src="<?php echo esc_url( $url ); ?>" alt="<?php echo esc_attr( $alt ); ?>" width="<?php echo esc_attr( $img_size['width'] ); ?>" height="<?php echo esc_attr( $height ); ?>" />
					<?php if ($caption): ?>
						<div class="gallery-caption">
							<span><?php echo esc_html( $caption ); ?></span>
						</div>
					<?php endif; ?>
				</span>
			</a>
		</div>
	<?php endforeach; ?>
</div>