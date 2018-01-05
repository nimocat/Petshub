<?php

/*
Name:    d4pLib_Admin_Functions
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

if (!function_exists('d4p_split_textarea_to_list')) {
    function d4p_split_textarea_to_list($value, $empty_lines = false) {
        $elements = preg_split("/[\n\r]/", $value);

        if (!$empty_lines) {
            $results = array();

            foreach ($elements as $el) {
                if (trim($el) != "") {
                    $results[] = $el;
                }
            }

            return $results;
        } else {
            return $elements;
        }
    }
}

if (!function_exists('d4p_html_id_from_name')) {
    function d4p_html_id_from_name($name, $id = '') {
        if ($id == '') {
            $id = str_replace(']', '', $name);
            $id = str_replace('[', '_', $id);
        } else if ($id == '_') {
            $id = '';
        }

        return $id;
    }
}

if (!function_exists('d4p_render_select')) {
    function d4p_render_select($values, $args = array(), $attr = array()) {
        $defaults = array(
            'selected' => '', 'name' => '', 'id' => '', 'class' => '', 
            'style' => '', 'multi' => false, 'echo' => true, 'readonly' => false);
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $render = '';
        $attributes = array();
        $selected = is_null($selected) ? array_keys($values) : (array)$selected;
        $associative = !d4p_is_array_associative($values);
        $id = d4p_html_id_from_name($name, $id);

        if ($class != '') {
            $attributes[] = 'class="'.$class.'"';
        }

        if ($style != '') {
            $attributes[] = 'style="'.$style.'"';
        }

        if ($multi) {
            $attributes[] = 'multiple';
        }

        if ($readonly) {
            $attributes[] = 'readonly';
        }

        foreach ($attr as $key => $value) {
            $attributes[] = $key.'="'.esc_attr($value).'"';
        }

        $name = $multi ? $name.'[]' : $name;

        if ($id != '') {
            $attributes[] = 'id="'.$id.'"';
        }

        if ($name != '') {
            $attributes[] = 'name="'.$name.'"';
        }

        $render.= '<select '.join(' ', $attributes).'>';
        foreach ($values as $value => $display) {
            $real_value = $associative ? $display : $value;

            $sel = in_array($real_value, $selected) ? ' selected="selected"' : '';
            $render.= '<option value="'.$value.'"'.$sel.'>'.$display.'</option>';
        }
        $render.= '</select>';

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }
}

if (!function_exists('d4p_render_grouped_select')) {
    function d4p_render_grouped_select($values, $args = array(), $attr = array()) {
        $defaults = array(
            'selected' => '', 'name' => '', 'id' => '', 'class' => '', 
            'style' => '', 'multi' => false, 'echo' => true, 'readonly' => false);
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $render = '';
        $attributes = array();
        $selected = (array)$selected;
        $id = d4p_html_id_from_name($name, $id);

        if ($class != '') {
            $attributes[] = 'class="'.$class.'"';
        }

        if ($style != '') {
            $attributes[] = 'style="'.$style.'"';
        }

        if ($multi) {
            $attributes[] = 'multiple';
        }

        if ($readonly) {
            $attributes[] = 'readonly';
        }

        foreach ($attr as $key => $value) {
            $attributes[] = $key.'="'.esc_attr($value).'"';
        }

        $name = $multi ? $name.'[]' : $name;

        if ($id != '') {
            $attributes[] = 'id="'.$id.'"';
        }

        if ($name != '') {
            $attributes[] = 'name="'.$name.'"';
        }

        $render.= '<select '.join(' ', $attributes).'>';
        foreach ($values as $group) {
            $render.= '<optgroup label="'.$group['title'].'">';
            foreach ($group['values'] as $value => $display) {
                $sel = in_array($value, $selected) ? ' selected="selected"' : '';
                $render.= '<option value="'.esc_attr($value).'"'.$sel.'>'.$display.'</option>';
            }
            $render.= '</optgroup>';
        }
        $render.= '</select>';

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }
}

if (!function_exists('d4p_render_checkradios')) {
    function d4p_render_checkradios($values, $args = array(), $attr = array()) {
        $defaults = array(
            'selected' => '', 'name' => '', 'id' => '', 'class' => '', 
            'style' => '', 'multi' => true, 'echo' => true, 'readonly' => false);
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $render = '<div class="d4p-setting-checkboxes">';
        $attributes = array();
        $selected = (array)$selected;
        $associative = d4p_is_array_associative($values);
        $id = d4p_html_id_from_name($name, $id);

        if ($class != '') {
            $attributes[] = 'class="'.$class.'"';
        }

        if ($style != '') {
            $attributes[] = 'style="'.$style.'"';
        }

        if ($readonly) {
            $attributes[] = 'readonly';
        }

        foreach ($attr as $key => $value) {
            $attributes[] = $key.'="'.esc_attr($value).'"';
        }

        $name = $multi ? $name.'[]' : $name;

        if ($id != '') {
            $attributes[] = 'id="'.$id.'"';
        }

        if ($name != '') {
            $attributes[] = 'name="'.$name.'"';
        }

        if ($multi) {
            $render.= '<div class="d4p-check-uncheck">';

            $render.= '<a href="#checkall">'.__("Check All", "d4plib").'</a>';
            $render.= ' | <a href="#uncheckall">'.__("Uncheck All", "d4plib").'</a>';

            $render.= '</div>';
        }

        foreach ($values as $key => $title) {
            $real_value = $associative ? $key : $title;
            $sel = in_array($real_value, $selected) ? ' checked="checked"' : '';

            $render.= sprintf('<label><input type="%s" id="%s" value="%s" name="%s"%s class="widefat" />%s</label>', 
                    $multi ? 'checkbox' : 'radio', $id, $real_value, $name, $sel, $title);
        }

        $render.= '</div>';

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }
}

if (!function_exists('d4p_render_checkradios_hierarchy')) {
    function d4p_render_checkradios_hierarchy($values, $args = array(), $attr = array()) {
        $defaults = array(
            'selected' => '', 'name' => '', 'id' => '', 'class' => '', 
            'style' => '', 'multi' => true, 'echo' => true, 'readonly' => false);
        $args = wp_parse_args($args, $defaults);
        extract($args);

        $render = '<div class="d4p-setting-checkboxes">';
        $attributes = array();
        $selected = (array)$selected;
        $associative = d4p_is_array_associative($values);
        $id = d4p_html_id_from_name($name, $id);

        if ($class != '') {
            $attributes[] = 'class="'.$class.'"';
        }

        if ($style != '') {
            $attributes[] = 'style="'.$style.'"';
        }

        if ($readonly) {
            $attributes[] = 'readonly';
        }

        foreach ($attr as $key => $value) {
            $attributes[] = $key.'="'.esc_attr($value).'"';
        }

        $name = $multi ? $name.'[]' : $name;

        if ($id != '') {
            $attributes[] = 'id="'.$id.'"';
        }

        if ($name != '') {
            $attributes[] = 'name="'.$name.'"';
        }

        if ($multi) {
            $render.= '<div class="d4p-check-uncheck">';

            $render.= '<a href="#checkall">'.__("Check All", "d4plib").'</a>';
            $render.= ' | <a href="#uncheckall">'.__("Uncheck All", "d4plib").'</a>';

            $render.= '</div>';
        }

        $walker = new d4pCheckboxRadioWalker();
        $input = $multi ? 'checkbox' : 'radio';

        $render.= '<div class="d4p-content-wrapper">';
            $render.= '<ul class="d4p-wrapper-hierarchy">';
            $render.= $walker->walk($values, 0, array('input' => $input, 'id' => $id, 'name' => $name, 'selected' => $selected));
            $render.= '</ul>';
        $render.= '</div>';

        $render.= '</div>';

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }
}

if (!function_exists('d4p_render_toggle_block')) {
    function d4p_render_toggle_block($title, $content, $classes = array()) {
        $render = '<div class="d4p-section-toggle '.join(' ', $classes).'">';
        $render.= '<div class="d4p-toggle-title">';
        $render.= '<i class="fa fa-caret-down fa-fw"></i> '.$title;
        $render.= '</div>';
        $render.= '<div class="d4p-toggle-content" style="display: none;">';
        $render.= $content;
        $render.= '</div>';
        $render.= '</div>';

        return $render;
    }
}

if (!function_exists('d4p_css_size_units')) {
    function d4p_css_size_units() {
        return array(
            'px' => 'px',
            'pt' => 'pt',
            'pc' => 'pc',
            'em' => 'em',
            'ex' => 'ex',
            'in' => 'in',
            'mm' => 'mm',
            'cm' => 'cm',
            'csh' => 'ch',
            'rem' => 'rem',
            '%' => '%'
        );
    }
}
