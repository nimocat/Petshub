<?php

/*
Name:    d4pLib - Classes - Object Sort
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

if (!class_exists('d4p_object_sort')) {
    class d4p_object_sort {
        public $properties;
        public $sorted;

        public function  __construct($objects_array, $properties = array(), $uasort = false) {
            $properties = (array)$properties;

            if (count($properties) > 0) {
                $this->properties = $properties;

                if ($uasort) {
                    uasort($objects_array, array($this, 'array_compare'));
                } else {
                    usort($objects_array, array($this, 'array_compare'));
                }
            }

            $this->sorted = $objects_array;
        }

        public function array_compare($one, $two, $i = 0) {
            $column = $this->properties[$i]['property'];
            $order = strtolower($this->properties[$i]['order']);

            if ($one->$column == $two->$column) {
                if ($i < count($this->properties) - 1) {
                    $i++;
                    return $this->array_compare($one, $two, $i);
                } else {
                    return 0;
                }
            }

            if (strtolower($order) == 'asc') {
                return ($one->$column < $two->$column) ? -1 : 1;
            } else {
                return ($one->$column < $two->$column) ? 1 : -1;
            }
        }
    }
}

if (!class_exists('d4p_do_object_sort')) {
    function d4p_do_object_sort($objects_array, $properties = array(), $uasort = false) {
        $_sort = new d4p_object_sort($objects_array, $properties, $uasort);

        return $_sort->sorted;
    }
}
