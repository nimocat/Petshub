<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_method_stars_rating_init extends gdrts_extension_init {
    public $group = 'methods';
    public $prefix = 'stars-rating';

    public function __construct() {
        parent::__construct();

        add_action('gdrts_load_method_stars-rating', array($this, 'load'), 1);
        add_filter('gdrts_info_method_stars-rating', array($this, 'info'));
    }

    public function register() {
        gdrts_register_method('stars-rating', __("Stars Rating", "gd-rating-system"), true);
    }

    public function settings() {
        $this->register_option('stars', 5);
        $this->register_option('resolution', 100);
        $this->register_option('vote', 'revote');
        $this->register_option('vote_limit', 0);

        $this->register_option('allow_super_admin', true);
        $this->register_option('allow_user_roles', true);
        $this->register_option('allow_visitor', true);
        $this->register_option('allow_author', false);

        $this->register_option('template', 'default');
        $this->register_option('alignment', 'none');
        $this->register_option('responsive', true);
        $this->register_option('distribution', 'normalized');
        $this->register_option('rating', 'average');
        $this->register_option('class', '');

        $this->register_option('style_type', 'font');
        $this->register_option('style_image_name', 'star');
        $this->register_option('style_size', 30);

        $this->register_option('font_color_empty', '#dddddd');
        $this->register_option('font_color_current', '#ffff00');
        $this->register_option('font_color_active', '#dd0000');

        $this->register_option('labels', array(
            __("Poor", "gd-rating-system"),
            __("Bad", "gd-rating-system"),
            __("Good", "gd-rating-system"),
            __("Great", "gd-rating-system"),
            __("Excellent", "gd-rating-system")
        ));
    }

    public function info($info = array()) {
        return array('icon' => 'star', 'description' => __("Classic, stars based rating method.", "gd-rating-system"));
    }

    public function load() {
        do_action('gdrts_pre_load_method_stars-rating');

        require_once(GDRTS_PATH.'methods/stars-rating/load.php');
    }
}

$__gdrts_method_stars_rating = new gdrts_method_stars_rating_init();
