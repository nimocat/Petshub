<?php

if (!defined('ABSPATH')) { exit; }

abstract class gdrts_base_engine {
    protected $_engine = '';

    protected $_loop_status = false;

    protected $_args;
    protected $_item;
    protected $_user;

    public $abort = false;

    public function __construct() { }

    public function do_loop_status($status = true) {
        $this->_loop_status = $status;

        if ($status) {
            gdrts_loop()->start($this->_engine);
        } else {
            gdrts_loop()->end();
        }
    }

    public function is_loop() {
        return $this->_loop_status;
    }

    /** @return gdrts_rating_item */
    public function item() {
        return $this->_item;
    }

    /** @return gdrts_core_user */
    public function user() {
        return $this->_user;
    }

    public function args($arg = null) {
        return is_null($arg) ? $this->_args : 
            (isset($this->_args[$arg]) ? $this->_args[$arg] : null);
    }

    public function method() {
        if ($this->is_loop()) {
            return $this->args('method');
        }

        return null;
    }

    abstract public function render($args = array(), $method = array());
    abstract public function loop($args = array(), $method = array());

    abstract public function json();
}
