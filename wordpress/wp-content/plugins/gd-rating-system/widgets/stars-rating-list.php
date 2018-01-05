<?php

if (!defined('ABSPATH')) { exit; }

class gdrtsWidget_stars_rating_list extends gdrts_widget_core {
    public $rating_method = 'stars-rating';
    public $widget_code = 'stars_rating_list';
    public $widget_base = 'gdrts_stars_rating_list';

    public $defaults = array(
        'title' => 'Top Ratings',
        'type' => 'posts.post',
        'orderby' => 'rating', 
        'order' => 'DESC', 
        'limit' => 5, 
        'rating_min' => 0, 
        'votes_min' => 0, 
        'template' => 'widget',
        'style_type' => '',
        'style_image_name' => '',
        'style_size' => 20,
        'font_color_empty' => '', 
        'font_color_current' => '',
        'style_class' => ''
    );

    public function __construct($id_base = false, $name = "", $widget_options = array(), $control_options = array()) {
        $this->widget_description = __("Show Stars Rating list.", "gd-rating-system");
        $this->widget_name = 'GD Rating System: '.__("Stars Rating List", "gd-rating-system");

        parent::__construct($this->widget_base, $this->widget_name, array(), array('width' => 500));
    }

    public function widget($args, $instance) {
        if (!$this->_method_available()) {
            return;
        }

        parent::widget($args, $instance);
    }

    public function form($instance) {
        if ($this->_form_available()) {
            $instance = wp_parse_args((array)$instance, $this->get_defaults());

            $_tabs = array(
                'global' => array('name' => __("Global", "gd-rating-system"), 'include' => array('shared-global', 'shared-display')),
                'content' => array('name' => __("Content", "gd-rating-system"), 'include' => array('stars-rating-list-content')),
                'display' => array('name' => __("Display", "gd-rating-system"), 'include' => array('stars-rating-list-display')),
                'extra' => array('name' => __("Extra", "gd-rating-system"), 'include' => array('shared-wrapper'))
            );

            include(GDRTS_PATH.'forms/widgets/shared-loader.php');
        }
    }

    public function update($new_instance, $old_instance) {
        if (!$this->_method_available()) {
            return $old_instance;
        }

        $instance = $this->_shared_update($new_instance, $old_instance);

        $instance['type'] = d4p_sanitize_basic($new_instance['type']);
        $instance['orderby'] = d4p_sanitize_basic($new_instance['orderby']);
        $instance['order'] = d4p_sanitize_basic($new_instance['order']);

        $instance['limit'] = intval($new_instance['limit']);
        $instance['rating_min'] = intval($new_instance['rating_min']);
        $instance['votes_min'] = intval($new_instance['votes_min']);

        $instance['template'] = d4p_sanitize_basic($new_instance['template']);
        $instance['style_class'] = d4p_sanitize_basic($new_instance['style_class']);
        $instance['style_type'] = d4p_sanitize_basic($new_instance['style_type']);
        $instance['style_image_name'] = d4p_sanitize_basic($new_instance['style_image_name']);
        $instance['style_size'] = intval($new_instance['style_size']);

        $instance['font_color_empty'] = d4p_sanitize_basic($new_instance['font_color_empty']);
        $instance['font_color_current'] = d4p_sanitize_basic($new_instance['font_color_current']);

        return apply_filters('gdrts_widget_settings_save', $instance, $new_instance, $this->widget_code, $this->rating_method);
    }

    public function render($results, $instance) {
        gdrts()->load_embed();

        $instance = wp_parse_args((array)$instance, $this->get_defaults());

        echo _gdrts_widget_render_header($instance, $this->widget_base);

        echo _gdrts_embed_stars_rating_list($instance);

        echo _gdrts_widget_render_footer($instance);
    }
}
