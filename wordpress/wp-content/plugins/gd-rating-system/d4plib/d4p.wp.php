<?php

/*
Name:    d4pLib_WP_Functions
Version: v2.2.1
Author:  Milan Petrovic
Email:   milan@gdragon.info
Website: https://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2017 Milan Petrovic (email: milan@gdragon.info)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!function_exists('is_wplogin_page')) {
    function is_wplogin_page() {
        return $GLOBALS['pagenow'] == 'wp-login.php';
    }
}

if (!function_exists('is_wpsignup_page')) {
    function is_wpsignup_page() {
        return $GLOBALS['pagenow'] == 'wp-signup.php';
    }
}

if (!function_exists('is_wpactivate_page')) {
    function is_wpactivate_page() {
        return $GLOBALS['pagenow'] == 'wp-activate.php';
    }
}

if (!function_exists('is_posts_page')) {
    function is_posts_page() {
        global $wp_query;

        return $wp_query->is_posts_page;
    }
}

if (!function_exists('is_any_tax')) {
    function is_any_tax() {
        return is_tag() || 
               is_tax() || 
               is_category();
    }
}

if (!function_exists('d4p_admin_enqueue_defaults')) {
    function d4p_admin_enqueue_defaults() {
        wp_enqueue_script('jquery');

        wp_enqueue_script('wpdialogs');
        wp_enqueue_style('wp-jquery-ui-dialog');

        d4p_enqueue_color_picker();

        wp_enqueue_media();
    }
}

if (!function_exists('d4p_enqueue_color_picker')) {
    function d4p_enqueue_color_picker() {
        if (is_admin()) {
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');
        } else {
            $suffix = SCRIPT_DEBUG ? '' : '.min';

            wp_enqueue_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'));
            wp_enqueue_script('wp-color-picker', admin_url("js/color-picker$suffix.js"), array('iris'));
            wp_enqueue_style('wp-color-picker', admin_url("css/color-picker$suffix.css"));

            wp_localize_script('wp-color-picker', 'wpColorPickerL10n', array(
                'clear' => __("Clear", "d4plib"),
                'defaultString' => __("Default", "d4plib"),
                'pick' => __("Select Color", "d4plib"),
                'current' => __("Current Color", "d4plib")
            ));
        }
    }
}

if (!function_exists('d4p_gmt_offset')) {
    function d4p_gmt_offset() {
        $offset = get_option('gmt_offset');

        if (empty($offset)) {
            $offset = wp_timezone_override_offset();
        }

        return $offset === false ? 0 : $offset;
    }
}

if (!function_exists('d4p_is_current_user_roles')) {
    function d4p_is_current_user_roles($roles = array()) {
        $current = d4p_current_user_roles();
        $roles = (array)$roles;

        if (is_array($current) && !empty($roles)) {
            $match = array_intersect($roles, $current);

            return !empty($match);
        } else {
            return false;
        }
    }
}

if (!function_exists('d4p_current_user_roles')) {
    function d4p_current_user_roles() {
        if (is_user_logged_in()) {
            global $current_user;

            return (array)$current_user->roles;
        } else {
            return array();
        }
    }
}

if (!function_exists('d4p_is_current_user_admin')) {
    function d4p_is_current_user_admin() {
        return d4p_is_current_user_roles('administrator');
    }
}

if (!function_exists('d4p_switch_to_default_theme')) {
    function d4p_switch_to_default_theme() {
        switch_theme(WP_DEFAULT_THEME, WP_DEFAULT_THEME);
    }
}

if (!function_exists('d4p_get_post_excerpt')) {
    function d4p_get_post_excerpt($post, $word_limit = 50) {
        $content = $post->post_excerpt == '' ? $post->post_content : $post->post_excerpt;

        $content = strip_shortcodes($content);
        $content = str_replace(']]>', ']]&gt;', $content);
        $content = strip_tags($content);

        $words = explode(' ', $content, $word_limit + 1);

        if (count($words) > $word_limit) {
            array_pop($words);
            $content = implode(' ', $words);
            $content.= '...';
        }

        return $content;
    }
}

if (!function_exists('d4p_get_post_content')) {
    function d4p_get_post_content($post) {
        $content = $post->post_content;

        if (post_password_required($post)) {
            $content = get_the_password_form($post);
        }

        $content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);

        return $content;
    }
}

if (!function_exists('d4p_get_thumbnail_url')) {
    function d4p_get_thumbnail_url($post_id, $size = 'full') {
        if (has_post_thumbnail($post_id)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);

            return $image[0];
        } else {
            return '';
        }
    }
}

if (!function_exists('get_the_slug')) {
    function get_the_slug() {
        $post = get_post();
        return !empty($post) ? $post->post_name : false;
    }
}

if (!function_exists('d4p_list_post_types')) {
    function d4p_list_post_types($args = array()) {
        $list = array();
        $post_types = get_post_types($args, 'objects');

        foreach ($post_types as $cpt => $obj) {
            $list[$cpt] = $obj->labels->name;
        }

        return $list;
    }
}

if (!function_exists('d4p_list_taxonomies')) {
    function d4p_list_taxonomies($args = array()) {
        $list = array();
        $taxonomies = get_taxonomies($args, 'objects');

        foreach ($taxonomies as $tax => $obj) {
            $list[$tax] = $obj->labels->name;
        }

        return $list;
    }
}

if (!function_exists('d4p_list_user_roles')) {
    function d4p_list_user_roles() {
        $roles = array();

        foreach (wp_roles()->roles as $role => $details) {
            $roles[$role] = translate_user_role($details['name']);
        }

        return $roles;
    }
}

if (!function_exists('d4p_next_scheduled')) {
    function d4p_next_scheduled($hook, $args = null) {
        if (!is_null($args)) {
            return wp_next_scheduled($hook, $args);
        } else {
            $crons = _get_cron_array();

            if (empty($crons)) {
		return false;
            }

            $t = -1;
            foreach ($crons as $timestamp => $cron) {
		if (isset($cron[$hook])) {
                    if ($t == -1 || $timestamp < $t) {
                        $t = $timestamp;
                    }
                }
            }

            return $t == -1 ? false : $t;
        }
    }
}

if (!function_exists('d4p_delete_cron_job')) {
    function d4p_delete_cron_job($timestamp, $hook, $hash) {
        $crons = _get_cron_array();

        if (!empty($crons)) {
            $save = false;

            if (is_array($hash) || is_object($hash)) {
                $hash = md5(serialize($hash));
            }

            if (isset($crons[$timestamp]) && isset($crons[$timestamp][$hook]) && isset($crons[$timestamp][$hook][$hash])) {
                unset($crons[$timestamp][$hook][$hash]);
                $save = true;

                if (empty($crons[$timestamp][$hook])) {
                    unset($crons[$timestamp][$hook]);

                    if (empty($crons[$timestamp])) {
                        unset($crons[$timestamp]);
                    }
                }
            }

            if ($save) {
                _set_cron_array($crons);
            }
        }
    }
}

if (!function_exists('d4p_remove_cron')) {
    function d4p_remove_cron($hook) {
        $crons = _get_cron_array();

        if (!empty($crons)) {
            $save = false;

            foreach ($crons as $timestamp => $cron) {
                if (isset($cron[$hook])) {
                    unset($crons[$timestamp][$hook]);
                    $save = true;

                    if (empty($crons[$timestamp])) {
                        unset($crons[$timestamp]);
                    }
                }
            }

            if ($save) {
                _set_cron_array($crons);
            }
        }
    }
}

if (!function_exists('d4p_html_excerpt')) {
    function d4p_html_excerpt($text, $limit, $more = null) {
        return wp_html_excerpt(strip_shortcodes($text), $limit, $more);
    }
}

if (!function_exists('d4p_check_ajax_referer')) {
    function d4p_check_ajax_referer($action, $nonce, $die = true) {
        $result = wp_verify_nonce($nonce, $action);

        if ($die && false === $result) {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                wp_die(-1);
            } else {
                die('-1');
            }
        }

        do_action('check_ajax_referer', $action, $result);

        return $result;
    }
}

if (!function_exists('d4p_permalinks_enabled')) {
    function d4p_permalinks_enabled() {
        return get_option('permalink_structure');
    }
}

if (!function_exists('d4p_json_encode')) {
    function d4p_json_encode($data, $options = 0, $depth = 512) {
        if (function_exists('wp_json_encode') ) {
            return wp_json_encode($data, $options, $depth);
        } else {
            return json_encode($data, $options, $depth);
        }
    }
}

if (!function_exists('d4p_is_user_allowed')) {
    function d4p_is_user_allowed($super_admin, $user_roles, $visitor) {
        if (is_super_admin()) {
            return $super_admin;
        } else if (is_user_logged_in()) {
            $allowed = $user_roles;

            if ($allowed === true || is_null($allowed)) {
                return true;
            } else if (is_array($allowed) && empty($allowed)) {
                return false;
            } else if (is_array($allowed) && !empty($allowed)) {
                global $current_user;

                if (is_array($current_user->roles)) {
                    $matched = array_intersect($current_user->roles, $allowed);

                    return !empty($matched);
                }
            }
        } else {
            return $visitor;
        }
    }
}

if (!function_exists('d4p_is_plugin_active_for_network')) {
    function d4p_is_plugin_active_for_network($plugin) {
        if (!is_multisite()) {
            return false;
        }

        $plugins = get_site_option( 'active_sitewide_plugins');

        return isset($plugins[$plugin]);
    }
}

if (!function_exists('d4p_is_plugin_active')) {
    function d4p_is_plugin_active($plugin) {
        return in_array($plugin, (array)get_option('active_plugins', array())) || d4p_is_plugin_active_for_network($plugin);
    }
}

if (!function_exists('d4p_post_type_has_archive')) {
    function d4p_post_type_has_archive($post_type) {
        if (post_type_exists($post_type)) {
            $cpt = get_post_type_object($post_type);

            return $cpt->has_archive !== false;
        } else {
            return false;
        }
    }
}

if (!function_exists('d4p_is_login_page')) {
    function d4p_is_login_page($action = '') {
        $login_page = in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));

        if ($login_page) {
            if ($action != '') {
                $real_action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
                return $real_action == $action;
            }

            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('d4p_get_attachment_id_from_url')) {
    /*
     * Function by Micah Wood
     * https://wpscholar.com/blog/get-attachment-id-from-wp-image-url/
     */
    function d4p_get_attachment_id_from_url($url) {
	$attachment_id = 0;
	$dir = wp_upload_dir();

	if (false !== strpos($url, $dir['baseurl'].'/')) {
            $file = basename($url);
            $query_args = array(
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'value' => $file,
                        'compare' => 'LIKE',
                        'key' => '_wp_attachment_metadata'
                    )
                )
            );

            $query = new WP_Query($query_args);

            if ($query->have_posts()) {
                foreach ($query->posts as $post_id) {
                    $meta = wp_get_attachment_metadata($post_id);

                    $original_file = basename($meta['file']);
                    $cropped_image_files = wp_list_pluck($meta['sizes'], 'file');

                    if ($original_file === $file || in_array($file, $cropped_image_files)) {
                        $attachment_id = $post_id;
                        break;
                    }
                }
            }
	}

	return $attachment_id;
    }
}

