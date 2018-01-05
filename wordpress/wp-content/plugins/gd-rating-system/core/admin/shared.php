<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_shared {
    public static function data_list_entity_name_types() {
        $items = array();

        foreach (gdrts()->get_entities() as $entity => $obj) {
            foreach ($obj['types'] as $name => $label) {
                $items[$entity.'.'.$name] = $obj['label'].': '.$label;
            }
        }

        return $items;
    }

    public static function data_list_embed_methods() {
        $list = array();

        foreach (gdrts()->methods as $key => $data) {
            if ($data['autoembed'] && gdrts_is_method_loaded($key)) {
                if (gdrts_method_has_series($key)) {
                    $obj = gdrts_get_method_object($key);

                    foreach ($obj->all_series_list() as $sers => $label) {
                        $list[$key.'::'.$sers] = $data['label'].' &minus; '.$label;
                    }
                } else {
                    $list[$key] = $data['label'];
                }
            }
        }

        return $list;
    }

    public static function data_list_style_image_name() {
        return gdrts_base_data::stars_style_image_name();
    }

    public static function data_list_style_type() {
        return gdrts_base_data::stars_style_type();
    }

    public static function data_list_rating_value() {
        return apply_filters('gdrts_list_stars-rating_rating_value', array(
            'average' => __("Average", "gd-rating-system")
        ));
    }

    public static function data_list_orderby() {
        return apply_filters('gdrts_list_stars-rating_orderby', array(
            'rating' => __("Average Rating", "gd-rating-system"),
            'votes' => __("Votes", "gd-rating-system"),
            'item_id' => __("Item ID", "gd-rating-system"),
            'id' => __("Object ID", "gd-rating-system"),
            'latest' => __("Latest Vote", "gd-rating-system")
        ));
    }

    public static function data_list_order() {
        return array(
            'DESC' => __("Descending", "gd-rating-system"),
            'ASC' => __("Ascending", "gd-rating-system")
        );
    }

    public static function data_list_stars() {
        $list = array();

        for ($i = 1; $i < 26; $i++) {
            $list[$i] = $i.' '._n("star", "stars", $i, "gd-rating-system");
        }

        return $list;
    }

    public static function data_list_templates($method, $type = 'single') {
        if (gdrts_is_method_valid($method) && gdrts_is_template_type_valid($type)) {
            $templates = gdrts_settings()->get($method, 'templates');

            if (!isset($templates[$type]) || empty($templates[$type])) {
                gdrts_rescan_for_templates();

                $templates = gdrts_settings()->get($method, 'templates');
            }

            return $templates[$type];
        } else {
            return array();
        }
    }

    public static function data_list_distributions() {
        return array(
            'normalized' => __("Normalized", "gd-rating-system"),
            'exact' => __("Exact", "gd-rating-system")
        );
    }

    public static function data_list_resolutions() {
        return array(
            100 => __("100% - Full Star", "gd-rating-system"),
            50 => __("50% - Half Star", "gd-rating-system"),
            25 => __("25% - One Quarter Star", "gd-rating-system"),
            20 => __("20% - One Fifth Star", "gd-rating-system"),
            10 => __("10% - One Tenth Star", "gd-rating-system")
        );
    }

    public static function data_list_vote() {
        return array(
            'single' => __("Single vote only", "gd-rating-system"),
            'revote' => __("Single vote with revote", "gd-rating-system"),
            'multi' => __("Multiple votes", "gd-rating-system")
        );
    }

    public static function data_list_align() {
        return array(
            'none' => __("No alignment", "gd-rating-system"),
            'left' => __("Left", "gd-rating-system"),
            'center' => __("Center", "gd-rating-system"),
            'right' => __("Right", "gd-rating-system")
        );
    }
}
