<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_shortcake_init extends gdrts_extension_init {
    public $group = 'addons';
    public $prefix = 'shortcake';

    public function __construct() {
        parent::__construct();

        add_action('gdrts_load_addon_shortcake', array($this, 'load'), 2);
        add_filter('gdrts_info_addon_shortcake', array($this, 'info'));
    }

    public function register() {
        gdrts_register_addon('shortcake', __("Shortcake", "gd-rating-system"));
    }

    public function settings() { }

    public function info($info = array()) {
        return array('icon' => 'code', 'description' => __("Integration with Shortcake Shortcode UI plugin.", "gd-rating-system"));
    }

    public function load() {
        if (defined('SHORTCODE_UI_VERSION')) {
            require_once(GDRTS_PATH.'addons/shortcake/load.php');
        }
    }
}

$__gdrts_addon_shortcake = new gdrts_addon_shortcake_init();
