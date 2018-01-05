<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_ajax {
    public $time_start = 0;

    public function __construct() {
        add_action('wp_ajax_gdrts_tools_recalc', array($this, 'recalc'));
        add_action('wp_ajax_gdrts_transfer_process', array($this, 'transfer'));

        do_action('gdrts_admin_ajax');
    }

    public function check_nonce() {
        $nonce = wp_verify_nonce($_REQUEST['_ajax_nonce'], 'gdrts-admin-internal');

        if ($nonce === false) {
            wp_die(-1);
        }
    }

    public function transfer() {
        $this->check_nonce();

        @ini_set('memory_limit', '256M');
        @set_time_limit(0);

        require_once(GDRTS_PATH.'core/admin/transfer.php');

        $operation = $_POST['operation'];

        switch ($operation) {
            case 'start':
                $settings = (array)$_POST['settings'];
                $total = gdrts_admin_transfer::count_objects($settings);
                $response = array(
                    'objects' => $total,
                    'message' => '- '.__("Transfer process is starting...", "gd-rating-system").
                                 '<br/>* '.sprintf(__("Total of %s rating objects found.", "gd-rating-system"), $total)
                );

                die(json_encode($response));
                break;
            case 'break':
                $response = array(
                    'message' => '* '.__("Transfer process has been stopped.", "gd-rating-system")
                );

                die(json_encode($response));
                break;
            case 'stop':
                $response = array(
                    'message' => '* '.__("Transfer process has finished.", "gd-rating-system")
                );

                die(json_encode($response));
                break;
            case 'run':
                $result = $this->transfer_run();

                $response = array(
                    'message' => '- '.$result
                );

                die(json_encode($response));
                break;
        }
    }

    public function transfer_run() {
        $total = absint($_POST['total']);
        $current = absint($_POST['current']);
        $step = absint($_POST['step']);
        $offset = $current * $step;

        $settings = (array)$_POST['settings'];

        $this->timer_start();

        gdrts_admin_transfer::transfer_objects($offset, $step, $settings);

        $timer = round($this->timer_stop(), 5);

        $done = ($current + 1) * $step;

        if ($done > $total) {
            $done = $total;
        }

        $render = sprintf(__("%s of %s done (%s sec).", "gd-rating-system"), $done, $total, $timer);

        return $render;
    }

    public function recalc() {
        $this->check_nonce();

        @ini_set('memory_limit', '256M');
        @set_time_limit(0);

        require_once(GDRTS_PATH.'core/admin/maintenance.php');

        $operation = $_POST['operation'];

        switch ($operation) {
            case 'start':
                gdrts_settings()->set('maintenance', true, 'settings', true);

                $total = gdrts_admin_maintenance::count_rating_objects();
                $response = array(
                    'objects' => $total,
                    'message' => '* '.sprintf(__("Total of %s rating objects found.", "gd-rating-system"), $total)
                );

                die(json_encode($response));
                break;
            case 'stop':
                gdrts_settings()->set('maintenance', false, 'settings', true);

                $response = array(
                    'message' => '* '.__("Process has completed.", "gd-rating-system")
                );

                die(json_encode($response));
                break;
            case 'run':
                $result = $this->recalc_run();

                $response = array(
                    'message' => '- '.$result
                );

                die(json_encode($response));
                break;
        }
    }

    public function recalc_run() {
        $total = intval($_POST['total']);
        $current = intval($_POST['current']);
        $step = intval($_POST['step']);
        $offset = $current * $step;

        $settings = array();
        $raw = (array)$_POST['settings'];

        foreach ($raw as $operation) {
            $o = explode('|', $operation);

            if (gdrts_is_method_valid($o[0])) {
                if (!isset($settings[$o[0]])) {
                    $settings[$o[0]] = array();
                }

                $settings[$o[0]][] = $o[1];
            }
        }

        $this->timer_start();

        gdrts_admin_maintenance::recalculate_rating_objects($offset, $step, $settings);

        $timer = round($this->timer_stop(), 5);

        $done = ($current + 1) * $step;

        if ($done > $total) {
            $done = $total;
        }

        $render = sprintf(__("%s of %s done (%s sec).", "gd-rating-system"), $done, $total, $timer);

        return $render;
    }

    public function timer_start() {
        $this->time_start = microtime( true );
        return true;
    }

    public function timer_stop() {
        return ( microtime( true ) - $this->time_start );
    }
}

global $_gdrts_admin_ajax;

$_gdrts_admin_ajax = new gdrts_admin_ajax();

function gdrts_ajax_admin() {
    global $_gdrts_admin_ajax;
    return $_gdrts_admin_ajax;
}
