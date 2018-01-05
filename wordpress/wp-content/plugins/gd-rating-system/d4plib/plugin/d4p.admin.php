<?php

/*
Name:    d4pLib_Class_Admin
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

if (!class_exists('d4p_admin_core')) {
    abstract class d4p_admin_core {
        public $plugin = '';
        public $url = '';

        public $is_debug;

        public $page = false;
        public $panel = false;
        public $action = false;

        public $menu_items;
        public $page_ids = array();

        function __construct() { }

        public function file($type, $name, $d4p = false, $min = true, $base_url = null) {
            $get = is_null($base_url) ? $this->url : $base_url;

            if ($d4p) {
                $get.= 'd4plib/resources/';
            }

            if ($name == 'font') {
                $get.= 'font/styles.css';
            } else if ($name == 'flags') {
                $get.= 'flags/flags.css';
            } else {
                $get.= $type.'/'.$name;

                if (!$this->is_debug && $type != 'font' && $min) {
                    $get.= '.min';
                }

                $get.= '.'.$type;
            }

            return $get;
        }

        public function core() {
            $this->is_debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;

            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_init', array($this, 'admin_columns'));
            add_action('admin_menu', array($this, 'admin_menu'));
            add_action('add_meta_boxes', array($this, 'admin_meta'));

            add_action('current_screen', array($this, 'current_screen'));

            add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        }

        public function title() {
            return '';
        }

        public function admin_init() {
            if (isset($_GET['panel']) && $_GET['panel'] != '') {
                $this->panel = trim(sanitize_key($_GET['panel']));
            }
        }

        public function admin_load_hooks() {
            foreach ($this->page_ids as $id) {
                add_action('load-'.$id, array($this, 'load_admin_page'));
            }
        }

        public function current_screen($screen) { }

        public function admin_meta() { }

        public function admin_columns() { }

        public function enqueue_scripts($hook) { }

        public function admin_menu() { }

        public function load_admin_page() { }

        public function help_tab_sidebar() {
            $screen = get_current_screen();

            $screen->set_help_sidebar(
                '<p><strong>'.$this->title().'</strong></p>'.
                '<p><a target="_blank" href="https://plugins.dev4press.com/'.$this->plugin.'/">'.__("Home Page", "d4plib").'</a><br/>'.
                '<a target="_blank" href="https://support.dev4press.com/kb/product/'.$this->plugin.'/">'.__("Knowledge Base", "d4plib").'</a><br/>'.
                '<a target="_blank" href="https://support.dev4press.com/forums/forum/plugins/'.$this->plugin.'/">'.__("Support Forum", "d4plib").'</a></p>'
            );
        }

        public function help_tab_getting_help() {
            $screen = get_current_screen();

            $screen->add_help_tab(
                array(
                    'id' => 'd4p-help-info',
                    'title' => __("Getting Help", "d4plib"),
                    'content' => '<p>'.__("To get help with this plugin, you can start with Knowledge Base list of frequently asked questions and articles. If you have any questions, or you want to report a bug, or you have a suggestion, you can use support forum. All important links for this are on the right side of this help dialog.", "d4plib").'</p>'
                )
            );
        }
    }
}
