<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_rich_snippets_init extends gdrts_extension_init {
    public $group = 'addons';
    public $prefix = 'rich-snippets';

    public function __construct() {
        parent::__construct();

        add_action('gdrts_load_addon_rich-snippets', array($this, 'load'), 2);
        add_filter('gdrts_info_addon_rich-snippets', array($this, 'info'));
    }

    public function register() {
        gdrts_register_addon('rich-snippets', __("Rich Snippets", "gd-rating-system"));
    }

    public function settings() {
        $this->register_option('single_snippet_per_page', true);
        $this->register_option('snippets_on_singular_pages_only', true);

        foreach (array_keys(gdrts()->get_entity_types('posts')) as $name) {
            $location = in_array($name, array('post', 'page')) ? 'microdata' : 'hide';

            $itemscope = $name == 'page' ? 'WebPage' : (
                         $name == 'post' ? 'CreativeWork' : (
                         $name == 'attachment' ? 'MediaObject' : 'Product'));

            $this->register_option($name.'_snippet_display', $location);
            $this->register_option($name.'_snippet_method', 'stars-rating');
            $this->register_option($name.'_snippet_itemscope', $itemscope);
        }
    }

    public function info($info = array()) {
        return array('icon' => 'flag', 'description' => __("Generate rich snippets used by Google for search engine results.", "gd-rating-system"));
    }

    public function load() {
        require_once(GDRTS_PATH.'addons/rich-snippets/load.php');
    }
}

$__gdrts_addon_rich_snippets = new gdrts_addon_rich_snippets_init();
