<h4><?php _e("Filter", "gd-rating-system"); ?></h4>
<table>
    <tbody>
        <tr>
            <td class="cell-left">
                <label for="<?php echo $this->get_field_id('type'); ?>"><?php _e("Type", "gd-rating-system"); ?>:</label>
                <?php d4p_render_select(gdrts_admin_shared::data_list_entity_name_types(), array('id' => $this->get_field_id('type'), 'class' => 'widefat', 'name' => $this->get_field_name('type'), 'selected' => $instance['type'])); ?>
            </td>
            <td class="cell-right">
                <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e("Limit", "gd-rating-system"); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo esc_attr($instance['limit']); ?>" />
            </td>
        </tr>
        <tr>
            <td class="cell-left">
                <label for="<?php echo $this->get_field_id('rating_min'); ?>"><?php _e("Minimum Rating", "gd-rating-system"); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('rating_min'); ?>" name="<?php echo $this->get_field_name('rating_min'); ?>" type="text" value="<?php echo esc_attr($instance['rating_min']); ?>" />
            </td>
            <td class="cell-right">
                <label for="<?php echo $this->get_field_id('votes_min'); ?>"><?php _e("Minimum Votes", "gd-rating-system"); ?>:</label>
                <input class="widefat" id="<?php echo $this->get_field_id('votes_min'); ?>" name="<?php echo $this->get_field_name('votes_min'); ?>" type="text" value="<?php echo esc_attr($instance['votes_min']); ?>" />
            </td>
        </tr>
    </tbody>
</table>

<h4><?php _e("Order", "gd-rating-system"); ?></h4>
<table>
    <tbody>
        <tr>
            <td class="cell-left">
                <label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e("Order by", "gd-rating-system"); ?>:</label>
                <?php d4p_render_select(gdrts_admin_shared::data_list_orderby(), array('id' => $this->get_field_id('orderby'), 'class' => 'widefat', 'name' => $this->get_field_name('orderby'), 'selected' => $instance['orderby'])); ?>
            </td>
            <td class="cell-right">
                <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e("Order", "gd-rating-system"); ?>:</label>
                <?php d4p_render_select(gdrts_admin_shared::data_list_order(), array('id' => $this->get_field_id('order'), 'class' => 'widefat', 'name' => $this->get_field_name('order'), 'selected' => $instance['order'])); ?>
            </td>
        </tr>
    </tbody>
</table>

