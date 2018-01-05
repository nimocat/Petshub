<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_settings {
    private $settings;

    function __construct() {
        $this->init();
    }

    public function get($panel, $group = '') {
        if ($group == '') {
            return $this->settings[$panel];
        } else {
            return $this->settings[$panel][$group];
        }
    }

    public function settings($panel) {
        $list = array();

        foreach ($this->settings[$panel] as $obj) {
            foreach ($obj['settings'] as $o) {
                $list[] = $o;
            }
        }

        return $list;
    }

    private function init() {
        $extensions = array(
            'extensions_methods' => array('name' => __("Rating Methods", "gd-rating-system"), 'settings' => array()),
            'extensions_addons' => array('name' => __("Addons", "gd-rating-system"), 'settings' => array()),
            'extensions_pro' => array('name' => "More rating methods and addons", 'settings' => array(
                new d4pSettingElement('', '', 'GD Rating System Pro', 'You can upgrade to GD Rating System Pro <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/">here</a>. <p style="font-weight: normal; margin: 10px 0 0;">To learn more about the features available in Pro version only, check out this <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/free-vs-pro-plugin/">FREE vs. PRO</a> comparison.</p>', d4pSettingType::INFO)
            )),
        );

        foreach (gdrts()->methods as $method => $obj) {
            $info = apply_filters('gdrts_info_method_'.$method, array('icon' => '', 'description' => ''));
            $label = ($info['icon'] != '' ? d4p_render_icon($info['icon'], 'i', true, true).' ' : '').$obj['label'];

            $extensions['extensions_methods']['settings'][] =
                    new d4pSettingElement('load', 'method_'.$method, $label, $info['description'], d4pSettingType::BOOLEAN, gdrts_settings()->get('method_'.$method, 'load'));
        }

        foreach (gdrts()->addons as $addon => $obj) {
            $info = apply_filters('gdrts_info_addon_'.$addon, array('icon' => '', 'description' => ''));
            $label = ($info['icon'] != '' ? d4p_render_icon($info['icon'], 'i', true, true).' ' : '').$obj['label'];

            $extensions['extensions_addons']['settings'][] =
                    new d4pSettingElement('load', 'addon_'.$addon, $label, $info['description'], d4pSettingType::BOOLEAN, gdrts_settings()->get('addon_'.$addon, 'load'));
        }

        $this->settings = apply_filters('gdrts_admin_internal_settings', array(
            'extensions' => $extensions,
            'global' => array(
                'global_log' => array('name' => __("Votes Log", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'log_vote_user_agent', __("Save User Agent", "gd-rating-system"), __("User agent string can take a lot of space in the database, and they represent user browser or application used to vote.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('log_vote_user_agent'))
                )),
                'global_anonymous' => array('name' => __("Anonymous Ratings", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'annonymous_verify', __("Verification", "gd-rating-system"), __("If the user voting is visitor (not logged in), there are different methods to verify if visitor can vote.", "gd-rating-system"), d4pSettingType::SELECT, gdrts_settings()->get('annonymous_verify'), 'array', $this->data_list_annonymous_verify()),
                    new d4pSettingElement('settings', 'annonymous_same_ip', __("IP Validation", "gd-rating-system"), __("If logged user and visitor (not logged in) share IP, this option determines if visitor can vote.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('annonymous_same_ip'), null, array(), array('label' => __("Allow visitor to have same IP as logged user", "gd-rating-system")))
                )),
                'global_ajax' => array('name' => __("AJAX Requests", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'ajax_header_no_cache', __("Set no cache header", "gd-rating-system"), '', d4pSettingType::BOOLEAN, gdrts_settings()->get('ajax_header_no_cache'))
                )),
                'global_calculations' => array('name' => __("Calculations", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'decimal_round', __("Decimal rounding", "gd-rating-system"), '', d4pSettingType::SELECT, gdrts()->decimals(), 'array', $this->data_list_decimal_points())
                ))
            ),
            'security' => array(
                'security_nonce' => array('name' => __("Nonce Protection", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'use_nonce', __("Use Nonce Protection", "gd-rating-system"), __("Each AJAX rating request will be protected by Nonce for additional security. But, if you use cache plugins, Nonce check will fail if the cached pages are too old.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('use_nonce'))
                ))
            ),
            'debug' => array(
                'debug_debug' => array('name' => __("Debug Info", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'debug_rating_block', __("Add into rating block", "gd-rating-system"), __("This will include various useful information into each rating block for debug purposes.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('debug_rating_block'))
                )),
                'debug_files' => array('name' => __("Files", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'debug_files', __("Load CSS and JS", "gd-rating-system"), __("If set to 'Force load sources', plugin will load source files. If set to 'Auto load', plugin will load minified files, or source files, based on the SCRIPT_DEBUG value from WP Config.", "gd-rating-system"), d4pSettingType::SELECT, gdrts_settings()->get('debug_files'), 'array', $this->data_list_debug_files())
                )),
                'debug_js' => array('name' => __("JavaScript", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'debug_ajax_error', __("AJAX Errors", "gd-rating-system"), __("Control how the AJAX errors are handled.", "gd-rating-system"), d4pSettingType::SELECT, gdrts_settings()->get('debug_ajax_error'), 'array', $this->data_list_ajax_errors())
                ))
            ),
            'cache' => array(
                'cache_global' => array('name' => __("Global", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'db_cache_time_global', __("Cache Time to Live", "gd-rating-system"), __("Time (in seconds) for the cache entry to remain in cache before expiration.", "gd-rating-system"), d4pSettingType::ABSINT, gdrts_settings()->get('db_cache_time_global'))
                )),
                'cache_query' => array('name' => __("Main Query System", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'db_cache_on_query', __("Status", "gd-rating-system"), __("Each main period query result will be stored to cache.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('db_cache_on_query'), null, array(), array('label' => __("Enable Cache", "gd-rating-system"))),
                    new d4pSettingElement('settings', 'db_cache_time_query', __("Cache Time to Live", "gd-rating-system"), __("Time (in seconds) for the cache entry to remain in cache before expiration.", "gd-rating-system"), d4pSettingType::ABSINT, gdrts_settings()->get('db_cache_time_query'))
                ))
            ),
            'administration' => array(
                'administration_votes' => array('name' => __("Votes Log", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'admin_log_remove', __("Show Remove from Log", "gd-rating-system"), __("Remove from log option is used for raw removal of votes from log, and it doesn't affect the rating object agregated results. If you don't understand how this works, do not use this option.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('admin_log_remove'))
                )),
                'administration_transfer' => array('name' => __("Transfer Data", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'step_transfer', __("Records per Call", "gd-rating-system"), __("Number of records to process with each call.", "gd-rating-system"), d4pSettingType::ABSINT, gdrts_settings()->get('step_transfer'))
                )),
                'administration_recalculate' => array('name' => __("Recalculation", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'step_recalculate', __("Records per Call", "gd-rating-system"), __("Number of records to process with each call.", "gd-rating-system"), d4pSettingType::ABSINT, gdrts_settings()->get('step_recalculate'))
                ))
            ),
            'advanced' => array(
                'advanced_disabled' => array('name' => __("Disable Voting", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'voting_disabled', __("Status", "gd-rating-system"), __("This option will disable all voting.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('voting_disabled'), null, array(), array('label' => __("Disable Voting", "gd-rating-system"))),
                    new d4pSettingElement('settings', 'voting_disabled_message', __("Message", "gd-rating-system"), __("If you want, you can set the message to be displayed with rating blocks if the voting is disabled.", "gd-rating-system"), d4pSettingType::TEXT, gdrts_settings()->get('voting_disabled_message'))
                )),
                'advanced_maintenance' => array('name' => __("Maintenance Mode", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'maintenance', __("Status", "gd-rating-system"), __("This option will disable all voting.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('maintenance'), null, array(), array('label' => __("Disable Voting", "gd-rating-system"))),
                    new d4pSettingElement('settings', 'maintenance_message', __("Message", "gd-rating-system"), __("If you want, you can set the message to be displayed with rating blocks if the voting is disabled.", "gd-rating-system"), d4pSettingType::TEXT, gdrts_settings()->get('maintenance_message'))
                )),
                'advanced_cron_job' => array('name' => __("Background Maintenance Job", "gd-rating-system"), 'settings' => array(
                    new d4pSettingElement('settings', 'cronjob_hour_of_day', __("Hour of the day to run", "gd-rating-system"), __("Maintenance job will run once a day at the specified hour. Set the time of day when you have smallest number of visitors (usually night time). Based on the server time.", "gd-rating-system"), d4pSettingType::SELECT, gdrts_settings()->get('cronjob_hour_of_day'), 'array', $this->data_list_cronjob_hours()),
                    new d4pSettingElement('settings', 'cronjob_recheck_max', __("Stars Changed Recalculation", "gd-rating-system"), __("If you make changes to the number of stars for stars rating or review methods, enable this option to recalculate ratings during daily maintenance. This option will auto disable after it is done. It will be auto activated if number of stars change is detected.", "gd-rating-system"), d4pSettingType::BOOLEAN, gdrts_settings()->get('cronjob_recheck_max'))
                ))
            )
        ));
    }

    private function data_list_cronjob_hours() {
        $hours = array();

        for ($i = 0; $i < 24; $i++) {
            $hours[$i] = $i;
        }

        return $hours;
    }

    private function data_list_decimal_points() {
        return array(
            1 => __("One decimal", "gd-rating-system"),
            2 => __("Two decimals", "gd-rating-system")
        );
    }

    private function data_list_annonymous_verify() {
        return array(
            'ip_or_cookie' => __("IP or Cookie", "gd-rating-system"),
            'ip_and_cookie' => __("IP and Cookie", "gd-rating-system"),
            'ip' => __("IP Only", "gd-rating-system"),
            'cookie' => __("Cookie Only", "gd-rating-system")
        );
    }

    private function data_list_ajax_errors() {
        return array(
            'hide' => __("Hide", "gd-rating-system"),
            'console' => __("Log to Console", "gd-rating-system"),
            'alert' => __("Show Alert", "gd-rating-system")
        );
    }

    private function data_list_debug_files() {
        return array(
            'auto' => __("Auto load sources or minified", "gd-rating-system"),
            'source' => __("Force load sources", "gd-rating-system")
        );
    }
}
