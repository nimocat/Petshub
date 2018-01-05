<?php

/*
Name:    d4pLib_Class_Shortcodes
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

if (!class_exists('d4p_shortcodes_core')) {
    abstract class d4p_shortcodes_core {
        public $shortcake = '';
        public $shortcake_full = '';
        public $shortcake_title = 'Dev4Press';

        public $prefix = 'd4p';
        public $shortcodes = array();
        public $registered = array();

        public function __construct() {
            $this->init();

            $this->_register();
            $this->_shortcake();
        }

        abstract public function init();

        protected function _real_code($code) {
            return $this->prefix != '' ? $this->prefix.'_'.$code : $code;
        }

        protected function _wrapper($content, $name, $extra_class = '', $tag = 'div') {
            $classes = array(
                $this->prefix.'-shortcode-wrapper',
                $this->prefix.'-shortcode-'.str_replace('_', '-', $name)
            );

            if (!empty($extra_class)) {
                $classes[] = $extra_class;
            }

            $wrapper = '<'.$tag.' class="'.join(' ', $classes).'">';
            $wrapper.= $content;
            $wrapper.= '</'.$tag.'>';

            return $wrapper;
        }

        protected function _register() {
            $list = array_keys($this->shortcodes);

            foreach ($list as $shortcode) {
                $name = $this->_real_code($shortcode);

                add_shortcode($name, array($this, 'shortcode_'.$shortcode));

                $this->registered[$name] = $shortcode;
            }
        }

        protected function _shortcake() {
            add_action('shortcode_ui_before_do_shortcode', array($this, 'shortcake_before'));
        }

        protected function _args($code) {
            return isset($this->shortcodes[$code]['args']) ? $this->shortcodes[$code]['args'] : array();
        }

        protected function _atts($code, $atts = array()) {
            $real_code = $this->_real_code($code);

            if (isset($atts[0])) {
                $atts[$real_code] = substr($atts[0], 1);
                unset($atts[0]);
            }

            $default = $this->shortcodes[$code]['atts'];
            $default[$real_code] = '';

            return shortcode_atts($default, $atts);
        }

        protected function _content($content, $raw = false) {
            if ($raw) {
                return $content;
            } else {
                return do_shortcode($content);
            }
        }

        protected function _regex($list = array()) {
            if (empty($list)) {
                $tagnames = array_keys($this->registered);
                $tagregexp = join('|', array_map('preg_quote', $tagnames));
            } else {
                $tagregexp = join('|', $list);
            }

            return    '\\['
                    . '(\\[?)'
                    . "($tagregexp)"
                    . '(?![\\w-])'
                    . '('
                    .     '[^\\]\\/]*'
                    .     '(?:'
                    .         '\\/(?!\\])'
                    .         '[^\\]\\/]*'
                    .     ')*?'
                    . ')'
                    . '(?:'
                    .     '(\\/)'
                    .     '\\]'
                    . '|'
                    .     '\\]'
                    .     '(?:'
                    .         '('
                    .             '[^\\[]*+'
                    .             '(?:'
                    .                 '\\[(?!\\/\\2\\])'
                    .                 '[^\\[]*+'
                    .             ')*+'
                    .         ')'
                    .         '\\[\\/\\2\\]'
                    .     ')?'
                    . ')'
                    . '(\\]?)';

        }

        public function shortcake_before($shortcode) {
            $matches = array();
            $regex = $this->_regex();

            preg_match("/$regex/s", $shortcode, $matches);

            if (!empty($matches)) {
                $name = $matches[2];

                if (isset($this->registered[$name])) {
                    $this->shortcake = $this->registered[$name];
                    $this->shortcake_full = $shortcode;
                }
            }
        }

        public function shortcake_preview($atts, $shortcode) {
            $render = '<div style="line-height: 1.3; border: 2px dashed #444444; margin: 5px; padding: 10px;">';
            $render.= '<div style="color: #666666; font-size: 11px; text-transform: uppercase;">'.$this->shortcake_title.'</div>';
            $render.= '<div style="color: #333333; font-size: 14px; font-weight: bold;">'.$this->shortcodes[$shortcode]['name'].'</div>';
            $render.= '<div style="color: #333333; font-size: 12.5px; font-family: monospace; margin: 5px 0 0 5px;">'.$this->shortcake_full.'</div>';
            $render.= '</div>';

            return $render;
        }
        
        public function in_shortcake_preview($name) {
            return $this->shortcake == $name;
        }
    }
}
