<input type="hidden" name="gdrts[rich-snippets][nonce]" value="<?php echo wp_create_nonce('gdrts-rich-snippets-'.$_gdrts_id); ?>" />
<p>
    <label for="gdrts_rich-snippets_display"><?php _e("Display", "gd-rating-system"); ?></label>
    <?php d4p_render_select(array_merge(array('default' => __("Default", "gd-rating-system")), gdrtsa_admin_rich_snippets()->get_list_embed_locations()), array('selected' => $_gdrts_display, 'name' => 'gdrts[rich-snippets][display]')); ?>
</p>
<p>
    <label for="gdrts_rich-snippets_method"><?php _e("Rating Method", "gd-rating-system"); ?></label>
    <?php d4p_render_select(array_merge(array('default' => __("Default", "gd-rating-system")), gdrtsa_admin_rich_snippets()->get_list_embed_methods()), array('selected' => $_gdrts_method, 'name' => 'gdrts[rich-snippets][method]')); ?>
</p>
<p>
    <label for="gdrts_rich-snippets_itemscope"><?php _e("Item Scope", "gd-rating-system"); ?></label>
    <input name="gdrts[rich-snippets][itemscope]" class="widefat" type="text" value="<?php echo esc_attr($_gdrts_itemscope); ?>" />
    <em><?php _e("Leave empty to use default value from the Rich Snippet settings.", "gd-rating-system"); ?></em>
</p>
