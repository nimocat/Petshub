<?php

/*
Name:    d4pLib_Debug
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

if (!function_exists('d4p_error_log')) {
    function d4p_error_log($log, $title = '') {
        d4p_debug::error_log($log, $title);
    }
}

if (!function_exists('d4p_print_r')) {
    function d4p_print_r($obj, $pre = true, $title = '', $before = '', $after = '') {
        d4p_debug::print_r($obj, $pre, $title, $before, $after);
    }
}

if (!function_exists('d4p_print_hooks')) {
    function d4p_print_hooks($filter = false, $destination = 'print') {
        d4p_debug::print_hooks($filter, $destination);
    }
}

if (!function_exists('d4p_debug_print_page_summary')) {
    function d4p_debug_print_page_summary() {
        global $wpdb;
        
        echo D4P_EOL;
        echo '<!-- '.__("SQL Queries", "d4plib").'           : ';
        echo $wpdb->num_queries;
        echo ' -->'.D4P_EOL;
        echo '<!-- '.__("Total Page Time", "d4plib").'       : ';
        echo timer_stop(0, 6).' '.__("seconds", "d4plib");
        echo ' -->'.D4P_EOL;

        if (function_exists('memory_get_peak_usage')) {
            echo '<!-- '.__("PHP Memory Peak", "d4plib").'       : ';
            echo round(memory_get_peak_usage() / 1024 / 1024, 2).' MB';
            echo ' -->'.D4P_EOL;
        }

        if (function_exists('memory_get_usage')) {
            echo '<!-- '.__("PHP Memory Final", "d4plib").'      : ';
            echo round(memory_get_usage() / 1024 / 1024, 2).' MB';
            echo ' -->'.D4P_EOL;
        }

        echo D4P_EOL;
    }
}

if (!function_exists('d4p_debug_print_query_conditions')) {
    function d4p_debug_print_query_conditions() {
        global $wp_query;

        echo D4P_EOL;

        $true = $false = '';

        foreach ($wp_query as $key => $value) {
            if (substr($key, 0, 3) == 'is_') {
                $line = '<!-- '.$key.': '.($value ? 'true' : 'false').' -->'.D4P_EOL;

                if ($value) {
                    $true.= $line;
                } else {
                    $false.= $line;
                }
            }
        }

        foreach (array('is_front_page') as $key) {
            $value = $wp_query->$key();

            $line = '<!-- '.$key.': '.($value ? 'true' : 'false').' -->'.D4P_EOL;

            if ($value) {
                $true.= $line;
            } else {
                $false.= $line;
            }
        }
        
        echo $true.D4P_EOL.$false;

        echo D4P_EOL;
    }
}

if (!function_exists('d4p_debug_print_page_request')) {
    function d4p_debug_print_page_request() {
        global $wp, $template;

        echo D4P_EOL;
        echo '<!-- '.__("Request", "d4plib").'               : ';
        echo empty($wp->request) ? __("None", "d4plib") : esc_html($wp->request);
        echo ' -->'.D4P_EOL;
        echo '<!-- '.__("Matched Rewrite Rule", "d4plib").'  : ';
        echo empty($wp->matched_rule) ? __("None", "d4plib") : esc_html($wp->matched_rule);
        echo ' -->'.D4P_EOL;
        echo '<!-- '.__("Matched Rewrite Query", "d4plib").' : ';
        echo empty($wp->matched_query) ? __("None", "d4plib") : esc_html($wp->matched_query);
        echo ' -->'.D4P_EOL;
        echo '<!-- '.__("Loaded Template", "d4plib").'       : ';
        echo basename($template);
        echo ' -->'.D4P_EOL;
    }
}
