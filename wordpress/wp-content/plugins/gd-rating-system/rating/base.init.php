<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core {
    private $_embed_loaded = false;

    public $addons = array();
    public $methods = array();
    public $fonts = array();

    public $loaded = array();
    public $debug = array();

    private $entities = array(
        'posts' => array('name' => 'posts', 'label' => 'Post Types', 'types' => array(), 'icon' => 'file-text-o'),
        'terms' => array('name' => 'terms', 'label' => 'Terms', 'types' => array(), 'icon' => 'tags'),
        'comments' => array('name' => 'comments', 'label' => 'Comments', 'types' => array(), 'icon' => 'comments-o'),
        'users' => array('name' => 'users', 'label' => 'Users', 'types' => array(), 'icon' => 'users'),
        'custom' => array('name' => 'custom', 'label' => 'Custom', 'types' => array(), 'icon' => 'asterisk')
    );

    public function __construct() {
        require_once(GDRTS_PATH.'rating/base.data.php');

        require_once(GDRTS_PATH.'rating/base.functions.php');
        require_once(GDRTS_PATH.'rating/base.expanded.php');

        require_once(GDRTS_PATH.'rating/core.log-item.php');
        require_once(GDRTS_PATH.'rating/core.item.php');
        require_once(GDRTS_PATH.'rating/core.user.php');
        require_once(GDRTS_PATH.'rating/core.user.rating.php');
        require_once(GDRTS_PATH.'rating/core.cache.php');
        require_once(GDRTS_PATH.'rating/core.db.cache.php');

        add_action('gdrts_load', array($this, 'prepare'));
        add_action('gdrts_init', array($this, 'prepared'));

        add_action('wp', array($this, 'ready'));
    }

    public function _types_registration() {
        do_action('gdrts_register_entities');

        $custom_entities = gdrts_settings()->get('custom_entities', 'early');

        foreach ($custom_entities as $entity => $data) {
            if ($entity != 'custom') {
                $this->register_entity($entity, $data['label'], $data['types'], $data['icon']);
            }
        }

        global $wp_post_types, $wp_taxonomies;

        foreach ($wp_post_types as $post_type) {
            if ($post_type->public) {
                $this->entities['posts']['types'][$post_type->name] = $post_type->label;
            }
        }

        foreach ($wp_taxonomies as $taxonomy) {
            if ($taxonomy->public) {
                $this->entities['terms']['types'][$taxonomy->name] = $taxonomy->label;
            }
        }

        $this->entities['comments']['types']['comment'] = 'Comments';
        $this->entities['users']['types']['user'] = 'Users';
        $this->entities['custom']['types']['free'] = 'Free';

        if (isset($custom_entities['custom'])) {
            $this->entities['custom']['types'] = array_merge($this->entities['custom']['types'], $custom_entities['custom']['types']);
        }

        do_action('gdrts_register_types');

        foreach ($this->entities as $entity => $obj) {
            gdrts_settings()->register('entities', $entity, array());

            foreach (array_keys($obj['types']) as $type) {
                gdrts_settings()->register('entities', $entity.'.'.$type, array());
            }
        }
    }

    public function _extensions_registration() {
        do_action('gdrts_register_methods_and_addons');

        foreach ($this->addons as $addon => $obj) {
            gdrts_settings()->register('load', 'addon_'.$addon, $obj['autoload']);
        }

        foreach ($this->methods as $method => $obj) {
            gdrts_settings()->register('load', 'method_'.$method, $obj['autoload']);
        }
    }

    public function _fonts_registration() {
        require_once(GDRTS_PATH.'rating/font.default.php');
        $this->fonts['font'] = new gdrts_font_default();

        do_action('gdrts_register_icons_fonts');
    }

    public function db() {
        return gdrts_db();
    }

    public function settings() {
        return gdrts_settings();
    }

    public function decimals() {
        return gdrts_settings()->get('decimal_round');
    }

    public function register_item_option($entity, $name, $option, $value) {
        gdrts_settings()->register('items', $entity.'_'.$name.'_'.$option, $value);
    }

    public function prepare() {
        do_action('gdrts_early_settings');

        $this->_types_registration();
        $this->_extensions_registration();
        $this->_fonts_registration();

        do_action('gdrts_load_settings');

        $load = gdrts_settings()->group_get('load');

        foreach ($load as $key => $do) {
            if ($do) {
                $this->loaded[] = $key;

                do_action('gdrts_load_'.$key);
            }
        }

        do_action('gdrts_populate_settings');

        foreach (array_keys($this->fonts) as $type) {
            $load_font = gdrts_settings()->get('fonticons_'.$type);
            $load_font = is_null($load_font) ? true : $load_font;

            if (apply_filters('gdrts_activate_font_'.$type, $load_font)) {
                $this->fonts[$type]->actions();
            }
        }

        do_action('gdrts_plugin_rating_ready');
    }

    public function prepared() { }

    public function ready() {
        do_action('gdrts_ready');

        if ($this->is_locked()) {
            add_action('gdrts-template-rating-block-after', array($this, 'show_disabled_message'));
        }
    }

    public function show_disabled_message() {
        echo '<div class="gdrts-voting-disabled">';

        if (gdrts_settings()->get('maintenance')) {
            echo gdrts_settings()->get('maintenance_message');
        } else if (gdrts_settings()->get('voting_disabled')) {
            echo gdrts_settings()->get('voting_disabled_message');
        }

        echo '</div>';
    }

    public function cookie_key() {
        return apply_filters('gdrts_cookie_key', 'wp-gdrts-log');
    }

    public function cookie_expiration($time = null) {
        if (is_null($time)) {
            $time = apply_filters('gdrts_cookie_expiration', YEAR_IN_SECONDS);
        }

        return time() + $time;
    }

    public function is_locked() {
        return gdrts_settings()->get('voting_disabled') || gdrts_settings()->get('maintenance');
    }

    public function load_embed() {
        if (!$this->_embed_loaded) {
            require_once(GDRTS_PATH.'rating/base.embed.php');

            $this->_embed_loaded = true;
        }
    }

    public function debug_queue($value, $label = '') {
        $this->debug[] = array('value' => $value, 'label' => $label);
    }

    public function flush_debug_queue() {
        foreach ($this->debug as $debug) {
            $item = D4P_EOL.'<!-- ';

            if ($debug['label'] != '') {
                $item.= $debug['label'].':'.D4P_EOL;
            }

            $_value = gdrts_print_debug_info($debug['value']);

            $item.= $_value.' -->';

            echo $item;
        }

        $this->debug = array();
    }

    public function get_font_star_char($type, $name) {
        if ($type == 'image') {
            return '';
        }

        if (!isset($this->fonts[$type])) {
            $type = 'font';
        }

        return $this->fonts[$type]->get_star_char($name);
    }

    public function get_font_thumb_chars($type, $name) {
        if ($type == 'image') {
            return '';
        }

        if (!isset($this->fonts[$type])) {
            $type = 'font';
        }

        return $this->fonts[$type]->get_thumb_chars($name);
    }

    public function get_font_like_chars($type, $name) {
        if ($type == 'image') {
            return '';
        }

        if (!isset($this->fonts[$type])) {
            $type = 'font';
        }

        return $this->fonts[$type]->get_like_chars($name);
    }

    public function has_entity($entity) {
        return isset($this->entities[$entity]);
    }

    public function get_entities() {
        return $this->entities;
    }

    public function get_entity($entity) {
        return $this->entities[$entity];
    }

    public function has_entity_type($entity, $type) {
        return isset($this->entities[$entity]['types'][$type]);
    }

    public function get_entity_types($entity) {
        return $this->entities[$entity]['types'];
    }

    public function get_entity_type_label($entity, $type) {
        return $this->entities[$entity]['types'][$type];
    }

    public function register_entity($entity, $label, $types = array(), $icon = 'ticket') {
        if (!$this->has_entity($entity)) {
            $this->entities[$entity] = array('name' => $entity, 'label' => $label, 'types' => $types, 'icon' => $icon);
        }
    }

    public function register_type($entity, $name, $label) {
        if ($this->has_entity($entity)) {
            $this->entities[$entity]['types'][$name] = $label;
        }
    }

    public function default_storages_paths() {
        return apply_filters('gdrts_default_templates_storage_paths', array(
            GDRTS_PATH.'templates/'
        ));
    }
}
