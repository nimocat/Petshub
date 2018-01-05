<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_feeds extends gdrts_addon {
    public $prefix = 'feeds';

    public function _load_admin() {
        require_once(GDRTS_PATH.'addons/feeds/admin.php');
    }

    public function core() {
        if (is_admin()) {
            return;
        }

        add_filter('gdrts_engine_single_rendering_override', array($this, 'single_rendering_override'), 100);

        add_filter('gdrts_stars_rating_loop_single_args', array($this, 'single_loop_args'), 100);
        add_filter('gdrts_thumbs_rating_loop_single_args', array($this, 'single_loop_args'), 100);
        add_filter('gdrts_like_this_loop_single_args', array($this, 'single_loop_args'), 100);
        add_filter('gdrts_emote_this_loop_single_args', array($this, 'single_loop_args'), 100);
    }

    public function single_rendering_override($render) {
        if (($this->get('amp') && gdrts_is_amp() && $this->get('amp_hide')) ||
            ($this->get('fia') && gdrts_is_fia() && $this->get('fia_hide')) ||
            ($this->get('anf') && gdrts_is_anf() && $this->get('anf_hide')) ||
            ($this->get('rss') && gdrts_is_rss() && $this->get('amp_hide'))) {
            $render = '';
        }

        return $render;
    }

    public function single_loop_args($args) {
        if ($this->is_feed_detected()) {
            $args['template'] = 'feed';
        }

        return $args;
    }

    public function is_feed_detected() {
        if (D4P_AJAX) {
            return false;
        }

        return ($this->get('amp') && gdrts_is_amp()) || 
               ($this->get('fia') && gdrts_is_fia()) || 
               ($this->get('anf') && gdrts_is_anf()) ||
               ($this->get('rss') && gdrts_is_rss());
    }
}

global $_gdrts_addon_feeds;
$_gdrts_addon_feeds = new gdrts_addon_feeds();

function gdrtsa_feeds() {
    global $_gdrts_addon_feeds;
    return $_gdrts_addon_feeds;
}
