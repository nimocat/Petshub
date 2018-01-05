<?php

/*
Name:    d4pLib - Functions - Transient Cache - Database Query
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

if (!function_exists('d4p_transient_sql_query')) {
    function d4p_transient_sql_query($query, $key, $method, $output = OBJECT, $x = 0, $y = 0, $ttl = 86400) {
        $var = get_transient($key);

        if ($var === false) {
            global $wpdb;

            switch ($method) {
                case 'var':
                    $var = $wpdb->get_var($query, $x, $y);
                    break;
                case 'row':
                    $var = $wpdb->get_row($query, $output, $y);
                    break;
                case 'results':
                    $var = $wpdb->get_results($query, $output);
                    break;
            }

            set_transient($key, $var, $ttl);
        }

        return $var;
    }
}

if (!function_exists('d4p_site_transient_sql_query')) {
    function d4p_site_transient_sql_query($query, $key, $method, $output = OBJECT, $x = 0, $y = 0, $ttl = 86400) {
        $var = get_site_transient($key);

        if ($var === false) {
            global $wpdb;

            switch ($method) {
                case 'var':
                    $var = $wpdb->get_var($query, $x, $y);
                    break;
                case 'row':
                    $var = $wpdb->get_row($query, $output, $y);
                    break;
                case 'results':
                    $var = $wpdb->get_results($query, $output);
                    break;
            }

            set_site_transient($key, $var, $ttl);
        }

        return $var;
    }
}
