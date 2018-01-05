<?php

/*
Name:    d4pLib - Core - DateTime
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

if (!class_exists('d4p_datetime_core')) {
    class d4p_datetime_core {
        public function __construct() {}

        public function offset() {
            $offset = get_option('gmt_offset');

            if (empty($offset)) {
                $offset = wp_timezone_override_offset();
            }

            return $offset === false ? 0 : $offset;
        }

        public function formatted_offset($offset = null) {
            if (is_null($offset)) {
                $offset = $this->offset();
            }

            $hours = intval($offset);
            $minutes = absint(($offset - floor($offset)) * 60);

            return sprintf('%+03d:%02d', $hours, $minutes);
        }

        public function timestamp_local_to_gmt($local) {
            return $local - $this->offset() * HOUR_IN_SECONDS;
        }

        public function timestamp_gmt_to_local($gmt) {
            return $gmt + $this->offset() * HOUR_IN_SECONDS;
        }

        public function timestamp($gmt = true) {
            return $gmt ? time() : $this->timestamp_gmt_to_local(time());
        }

        public function date($format, $gmt = true) {
            return $gmt ? gmdate($format) : gmdate($format, $this->timestamp_gmt_to_local(time()));
        }

        public function mysql_date($gmt = true) {
            return $this->date('Y-m-d H:i:s', $gmt);
        }
    }
}
