<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div class="wrap nosubsub">
    <div class="form-wrap">
        <div class="icon32 icon32-posts-inpost-gallery" id="icon-edit"><br></div>
        <h2><?php _e("InPost Gallery Settings", 'inpost-gallery') ?></h2>

        <div style="width: 60%">
            <form action="/" method="post" id="inpost_gallery_settings_form">
                <?php
                $all_post_types = get_post_types();
                unset($all_post_types['nav_menu_item']);
                unset($all_post_types['revision']);
                unset($all_post_types['attachment']);
                ?>

                <div class="form-field">
                    <h4><?php _e("Supported custom post types", 'inpost-gallery') ?>:</h4>

                    <?php foreach ($all_post_types as $post_type => $post_type_name) : ?>
                        <input style="width: auto !important;" class="inpost_gallery_checkbox_selfupdated" type="checkbox" <?php echo(@$post_types[$post_type] == 1 ? 'checked' : '') ?> />
                        <input type="hidden" value="<?php echo $post_types[$post_type] ?>" name="post_types[<?php echo $post_type ?>]" />
                        &nbsp;<label style="display: inline-block" for="post_types[<?php echo $post_type ?>]"><?php echo $post_type_name ?></label> &nbsp;
                    <?php endforeach; ?>
                </div>


                <div class="form-field" id="wordpress_custom_sizes">
                    <b><?php _e("Post metabox thumbnails size", 'inpost-gallery') ?></b>:&nbsp;<input class="inpost_gallery_image_size_field" type="text" value="<?php echo $admin_thumb_width ?>" name="admin_thumb_width" />&nbsp;x&nbsp;<input class="inpost_gallery_image_size_field" type="text" value="<?php echo $admin_thumb_height ?>" name="admin_thumb_height" />&nbsp;px<br />
                </div>
                
                <div class="form-field" id="wordpress_custom_sizes">
                    <b><?php _e("Front default thumbnail size", 'inpost-gallery') ?></b>:&nbsp;<input class="inpost_gallery_image_size_field" type="text" value="<?php echo $thumb_default_width ?>" name="thumb_default_width" />&nbsp;x&nbsp;<input class="inpost_gallery_image_size_field" type="text" value="<?php echo $thumb_default_height ?>" name="thumb_default_height" />&nbsp;px<br />
                </div>

                <div class="form-field" id="wordpress_custom_sizes">
                    <b><?php _e("Max thumbs groups", 'inpost-gallery') ?></b>:&nbsp;<input class="inpost_gallery_image_size_field" type="text" value="<?php echo $max_thumb_groups ?>" name="max_thumb_groups" /><br />
                </div>

                <div class="form-field">
                    <label style="display: inline-block" for="hide_donation_button"><?php _e("Hide donation button from post meta box", 'inpost-gallery') ?>:</label>&nbsp;<input style="width: auto !important;" class="inpost_gallery_checkbox_selfupdated" type="checkbox" <?php if ($hide_donation_button): ?>checked=""<?php endif; ?> />
                    <input type="hidden" value="<?php echo $hide_donation_button ?>" name="hide_donation_button" />
                    <p><b><?php _e("Hides donation button from gallery metabox", 'inpost-gallery') ?></b></p>
                </div>

                <p class="submit">
                    <input type="submit" value="<?php _e("Save settings", 'inpost-gallery') ?>" class="button button-primary" name="submit" />
                </p>
            </form>

            <?php InpostGallery::draw_donate_button() ?>
            <br />
            
            <?php InpostGallery::draw_adv_meta(); ?>

        </div>
    </div>
</div>

