<?php

/*
Name:    d4pLib_Functions
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

if (!function_exists('is_odd')) {
    function is_odd($number) {
        return $number&1;
    }
}

if (!function_exists('is_divisible')) {
    function is_divisible($number, $by_number) {
        return $number%$by_number == 0;
    }
}

if (!function_exists('d4p_replace_tags_in_content')) {
    function d4p_replace_tags_in_content($content, $tags) {
        foreach ($tags as $tag => $replace) {
            $_tag = '%'.$tag.'%';

            if (strpos($content, $_tag) !== false) {
                $content = str_replace($_tag, $replace, $content);
            }
        }

        return $content;
    }
}

if (!function_exists('d4p_is_array_associative')) {
    function d4p_is_array_associative($array) {
        return is_array($array) && 
               (0 !== count(array_diff_key($array, array_keys(array_keys($array)))) || count($array) == 0);
    }
}

if (!function_exists('d4p_strleft')) {
    function d4p_strleft($s1, $s2) {
        return substr($s1, 0, strpos($s1, $s2));
    }
}

if (!function_exists('d4p_scan_dir')) {
    function d4p_scan_dir($path, $filter = 'files', $extensions = array(), $reg_expr = '', $full_path = false) {
        $extensions = (array)$extensions;
        $filter = !in_array($filter, array('folders', 'files', 'all')) ? 'files' : $filter;
        $path = str_replace('\\', '/', $path);

        $files = array();
        $final = array();

        if (file_exists($path)) {
            $files = scandir($path);

            $path = rtrim($path, '/').'/';
            foreach ($files as $file) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if (empty($extensions) || in_array($ext, $extensions)) {
                    if (substr($file, 0, 1) != '.') {
                        if ((is_dir($path.$file) && (in_array($filter, array('folders', 'all')))) ||
                            (is_file($path.$file) && (in_array($filter, array('files', 'all')))) ||
                            ((is_file($path.$file) || is_dir($path.$file)) && (in_array($filter, array('all'))))) {
                                $add = $full_path ? $path : '';

                                if ($reg_expr == '') {
                                    $final[] = $add.$file;
                                } else if (preg_match($reg_expr, $file)) {
                                    $final[] = $add.$file;
                                }
                        }
                    }
                }
            }
        }

        return $final;
    }
}

if (!function_exists('d4p_file_size_format')) {
    function d4p_file_size_format($size, $decimals = 2) {
        $_size = intval($size);
        $unit = '';

        if (strlen($_size) <= 9 && strlen($_size) >= 7) {
            $_size = number_format($_size / 1048576, $decimals);
            $unit = 'MB';
        } else if (strlen($_size) >= 10) {
            $_size = number_format($_size / 1073741824, $decimals);
            $unit = 'GB';
        } else if (strlen($_size) <= 6 && strlen($_size) >= 4) {
            $_size = number_format($_size / 1024, $decimals);
            $unit = 'KB';
        } else {
            $unit = 'B';
        }

        if (floatval($_size) == intval($_size)) {
            $_size = intval($_size);
        }

        return $_size.' '.$unit;
    }
}

if (!function_exists('d4p_text_length_limit')) {
    function d4p_text_length_limit($text, $length = 200, $append = '&hellip;') {
        if (function_exists('mb_strlen')) {
            $text_length = mb_strlen($text);
        } else {
            $text_length = strlen($text);
        }

        if (!empty($length) && ($text_length > $length)) {
            $text = substr($text, 0, $length - 1);
            $text.= $append;
        }

        return $text;
    }
}

if (!function_exists('d4p_entity_decode')) {
    function d4p_entity_decode($content, $quote_style = null, $charset = null) {
        if (null === $quote_style) {
            $quote_style = ENT_QUOTES;
        }

        if (null === $charset) {
            $charset = D4P_CHARSET;
        }

        return html_entity_decode($content, $quote_style, $charset);
    }
}

if (!function_exists('d4p_str_replace_first')) {
    function d4p_str_replace_first($search, $replace, $subject) {
        $pos = strpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}

if (!function_exists('d4p_extract_images_urls')) {
    function d4p_extract_images_urls($search, $limit = 0) {
        $images = array();
        $matches = array();

        if (preg_match_all("/<img(.+?)>/", $search, $matches)) {
            foreach ($matches[1] as $image) {
                $match = array();

                if (preg_match( '/src=(["\'])(.*?)\1/', $image, $match)) {
                    $images[] = $match[2];
                }
            }
        }

        if ($limit > 0 && !empty($images)) {
            $images = array_slice($images, 0, $limit);
        }

        if ($limit == 1) {
            return count($images) > 0 ? $images[0] : '';
        } else {
            return $images;
        }
    }
}

if (!function_exists('d4p_gzip_uncompressed_size')) {
    function d4p_gzip_uncompressed_size($file_path) {
        $fp = fopen($file_path, "rb");
        fseek($fp, -4, SEEK_END);
        $buf = fread($fp, 4);
        $elm = unpack("V", $buf);

        return end($elm); 
    }
}

if (!function_exists('d4p_remove_from_array_by_value')) {
    function d4p_remove_from_array_by_value($input, $val, $preserve_keys = true) {
        if (empty($input) || !is_array($input)) {
            return false;
        }

        while (in_array($val, $input)) {
            unset($input[array_search($val, $input)]);
        }

        $output = $preserve_keys ? $input : array_values($input);

        return (array)$output;
    }
}

if (!function_exists('d4p_slug_to_name')) {
    function d4p_slug_to_name($code, $sep = '_') {
        $exp = explode($sep, $code);

        return ucwords(strtolower(join(' ', $exp)));
    }
}

if (!function_exists('d4p_has_gravatar')) {
    function d4p_has_gravatar($email) {
	$hash = md5(strtolower(trim($email)));

	$url = 'http://www.gravatar.com/avatar/'.$hash.'?d=404';
        $headers = get_headers($url);

        return preg_match("/200/", $headers[0]) == 1;
    }
}

if (!function_exists('d4p_is_valid_md5')) {
    function d4p_is_valid_md5($hash ='') {
        return strlen($hash) == 32 && ctype_xdigit($hash);
    }
}

if (!function_exists('d4p_php_ini_size_value')) {
    function d4p_php_ini_size_value($name) {
        $ini = ini_get($name);

        if ($ini === false) {
            return 0;
        }

        $ini = trim($ini);
        $last = strtoupper($ini[strlen($ini) - 1]);
        $ini = absint(substr($ini, 0, strlen($ini) - 1));

        switch($last) {
            case 'G':
                $ini = $ini * GB_IN_BYTES;
                break;
            case 'M':
                $ini = $ini * MB_IN_BYTES;
                break;
            case 'K':
                $ini = $ini * KB_IN_BYTES;
                break;
        }

        return $ini;
    }
}

if (!function_exists('d4p_is_datetime_valid')) {
    function d4p_is_datetime_valid($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }
}

if (!function_exists('d4p_mysql_date')) {
    function d4p_mysql_date($time) {
        return date('Y-m-d H:i:s', $time);
    }
}

if (!function_exists('d4p_mysql_datetime_format')) {
    function d4p_mysql_datetime_format() {
        return 'Y-m-d H:i:s';
    }
}

if (!function_exists('d4p_url_campaign_tracking')) {
    function d4p_url_campaign_tracking($url, $campaign = '', $medium = '', $content = '', $term = '', $source = null) {
        if (!empty($campaign)) {
            $url = add_query_arg('utm_campaign', $campaign, $url);
        }

        if (!empty($medium)) {
            $url = add_query_arg('utm_medium', $medium, $url);
        }

        if (!empty($content)) {
            $url = add_query_arg('utm_content', $content, $url);
        }

        if (!empty($term)) {
            $url = add_query_arg('utm_term', $term, $url);
        }

        if (is_null($source)) {
            $source = parse_url(get_bloginfo('url'), PHP_URL_HOST);
        }

        if (!empty($source)) {
            $url = add_query_arg('utm_source', $source, $url);
        }

        return $url;
    }
}

if (!function_exists('d4p_get_icon_class')) {
    function d4p_get_icon_class($name, $extra = array()) {
        $class = '';
        $d4p = false; 
        $dashicons = false;

        if (substr($name, 0, 3) == 'd4p') {
            $class.= 'd4p-icon '.$name;
            $d4p = true;
        } else if (substr($name, 0, 9) == 'dashicons') {
            $class.= 'dashicons '.$name;
            $dashicons = true;
        } else {
            $class.= 'fa fa-'.$name;
        }

        if (!empty($extra) && !$dashicons) {
            $extra = (array)$extra;

            foreach ($extra as $key) {
                $class.= ' '.($d4p ? 'd4p-icon' : 'fa').'-'.$key;
            }
        }

        return $class;
    }
}

if (!function_exists('d4p_render_icon')) {
    function d4p_render_icon($name, $tag = 'i', $aria_hidden = true, $fw = false, $class = '', $attr = array()) {
        $icon = '<'.$tag;

        if ($aria_hidden) {
            $icon.= ' aria-hidden="true"';
        }

        $extra = $fw ? 'fw' : '';
        $classes = d4p_get_icon_class($name, $extra).' '.$class;

        $icon.= ' class="'.trim($classes).'"';

        foreach ($attr as $key => $value) {
            $icon.= ' '.$key.'="'.esc_attr($value).'"';
        }

        $icon.= '></'.$tag.'>';

        return $icon;
    }
}
