<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_user {
    public $id = 0;
    public $ip = '';
    public $log = array();

    public $cookie = array();

    public function __construct($id = -1, $ip = true, $cookie = true) {
        if ($id > -1) {
            $this->id = absint($id);
        } else if (is_user_logged_in()) {
            $this->id = get_current_user_id();
        }

        if ($ip === true) {
            $this->ip = d4p_visitor_ip();
        } else if ($ip !== false) {
            $this->ip = $ip;
        }

        if ($cookie) {
            $this->cookie = gdrts_read_cookies();
        }
    }

    public function update_cookie($log_id) {
        $log_id = intval($log_id);

        if ($log_id > 0 && !in_array($log_id, $this->cookie)) {
            $this->cookie[] = $log_id;

            setcookie(gdrts()->cookie_key(), json_encode($this->cookie), gdrts()->cookie_expiration(), '/', COOKIE_DOMAIN);
        }
    }

    public function load_log($item_id, $method, $series = null) {
        $log_ids = array();

        if ($this->id == 0) {
            $log_ids = $this->cookie;
        }

        $data = gdrts_db()->get_log_item_user_method($item_id, $this->id, $method, $series, $this->ip, $log_ids);

        if (is_null($series) || empty($series)) {
            $this->log[$item_id][$method] = $data;
        } else {
            $this->log[$item_id][$method][$series] = $data;
        }
    }

    public function get_log_item_user_method($item_id, $method, $series = null) {
        if (is_null($series)) {
            if (!isset($this->log[$item_id][$method])) {
                $this->load_log($item_id, $method);
            }

            return $this->log[$item_id][$method];
        } else {
            if (!isset($this->log[$item_id][$method][$series])) {
                $this->load_log($item_id, $method, $series);
            }

            return $this->log[$item_id][$method][$series];
        }
    }
}
