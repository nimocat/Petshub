<?php

if (!defined('ABSPATH')) { exit; }

function gdrts_render_rating($args = array(), $method = array()) {
    return gdrts_single()->render((array)$args, (array)$method);
}

function gdrts_render_ratings_list($args = array(), $method = array()) {
    return gdrts_list()->render((array)$args, (array)$method);
}

function gdrts_query_ratings($method = 'stars-rating', $args = array()) {
    return gdrts_query()->run($method, $args);
}

function gdrts_get_rating_item_by_id($item_id) {
    return gdrts_rating_item::get_instance($item_id);
}

function gdrts_get_user_rating($user_id) {
    if ($user_id < 1) {
        return new WP_Error('user-missing', __("User ID is required, and it has to be for valid registered user.", "gd-rating-system"));
    } else {
        return gdrts_core_user_rating::get_instance($user_id);
    }
}

function gdrts_get_current_user_rating() {
    if (is_user_logged_in()) {
        return gdrts_get_user_rating(get_current_user_id());
    } else {
        return gdrts_core_visitor_rating::get_instance();
    }
}

function gdrts_get_rating_item_by_post($post = null) {
    if (is_null($post)) {
        global $post;
    }

    if (is_null($post)) {
        return false;
    }

    return gdrts_rating_item::get_instance(null, 'posts', $post->post_type, $post->ID);
}

function gdrts_get_rating_item($args) {
    $defaults = array(
        'entity' => null, 
        'name' => null, 
        'item_id' => null,
        'id' => null
    );

    $atts = shortcode_atts($defaults, $args);

    return gdrts_rating_item::get_instance($atts['item_id'], $atts['entity'], $atts['name'], $atts['id']);
}

function gdrts_read_cookies() {
    $key = gdrts()->cookie_key();
    $raw = isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';

    $cookie = array();

    if ($raw != '') {
        $raw = stripslashes($raw);

       $cookie = json_decode($raw);

        if (!empty($cookie)) {
            $cookie = array_map('intval', $cookie);
            $cookie = array_filter($cookie);
        }
    }

    return $cookie;
}

function gdrts_return_template($templates) {
    ob_start();

    gdrts()->flush_debug_queue();

    gdrts_load_template($templates, true);

    $result = ob_get_contents();

    ob_end_clean();

    return $result;
}

function gdrts_load_template($templates, $load = true) {
    $theme = array();

    foreach ($templates as $template) {
        $theme[] = 'gdrts/'.$template;
        $theme[] = $template;
    }

    $found = locate_template($theme, false);

    if (empty($found)) {
        $storages = gdrts()->default_storages_paths();

        foreach ($templates as $template) {
            foreach ($storages as $path) {
                if (file_exists($path.$template)) {
                    $found = $path.$template;
                    break 2;
                }
            }
        }
    }

    if (empty($found)) {
        return null;
    }

    if ($load) {
        include($found);
    } else {
        return $found;
    }
}

function gdrts_is_addon_loaded($name) {
    return in_array('addon_'.$name, gdrts()->loaded);
}

function gdrts_is_method_loaded($name) {
    return in_array('method_'.$name, gdrts()->loaded);
}

function gdrts_is_method_valid($method) {
    return isset(gdrts()->methods[$method]);
}

function gdrts_method_has_series($method) {
    $obj = gdrts_get_method_object($method);

    if (is_null($obj)) {
        return false;
    } else {
        return $obj->has_series();
    }
}

function gdrts_method_allows_multiple_votes($method) {
    $obj = gdrts_get_method_object($method);

    if (is_null($obj)) {
        return false;
    } else {
        return $obj->allows_multiple_votes();
    }
}

function gdrts_is_template_type_valid($type) {
    return in_array($type, array('single', 'list'));
}

function gdrts_register_entity($entity, $label, $types = array(), $icon = 'ticket') {
    gdrts()->register_entity($entity, $label, $types, $icon);
}

function gdrts_register_type($entity, $name, $label) {
    gdrts()->register_type($entity, $name, $label);
}

function gdrts_register_addon($name, $label, $override = false, $autoload = true) {
    if (!isset(gdrts()->addons[$name])) {
        gdrts()->addons[$name] = array(
            'label' => $label, 
            'override' => $override, 
            'autoload' => $autoload);
    }
}

function gdrts_register_method($name, $label, $override = false, $autoembed = true, $autoload = true, $review = false) {
    if (!isset(gdrts()->methods[$name])) {
        gdrts()->methods[$name] = array(
            'label' => $label, 
            'override' => $override, 
            'autoembed' => $autoembed, 
            'autoload' => $autoload, 
            'review' => $review);
    }
}

function gdrts_register_font($name, $object) {
    if (!isset(gdrts()->fonts[$name])) {
        gdrts()->fonts[$name] = $object;
    }
}

function gdrts_load_object_data($entity, $name, $id) {
    $data = apply_filters('gdrts_object_data_'.$entity.'_'.$name, null, $id);

    if (is_null($data)) {
        switch ($entity) {
            case 'posts':
                $data = new gdrts_item_post($entity, $name, $id);
                break;
            case 'terms':
                $data = new gdrts_item_term($entity, $name, $id);
                break;
            case 'comments':
                $data = new gdrts_item_comment($entity, $name, $id);
                break;
            case 'users':
                $data = new gdrts_item_user($entity, $name, $id);
                break;
            default:
            case 'custom':
                $data = new gdrts_item_custom($entity, $name, $id);
                break;
        }
    }

    return $data;
}

function gdrts_print_debug_info($value) {
    $render = $value;

    if (is_array($value) || is_object($value)) {
        $render = '';

        foreach ($value as $key => $val) {
            $render.= $key.' => '.gdrts_print_debug_info($val).', ';
        }

        if (!empty($render)) {
            $render = substr($render, 0, strlen($render) - 2);
        }
    } else if (is_bool($value)) {
        $render = $value ? 'TRUE' : 'FALSE';
    } else if (is_null($value)) {
        $render = 'NULL';
    } else if (is_string($value)) {
        $render = "'".$value."'";
    }

    return $render;
}

function gdrts_get_method_object($method) {
    if (gdrts_is_method_loaded($method)) {
        switch ($method) {
            case 'stars-rating':
                return gdrtsm_stars_rating();
            default:
                return apply_filters('gdrts_get_method_object_'.$method, null);
        }
    }

    return null;
}

function gdrts_get_method_label($method) {
    if (gdrts_is_method_loaded($method)) {
        return gdrts()->methods[$method]['label'];
    } else {
        return $method;
    }
}

function gdrts_list_all_method($include_series = false) {
    $items = array();

    foreach (gdrts()->methods as $method => $obj) {
        if (gdrts_is_method_loaded($method)) {
            $items[$method] = $obj['label'];
        }
    }

    return $items;
}

function gdrts_list_all_entities() {
    $items = array();

    foreach (gdrts()->get_entities() as $entity => $obj) {
        $rule = array(
            'title' => $obj['label'],
            'values' => array(
                $entity => sprintf(__("All %s Types", "gd-rating-system"), $obj['label'])
            )
        );

        foreach ($obj['types'] as $name => $label) {
            $rule['values'][$entity.'.'.$name] = $label;
        }

        $items[] = $rule;
    }

    return $items;
}
