<?php

/*
Name:    d4pLib - Functions - Cache - WordPress
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

if (!function_exists('wp_flush_rewrite_rules')) {
    function wp_flush_rewrite_rules() {
        global $wp_rewrite;

        $wp_rewrite->flush_rules();
    }
}

if (!function_exists('d4p_cache_flush')) {
    /** @global wpdb $wpdb */
    function d4p_cache_flush($cache = true, $queries = true) {
        if ($cache) {
            wp_cache_flush();
        }

        if ($queries) {
            global $wpdb;

            if (is_array($wpdb->queries) && !empty($wpdb->queries)) {
                unset($wpdb->queries);
                $wpdb->queries = array();
            }
        }
    }
}

if (!function_exists('d4p_posts_cache_by_ids')) {
    /** @global wpdb $wpdb */
    function d4p_posts_cache_by_ids($posts) {
        global $wpdb;

        $posts = _get_non_cached_ids($posts, 'posts');
        $posts = array_filter($posts);

        if (!empty($posts)) {
            $sql = 'SELECT * FROM '.$wpdb->posts.' WHERE ID IN ('.join(',', (array)$posts).')';
            $raw = $wpdb->get_results($sql);

            foreach ($raw as $_post) {
                $_post = sanitize_post($_post, 'raw');
                wp_cache_add($_post->ID, $_post, 'posts');
            }
        }
    }
}
