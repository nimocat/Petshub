<?php

if (!defined('ABSPATH')) { exit; }

function _gdrts_widget_render_header($instance, $widget_base, $base_class = '') {
    $class = array('gdrts-widget-wrapper');
    $class[] = str_replace('_', '-', $widget_base);

    if ($base_class != '') {
        $class[] = $base_class;
    }

    if ($instance['_class'] != '') {
        $class[] = $instance['_class'];
    }

    $render = '<div class="'.join(' ', $class).'">'.D4P_EOL;

    if ($instance['before'] != '') {
        $render.= '<div class="gdrts-widget-before">'.$instance['before'].'</div>';
    }

    return $render;
}

function _gdrts_widget_render_footer($instance) {
    $render = '';

    if ($instance['after'] != '') {
        $render.= '<div class="gdrts-widget-after">'.$instance['after'].'</div>';
    }

    $render.= '</div>';

    return $render;
}

function _gdrts_helper_clean_call_args($args) {
    if ($args['type'] != '') {
        $_type_name = explode('.', $args['type']);

        if (count($_type_name) == 2) {
            $args['entity'] = $_type_name[0];
            $args['name'] = $_type_name[1];
        }
    }

    unset($args['type']);

    $args['echo'] = false;

    return $args;
}

function _gdrts_helper_clean_call_list_args($args) {
    if (!isset($args['object'])) {
        $args['object'] = array();
    }

    foreach (array('terms', 'author') as $_key) {
        if (isset($args[$_key])) {
            if (!is_array($args[$_key])) {
                $args[$_key] = explode(',', $args[$_key]);
                $args[$_key] = array_map('absint', $args[$_key]);
                $args[$_key] = array_filter($args[$_key]);
            }
        } else {
            $args[$_key] = array();
        }

        $args['object'][$_key] = $args[$_key];

        unset($args[$_key]);
    }

    foreach (array('status', 'meta') as $_key) {
        if (!isset($args[$_key])) {
            $args[$_key] = $_key == 'status' ? array('publish', 'closed', 'inherit') : array();
        }

        $args['object'][$_key] = $args[$_key];

        unset($args[$_key]);
    }

    return _gdrts_helper_clean_call_args($args);
}

function _gdrts_helper_clean_call_method($args) {
    if (isset($args['style_type']) && !empty($args['style_type'])) {
        $_type_name = 'style_'.$args['style_type'].'_name';

        $args['style_name'] = $args[$_type_name];
    }

    $call_method = array();

    foreach ($args as $key => $value) {
        if ($value != '') {
            $call_method[$key] = $value;
        }
    }

    return $call_method;
}

function _gdrts_embed_stars_rating($atts) {
    $defaults_atts = array(
        'type' => '',
        'entity' => '',
        'name' => '',
        'id' => 0,
        'item_id' => 0
    );

    $defaults_method = apply_filters('gdrts_embed_function_defaults_method', array(
        'class' => '',
        'template' => '',
        'alignment' => '',
        'distribution' => '',
        'style_type' => '',
        'style_image_name' => '',
        'style_size' => '',
        'font_color_empty' => '', 
        'font_color_current' => '', 
        'font_color_active' => '',
        'style_class' => ''
    ), 'stars_rating');

    $call_args = shortcode_atts($defaults_atts, $atts);
    $call_args = _gdrts_helper_clean_call_args($call_args);

    $call_method = shortcode_atts($defaults_method, $atts);
    $call_method = _gdrts_helper_clean_call_method($call_method);

    $call_args['method'] = 'stars-rating';

    if (gdrts_debug_on()) {
        gdrts()->debug_queue($call_args, 'stars-rating - args, embed');
        gdrts()->debug_queue($call_method, 'stars-rating - method, embed');
    }

    return gdrts_render_rating($call_args, $call_method);
}

function _gdrts_embed_stars_rating_auto($atts) {
    $atts['id'] = get_post()->ID;
    $atts['type'] = 'posts.'.get_post()->post_type;

    return _gdrts_embed_stars_rating($atts);
}

function _gdrts_embed_stars_rating_list($atts) {
    $defaults_atts = array(
        'type' => '',
        'entity' => '',
        'name' => '',
        'id__in' => array(),
        'id__not_in' => array(),
        'orderby' => 'rating',
        'order' => 'DESC',
        'offset' => 0,
        'limit' => 5,
        'return' => 'objects',
        'rating_min' => 0,
        'votes_min' => 0,
        'period' => false,
        'source' => ''
    );

    $defaults_method = apply_filters('gdrts_embed_function_defaults_method', array(
        'template' => '',
        'style_type' => '',
        'style_image_name' => '',
        'style_size' => '',
        'font_color_empty' => '', 
        'font_color_current' => '', 
        'font_color_active' => '',
        'style_class' => ''
    ), 'stars_rating_list');

    $call_args = shortcode_atts($defaults_atts, $atts);
    $call_args = _gdrts_helper_clean_call_list_args($call_args);

    $call_method = shortcode_atts($defaults_method, $atts);
    $call_method = _gdrts_helper_clean_call_method($call_method);

    $call_args['method'] = 'stars-rating';

    if (gdrts_debug_on()) {
        gdrts()->debug_queue($call_args, 'stars-rating-list - args, embed');
        gdrts()->debug_queue($call_method, 'stars-rating-list - method, embed');
    }

    return gdrts_render_ratings_list($call_args, $call_method);
}
