<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_font_default extends gdrts_font {
    public $version = '1.4';
    public $name = 'font';

    public function __construct() {
        parent::__construct();

        $this->label = __("Default Font Icon", "gd-rating-system");

        $this->icons = array(
            'star' => array('char' => 's', 'label' => __("Star", "gd-rating-system")),
            'asterisk' => array('char' => 'a', 'label' => __("Asterisk", "gd-rating-system")),
            'heart' => array('char' => 'h', 'label' => __("Heart", "gd-rating-system")),
            'bell' => array('char' => 'b', 'label' => __("Bell", "gd-rating-system")),
            'square' => array('char' => 'q', 'label' => __("Square", "gd-rating-system")),
            'circle' => array('char' => 'c', 'label' => __("Circle", "gd-rating-system")),
            'gear' => array('char' => 'g', 'label' => __("Gear", "gd-rating-system")),
            'trophy' => array('char' => 't', 'label' => __("Trophy", "gd-rating-system")),
            'snowflake' => array('char' => 'f', 'label' => __("Snowflake", "gd-rating-system")),
            'like' => array('char' => 'l', 'label' => __("Thumb", "gd-rating-system")),
            'like2' => array('char' => 'k', 'label' => __("Thumb Alt", "gd-rating-system")),
            'dislike' => array('char' => 'd', 'label' => __("Thumb Down", "gd-rating-system")),
            'dislike2' => array('char' => 'i', 'label' => __("Thumb Down Alt", "gd-rating-system")),
            'smile' => array('char' => 'm', 'label' => __("Smile", "gd-rating-system")),
            'frown' => array('char' => 'r', 'label' => __("Frown", "gd-rating-system")),
            'plus' => array('char' => '+', 'label' => __("Plus", "gd-rating-system")),
            'minus' => array('char' => '-', 'label' => __("Minus", "gd-rating-system")),
            'spinner' => array('char' => 'x', 'label' => __("Spinner", "gd-rating-system")),
            'clear' => array('char' => 'e', 'label' => __("Clear", "gd-rating-system")),
            'check' => array('char' => 'j', 'label' => __("Check", "gd-rating-system"))
        );
    }

    public function enqueue_core_files() {
        $file = gdrts_plugin()->is_debug ? 'default.css' : 'default.min.css';

        wp_enqueue_style('gdrts-font', GDRTS_URL.'font/'.$file, array('gdrts-rating'), $this->version);
    }
}
