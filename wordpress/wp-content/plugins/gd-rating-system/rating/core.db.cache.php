<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_db_cache {
    private $cache_hits = 0;
    private $cache_misses = 0;

    public function __construct() {}

    public function set($module, $method, $args, $store, $time = false) {
        $expire = $this->_expire() + $this->_time($module, $method, $time);
        $cache = $this->get_cache_id($module, $method, $args);
        $store = is_null($store) ? serialize($store) : maybe_serialize($store);

        if ($cache > 0) {
            gdrts_db()->update(gdrts_db()->db->cache, array(
                'store' => $store,
                'expire' => $expire
            ), array(
                'cache_id' => $cache
            ));
        } else {
            gdrts_db()->insert(gdrts_db()->db->cache, array(
                'module' => $module,
                'method' => $method,
                'access' => $this->_access($args),
                'store' => $store,
                'expire' => $expire
            ));
        }
    }

    public function get($module, $method, $args, $default = false) {
        $raw = $this->_get($module, $method, $args);

        if (!is_null($raw) && isset($raw->cache_id) && $raw->cache_id > 0) {
            $this->_hit();

            return maybe_unserialize($raw->store);
        }

        $this->_miss();

        return $default;
    }

    public function get_cache_id($module, $method, $args) {
        $raw = $this->_get($module, $method, $args);

        if (!is_null($raw) && isset($raw->cache_id) && $raw->cache_id > 0) {
            return intval($raw->cache_id);
        }

        return 0;
    }

    public function delete($module, $method, $args) {
        $query = gdrts_db()->prepare(
                    "DELETE FROM ".gdrts_db()->db->cache." WHERE `module` = %s 
                     AND `method` = %s AND `access` = %s", 
                     $module, $method, $this->_access($args)
            );

        gdrts_db()->query($query);
    }

    public function clean() {
        $query = gdrts_db()->wpdb()->prepare("DELETE FROM ".gdrts_db()->db->cache." WHERE expire < %d", $this->_expire());

        gdrts_db()->query($query);
    }

    private function _time($module, $method, $default = false) {
        if ($default === false) {
            $default = gdrts_settings()->get('db_cache_time_'.$module);

            if (is_null($default)) {
                $default = gdrts_settings()->get('db_cache_time_global');
            }
        }

        return apply_filters('gdrts_db_cache_time_'.$module, intval($default), $method);
    }

    private function _access($args) {
        return md5(serialize($args));
    }

    private function _expire() {
        return time();
    }

    private function _hit() {
        $this->cache_hits++;
    }

    private function _miss() {
        $this->cache_misses++;
    }

    private function _get($module, $method, $args) {
        $query = gdrts_db()->prepare(
                    "SELECT cache_id, store, expire FROM ".gdrts_db()->db->cache." WHERE `module` = %s 
                     AND `method` = %s AND `access` = %s", 
                     $module, $method, $this->_access($args)
            );

        $raw = gdrts_db()->get_row($query);

        if (!is_null($raw) && isset($raw->cache_id) && $raw->cache_id > 0) {
            if ($raw->expire < $this->_expire()) {
                $this->delete($module, $method, $args);

                return null;
            }
        }

        return $raw;
    }
}

global $_gdrts_db_cache;
$_gdrts_db_cache = new gdrts_core_db_cache();

/** @return gdrts_core_db_cache */
function gdrts_db_cache() {
    global $_gdrts_db_cache;
    return $_gdrts_db_cache;
}
