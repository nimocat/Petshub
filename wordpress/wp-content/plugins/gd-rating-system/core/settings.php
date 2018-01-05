<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_settings extends d4p_settings_core {
    public $base = 'gdrts';

    public $settings = array(
        'core' => array(
            'activated' => 0
        ),
        'early' => array(
            'custom_entities' => array()
        ),
        'load' => array(
            
        ),
        'entities' => array(
            
        ),
        'settings' => array(
            'db_cache_time_global' => 86400,
            'db_cache_time_query' => 14400,
            'db_cache_on_query' => false,
            'fonticons_font' => true,
            'debug_rating_block' => false,
            'debug_files' => 'auto',
            'debug_ajax_error' => 'console',
            'cronjob_hour_of_day' => '2',
            'cronjob_recheck_max' => false,
            'voting_disabled' => false,
            'voting_disabled_message' => 'Voting is currently disabled.',
            'maintenance' => false,
            'maintenance_message' => 'Voting is currently disabled, data maintenance in progress.',
            'use_nonce' => true,
            'annonymous_same_ip' => false,
            'annonymous_verify' => 'ip_or_cookie',
            'ajax_header_no_cache' => true,
            'decimal_round' => 1,
            'admin_log_remove' => false,
            'log_vote_user_agent' => false,
            'step_transfer' => 500,
            'step_recalculate' => 50
        ),
        'templates' => array(
            
        ),
        'items' => array(
            
        ),
        'methods' => array(
            
        ),
        'addons' => array(
            
        )
    );
    public $skip_update = array('items');

    public function __construct() {
        $this->info = new gdrts_core_info();

        add_action('gdrts_early_settings', array($this, 'early_init'));
        add_action('gdrts_load_settings', array($this, 'init'));

        add_filter('gdrts_emote_this_emote_extra_series', array($this, 'emote_extra_series'));
    }

    protected function _update() {
        $old_build = $this->current['info']['build'];

        $this->current['info'] = $this->info->to_array();
        $this->current['info']['install'] = false;
        $this->current['info']['update'] = true;
        $this->current['info']['previous'] = $old_build;

        $this->_settings_update('info', $this->current['info']);

        $settings = $this->_merge();

        foreach ($settings as $key => $data) {
            $now = $this->_settings_get($key);

            if (!is_array($now)) {
                $now = $data;
            } else if (!in_array($key, $this->skip_update)) {
                $now = $this->_upgrade($now, $data);
            }

            $this->current[$key] = $now;

            $this->_settings_update($key, $now);
        }

        $this->_db();
    }

    public function early_init() {
        $now = $this->_settings_get('early');

        if (!is_array($now)) {
            $now = $this->settings['early'];
        }

        $this->current['early'] = $now;
    }

    protected function _db() {
        wp_schedule_single_event(time(), 'gdrts_cron_daily_maintenance_job');

        require_once(GDRTS_PATH.'core/admin/install.php');

        gdrts_install_database();
    }

    protected function _name($name) {
        return 'dev4press_'.$this->info->code.'_'.$name;
    }

    public function items_get($prefix) {
        $results = array();

        foreach ($this->current['items'] as $key => $value) {
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $results[substr($key, strlen($prefix))] = $value;
            }
        }

        return $results;
    }

    public function set($name, $value, $group = 'settings', $save = false) {
        $old = isset($this->current[$group][$name]) ? $this->current[$group][$name] : null;

        if ($old != $value) {
            do_action('gdrts_settings_value_changed', $name, $group, $old, $value);
        }

        $this->current[$group][$name] = $value;

        if ($save) {
            $this->save($group);
        }
    }

    public function save_custom_entity($entity) {
        $this->current['early']['custom_entities'][$entity['name']] = $entity;

        $this->save('early');
    }

    public function delete_custom_entity($entity) {
        if (isset($this->current['early']['custom_entities'][$entity])) {
            unset($this->current['early']['custom_entities'][$entity]);
        }

        $this->save('early');
    }

    public function file_version() {
        return $this->info_version.'.'.$this->info_build;
    }

    public function force_debug() {
        return $this->get('debug_files') == 'source';
    }
}
