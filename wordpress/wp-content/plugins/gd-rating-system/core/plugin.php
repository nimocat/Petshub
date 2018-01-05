<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_plugin {
    public $is_debug;
    public $wp_version;
    public $cap = 'gdrts_standard';

    public $widgets = array(
        'stars-rating-list' => array('method' => 'stars-rating', 'widget' => 'gdrtsWidget_stars_rating_list')
    );

    function __construct() {
        add_action('plugins_loaded', array($this, 'core'));
        add_action('after_setup_theme', array($this, 'init'));
    }

    public function core() {
        global $wp_version;

        $this->wp_version = substr(str_replace('.', '', $wp_version), 0, 2);

        add_action('widgets_init', array($this, 'widgets_init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        $this->load_language();

        define('GDRTS_WPV', intval($this->wp_version));

        add_action('init', array($this, 'rating_load'), 15);
        add_action('init', array($this, 'rating_start'), 20);

        add_action('gdrts_cron_daily_maintenance_job', array($this, 'maintenance_job'));
    }

    public function init() {
        $this->is_debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG;

        require_once(GDRTS_PATH.'rating/base.render.php');
    }

    public function maintenance_job() {
        @ini_set('memory_limit', '256M');
        @set_time_limit(0);

        require_once(GDRTS_PATH.'core/admin/cron.php');

        do_action('gdrts_daily_maintenance_job_start');

        if (gdrts_settings()->get('cronjob_recheck_max')) {
            gdrts_admin_cron::recalculate_max_changed();

            gdrts_settings()->set('cronjob_recheck_max', false, 'settings', true);
        }

        gdrts_admin_cron::recalculate_statistics();

        do_action('gdrts_daily_maintenance_job_end');
    }

    public function file($type, $name, $d4p = false, $min = true, $base_url = null) {
        $get = is_null($base_url) ? GDRTS_URL : $base_url;

        if ($d4p) {
            $get.= 'd4plib/resources/';
        }

        if ($name == 'font') {
            $get.= 'font/styles.css';
        } else {
            $get.= $type.'/'.$name;

            if (!$this->is_debug && $type != 'font' && $min) {
                $get.= '.min';
            }

            $get.= '.'.$type;
        }

        return $get;
    }

    public function rating_load() {
        do_action('gdrts_load');

        $this->init_capabilities();

        if (!wp_next_scheduled('gdrts_cron_daily_maintenance_job')) {
            $cron_hour = intval(gdrts_settings()->get('cronjob_hour_of_day'));
            $cron_time = mktime($cron_hour, 0, 0, date('m'), date('d') + 1, date('Y'));

            wp_schedule_event($cron_time, 'daily', 'gdrts_cron_daily_maintenance_job');
        }

        if (gdrts_settings()->get('debug_rating_block')) {
            add_action('gdrts-template-rating-block-before', array($this, 'rating_block_single'), 1);
        }

        do_action('gdrts_init');
    }

    public function rating_start() {
        do_action('gdrts_core');
    }

    public function enqueue_scripts() {
        $this->enqueue_core_files();
    }

    public function widgets_init() {
        $this->widgets = apply_filters('gdrts_widgets_list', $this->widgets);

        foreach ($this->widgets as $folder => $data) {
            $path = isset($data['folder']) ? $data['folder'] : GDRTS_PATH.'widgets/';

            require_once($path.$folder.'.php');

            register_widget($data['widget']);
        }
    }

    public function init_capabilities() {
        $role = get_role('administrator');

        if (!is_null($role)) {
            $role->add_cap('gdrts_standard');
        } else {
            $this->cap = 'activate_plugins';
        }

        define('GDRTS_CAP', $this->cap);
    }

    public function load_language() {
        load_plugin_textdomain('gd-rating-system', false, 'gd-rating-system/languages');
    }

    public function enqueue_core_files() {
        if (gdrts_settings()->force_debug()) {
            $this->is_debug = true;
        }

        $depend_css = apply_filters('gdrts_enqueue_core_depend_css', array('gdrts-gridism'));
        $depend_js = apply_filters('gdrts_enqueue_core_depend_js', array('jquery'));

        wp_enqueue_style('gdrts-gridism', $this->file('css', 'gridism', false, false), array(), gdrts_settings()->file_version());
        wp_enqueue_style('gdrts-rating', $this->file('css', 'rating'), $depend_css, gdrts_settings()->file_version());

        wp_enqueue_script('gdrts-rating', $this->file('js', 'rating'), $depend_js, gdrts_settings()->file_version(), true);

        do_action('gdrts_enqueue_core_files');

        $_args = apply_filters('gdrts_enqueue_core_files_data', array(
            'url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gd-rating-system'),
            'user' => get_current_user_id(),
            'handler' => 'gdrts_live_handler',
            'ajax_error' => gdrts_settings()->get('debug_ajax_error'),
            'wp_version' => GDRTS_WPV
        ));

        wp_localize_script('gdrts-rating', 'gdrts_rating_data', $_args);
    }

    public function rating_block_single() {
        $lines = array('GD Rating System '.ucfirst(gdrts_settings()->info_edition).' '.gdrts_settings()->info_version.' - b'.gdrts_settings()->info_build);

        echo '<!-- '.join(D4P_EOL, $lines).' -->'.D4P_EOL;
    }
}
