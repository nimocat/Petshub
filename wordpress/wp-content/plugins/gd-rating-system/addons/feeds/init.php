<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_feeds_init extends gdrts_extension_init {
    public $group = 'addons';
    public $prefix = 'feeds';

    public function __construct() {
        parent::__construct();

        add_action('gdrts_load_addon_feeds', array($this, 'load'), 2);
        add_filter('gdrts_info_addon_feeds', array($this, 'info'));
    }

    public function register() {
        gdrts_register_addon('feeds', __("Feeds", "gd-rating-system"));
    }

    public function settings() {
        $this->register_option('rss', true);
        $this->register_option('rss_hide', false);

        $this->register_option('amp', true);
        $this->register_option('amp_hide', false);

        $this->register_option('fia', true);
        $this->register_option('fia_hide', false);

        $this->register_option('anf', true);
        $this->register_option('anf_hide', false);
    }

    public function info($info = array()) {
        return array('icon' => 'rss-square', 'description' => __("Add feed friends rating into different feeds versions.", "gd-rating-system"));
    }

    public function load() {
        require_once(GDRTS_PATH.'addons/feeds/functions.php');
        require_once(GDRTS_PATH.'addons/feeds/load.php');
    }
}

$__gdrts_addon_feeds = new gdrts_addon_feeds_init();
