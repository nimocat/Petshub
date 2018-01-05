<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_posts_init extends gdrts_extension_init {
    public $group = 'addons';
    public $prefix = 'posts';

    public function __construct() {
        parent::__construct();

        add_action('gdrts_load_addon_posts', array($this, 'load'), 2);
        add_filter('gdrts_info_addon_posts', array($this, 'info'));
    }

    public function register() {
        gdrts_register_addon('posts', __("Posts", "gd-rating-system"));
    }

    public function settings() {
        foreach (gdrts_posts_valid_post_types() as $name) {
            $location = in_array($name, array('post', 'page')) ? 'bottom' : 'hide';

            $this->register_option($name.'_auto_embed_location', $location);
            $this->register_option($name.'_auto_embed_method', 'stars-rating');
        }
    }

    public function info($info = array()) {
        return array('icon' => 'thumb-tack', 'description' => __("Easy to use direct rating integration for posts and pages.", "gd-rating-system"));
    }

    public function load() {
        require_once(GDRTS_PATH.'addons/posts/load.php');
    }
}

$__gdrts_addon_posts = new gdrts_addon_posts_init();

function gdrts_posts_valid_post_types() {
    $all = array_keys(gdrts()->get_entity_types('posts'));

    $remove = array();

    if (gdrts_has_bbpress()) {
        $remove[] = bbp_get_forum_post_type();
        $remove[] = bbp_get_topic_post_type();
        $remove[] = bbp_get_reply_post_type();
    }

    return array_values(array_diff($all, $remove));
}
