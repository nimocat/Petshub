<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_shortcake extends gdrts_addon {
    public $prefix = 'shortcake';

    public function _load_admin() {}

    public function init() {
        $this->register();
    }

    public function register() {
        require_once(GDRTS_PATH.'core/admin/shared.php');

        if (gdrts_is_method_loaded('stars-rating')) {
            shortcode_ui_register_for_shortcode('gdrts_stars_rating', array(
                'label' => 'Rating: '.__("Stars Rating", "gd-rating-system"),
                'listItemImage' => 'dashicons-star-filled',
                'attrs' => apply_filters('gdrts_shortcake_attrs_stars_rating', 
                        array_merge($this->_atts_basic_attributes(), 
                                    $this->_atts_stars_rating_method(), 
                                    $this->_atts_css_classes()))
            ));

            shortcode_ui_register_for_shortcode('gdrts_stars_rating_auto', array(
                'label' => 'Rating: '.__("Stars Rating - Auto Item", "gd-rating-system"),
                'listItemImage' => 'dashicons-star-filled',
                'attrs' => apply_filters('gdrts_shortcake_attrs_stars_rating_auto', 
                        array_merge($this->_atts_stars_rating_method(), 
                                    $this->_atts_css_classes()))
            ));

            shortcode_ui_register_for_shortcode('gdrts_stars_rating_list', array(
                'label' => 'Rating: '.__("Stars Rating - Items List", "gd-rating-system"),
                'listItemImage' => 'dashicons-star-filled',
                'attrs' => apply_filters('gdrts_shortcake_attrs_stars_rating_list', 
                        array_merge($this->_atts_list_attributes(), 
                                    $this->_atts_stars_rating_list_method(), 
                                    $this->_atts_css_classes()))
            ));
        }

        do_action('gdrts_shortcake_register');
    }

    public function _atts_basic_attributes() {
        return array(
            array(
                'label' => __("Rating object Entity and Type", "gd-rating-system"),
                'attr' => 'type',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_entity_name_types(),
                'value' => 'posts.post'
            ),
            array(
                'label' => __("Rating object ID", "gd-rating-system"),
                'attr' => 'id',
                'type' => 'number',
                'value' => 0,
                'description' => __("Entity and Type with ID here descirbe rating object.", "gd-rating-system").' '.__("This ID must be integer higher than 0.", "gd-rating-system")
            ),
            array(
                'label' => __("Rating Item ID", "gd-rating-system"),
                'attr' => 'item_id',
                'type' => 'number',
                'value' => 0,
                'description' => __("If you now item ID for rating object, you can use it instead of Rating object entity, type and ID defined above.", "gd-rating-system").' '.__("This ID must be integer higher than 0.", "gd-rating-system")
            )
        );
    }

    public function _atts_list_attributes() {
        return array(
            array(
                'label' => __("Rating object Entity and Type", "gd-rating-system"),
                'attr' => 'type',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_entity_name_types(),
                'value' => 'posts.post'
            ),
            array(
                'label' => __("Order By", "gd-rating-system"),
                'attr' => 'orderby',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_orderby(),
                'value' => 'rating'
            ),
            array(
                'label' => __("Order", "gd-rating-system"),
                'attr' => 'order',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_order(),
                'value' => 'DESC'
            ),
            array(
                'label' => __("Limit", "gd-rating-system"),
                'attr' => 'limit',
                'type' => 'number',
                'value' => 5
            ),
            array(
                'label' => __("Terms ID's (comma separated)", "gd-rating-system"),
                'attr' => 'terms',
                'type' => 'text',
                'description' => __("For posts and custom post types.", "gd-rating-system")
            ),
            array(
                'label' => __("Authors ID's (comma separated)", "gd-rating-system"),
                'attr' => 'author',
                'type' => 'text',
                'description' => __("For posts, pages and custom post types.", "gd-rating-system")
            ),
            array(
                'label' => __("With minimal rating", "gd-rating-system"),
                'attr' => 'rating_min',
                'type' => 'number',
                'value' => 0
            ),
            array(
                'label' => __("With minimal number of votes", "gd-rating-system"),
                'attr' => 'votes_min',
                'type' => 'number',
                'value' => 1
            )
        );
    }

    public function _atts_css_classes() {
        return array(
            array(
                'label' => __("Additional rating block CSS classes (space separated)", "gd-rating-system"),
                'attr' => 'style_class',
                'type' => 'text'
            ),
            array(
                'label' => __("Shortcode wrapper CSS classes (space separated)", "gd-rating-system"),
                'attr' => 'class',
                'type' => 'text'
            )
        );
    }

    private function _atts_stars_rating_method($method = 'stars-rating') {
        return array(
            array(
                'label' => __("Votes Distribution", "gd-rating-system"),
                'attr' => 'distribution',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_distributions(),
                'value' => ''
            ),
            array(
                'label' => __("Template", "gd-rating-system"),
                'attr' => 'template',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_templates($method),
                'value' => ''
            ),
            array(
                'label' => __("Alignment", "gd-rating-system"),
                'attr' => 'alignment',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_align(),
                'value' => ''
            ),
            array(
                'label' => __("Style Type", "gd-rating-system"),
                'attr' => 'style_type',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_style_type(),
                'value' => ''
            ),
            array(
                'label' => __("Image", "gd-rating-system"),
                'attr' => 'style_image_name',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_style_image_name(),
                'value' => ''
            ),
            array(
                'label' => __("Size", "gd-rating-system"),
                'attr' => 'style_size',
                'type' => 'number',
                'value' => 30,
                'description' => __("Size in pixels for each star.", "gd-rating-system")
            ),
            array(
                'label' => __("Font Icon Colors", "gd-rating-system").': '.__("Empty Stars", "gd-rating-system"),
                'attr' => 'font_color_empty',
                'type' => 'color',
                'value' => ''
            ),
            array(
                'label' => __("Font Icon Colors", "gd-rating-system").': '.__("Current Stars", "gd-rating-system"),
                'attr' => 'font_color_current',
                'type' => 'color',
                'value' => ''
            ),
            array(
                'label' => __("Font Icon Colors", "gd-rating-system").': '.__("Active Stars", "gd-rating-system"),
                'attr' => 'font_color_active',
                'type' => 'color',
                'value' => ''
            )
        );
    }

    private function _atts_stars_rating_list_method($method = 'stars-rating') {
        return array(
            array(
                'label' => __("Template", "gd-rating-system"),
                'attr' => 'template',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_templates($method, 'list'),
                'value' => ''
            ),
            array(
                'label' => __("Style Type", "gd-rating-system"),
                'attr' => 'style_type',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_style_type(),
                'value' => 'font'
            ),
            array(
                'label' => __("Image", "gd-rating-system"),
                'attr' => 'style_image_name',
                'type' => 'select',
                'options' => gdrts_admin_shared::data_list_style_image_name(),
                'value' => 'star'
            ),
            array(
                'label' => __("Size", "gd-rating-system"),
                'attr' => 'style_size',
                'type' => 'number',
                'value' => 20,
                'description' => __("Size in pixels for each star.", "gd-rating-system")
            ),
            array(
                'label' => __("Font Icon Colors", "gd-rating-system").': '.__("Empty Stars", "gd-rating-system"),
                'attr' => 'font_color_empty',
                'type' => 'color',
                'value' => ''
            ),
            array(
                'label' => __("Font Icon Colors", "gd-rating-system").': '.__("Current Stars", "gd-rating-system"),
                'attr' => 'font_color_current',
                'type' => 'color',
                'value' => ''
            ),
            array(
                'label' => __("Font Icon Colors", "gd-rating-system").': '.__("Active Stars", "gd-rating-system"),
                'attr' => 'font_color_active',
                'type' => 'color',
                'value' => ''
            )
        );
    }
}

global $_gdrts_addon_shortcake;
$_gdrts_addon_shortcake = new gdrts_addon_shortcake();

function gdrtsa_shortcake() {
    global $_gdrts_addon_shortcake;
    return $_gdrts_addon_shortcake;
}
