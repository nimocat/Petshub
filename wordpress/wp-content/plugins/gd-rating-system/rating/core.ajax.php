<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_ajax {
    public function __construct() {
        add_action('wp_ajax_gdrts_live_handler', array($this, 'handler'));
        add_action('wp_ajax_nopriv_gdrts_live_handler', array($this, 'handler'));
    }

    public function handler() {
        $request = json_decode(wp_unslash($_REQUEST['req']));

        $process = apply_filters('gdrts_ajax_live_handler', false, $request);

        if ($process === false) {
            switch ($request->todo) {
                case 'vote':
                    $this->vote($request);
                    break;
                default:
                    $this->error(__("Invalid Request.", "gd-rating-system"), $request->uid);
                    break;
            }
        }
    }

    public function vote($request) {
        gdrts_single()->do_suppress_filters();

        $check_nonce = apply_filters('gdrts_ajax_check_nonce', gdrts_settings()->get('use_nonce'));

        $item_id = intval($request->item);
        $item = gdrts_get_rating_item_by_id($item_id);

        if ($item->error) {
            $this->error(__("Invalid item for rating.", "gd-rating-system"), $request->uid);
        }

        if ($check_nonce) {
            d4p_check_ajax_referer($item->nonce_key(), $request->nonce);
        }

        $request->series = null;

        $user = new gdrts_core_user();
        $user->load_log($item_id, $request->method, $request->series);
        $request->render->series = $request->series;

        $completed = false;
        if (isset($request->meta) && isset($request->method) && is_string($request->method)) {
            switch ($request->method) {
                case 'stars-rating':
                    $completed = gdrtsm_stars_rating()->vote($request->meta, $item, $user, $request->render);
                    break;
                default:
                    $completed = apply_filters('gdrts_ajax_vote_'.$request->method, false, $request->meta, $item, $user, $request->render);
                    break;
            }
        }

        if (is_wp_error($completed)) {
            $this->error($completed->get_error_message(), $request->uid);
        } else {
            $request->render->args->echo = false;

            gdrts_single()->do_loop_save();

            $render = gdrts_single()->render((array)$request->render->args, (array)$request->render->method);

            $result = array(
                'status' => 'ok',
                'render' => $render,
                'uid' => $request->uid
            );

            $this->respond(json_encode($result));
        }
    }

    public function error($message = '', $uid = '') {
        if (empty($message)) {
            $message = __("Unspecified Rating Problem.", "gd-rating-system");
        }

        $result = array(
            'status' => 'error', 
            'message' => $message
        );

        if (!empty($uid)) {
            $result['uid'] = $uid;
        }

        $this->respond(json_encode($result));
    }

    public function respond($response) {
        if (gdrts_settings()->get('ajax_header_no_cache')) {
            nocache_headers();
        }

        header('Content-Type: application/json');

        die($response);
    }
}

global $_gdrts_ajax;
$_gdrts_ajax = new gdrts_core_ajax();

function gdrts_ajax() {
    global $_gdrts_ajax;
    return $_gdrts_ajax;
}
