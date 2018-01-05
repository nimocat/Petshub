<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_admin_dynamic_load {
    public $prefix = 'dynamic-load';

    public function __construct() {
        add_filter('gdrts_admin_icon_dynamic-load', array($this, 'icon'));
    }

    public function icon($icon) {
        return 'spinner';
    }
}

global $_gdrts_addon_admin_dynamic_load;
$_gdrts_addon_admin_dynamic_load = new gdrts_addon_admin_dynamic_load();

function gdrtsa_admin_dynamic_load() {
    global $_gdrts_addon_admin_dynamic_load;
    return $_gdrts_addon_admin_dynamic_load;
}
