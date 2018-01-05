<?php

/*
Name:    d4pLib_Class_Errors
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

if (!class_exists('d4p_errors')) {
    class d4p_errors extends WP_Error {
        public function has_errors() {
            return !empty($this->errors);
        }

        public function merge_errors($errors) {
            $this->errors = array_merge($this->errors, $errors);
        }

        public function merge_errors_data($error_data) {
            $this->error_data = array_merge($this->error_data, $error_data);
        }
    }
}

if (!function_exists('d4p_is_wp_error')) {
    function d4p_is_wp_error($thing) {
        return ($thing instanceof WP_Error) || ($thing instanceof d4p_errors);
    }
}
