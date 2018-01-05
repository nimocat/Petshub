<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_getback {
    public function __construct() {
        if (gdrts_admin()->page === 'tools') {
            if (isset($_GET['run']) && $_GET['run'] == 'export') {
                $this->tools_export();
            }
        }

        if (gdrts_admin()->page === 'types' && isset($_GET['single-action'])) {
            if ($_GET['single-action'] == 'delete') {
                $this->entity_delete();
            }
        }

        if (gdrts_admin()->page === 'rules' && isset($_GET['single-action'])) {
            if ($_GET['single-action'] == 'remove-rule') {
                $this->rule_remove();
            }

            if ($_GET['single-action'] == 'disable-rule') {
                $this->rule_activation(false);
            }

            if ($_GET['single-action'] == 'enable-rule') {
                $this->rule_activation(true);
            }
        }

        if (gdrts_admin()->page === 'log') {
            if (isset($_GET['single-action']) && $_GET['single-action'] == 'remove') {
                $this->log_remove();
            }

            if (isset($_GET['single-action']) && $_GET['single-action'] == 'delete') {
                $this->log_delete();
            }

            if (isset($_GET['action']) || isset($_GET['action2'])) {
                $this->log_bulk();
            }
        }

        if (gdrts_admin()->page === 'ratings') {
            if (isset($_GET['single-action']) && $_GET['single-action'] == 'clear') {
                $this->ratings_clear();
            }

            if (isset($_GET['single-action']) && $_GET['single-action'] == 'delete') {
                $this->ratings_delete();
            }

            if (isset($_GET['action']) || isset($_GET['action2'])) {
                $this->ratings_bulk();
            }
        }

        do_action('gdrts_admin_getback_handler');
    }

    private function _load_maintenance() {
        require_once(GDRTS_PATH.'core/admin/maintenance.php');
    }

    private function _bulk_action() {
        $action = isset($_GET['action']) && $_GET['action'] != '' && $_GET['action'] != '-1' ? d4p_sanitize_basic($_GET['action']) : '';

        if ($action == '') {
            $action = isset($_GET['action2']) && $_GET['action2'] != '' && $_GET['action2'] != '-1' ? d4p_sanitize_basic($_GET['action2']) : '';
        }

        return $action;
    }

    private function entity_delete() {
        check_ajax_referer('gdrts-admin-panel');

        $url = self_admin_url('admin.php?page=gd-rating-system-types');

        $entity = isset($_GET['entity']) ? d4p_sanitize_key_expanded($_GET['entity']) : '';

        if (!empty($entity)) {
            gdrts_settings()->delete_custom_entity($entity);

            $url.= '&message=entity-removed';
        }

        wp_redirect($url);
        exit;
    }

    private function rule_activation($enabled = true) {
        $item = isset($_GET['item']) ? d4p_sanitize_basic($_GET['item']) : '';
        $obj = isset($_GET['obj']) ? d4p_sanitize_basic($_GET['obj']) : '';
        $nonce = isset($_GET['_wpnonce']) ? d4p_sanitize_basic($_GET['_wpnonce']) : '';

        $url = self_admin_url('admin.php?page=gd-rating-system-rules');

        if (!empty($item) && !empty($obj) && !empty($nonce)) {
            if (wp_verify_nonce($nonce, 'gdrts-remove-'.$item.'-'.$obj)) {
                $key = $item.'_'.$obj.'_rule_active';

                gdrts_settings()->set($key, $enabled, 'items', true);

                $url.= '&message=rule-updated';
            }
        }

        wp_redirect($url);
        exit;
    }

    private function rule_remove() {
        $item = isset($_GET['item']) ? d4p_sanitize_basic($_GET['item']) : '';
        $obj = isset($_GET['obj']) ? d4p_sanitize_basic($_GET['obj']) : '';
        $nonce = isset($_GET['_wpnonce']) ? d4p_sanitize_basic($_GET['_wpnonce']) : '';

        $url = self_admin_url('admin.php?page=gd-rating-system-rules');

        if (!empty($item) && !empty($obj) && !empty($nonce)) {
            if (wp_verify_nonce($nonce, 'gdrts-remove-'.$item.'-'.$obj)) {
                $key = $item.'_'.$obj.'_';

                gdrts_settings()->remove_by_prefix($key, 'items', true);

                $url.= '&message=rule-removed';
            }
        }

        wp_redirect($url);
        exit;
    }

    private function tools_export() {
        @ini_set('memory_limit', '128M');
        @set_time_limit(360);

        check_ajax_referer('dev4press-plugin-export');

        if (!d4p_is_current_user_admin()) {
            wp_die(__("Only administrators can use export features.", "gd-rating-system"));
        }

        $export_date = date('Y-m-d-H-m-s');

        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename="gd_rating_system_settings_'.$export_date.'.gdrts"');

        die(gdrts_settings()->serialized_export());
    }

    private function ratings_clear() {
        check_ajax_referer('gdrts-admin-panel');

        $item_id = isset($_GET['item_id']) ? absint($_GET['item_id']) : 0;
        $method = isset($_GET['method']) ? sanitize_text_field($_GET['method']) : '';

        $url = self_admin_url('admin.php?page=gd-rating-system-ratings');

        if ($item_id > 0 && $method != '') {
            $this->_load_maintenance();

            gdrts_admin_maintenance::clear_rating_item_method($item_id, $method);

            $url.= '&message=removed';
        }

        wp_redirect($url);
        exit;
    }

    private function ratings_delete() {
        check_ajax_referer('gdrts-admin-panel');

        $item_id = isset($_GET['item_id']) ? absint($_GET['item_id']) : 0;

        $url = self_admin_url('admin.php?page=gd-rating-system-ratings');

        if ($item_id > 0) {
            $this->_load_maintenance();

            gdrts_admin_maintenance::delete_rating_item($item_id);

            $url.= '&message=removed';
        }

        wp_redirect($url);
        exit;
    }

    private function ratings_bulk() {
        check_admin_referer('bulk-ratings');

        $action = $this->_bulk_action();

        if ($action != '') {
            $items = isset($_GET['rating']) ? d4p_sanitize_basic_array((array)$_GET['rating']) : array();

            $url = self_admin_url('admin.php?page=gd-rating-system-ratings');

            if (!empty($items)) {
                $this->_load_maintenance();

                switch ($action) {
                    case 'delete':
                        gdrts_admin_maintenance::delete_rating_item($items);
                        break;
                    case 'clear':
                        gdrts_admin_maintenance::clear_rating_item_method($items);
                        break;
                    case 'clear_stars-rating':
                        gdrts_admin_maintenance::clear_rating_item_method($items, 'stars-rating');
                        break;
                }

                $url.= '&message=removed';
            }

            wp_redirect($url);
            exit;
        }
    }

    private function log_delete() {
        check_ajax_referer('gdrts-admin-panel');

        $log_id = isset($_GET['log_id']) ? absint($_GET['log_id']) : 0;

        $url = self_admin_url('admin.php?page=gd-rating-system-log');

        if ($log_id > 0) {
            $this->_load_maintenance();

            gdrts_admin_maintenance::delete_vote_log($log_id);

            $url.= '&message=removed';
        }

        wp_redirect($url);
        exit;
    }

    private function log_remove() {
        check_ajax_referer('gdrts-admin-panel');
        
        $log_id = isset($_GET['log_id']) ? absint($_GET['log_id']) : 0;

        $url = self_admin_url('admin.php?page=gd-rating-system-log');

        if (gdrts_settings()->get('admin_log_remove')) {
            if ($log_id > 0) {
                $this->_load_maintenance();

                gdrts_admin_maintenance::remove_vote_log($log_id);

                $url.= '&message=removed';
            }
        }

        wp_redirect($url);
        exit;
    }

    private function log_bulk() {
        check_admin_referer('bulk-votes');

        $action = $this->_bulk_action();

        if ($action != '') {
            $items = isset($_GET['vote']) ? d4p_sanitize_basic_array((array)$_GET['vote']) : array();

            $url = self_admin_url('admin.php?page=gd-rating-system-log');

            if (!empty($items)) {
                $this->_load_maintenance();

                switch ($action) {
                    case 'delete':
                        gdrts_admin_maintenance::delete_vote_log($items);
                        break;
                    case 'remove':
                        gdrts_admin_maintenance::remove_vote_log($items);
                        break;
                }

                $url.= '&message=removed';
            }

            wp_redirect($url);
            exit;
        }
    }
}
