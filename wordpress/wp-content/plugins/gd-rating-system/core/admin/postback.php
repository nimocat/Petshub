<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_postback {
    public function __construct() {
        if (isset($_POST['option_page']) && $_POST['option_page'] == 'gd-rating-system-newrule') {
            $this->new_rule();
        }

        if (isset($_POST['option_page']) && $_POST['option_page'] == 'gd-rating-system-ruledit') {
            $this->edit_rule();
        }

        if (isset($_POST['option_page']) && $_POST['option_page'] == 'gd-rating-system-entityedit') {
            $this->edit_entity();
        }

        if (isset($_POST['option_page']) && $_POST['option_page'] == 'gd-rating-system-tools') {
            $this->tools();
        }

        if (isset($_POST['option_page']) && $_POST['option_page'] == 'gd-rating-system-settings') {
            $this->settings();
        }

        do_action('gdrts_admin_postback_handler');
    }

    private function save_rule($item, $obj) {
        require_once(GDRTS_D4PLIB.'admin/d4p.functions.php');
        require_once(GDRTS_D4PLIB.'admin/d4p.settings.php');

        $prekey = $item.'_'.$obj;
        $groups = apply_filters('gdrts_admin_rule_'.$obj, array(), 'items', $prekey);
        $active = gdrts_settings()->get($prekey.'_rule_active', 'items');

        $settings = array(
            new d4pSettingElement('items', $prekey.'_rule_active', __("Active", "gd-rating-system"), '', d4pSettingType::BOOLEAN, $active)
        );

        foreach ($groups as $group) {
            $settings = array_merge($settings, $group['settings']);
        }

        $processor = new d4pSettingsProcess($settings);
        $processor->base = 'gdrtsvalue';

        $data = $processor->process();

        foreach ($data as $group => $values) {
            foreach ($values as $name => $value) {
                gdrts_settings()->set($name, $value, $group);
            }

            gdrts_settings()->save($group);
        }
    }

    private function save_settings($panel) {
        require_once(GDRTS_D4PLIB.'admin/d4p.functions.php');
        require_once(GDRTS_D4PLIB.'admin/d4p.settings.php');
        include(GDRTS_PATH.'core/internal.php');

        $options = new gdrts_admin_settings();
        $settings = $options->settings($panel);

        $processor = new d4pSettingsProcess($settings);
        $processor->base = 'gdrtsvalue';

        $data = $processor->process();

        foreach ($data as $group => $values) {
            foreach ($values as $name => $value) {
                gdrts_settings()->set($name, $value, $group);
            }

            if ($panel == 'extensions') {
                $ok = false;

                foreach (array_keys(gdrts()->methods) as $method) {
                    if (gdrts_settings()->get('method_'.$method, 'load')) {
                        $ok = true;
                    }
                }

                if (!$ok) {
                    gdrts_settings()->set('method_stars-rating', true, 'load');
                }
            }

            gdrts_settings()->save($group);
        }
    }

    private function new_rule() {
        check_admin_referer('gd-rating-system-newrule-options');

        $item = d4p_sanitize_key_expanded($_POST['item']);
        $object = d4p_sanitize_key_expanded($_POST['object']);

        $url = 'admin.php?page=gd-rating-system-rules';
        $url.= '&action=rule&item='.$item.'&obj='.$object;

        wp_redirect($url);
        exit;
    }

    private function edit_entity() {
        check_admin_referer('gd-rating-system-entityedit-options');

        $url = 'admin.php?page=gd-rating-system-types';

        if (isset($_POST['gdrtsvalue']['entity'])) {
            $defaults = array('name' => '', 'label' => '', 'icon' => '', 'types' => array());
            $data = wp_parse_args($_POST['gdrtsvalue']['entity'], $defaults);

            $data['name'] = d4p_sanitize_key_expanded($data['name']);
            $data['label'] = d4p_sanitize_basic($data['label']);
            $data['icon'] = d4p_sanitize_basic($data['icon']);

            $value = array();

            foreach ($data['types']  as $id => $item) {
                if ($id > 0) {
                    $_key = d4p_sanitize_basic($item['key']);
                    $_val = d4p_sanitize_basic($item['value']);

                    if ($_key != '' && $_val != '') {
                        $value[$_key] = $_val;
                    }
                }
            }

            $data['types'] = $value;

            gdrts_settings()->save_custom_entity($data);

            $url.= '&message=saved';
        }

        wp_redirect($url);
        exit;
    }

    private function edit_rule() {
        check_admin_referer('gd-rating-system-ruledit-options');

        $_edit_item = d4p_sanitize_key_expanded($_GET['item']);
        $_edit_object = d4p_sanitize_key_expanded($_GET['obj']);

        $this->save_rule($_edit_item, $_edit_object);

        $url = 'admin.php?page=gd-rating-system-rules&action=rule&item='.$_edit_item.'&obj='.$_edit_object;
        wp_redirect($url.'&message=saved');
        exit;
    }

    private function tools() {
        check_admin_referer('gd-rating-system-tools-options');

        $post = $_POST['gdrtstools'];
        $action = $post['panel'];

        $url = 'admin.php?page=gd-rating-system-tools&panel='.$action;

        if ($action == 'import') {
            if (is_uploaded_file($_FILES['import_file']['tmp_name'])) {
                $raw = file_get_contents($_FILES['import_file']['tmp_name']);
                $data = maybe_unserialize($raw);

                if (is_object($data)) {
                    gdrts_settings()->import_from_object($data);

                    $message = 'imported';
                }
            }
        } else if ($action == 'remove') {
            $remove = isset($post['remove']) ? (array)$post['remove'] : array();

            if (empty($remove)) {
                $message = 'nothing-removed';
            } else {
                if (isset($remove['settings']) && $remove['settings'] == 'on') {
                    gdrts_settings()->remove_plugin_settings();
                }

                if (isset($remove['drop']) && $remove['drop'] == 'on') {
                    require_once(GDRTS_PATH.'core/admin/install.php');

                    gdrts_drop_database_tables();

                    if (!isset($remove['disable'])) {
                        gdrts_settings()->mark_for_update();
                    }
                } else if (isset($remove['truncate']) && $remove['truncate'] == 'on') {
                    require_once(GDRTS_PATH.'core/admin/install.php');

                    gdrts_truncate_database_tables();
                }

                if (isset($remove['disable']) && $remove['disable'] == 'on') {
                    deactivate_plugins('gd-rating-system/gd-rating-system.php', false, false);

                    wp_redirect(admin_url('plugins.php'));
                    exit;
                }

                $message = 'removed';
            }
        }

        wp_redirect($url.'&message='.$message);
        exit;
    }

    private function settings() {
        check_admin_referer('gd-rating-system-settings-options');

        $this->save_settings(gdrts_admin()->panel);

        $url = 'admin.php?page=gd-rating-system-settings&panel='.gdrts_admin()->panel;
        wp_redirect($url.'&message=saved');
        exit;
    }
}
