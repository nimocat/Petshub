<?php

/*
Name:    d4pLib_Sanitize
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

if (!function_exists('d4p_sanitize_key_expanded')) {
    function d4p_sanitize_key_expanded($key) {
        $key = strtolower($key);
	$key = preg_replace('/[^a-z0-9._\-]/', '', $key);

        return $key;
    }
}

if (!function_exists('d4p_sanitize_extended')) {
    function d4p_sanitize_extended($text, $tags = null, $protocols = array(), $strip_shortcodes = false) {
        $tags = is_null($tags) ? wp_kses_allowed_html('post') : $tags;
        $text = stripslashes($text);

        if ($strip_shortcodes) {
            $text = strip_shortcodes($text);
        }

        return wp_kses(trim($text), $tags, $protocols);
    }
}

if (!function_exists('d4p_sanitize_basic')) {
    function d4p_sanitize_basic($text, $strip_shortcodes = true) {
        $text = stripslashes($text);

        if ($strip_shortcodes) {
            $text = strip_shortcodes($text);
        }

        return trim(wp_kses($text, array()));
    }
}

if (!function_exists('d4p_sanitize_html')) {
    function d4p_sanitize_html($text, $tags = null, $protocols = array()) {
        $tags = is_null($tags) ? wp_kses_allowed_html('post') : $tags;

        return wp_kses(trim(stripslashes($text)), $tags, $protocols);
    }
}

if (!function_exists('d4p_sanitize_slug')) {
    function d4p_sanitize_slug($text) {
        return trim(sanitize_title_with_dashes(stripslashes($text)), "-_ \t\n\r\0\x0B");
    }
}

if (!function_exists('d4p_sanitize_html_classes')) {
    function d4p_sanitize_html_classes($classes) {
        $list = explode(' ', trim(stripslashes($classes)));
        $list = array_map('sanitize_html_class', $list);

        return trim(join(' ', $list));
    }
}

if (!function_exists('d4p_sanitize_basic_array')) {
    function d4p_sanitize_basic_array($input, $strip_shortcodes = true) {
        $output = array();

        foreach ($input as $key => $value) {
            $output[$key] = d4p_sanitize_basic($value);
        }

        return $output;
    }
}

if (!function_exists('d4p_ids_from_string')) {
    function d4p_ids_from_string($input, $delimiter = ',', $map = 'absint') {
        $ids = strip_tags(stripslashes($input));

        $ids = explode($delimiter, $ids);
        $ids = array_map('trim', $ids);
        $ids = array_map($map, $ids);
        $ids = array_filter($ids);

        return $ids;
    }
}

if (!function_exists('d4p_kses_expanded_list_of_tags')) {
    function d4p_kses_expanded_list_of_tags() {
        return array(
            'a' => array(
                'class' => true,
                'href' => true,
                'title' => true,
                'rel' => true,
                'class' => true,
                'style' => true,
                'target' => true
            ),
            'abbr' => array(),
            'blockquote' => array(
                'class' => true,
                'style' => true,
                'cite' => true
            ),
            'div' => array(
                'class' => true,
                'style' => true
            ),
            'span' => array(
                'class' => true,
                'style' => true
            ),
            'code' => array(
                'class' => true,
                'style' => true
            ),
            'pre' => array(
                'class' => true,
                'style' => true
            ),
            'em' => array(
                'class' => true,
                'style' => true
            ),
            'i' => array(
                'class' => true,
                'style' => true
            ),
            'b' => array(
                'class' => true,
                'style' => true
            ),
            'strong' => array(
                'class' => true,
                'style' => true
            ),
            'del' => array(
                'datetime' => true,
                'class' => true,
                'style' => true
            ),
            'h1' => array(
                'align' => true,
                'class' => true,
                'style' => true
            ),
            'h2' => array(
                'align' => true,
                'class' => true,
                'style' => true
            ),
            'h3' => array(
                'align' => true,
                'class' => true,
                'style' => true
            ),
            'h4' => array(
                'align' => true,
                'class' => true,
                'style' => true
            ),
            'h5' => array(
                'align' => true,
                'class' => true,
                'style' => true
            ),
            'h6' => array(
                'align' => true,
                'class' => true,
                'style' => true
            ),
            'ul' => array(
                'class' => true,
                'style' => true
            ),
            'ol' => array(
                'class' => true,
                'style' => true,
                'start' => true
            ),
            'li' => array(
                'class' => true,
                'style' => true
            ),
            'img' => array(
                'class' => true,
                'style' => true,
                'src' => true,
                'border' => true,
                'alt' => true,
                'height' => true,
                'width' => true
            ),
            'table' => array(
                'align' => true,
                'bgcolor' => true,
                'border' => true,
                'class' => true,
                'style' => true
            ),
            'tbody' => array(
                'align' => true,
                'valign' => true,
                'class' => true,
                'style' => true
            ),
            'td' => array(
                'align' => true,
                'valign' => true,
                'class' => true,
                'style' => true
            ),
            'tfoot' => array(
                'align' => true,
                'valign' => true,
                'class' => true,
                'style' => true
            ),
            'th' => array(
                'align' => true,
                'valign' => true,
                'class' => true,
                'style' => true
            ),
            'thead' => array(
                'align' => true,
                'valign' => true,
                'class' => true,
                'style' => true
            ),
            'tr' => array(
                'align' => true,
                'valign' => true,
                'class' => true,
                'style' => true
            )
        );
    }
}
