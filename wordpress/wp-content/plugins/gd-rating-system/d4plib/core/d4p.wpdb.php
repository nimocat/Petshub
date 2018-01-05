<?php

/*
Name:    d4pLib - Core - WPDB Core
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

== Changes ==
- found_rows -> get_found_rows
*/

if (!class_exists('d4p_wpdb_core')) {
    abstract class d4p_wpdb_core {
        public $db_site = array();
        public $db;

        public $_prefix = '';
        public $_tables = array();
        public $_network_tables = array();
        public $_metas = array();

        protected $_meta_translate = array();
        protected $_queries_log = array();

        public function __construct() {
            $this->init();

            add_action('switch_blog', array($this, 'init'));
        }

        /** @global wpdb $wpdb */
        public function init() {
            global $wpdb;

            $plugin = new stdClass();
            $this->db = new stdClass();
            $this->db_site = array();

            foreach ($this->_tables as $name) {
                $prefix = in_array($name, $this->_network_tables) ? $wpdb->base_prefix : $wpdb->prefix;

                $wpdb_name = $this->_prefix.'_'.$name;
                $real_name = $prefix.$wpdb_name;

                $plugin->$name = $real_name;
                $this->db->$name = $real_name;

                $wpdb->$wpdb_name = $real_name;

                if (!in_array($name, $this->_network_tables)) {
                    $this->db_site[] = $real_name;
                }
            }

            $wpdb->{$this->_prefix} = $plugin;

            if (!empty($this->_prefix) && !empty($this->_metas)) {
                foreach ($this->_metas as $key => $id) {
                    $this->_meta_translate[$this->_prefix.'_'.$key.'_id'] = $id;
                }

                add_filter('sanitize_key', array($this, 'sanitize_meta'));
            }
        }

        public function __get($name) {
            if (isset($this->db->$name)) {
                return $this->db->$name;
            } else if (isset($this->wpdb()->$name)) {
                return $this->wpdb()->$name;
            }
        }

        public function sanitize_meta($key) {
            if (isset($this->_meta_translate[$key])) {
                return $this->_meta_translate[$key];
            }

            return $key;
        }

        public function timestamp($gmt = true) {
            return current_time('timestamp', $gmt);
        }

        public function datetime($gmt = true) {
            return current_time('mysql', $gmt);
        }

        public function clean_ids_list($ids) {
            $ids = (array)$ids;

            $ids = array_map('absint', $ids);
            $ids = array_unique($ids);
            $ids = array_filter($ids);

            return $ids;
        }

        public function build_query($sql, $calc_found_rows = true) {
            $defaults = array(
                'select' => array(),
                'from' => array(),
                'where' => array(),
                'group' => '',
                'order' => '',
                'limit' => ''
            );

            $sql = wp_parse_args($sql, $defaults);

            $_build = 'SELECT'.($calc_found_rows ? ' SQL_CALC_FOUND_ROWS' : '').' '.join(', ', $sql['select']).' FROM '.join(' ', $sql['from']);

            if (!empty($sql['where'])) {
                $_build.= ' WHERE '.join(' AND ', $sql['where']);
            }

            if (!empty($sql['group'])) {
                $_build.= ' GROUP BY '.$sql['group'];
            }

            if (!empty($sql['order'])) {
                $_build.= ' ORDER BY '.$sql['order'];
            }

            if (!empty($sql['limit'])) {
                $_build.= ' LIMIT '.$sql['limit'];
            }

            return $_build;
        }

        public function get_insert_id() {
            return $this->wpdb()->insert_id;
        }

        public function get_found_rows() {
            return $this->get_var('SELECT FOUND_ROWS()');
        }

        public function save_queries() {
            return defined('SAVEQUERIES') && SAVEQUERIES;
        }

        public function log_get_queries() {
            return $this->_queries_log;
        }

        public function log_get_elapsed_time() {
            $time = 0;

            foreach ($this->_queries_log as $q) {
                $time+= $q[1];
            }

            return $time;
        }

        public function query($query) {
            $_value = $this->wpdb()->query($query);

            $this->_copy_logged_query();

            return $_value;
        }

        public function run($query = null, $output = OBJECT) {
            $_value = $this->wpdb()->get_results($query, $output);

            $this->_copy_logged_query();

            return $_value;
        }

        public function run_and_index($query, $field, $output = OBJECT) {
            $raw = $this->wpdb()->get_results($query, $output);

            $_value = $this->index($raw, $field);

            $this->_copy_logged_query();

            return $_value;
        }

        public function get_var($query, $x = 0, $y = 0) {
            $_value = $this->wpdb()->get_var($query, $x, $y);

            $this->_copy_logged_query();

            return $_value;
        }

        public function get_row($query = null, $output = OBJECT, $y = 0) {
            $_value = $this->wpdb()->get_row($query, $output, $y);

            $this->_copy_logged_query();

            return $_value;
        }

        public function get_col($query = null , $x = 0) {
            $_value = $this->wpdb()->get_col($query, $x);

            $this->_copy_logged_query();

            return $_value;
        }

        public function get_results($query = null, $output = OBJECT) {
            $_value = $this->wpdb()->get_results($query, $output);

            $this->_copy_logged_query();

            return $_value;
        }

        public function insert($table, $data, $format = null) {
            $_value = $this->wpdb()->insert($table, $data, $format);

            $this->_copy_logged_query();

            return $_value;
        }

        public function update($table, $data, $where, $format = null, $where_format = null) {
            $_value = $this->wpdb()->update($table, $data, $where, $format, $where_format);

            $this->_copy_logged_query();

            return $_value;
        }

        public function delete($table, $where, $where_format = null) {
            $_value = $this->wpdb()->delete($table, $where, $where_format);

            $this->_copy_logged_query();

            return $_value;
        }

        public function prepare($query, $args) {
            $args = func_get_args();
            array_shift($args);

            if (isset($args[0]) && is_array($args[0])) {
                $args = $args[0];
            }

            return $this->wpdb()->prepare($query, $args);
        }

        public function insert_meta_data($table, $column, $id, $meta) {
            foreach ($meta as $key => $value) {
                $this->insert($table, array(
                    $column => $id,
                    'meta_key' => $key,
                    'meta_value' => maybe_serialize($value)
                ));
            }
        }

        public function update_meta($meta_type, $object_id, $meta_key, $meta_value, $prev_value = '') {
            return update_metadata($this->_prefix.'_'.$meta_type, $object_id, $meta_key, $meta_value, $prev_value);
        }

        public function add_meta($meta_type, $object_id, $meta_key, $meta_value, $unique = false) {
            return add_metadata($this->_prefix.'_'.$meta_type, $object_id, $meta_key, $meta_value, $unique);
        }

        public function get_meta($meta_type, $object_id, $meta_key, $single = false) {
            return get_metadata($this->_prefix.'_'.$meta_type, $object_id, $meta_key, $single);
        }

        public function delete_meta($meta_type, $object_id, $meta_key, $delete_all = false) {
            return delete_metadata($this->_prefix.'_'.$meta_type, $object_id, $meta_key, $delete_all);
        }

        public function pluck($list, $field, $index_key = null) {
            return wp_list_pluck($list, $field, $index_key);
        }

        public function index($list, $field) {
            $new = array();

            foreach ($list as $item) {
                $id = is_array($item) ? $item[$field] : $item->$field;

                $new[$id] = $item;
            }

            return $new;
        }

        public function mysqli() {
            return isset($this->wpdb()->use_mysqli) ? $this->wpdb()->use_mysqli : false;
        }

        public function prefix() {
            return $this->wpdb()->prefix;
        }

        public function base_prefix() {
            return $this->wpdb()->base_prefix;
        }

        public function rows_affected() {
            return $this->wpdb()->rows_affected;
        }

        public function blog_id() {
            return $this->wpdb()->blogid;
        }

        /** @global wpdb $wpdb */
        public function wpdb() {
            global $wpdb;

            return $wpdb;
        }

        protected function _copy_logged_query() {
            if ($this->save_queries()) {
                $id = count($this->wpdb()->queries) - 1;
                $this->_queries_log[] = $this->wpdb()->queries[$id];
            }
        }
    }
}
