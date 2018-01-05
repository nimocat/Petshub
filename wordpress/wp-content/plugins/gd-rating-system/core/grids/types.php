<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_grid_types extends d4p_grid {
    public $_table_class_name = 'gdrts-grid-types';

    public $custom_types = array();

    public function __construct($args = array()) {
        parent::__construct(array(
            'singular'=> 'type',
            'plural' => 'types',
            'ajax' => false
        ));

        $this->custom_types = gdrts_settings()->get('custom_entities', 'early');
    }

    private function _self($args, $getback = false) {
        $base_url = 'admin.php?page=gd-rating-system-types';
        $url = $base_url.'&'.$args;

        if ($getback) {
            $url.= '&_wpnonce='.wp_create_nonce('gdrts-admin-panel');
            $url.= '&gdrts_handler=getback';
            $url.= '&_wp_http_referer='.wp_unslash(self_admin_url($base_url));
        }

        return self_admin_url($url);
    }

    public function display_tablenav($which) { }

    public function get_columns() {
	return array(
            'icon' => '',
            'entity' => __("Entity", "gd-rating-system"),
            'types' => __("Types", "gd-rating-system")
	);
    }

    function column_icon($item) {
        return d4p_render_icon($item['icon'], 'i', true, true);
    }

    function column_entity($item) {
        $actions = array();

        $render = __("Name", "gd-rating-system").': <strong>'.$item['name'].'</strong><br/>';
        $render.= __("Label", "gd-rating-system").': <strong>'.$item['label'].'</strong>';

        if (isset($this->custom_types[$item['name']]) || $item['name'] == 'custom') {
            $actions['edit'] = '<a class="gdrts-types-action-entity-edit" href="'.admin_url('admin.php?page=gd-rating-system-types&action=edit&entity='.$item['name']).'">'.__("Edit", "gd-rating-system").'</a>';

            if ($item['name'] != 'custom') {
                $actions['delete'] = '<a class="gdrts-types-action-entity-delete" href="'.$this->_self('single-action=delete&entity='.$item['name'], true).'">'.__("Delete", "gd-rating-system").'</a>';
            }
        }

        return $render.$this->row_actions($actions);
    }

    function column_types($item) {
        $types = array();

        foreach ($item['types'] as $name => $label) {
            $types[] = '<li>'.$label.' &middot; <strong>'.$name.'</strong></li>';
        }

        $render = '';

        if (empty($types)) {
            $render = __("No types registered.", "gd-rating-system");
        } else {
            $render = '<ul>'.join('', $types).'</ul>';
        }

        return $render;
    }

    public function prepare_items() {
        $this->get_column_info_simple();

        $this->items = gdrts()->get_entities();

        $this->set_pagination_args(array(
            'total_items' => count($this->items),
            'total_pages' => 1,
            'per_page' => count($this->items),
        ));
    }
}
