<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_base_data {
    public static function stars_style_image_name() {
        return apply_filters('gdrts_list_stars_styles_images', array(
            'star' => __("Star (512px)", "gd-rating-system"),
            'oxygen' => __("Oxygen Star (256px)", "gd-rating-system")
        ));
    }

    public static function stars_style_type() {
        return apply_filters('gdrts_list_stars_style_types', array(
            'image' => __("Image Based", "gd-rating-system")
        ));
    }
}
