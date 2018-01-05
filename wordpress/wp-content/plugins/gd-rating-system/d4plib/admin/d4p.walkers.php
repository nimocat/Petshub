<?php

/*
Name:    d4pLib_Admin_Walkers
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

if (!class_exists('d4pCheckboxRadioWalker')) {
    class d4pCheckboxRadioWalker extends Walker {
        public $tree_type = 'settings';

        public $db_fields = array('parent' => 'parent', 'id' => 'id');

        public function start_lvl(&$output, $depth = 0, $args = array()) {
            $indent = str_repeat("\t", $depth);
            $output.= "\n$indent<ul class='children'>\n";
        }

        public function end_lvl(&$output, $depth = 0, $args = array()) {
            $indent = str_repeat("\t", $depth);
            $output.= "$indent</ul>\n";
        }

        public function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
            if ($depth) {
                $indent = str_repeat("\t", $depth);
            } else {
                $indent = '';
            }

            $css_class = array(
                'option-item', 
                'option-item-parent-'.$page->parent, 
                'option-item'.$page->id);

            $css_classes = implode(' ', $css_class);

            $args['input'] = empty($args['input']) ? 'checkbox' : $args['input'];

            $selected = in_array($page->id, $args['selected']) ? ' checked="checked"' : '';

            $output.= $indent.sprintf(
                '<li class="%s"><label><input type="%s" value="%s" name="%s%s"%s class="widefat" />%s</label>',
                $css_classes,
                $args['input'],
                $page->id,
                $args['name'],
                $args['input'] == 'checkbox' ? '[]' : '',
                $selected,
                $page->title
            );
        }

        public function end_el(&$output, $page, $depth = 0, $args = array()) {
            $output.= "</li>\n";
        }
    }
}
