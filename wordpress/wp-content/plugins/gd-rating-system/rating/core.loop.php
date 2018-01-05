<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_loop {
    private $_loop = 'none';
    private $_method = '';

    public function __construct() {}

    public function start($name = 'single') {
        $this->_loop = $name;
        $this->_method = $this->engine()->method();
    }

    public function end() {
        $this->_loop = 'none';
        $this->_method = '';
    }

    public function is($name = '') {
        return empty($name) ? 
               $this->_loop != 'none' : 
               $this->_loop == $name;
    }

    public function is_save() {
        if ($this->is('single')) {
            return $this->engine()->is_loop_save();
        }

        return false;
    }

    public function engine() {
        if ($this->is('single')) {
            return gdrts_single();
        } else if ($this->is('list')) {
            return gdrts_list();
        }

        return null;
    }

    public function method() {
        if ($this->is()) {
            return gdrts_get_method_object($this->_method);
        }

        return null;
    }

    public function user() {
        if ($this->is()) {
            return $this->method()->user();
        }

        return null;
    }

    public function render() {
        if ($this->is()) {
            return $this->method()->render();
        }

        return null;
    }

    public function json() {
        if ($this->is()) {
            $this->engine()->json();
        }
    }

    public function calc($name, $key = null) {
        if ($this->is()) {
            return $this->method()->calc($name, $key);
        }

        return null;
    }

    public function value($name, $echo = true) {
        $render = '';

        if ($this->is()) {
            $render = $this->method()->value($name, false);
        }

        if ($echo) {
            echo $render;
        }

        return $render;
    }

    public function args($name) {
        if ($this->is()) {
            return $this->method()->args($name);
        }

        return null;
    }
}
