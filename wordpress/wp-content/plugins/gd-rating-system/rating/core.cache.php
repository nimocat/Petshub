<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_cache {
    public $group = 'gdrts';

    private $cache_hits = 0;
    private $cache_misses = 0;

    public function __construct() {}

    private function _hit() {
        $this->cache_hits++;
    }

    private function _miss() {
        $this->cache_misses++;
    }

    private function _key($group, $key) {
        return $group.'::'.$key;
    }

    public function in($group, $key) {
        global $wp_object_cache;

        return isset($wp_object_cache->cache[$this->group][$this->_key($group, $key)]);
    }

    public function add($group, $key, $data, $expire = 0) {
        return wp_cache_add($this->_key($group, $key), $data, $this->group, $expire);
    }

    public function set($group, $key, $data, $expire = 0) {
        return wp_cache_set($this->_key($group, $key), $data, $this->group, $expire);
    }

    public function get($group, $key) {
        $obj = wp_cache_get($this->_key($group, $key), $this->group);

        if ($obj === false) {
            $this->_miss();
        } else {
            $this->_hit();
        }

        return $obj;
    }

    public function delete($group, $key) {
        return wp_cache_delete($this->_key($group, $key), $this->group);
    }

    public function clear() {
        global $wp_object_cache;

        if (isset($wp_object_cache->cache[$this->group])) {
            unset($wp_object_cache->cache[$this->group]);
        }
    }

    public function get_item_id($entity, $name, $id) {
        if (!is_null($entity) && !is_null($name) && !is_null($id) && is_numeric($id)) {
            $item_id = $this->get('item_id', $entity.'-'.$name.'-'.$id);

            if ($item_id == false) {
                $item_id = gdrts_db()->get_item_id($entity, $name, $id);

                $this->set('item_id', $entity.'-'.$name.'-'.$id, $item_id);
            }

            return $item_id;
        } else {
            return false;
        }
    }
}

global $_gdrts_cache;
$_gdrts_cache = new gdrts_core_cache();

/** @return gdrts_core_cache */
function gdrts_cache() {
    global $_gdrts_cache;
    return $_gdrts_cache;
}
