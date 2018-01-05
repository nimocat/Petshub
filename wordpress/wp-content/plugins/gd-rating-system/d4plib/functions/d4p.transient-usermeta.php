<?php

/*
Name:    d4pLib - Functions - Transient Cache - Usermeta
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

if (!function_exists('d4p_delete_user_transient')) {
    function d4p_delete_user_transient($user_id, $transient) {
        $transient_option = '_transient_'.$transient;
        $transient_timeout = '_transient_timeout_'.$transient;

        delete_user_meta($user_id, $transient_option);
        delete_user_meta($user_id, $transient_timeout);
    }
}

if (!function_exists('d4p_get_user_transient')) {
    function d4p_get_user_transient($user_id, $transient) {
        $transient_option = '_transient_'.$transient;
        $transient_timeout = '_transient_timeout_'.$transient;

        if (get_user_meta($user_id, $transient_timeout, true) < time()) {
            delete_user_meta($user_id, $transient_option);
            delete_user_meta($user_id, $transient_timeout);
            return false;
        }

        return get_user_meta($user_id, $transient_option, true);
    }
}

if (!function_exists('d4p_set_user_transient')) {
    function d4p_set_user_transient($user_id, $transient, $value, $expiration = 86400) {
        $transient_option = '_transient_'.$transient;
        $transient_timeout = '_transient_timeout_'.$transient;

        if (get_user_meta($user_id, $transient_option, true) != '') {
            delete_user_meta($user_id, $transient_option);
            delete_user_meta($user_id, $transient_timeout);
        }

        add_user_meta($user_id, $transient_timeout, time() + $expiration, true);
        add_user_meta($user_id, $transient_option, $value, true);
    }
}
