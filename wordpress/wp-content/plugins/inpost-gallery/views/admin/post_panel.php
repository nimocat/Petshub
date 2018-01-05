<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<input type="hidden" name="inpost_gallery_data" value="1" />

<a href="#" class="js_inpost_gallery_add_slide inpost_gallery_add_slide_button"><?php _e("Add slides", 'inpost-gallery') ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="js_inpost_gallery_insert_shortcode inpost_gallery_add_slide_button"><?php _e("Insert shortcode", 'inpost-gallery') ?></a><br />

<ul id="inpost_gallery_slide_group">
    <?php if (!empty($inpost_gallery_data) AND is_array($inpost_gallery_data)): ?>
        <?php foreach ($inpost_gallery_data as $item_data): ?>
            <?php echo InpostGallery::render_html('views/admin/meta_slide_item.php', array('item_data' => $item_data)); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
<div style="clear: both;"></div>
<div style="float: right;">
    <a href="http://wordpress-gallery.com/" target="_blank" class="pluginus_link">www.wordpress-gallery.com</a><br />
</div>
<?php if (!InpostGallery::$settings['hide_donation_button']): ?>
    <div style="float: left;">
        <?php echo InpostGallery::draw_donate_button(); ?>
    </div>
<?php endif; ?>

<div style="clear: both;"></div>

<script type="text/javascript">
    inpost_gallery_post_id =<?php echo (int) $post_id ?>;
</script>
