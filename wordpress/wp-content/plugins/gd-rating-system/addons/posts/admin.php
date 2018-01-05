<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_admin_posts {
    public $prefix = 'posts';

    public $post_types;

    public function __construct() {
        $this->post_types = gdrts_posts_valid_post_types();

        add_filter('gdrts_admin_settings_panels', array($this, 'panels'), 20);
        add_filter('gdrts_admin_internal_settings', array($this, 'settings'));
        add_filter('gdrts_admin_icon_posts', array($this, 'icon'));

        add_filter('gdrts_admin_metabox_tabs', array($this, 'metabox_tabs'), 10, 3);
        add_action('gdrts_admin_metabox_content_posts-integration', array($this, 'metabox_content_integration'), 10, 2);
        add_action('gdrts_admin_metabox_save_post', array($this, 'metabox_save'));

        add_action('gdrts_load_admin_page_settings_addon_posts', array($this, 'help'));
    }

    public function help() {
        $screen = get_current_screen();

        $screen->add_help_tab(
            array(
                'id' => 'gdrts-help-settings-posts',
                'title' => __("Posts Integration", "gd-rating-system"),
                'content' => $this->help_posts_integration()
            )
        );
    }

    public function help_posts_integration() {
        $render = '<p>'.__("There are some limitations for using auto integration of ratings into posts.", "gd-rating-system").'</p>';
        $render.= '<ul>';
        $render.= '<li>'.__("This addon adds rating block into single post, page or other post types content using 'the_content' filter. It works on singular pages only and only inside the main query loop.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("If your theme uses some non standard approach to display posts, this addon will most likely fail to add rating block into content.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("For post types added by third party plugins, this addon might not work if these plugins use some non standard method to display the content or use own query or templates system.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("This addon will not work with bbPress topics and replies, and bbPress post types are not displayed in this addon settings.", "gd-rating-system").'</li>';
        $render.= '</ul>';
        $render.= '<p>'.__("There are different things you can do to add ratings if this addon is not working for your post types or theme.", "gd-rating-system").'</p>';
        $render.= '<ul>';
        $render.= '<li>'.__("You can use shortcodes in the posts, pages or custom post types posts to display rating block in any location inside the content.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("You can use manual integration functions to add rating block directly into theme templates.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("To add ratings into bbPress topics or replies, use bbPress addon.", "gd-rating-system").'</li>';
        $render.= '</ul>';

        return $render;
    }

    public function icon($icon) {
        return 'thumb-tack';
    }

    public function metabox_tabs($tabs, $post_id, $post_type) {
        if (in_array($post_type, $this->post_types)) {
            $tabs['posts-integration'] = '<span class="dashicons dashicons-admin-post"></span><span class="d4plib-metatab-label">'.__("Rating Embed", "gd-rating-system").'</span>';
        }

        return $tabs;
    }

    public function metabox_content_integration($post_id, $post_type) {
        global $post;

        $item = gdrts_rating_item::get_instance(null, 'posts', $post->post_type, $post->ID);

        $_gdrts_id = $post->ID;
        $_gdrts_display = $item->get('posts-integration_location', 'default');
        $_gdrts_method = $item->get('posts-integration_method', 'default');

        include(GDRTS_PATH.'forms/meta/posts-integration.php');
    }

    public function metabox_save($post) {
        if (isset($_POST['gdrts']['posts-integration'])) {
            $data = $_POST['gdrts']['posts-integration'];

            if (wp_verify_nonce($data['nonce'], 'gdrts-posts-integration-'.$post->ID) !== false) {
                $item = gdrts_rating_item::get_instance(null, 'posts', $post->post_type, $post->ID);

                $display = d4p_sanitize_basic($data['location']);
                $method = d4p_sanitize_basic($data['method']);

                $item->prepare_save();

                if ($display == 'default') {
                    $item->un_set('posts-integration_location');
                } else {
                    $item->set('posts-integration_location', $display);
                }

                if ($method == 'default') {
                    $item->un_set('posts-integration_method');
                } else {
                    $item->set('posts-integration_method', $method);
                }

                $item->save(false);
            }
        }
    }

    public function panels($panels) {
        $panels['addon_posts'] = array(
            'title' => __("Posts", "gd-rating-system"), 'icon' => 'thumb-tack', 'type' => 'addon',
            'info' => __("Settings on this panel are for control over posts integration.", "gd-rating-system"));

        return $panels;
    }

    public function settings($settings) {
        $settings['addon_posts'] = array('ap_embed' => array('name' => __("Auto Embed", "gd-rating-system"), 'settings' => array()));

        foreach ($this->post_types as $name) {
            $label = gdrts()->get_entity_type_label('posts', $name);

            $key = $name.'_auto_embed_';

            $settings['addon_posts']['ap_embed']['settings'][] = new d4pSettingElement('', '', $label, '', d4pSettingType::HR);

            $settings['addon_posts']['ap_embed']['settings'][] = new d4pSettingElement('addons', gdrtsa_posts()->key($key.'location'), __("Location", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsa_posts()->get($key.'location'), 'array', $this->get_list_embed_locations());
            $settings['addon_posts']['ap_embed']['settings'][] = new d4pSettingElement('addons', gdrtsa_posts()->key($key.'method'), __("Method", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsa_posts()->get($key.'method'), 'array', gdrts_admin_shared::data_list_embed_methods());
        }

        return $settings;
    }

    public function get_list_embed_locations() {
        return array(
            'top' => __("Top", "gd-rating-system"),
            'bottom' => __("Bottom", "gd-rating-system"),
            'both' => __("Top and Bottom", "gd-rating-system"),
            'hide' => __("Hide", "gd-rating-system")
        );
    }
}

global $_gdrts_addon_admin_posts;
$_gdrts_addon_admin_posts = new gdrts_addon_admin_posts();

function gdrtsa_admin_posts() {
    global $_gdrts_addon_admin_posts;
    return $_gdrts_addon_admin_posts;
}