if (!function_exists('d4p_add_filter')) {
    function d4p_add_filter($tags, $function_to_add, $priority = 10, $accepted_args = 1) {
	$tags = (array)$tags;

        foreach ($tags as $tag) {
            add_filter($tag, $function_to_add, $priority, $accepted_args);
        }
    }
}

if (!function_exists('d4p_add_action')) {
    function d4p_add_action($tags, $function_to_add, $priority = 10, $accepted_args = 1) {
	$tags = (array)$tags;

        foreach ($tags as $tag) {
            add_action($tag, $function_to_add, $priority, $accepted_args);
        }
    }
}

if (!function_exists('d4p_is_oembed_link')) {
    function d4p_is_oembed_link($url) {
        require_once(ABSPATH.WPINC.'/class-oembed.php');

        $oembed = _wp_oembed_get_object();
        $result = $oembed->get_html($url);

        return $result === false ? false : true;
    }
}

if (!function_exists('wp_redirect_self')) {
    function wp_redirect_self() {
        wp_redirect($_SERVER['REQUEST_URI']);
    }
}

if (!function_exists('wp_redirect_referer')) {
    function wp_redirect_referer() {
        wp_redirect($_REQUEST['_wp_http_referer']);
    }
}

if (!function_exists('wp_get_attachment_image_url')) {
    function wp_get_attachment_image_url($attachment_id, $size = 'thumbnail', $icon = false) {
        $image = wp_get_attachment_image_src($attachment_id, $size, $icon);

        return isset($image['0']) ? $image['0'] : false;
    }
}
