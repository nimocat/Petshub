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

if (!class_exists('d4p_settings_core')) {
    abstract class d4p_settings_core {
        public $base = 'd4p';

        public $info;
        public $scope = 'blog';
        public $current = array();
        public $settings = array();
        public $skip_update = array();

        public function __construct() { }

        public function __get($name) {
            $get = explode('_', $name, 2);

            return $this->get($get[1], $get[0]);
        }

        public function init() {
            $this->current['info'] = $this->_settings_get('info');

            do_action($this->base.'_settings_init');

            do_action($this->base.'_'.$this->scope.'_settings_init');

            $installed = is_array($this->current['info']) && isset($this->current['info']['build']);

            if (!$installed) {
                $this->_install();
            } else {
                $update = $this->current['info']['build'] != $this->info->build;

                if ($update) {
                    $this->_update();
                } else {
                    $groups = $this->_groups();

                    foreach ($groups as $key) {
                        $this->current[$key] = $this->_settings_get($key);

                        if (!is_array($this->current[$key])) {
                            $data = $this->group($key);

                            if (!is_null($data)) {
                                $this->current[$key] = $data;

                                $this->_settings_update($key, $data);
                            }
                        }
                    }
                }
            }

            do_action($this->base.'_'.$this->scope.'_settings_loaded');

            do_action($this->base.'_settings_loaded');
        }

        protected function _db() { }

        protected function _name($name) {
            return 'd4p_'.$this->scope.'_'.$this->info->code.'_'.$name;
        }

        protected function _install() {
            $this->current = $this->_merge();
            $this->current['info'] = $this->info->to_array();
            $this->current['info']['install'] = true;
            $this->current['info']['update'] = false;

            foreach ($this->current as $key => $data) {
                $this->_settings_update($key, $data);
            }

            $this->_db();
        }

        protected function _update() {
            $old_build = $this->current['info']['build'];

            $this->current['info'] = $this->info->to_array();
            $this->current['info']['install'] = false;
            $this->current['info']['update'] = true;
            $this->current['info']['previous'] = $old_build;

            $this->_settings_update('info', $this->current['info']);

            $settings = $this->_merge();

            foreach ($settings as $key => $data) {
                $now = $this->_settings_get($key);

                if (!is_array($now)) {
                    $now = $data;
                } else if (!in_array($key, $this->skip_update)) {
                    $now = $this->_upgrade($now, $data);
                }

                $this->current[$key] = $now;

                $this->_settings_update($key, $now);
            }

            $this->_db();
        }

        protected function _upgrade($old, $new) {
            foreach ($new as $key => $value) {
                if (!array_key_exists($key, $old)) {
                    $old[$key] = $value;
                }
            }

            $unset = array();
            foreach ($old as $key => $value) {
                if (!array_key_exists($key, $new)) {
                    $unset[] = $key;
                }
            }

            if (!empty($unset)) {
                foreach ($unset as $key) {
                    unset($old[$key]);
                }
            }

            return $old;
        }

        protected function _groups() {
            return array_keys($this->settings);
        }

        protected function _merge() {
            $merged = array();

            foreach ($this->settings as $key => $data) {
                $merged[$key] = array();

                foreach ($data as $name => $value) {
                    $merged[$key][$name] = $value;
                }
            }

            return $merged;
        }

        public function group($group) {
            if (isset($this->settings[$group])) {
                return $this->settings[$group];
            } else {
                return null;
            }
        }

        public function exists($name, $group = 'settings') {
            if (isset($this->current[$group][$name])) {
                return true;
            } else if (isset($this->settings[$group][$name])) {
                return true;
            } else {
                return false;
            }
        }

        public function prefix_get($prefix, $group = 'settings') {
            $settings = array_keys($this->group($group));

            $results = array();

            foreach ($settings as $key) {
                if (substr($key, 0, strlen($prefix)) == $prefix) {
                    $results[substr($key, strlen($prefix))] = $this->get($key, $group);
                }
            }

            return $results;
        }

        public function group_get($group) {
            $default = $this->settings[$group];
            $current = $this->current[$group];

            return wp_parse_args($current, $default);
        }

        public function register_group($group) {
            $this->settings[$group] = array();
        }

        public function register($group, $name, $value) {
            $this->settings[$group][$name] = $value;
        }

        public function get($name, $group = 'settings') {
            $exit = null;

            if (isset($this->current[$group][$name])) {
                $exit = $this->current[$group][$name];
            } else if (isset($this->settings[$group][$name])) {
                $exit = $this->settings[$group][$name];
            }

            return apply_filters($this->base.'_'.$this->scope.'_settings_get', $exit, $name, $group);
        }

        public function set($name, $value, $group = 'settings', $save = false) {
            $this->current[$group][$name] = $value;

            if ($save) {
                $this->save($group);
            }
        }

        public function save($group) {
            $this->_settings_update($group, $this->current[$group]);
        }

        public function is_install() {
            return $this->get('install', 'info');
        }

        public function is_update() {
            return $this->get('update', 'info');
        }

        public function mark_for_update() {
            $this->current['info']['update'] = true;

            $this->save('info');
        }

        public function remove_by_prefix($prefix, $group, $save = true) {
            $keys = array_keys($this->current[$group]);

            foreach ($keys as $key) {
                if (substr($key, 0, strlen($prefix)) == $prefix) {
                    unset($this->current[$group][$key]);
                }
            }

            if ($save) {
                $this->save($group);
            }
        }

        public function remove_plugin_settings() {
            $this->_settings_delete('info');

            foreach ($this->_groups() as $group) {
                $this->_settings_delete($group);
            }
        }

        public function remove_plugin_settings_by_group($group) {
            $this->_settings_delete($group);
        }

        public function import_from_object($import, $list = array()) {
            if (empty($list)) {
                $list = $this->_groups();
            }

            foreach ($import as $key => $data) {
                if (in_array($key, $list)) {
                    $this->current[$key] = (array)$data;

                    $this->save($key);
                }
            }
        }

        public function serialized_export($list = array()) {
            if (empty($list)) {
                $list = $this->_groups();
            }

            $data = new stdClass();
            $data->info = $this->current['info'];

            foreach ($list as $name) {
                $data->$name = $this->current[$name];
            }

            return serialize($data);
        }

        protected function _settings_get($name) {
            if ($this->scope == 'network') {
                return get_site_option($this->_name($name));
            } else {
                return get_option($this->_name($name));
            }
        }

        protected function _settings_delete($name) {
            if ($this->scope == 'network') {
                delete_site_option($this->_name($name));
            } else {
                delete_option($this->_name($name));
            }
        }

        protected function _settings_update($name, $data) {
            if ($this->scope == 'network') {
                update_site_option($this->_name($name), $data);
            } else {
                update_option($this->_name($name), $data);
            }
        }
    }
}
