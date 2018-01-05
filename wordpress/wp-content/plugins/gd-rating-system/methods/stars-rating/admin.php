<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_method_admin_stars_rating {
    public $prefix = 'stars-rating';

    public function __construct() {
        add_filter('gdrts_admin_settings_panels', array($this, 'panels'));
        add_filter('gdrts_admin_internal_settings', array($this, 'settings'));
        add_filter('gdrts_admin_icon_stars-rating', array($this, 'icon'));
        add_filter('gdrts_admin_rule_stars-rating', array($this, 'rule'), 10, 3);

        add_filter('gdrts_votes_grid_content_column_method', array($this, 'grid_vote_item'), 10, 2);
        add_filter('gdrts_ratings_grid_ratings', array($this, 'grid_ratings'), 10, 2);
        add_filter('gdrts_votes_grid_vote_stars-rating', array($this, 'grid_vote'), 10, 2);
    }

    public function icon($icon) {
        return 'star';
    }

    public function grid_ratings($list, $item) {
        if (isset($item->meta['stars-rating_rating'])) {
            $votes = $item->meta['stars-rating_votes'];

            $list[$this->prefix] = '<i aria-hidden="true" class="fa fa-star"></i> '.__("Stars Rating", "gd-rating-system").': <strong>'.$item->meta['stars-rating_rating'].'</strong>';
            $list[$this->prefix].= ' ('.$votes.' '._n("vote", "votes", $votes, "gd-rating-system").')';
        }

        return $list;
    }

    public function grid_vote_item($label, $item) {
        if ($item->method == 'stars-rating') {
            $label = '<i aria-hidden="true" class="fa fa-star"></i> '.$label;
        }

        return $label;
    }

    public function grid_vote($list, $item) {
        $render = '<i title="'.__("Status", "gd-rating-system").': '.$item->status.'" class="fa fa-'.($item->status == 'active' ? 'check-circle' : 'times-circle').' fa-fw"></i> ';
        $render.= '<strong>'.$item->meta['vote'].'</strong> '.__("out of", "gd-rating-system").' '.$item->meta['max'];

        return $render;
    }

    public function rule($settings, $prefix, $prekey) {
        return $this->_shared_settings($prefix, $prekey);
    }

    public function panels($panels) {
        $panels['method_stars_rating'] = array(
            'title' => __("Stars Rating", "gd-rating-system"), 'icon' => 'star', 'type' => 'method',
            'info' => __("Settings on this panel are for global control over Stars Rating integration. Each rating entity type can have own settings to override default ones.", "gd-rating-system"));

        return $panels;
    }

    public function settings($settings) {
        $settings['method_stars_rating'] = $this->_shared_settings();

        return $settings;
    }

    public function _shared_settings($prefix = '', $prekey = '') {
        $real_prefix = empty($prefix) ? 'methods' : $prefix;
        $type = gdrtsm_stars_rating()->get('style_type', $prefix, $prekey);

        return apply_filters('gdrts_shared_settings_stars-rating', array(
            'msr_rating' => array('name' => __("Rating", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('stars', $prekey), __("Stars", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsm_stars_rating()->get('stars', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_stars()),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('resolution', $prekey), __("Resolution", "gd-rating-system"), __("Determines minimal part of the star user is allowed to rate with.", "gd-rating-system"), d4pSettingType::SELECT, gdrtsm_stars_rating()->get('resolution', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_resolutions()),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('vote', $prekey), __("Vote", "gd-rating-system"), __("Control how many times user can vote.", "gd-rating-system"), d4pSettingType::SELECT, gdrtsm_stars_rating()->get('vote', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_vote()),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('vote_limit', $prekey), __("Vote Limit", "gd-rating-system"), __("Limit number of attemps per item. If the Vote is set to Multiple votes, this will limit number of votes. If the Vote is set to Revote, this will limit number of revote attempts.", "gd-rating-system"), d4pSettingType::NUMBER, gdrtsm_stars_rating()->get('vote_limit', $prefix, $prekey))
            )),
            'msr_allowed' => array('name' => __("Users allowed to vote", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('allow_author', $prekey), __("Author", "gd-rating-system"), __("This will be used only if author for the rating item can be determined (posts, comments).", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrtsm_stars_rating()->get('allow_author', $prefix, $prekey)),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('allow_super_admin', $prekey), __("Super Admin", "gd-rating-system"), '', d4pSettingType::BOOLEAN, gdrtsm_stars_rating()->get('allow_super_admin', $prefix, $prekey)),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('allow_visitor', $prekey), __("Visitors", "gd-rating-system"), __("Visitors are not logged in.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrtsm_stars_rating()->get('allow_visitor', $prefix, $prekey)),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('allow_user_roles', $prekey), __("User Roles", "gd-rating-system"), '', d4pSettingType::CHECKBOXES, gdrtsm_stars_rating()->get('allow_user_roles', $prefix, $prekey), 'array', d4p_list_user_roles())
            )),
            'msr_style' => array('name' => __("Style", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('template', $prekey), __("Template", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsm_stars_rating()->get('template', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_templates('stars-rating', 'single')),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('style_type', $prekey), __("Type", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsm_stars_rating()->get('style_type', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_style_type(), array('wrapper_class' => "gdrts-style-type-selection")),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('style_image_name', $prekey), __("Image", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsm_stars_rating()->get('style_image_name', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_style_image_name(), array('wrapper_class' => 'gdrts-select-type gdrts-sel-type-image '.($type == 'image' ? 'gdrts-select-type-show' : ''))),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('style_size', $prekey), __("Size", "gd-rating-system"), '', d4pSettingType::INTEGER, gdrtsm_stars_rating()->get('style_size', $prefix, $prekey), '', array(), array('label_unit' => "px"))
            )),
            'msr_color' => array('name' => __("Font Icons Colors", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('font_color_empty', $prekey), __("Empty Stars", "gd-rating-system"), '', d4pSettingType::COLOR, gdrtsm_stars_rating()->get('font_color_empty', $prefix, $prekey)),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('font_color_current', $prekey), __("Current Stars", "gd-rating-system"), '', d4pSettingType::COLOR, gdrtsm_stars_rating()->get('font_color_current', $prefix, $prekey)),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('font_color_active', $prekey), __("Active Stars", "gd-rating-system"), '', d4pSettingType::COLOR, gdrtsm_stars_rating()->get('font_color_active', $prefix, $prekey))
            )),
            'msr_extra' => array('name' => __("Extra", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('responsive', $prekey), __("Responsive", "gd-rating-system"), __("Plugin will attempt to detect available space for rating stars and make them smaller to fit.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrtsm_stars_rating()->get('responsive', $prefix, $prekey)),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('distribution', $prekey), __("Votes Distribution", "gd-rating-system"), __("For distribution display, it is best to use full star rating resolution. If you don't use full star resolution, normalized display will use ceil rounding of votes to full stars for display purposes only.", "gd-rating-system"), d4pSettingType::SELECT, gdrtsm_stars_rating()->get('distribution', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_distributions()),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('rating', $prekey), __("Rating for Display", "gd-rating-system"), '', d4pSettingType::SELECT, gdrtsm_stars_rating()->get('rating', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_rating_value()),
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('class', $prekey), __("CSS Class", "gd-rating-system"), __("One or more additional CSS classes to add to the rating block.", "gd-rating-system"), d4pSettingType::TEXT, gdrtsm_stars_rating()->get('class', $prefix, $prekey))
            )),
            'msr_labels' => array('name' => __("Labels", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('labels', $prekey), __("Labels", "gd-rating-system"), __("Each label corresponds to one star. If you use more stars than you have labels, plugin will generate labels automatically based on star number.", "gd-rating-system"), d4pSettingType::EXPANDABLE_TEXT, gdrtsm_stars_rating()->get('labels', $prefix, $prekey), '', array(), array('label_button_add' => __("Add New Label", "gd-rating-system")))
            )),
            'msr_advanced' => array('name' => __("Advanced", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement($real_prefix, gdrtsm_stars_rating()->key('alignment', $prekey), __("Alignment", "gd-rating-system"), __("This adds alignement class to the block, but will work only if you set block's inner wrapper element width. You need to adjust alignment styling through your theme, you can use alignment class added by the plugin to add styling.", "gd-rating-system"), d4pSettingType::SELECT, gdrtsm_stars_rating()->get('alignment', $prefix, $prekey), 'array', gdrts_admin_shared::data_list_align()),
            ))
        ), $type, $real_prefix, $prefix, $prekey);
    }
}

global $_gdrts_method_admin_stars_rating;
$_gdrts_method_admin_stars_rating = new gdrts_method_admin_stars_rating();

function gdrtsa_admin_stars_rating() {
    global $_gdrts_method_admin_stars_rating;
    return $_gdrts_method_admin_stars_rating;
}
