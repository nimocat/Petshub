<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_admin_rich_snippets {
    public $prefix = 'rich-snippets';

    public function __construct() {
        add_filter('gdrts_admin_settings_panels', array($this, 'panels'), 20);
        add_filter('gdrts_admin_internal_settings', array($this, 'settings'));
        add_filter('gdrts_admin_icon_rich-snippets', array($this, 'icon'));

        add_filter('gdrts_admin_metabox_tabs', array($this, 'metabox_tabs'));
        add_action('gdrts_admin_metabox_content_rich-snippets', array($this, 'metabox_content'));
        add_action('gdrts_admin_metabox_save_post', array($this, 'metabox_save'));

        add_action('gdrts_load_admin_page_settings_addon_rich_snippets', array($this, 'help'));
    }

    public function icon($icon) {
        return 'flag';
    }

    public function help() {
        $screen = get_current_screen();

        $screen->add_help_tab(
            array(
                'id' => 'gdrts-help-settings-rich-snippets',
                'title' => __("Rich Snippets", "gd-rating-system"),
                'content' => $this->help_richsnippets()
            )
        );

        $screen->add_help_tab(
            array(
                'id' => 'gdrts-help-settings-rich-snippets-links',
                'title' => __("Rich Snippets Links", "gd-rating-system"),
                'content' => $this->help_richsnippets_links()
            )
        );
    }

    public function help_richsnippets() {
        $render = '<p>'.__("These are some of the rules related to use of rich snippets by Google.", "gd-rating-system").'</p>';
        $render.= '<ul>';
        $render.= '<li>'.__("There is no guarantee that Google will use rich snippets in the search results!", "gd-rating-system").'</li>';
        $render.= '<li>'.__("Dpecification allows use of snippets only on single posts, pages and custom post type posts. You are not allowed to set snippets for archives, and this plugin will not generate rich snippets for anything other than singular content.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("Specification requires that only one rating block is on one page, you can't have rich snippets for different rating methods on the same page.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("When you make changes to the item scope parameters, make sure you test your page in Rich Snippets testing tool. Depending on the item scope you enter, additional parameters must be provided, but due to the nature of these you might need to provide custom code to expand data included for some item types you want to use.", "gd-rating-system").'</li>';
        $render.= '</ul>';
        $render.= '<p>'.__("Make sure you read additional information about using the Rich Snippets.", "gd-rating-system").'</p>';
        $render.= '<ul>';
        $render.= '<li>'.__("Many item scope schema types don't support use of review and rating elements. Make sure you check the Schema.org hierarchy to get all valid types.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("Rich snippet will be generated only if there is at least one vote recorded for specified rating method.", "gd-rating-system").'</li>';
        $render.= '<li>'.__("You can override settings from this page on individual posts or pages edit pages.", "gd-rating-system").'</li>';
        $render.= '</ul>';

        return $render;
    }

    public function help_richsnippets_links() {
        $render = '<p>'.__("Here are few important links for working with rich snippets.", "gd-rating-system").'</p>';
        $render.= '<ul>';
        $render.= '<li><a target="_blank" href="https://developers.google.com/structured-data/testing-tool/">'.__("Google Rich Snippets Testing Tool", "gd-rating-system").'</a></li>';
        $render.= '<li><a target="_blank" href="https://schema.org/docs/full.html">'.__("Schema.org full objects Hierarchy", "gd-rating-system").'</a></li>';
        $render.= '</ul>';

        return $render;
    }

    public function metabox_tabs($tabs) {
        $tabs['rich-snippets'] = '<span class="dashicons dashicons-flag"></span><span class="d4plib-metatab-label">'.__("Rich Snippet", "gd-rating-system").'</span>';

        return $tabs;
    }

    public function metabox_content() {
        global $post;

        $item = gdrts_rating_item::get_instance(null, 'posts', $post->post_type, $post->ID);

        $_gdrts_id = $post->ID;
        $_gdrts_display = $item->get('rich-snippets_display', 'default');
        $_gdrts_method = $item->get('rich-snippets_method', 'default');
        $_gdrts_itemscope = $item->get('rich-snippets_itemscope', '');

        include(GDRTS_PATH.'forms/meta/rich-snippets.php');
    }

    public function metabox_save($post) {
        if (isset($_POST['gdrts']['rich-snippets'])) {
            $data = $_POST['gdrts']['rich-snippets'];

            if (wp_verify_nonce($data['nonce'], 'gdrts-rich-snippets-'.$post->ID) !== false) {
                $item = gdrts_rating_item::get_instance(null, 'posts', $post->post_type, $post->ID);

                $display = sanitize_text_field($data['display']);
                $method = sanitize_text_field($data['method']);
                $itemscope = sanitize_text_field($data['itemscope']);

                $item->prepare_save();

                if ($display == 'default') {
                    $item->un_set('rich-snippets_display');
                } else {
                    $item->set('rich-snippets_display', $display);
                }

                if ($method == 'default') {
                    $item->un_set('rich-snippets_method');
                } else {
                    $item->set('rich-snippets_method', $method);
                }

                if ($itemscope == '') {
                    $item->un_set('rich-snippets_itemscope');
                } else {
                    $item->set('rich-snippets_itemscope', $itemscope);
                }

                $item->save(false);
            }
        }
    }

    public function panels($panels) {
        $panels['addon_rich_snippets'] = array(
            'title' => __("Rich Snippets", "gd-rating-system"), 'icon' => 'flag', 'type' => 'addon',
            'info' => __("Settings on this panel are for control over search engine rich snippets integration.", "gd-rating-system"));

        return $panels;
    }

    public function settings($settings) {
        $settings['addon_rich_snippets'] = array(
            'ars_embed' => array('name' => __("Generate Rich Snippets", "gd-rating-system"), 'settings' => array()),
            'ars_settings' => array('name' => __("Additional Settings", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement('addons', gdrtsa_rich_snippets()->key('single_snippet_per_page'), __("Single snippet per page", "gd-rating-system"), __("Google specification requires that only one rating or review snippet is on a single page.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrtsa_rich_snippets()->get('single_snippet_per_page')),
                new d4pSettingElement('addons', gdrtsa_rich_snippets()->key('snippets_on_singular_pages_only'), __("Snippet on singular pages only", "gd-rating-system"), __("Google specification requires that rating or review is placed on content pages, not in archives.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrtsa_rich_snippets()->get('snippets_on_singular_pages_only'))
            ))
        );

        foreach (gdrts()->get_entity_types('posts') as $name => $label) {
            $key = $name.'_snippet_';

            $settings['addon_rich_snippets']['ars_embed']['settings'][] = new d4pSettingElement('', '', $label, '', d4pSettingType::HR);

            $settings['addon_rich_snippets']['ars_embed']['settings'][] = new d4pSettingElement('addons', gdrtsa_rich_snippets()->key($key.'display'), __("Display", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsa_rich_snippets()->get($key.'display'), 'array', $this->get_list_embed_locations());
            $settings['addon_rich_snippets']['ars_embed']['settings'][] = new d4pSettingElement('addons', gdrtsa_rich_snippets()->key($key.'method'), __("Method", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsa_rich_snippets()->get($key.'method'), 'array', $this->get_list_embed_methods());
            $settings['addon_rich_snippets']['ars_embed']['settings'][] = new d4pSettingElement('addons', gdrtsa_rich_snippets()->key($key.'itemscope'), __("Item Scope", "gd-rating-system"), '', d4pSettingType::TEXT, gdrtsa_rich_snippets()->get($key.'itemscope'));
        }

        return $settings;
    }

    public function get_list_embed_locations() {
        return array(
            'jsonld' => __("Use JSON-LD", "gd-rating-system"),
            'microdata' => __("Use Microdata", "gd-rating-system"),
            'hide' => __("Hide", "gd-rating-system")
        );
    }

    public function get_list_embed_methods() {
        $list = array();

        foreach (gdrts()->methods as $key => $data) {
            if (gdrts_is_method_loaded($key) && $key != 'like-this' && $key != 'emote-this') {
                if (gdrts_method_has_series($key)) {
                    $obj = gdrts_get_method_object($key);

                    foreach ($obj->all_series_list() as $sers => $label) {
                        $list[$key.'::'.$sers] = $data['label'].' &minus; '.$label;
                    }
                } else {
                    $list[$key] = $data['label'];
                }
            }
        }

        return $list;
    }
}

global $_gdrts_addon_admin_rich_snippets;
$_gdrts_addon_admin_rich_snippets = new gdrts_addon_admin_rich_snippets();

function gdrtsa_admin_rich_snippets() {
    global $_gdrts_addon_admin_rich_snippets;
    return $_gdrts_addon_admin_rich_snippets;
}
