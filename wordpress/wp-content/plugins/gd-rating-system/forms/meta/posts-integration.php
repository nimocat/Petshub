<input type="hidden" name="gdrts[posts-integration][nonce]" value="<?php echo wp_create_nonce('gdrts-posts-integration-'.$_gdrts_id); ?>" />
<p>
    <label for="gdrts_posts-integration_location"><?php _e("Display Rating Block", "gd-rating-system"); ?></label>
    <?php d4p_render_select(array_merge(array('default' => __("Default", "gd-rating-system")), gdrtsa_admin_posts()->get_list_embed_locations()), array('selected' => $_gdrts_display, 'name' => 'gdrts[posts-integration][location]')); ?>
</p>
<p>
    <label for="gdrts_posts-integration_method"><?php _e("Rating Method", "gd-rating-system"); ?></label>
    <?php d4p_render_select(array_merge(array('default' => __("Default", "gd-rating-system")), gdrts_admin_shared::data_list_embed_methods()), array('selected' => $_gdrts_method, 'name' => 'gdrts[posts-integration][method]')); ?>
</p>
