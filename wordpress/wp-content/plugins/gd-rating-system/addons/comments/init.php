<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_comments_init extends gdrts_extension_init {
    public $group = 'addons';
    public $prefix = 'comments';

    public function __construct() {
        parent::__construct();

        add_action('gdrts_load_addon_comments', array($this, 'load'), 2);
        add_filter('gdrts_info_addon_comments', array($this, 'info'));
    }

    public function register() {
        gdrts_register_addon('comments', __("Comments", "gd-rating-system"));
    }

    public function settings() {
        $this->register_option('comments_auto_embed_location', 'bottom');
        $this->register_option('comments_auto_embed_method', 'stars-rating');
    }

    public function info($info = array()) {
        return array('icon' => 'comments-o', 'description' => __("Easy to use direct rating integration for comments.", "gd-rating-system"));
    }

    public function load() {
        require_once(GDRTS_PATH.'addons/comments/load.php');
    }
}

$__gdrts_addon_comments = new gdrts_addon_comments_init();
