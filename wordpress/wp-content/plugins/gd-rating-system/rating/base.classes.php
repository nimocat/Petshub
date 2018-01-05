<?php

if (!defined('ABSPATH')) { exit; }

abstract class gdrts_extension_init {
    public $group = '';
    public $prefix = '';

    public function __construct() {
        add_action('gdrts_settings_init', array($this, 'settings'));
        add_action('gdrts_register_methods_and_addons', array($this, 'register'));
        add_action('gdrts_admin_ajax', array($this, 'ajax'));
    }

    public function ajax() {}

    public function key($name, $prekey = '') {
        $prekey = empty($prekey) ? $this->prefix : $prekey;

        return $prekey.'_'.$name;
    }

    public function register_option($name, $value) {
        gdrts_settings()->register($this->group, $this->key($name), $value);
    }

    abstract public function settings();
    abstract public function register();

    abstract public function load();
}

abstract class gdrts_extension {
    public $group = '';

    public $prefix = '';
    public $settings = array();
    public $settings_rule = array();

    public $query = array();

    public function __construct() {
        add_action('gdrts_admin_load_modules', array($this, '_load_admin'));

        add_action('gdrts_populate_settings', array($this, '_load_settings'));

        add_action('gdrts_init', array($this, 'init'));
        add_action('gdrts_core', array($this, 'core'));
    }

    public function init() { }

    public function core() { }

    public function _load_settings() {
        $this->settings = gdrts_settings()->prefix_get($this->prefix.'_', $this->group);
    }

    public function _load_settings_rule($entity = null, $name = null) {
        $this->settings_rule = $this->get_settings_rule($entity, $name);
    }

    public function get_settings_rule($entity = null, $name = null) {
        $entity = is_null($entity) ? gdrts_single()->args('entity') : $entity;
        $name = is_null($name) ? gdrts_single()->args('name') : $name;

        $rule_entity = $entity.'_'.$this->prefix.'_';
        $rule_item = $entity.'.'.$name.'_'.$this->prefix.'_';

        $active = gdrts_settings()->get($rule_item.'rule_active', 'items');

        if ($active === true) {
            return gdrts_settings()->items_get($rule_item);
        } else {
            $active = gdrts_settings()->get($rule_entity.'rule_active', 'items');

            if ($active === true) {
                return gdrts_settings()->items_get($rule_entity);
            }
        }

        return $this->settings;
    }

    public function get_rule($name) {
        if (isset($this->settings_rule[$name])) {
            return $this->settings_rule[$name];
        } else if (isset($this->settings[$name])) {
            return $this->settings[$name];
        } else {
            return '';
        }
    }

    public function get($name, $prefix = '', $prekey = '') {
        if ($prefix != '' && $prekey != '') {
            $override = gdrts_settings()->get($prekey.'_'.$name, $prefix);

            if (!is_null($override)) {
                return $override;
            }
        }

        return isset($this->settings[$name]) ? $this->settings[$name] : null;
    }

    public function key($name, $prekey = '') {
        $prekey = empty($prekey) ? $this->prefix : $prekey;

        return $prekey.'_'.$name;
    }

    abstract public function _load_admin();
}

abstract class gdrts_addon extends gdrts_extension {
    public $group = 'addons';

    public function __construct() {
        parent::__construct();
    }
}

abstract class gdrts_method extends gdrts_extension {
    public $group = 'methods';

    protected $_user = null;
    protected $_render = null;
    protected $_args = array();
    protected $_calc = array();
    protected $_engine = '';

    public function __construct() {
        parent::__construct();

        add_filter('gdrts_loop_single_json_data', array($this, 'json_single'), 10, 2);
        add_filter('gdrts_loop_list_json_data', array($this, 'json_list'), 10, 2);
        add_filter('gdrts_query_has_votes_'.$this->prefix, array($this, 'implements_votes'));
    }

    public function form_ready() {
        return true;
    }

    public function allows_multiple_votes() {
        return true;
    }

    public function has_series() {
        return false;
    }

    public function all_series_list() {
        return array();
    }

    public function reset_loop() {
        $this->_args = array();
        $this->_calc = array();
    }

    public function method() {
        return $this->prefix;
    }

    public function loop() {
        return $this;
    }

    public function user() {
        return $this->_user;
    }

    public function render() {
        return $this->_render;
    }

    public function args($name) {
        return isset($this->_args[$name]) ? $this->_args[$name] : false;
    }

    public function calc($name, $key = null) {
        if (is_null($key)) {
            return isset($this->_calc[$name]) ? $this->_calc[$name] : false;
        } else {
            return isset($this->_calc[$name][$key]) ? $this->_calc[$name][$key] : false;
        }
    }

    public function value($name, $echo = true) {
        $value = '';

        if (isset($this->_calc[$name])) {
            $value = $this->_calc[$name];
        }

        if ($echo) {
            echo $value;
        }

        return $value;
    }

    public function has_votes() {
        return $this->value('votes', false) > 0;
    }

    public function templates_list($entity, $name) {
        $template = isset($this->_args['template']) ? $this->_args['template'] : 'widget';

        $base = 'gdrts--'.$this->prefix.'--list--'.$template;

        $templates = array(
            $base.'--'.$entity.'-'.$name.'.php',
            $base.'--'.$entity.'.php',
            $base.'.php'
        );

        return $templates;
    }

    public function templates_single($item) {
        $template = isset($this->_args['template']) ? $this->_args['template'] : 'default';

        $base = 'gdrts--'.$this->prefix.'--single--'.$template;

        $templates = array(
            $base.'.php'
        );

        return $templates;
    }

    abstract public function implements_votes($votes = false);

    abstract public function prepare_loop_list($method, $args = array());
    abstract public function prepare_loop_single($method, $args = array());

    abstract public function json_single($data, $method);
    abstract public function json_list($data, $method);

    abstract public function validate_vote($meta, $item, $user, $render = null);
    abstract public function vote($meta, $item, $user, $render = null);

    public function please_wait($text = null, $icon = null, $class = '', $echo = true) {
        $text = is_null($text) ? __("Please wait...", "gd-rating-system") : $text;
        $icon = is_null($icon) ? '<i aria-hidden="true" class="rtsicon-spinner rtsicon-spin rtsicon-va rtsicon-fw"></i>' : $icon;

        $class = 'gdrts-rating-please-wait '.$class;

        $render = '<div class="'.trim($class).'">';
        $render.= $icon.$text;
        $render.= '</div>';

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }
}

abstract class gdrts_method_series extends gdrts_method {
    protected $_current_series = null;
    protected $_series_list = array();

    public function has_series() {
        return true;
    }

    public function series() {
        return $this->_current_series;
    }

    public function series_name() {
        return $this->_current_series->name;
    }

    public function get_series($name) {
        $list = $this->all_series();

        return isset($list[$name]) ? (object)$list[$name] : null;
    }

    public function all_series_list() {
        return wp_list_pluck($this->all_series(), 'label');
    }

    abstract public function all_series();
}

abstract class gdrts_method_render_single {
    public function __construct() { }

    abstract public function owner();

    public function has_votes() {
        return $this->owner()->has_votes();
    }

    public function has_distribution() {
        return $this->owner()->value('real_votes', false) > 0;
    }

    public function value($name, $echo = true) {
        return $this->owner()->value($name, $echo);
    }

    protected function get_classes($extra = '') {
        $classes = array_merge(
            array(
                'gdrts-rating-block',
                'gdrts-align-'.$this->owner()->args('alignment'),
                'gdrts-method-'.$this->owner()->method(),
                'gdrts-block-'.$this->owner()->method().'-item-'. gdrts_single()->item()->item_id,
                $this->owner()->calc('allowed') ? 'gdrts-rating-allowed' : 'gdrts-rating-forbidden',
                $this->owner()->calc('open') ? 'gdrts-rating-open' : 'gdrts-rating-closed',
                $this->owner()->args('style_class')
            ),
            gdrts_single()->item()->rating_classes()
        );

        if (!empty($extra)) {
            $classes[] = $extra;
        }

        return $classes;
    }
}

abstract class gdrts_method_render_list {
    public function __construct() { }

    abstract public function owner();

    public function value($name, $echo = true) {
        return $this->owner()->value($name, $echo);
    }

    protected function get_classes($extra = '') {
        $classes = array(
            'gdrts-rating-list',
            'gdrts-method-'.$this->owner()->method()
        );

        if (!empty($extra)) {
            $classes[] = $extra;
        }

        return $classes;
    }
}

abstract class gdrts_method_user {
    public $method = '';
    public $user = null;
    public $item = 0;

    public $super_admin = true;
    public $user_roles = true;
    public $visitor = true;
    public $author = false;

    public function __construct($super_admin, $user_role, $visitor, $author = false) {
        $this->super_admin = $super_admin;
        $this->user_roles = $user_role;
        $this->visitor = $visitor;
        $this->author = $author;

        $this->user = gdrts_single()->user();
        $this->item = gdrts_single()->item()->item_id;
    }

    public function is_allowed() {
        $override = apply_filters('gdrts_user_is_allowed_override', null, $this);

        if (!is_null($override) && is_bool($override)) {
            return $override;
        }

        $author = $this->is_author_allowed();

        if ($author === false) {
            return false;
        }

        if (is_super_admin()) {
            return $this->super_admin;
        } else if (is_user_logged_in()) {
            $allowed = $this->user_roles;

            if ($allowed === true || is_null($allowed)) {
                return true;
            } else if (is_array($allowed) && empty($allowed)) {
                return false;
            } else if (is_array($allowed) && !empty($allowed)) {
                global $current_user;

                if (is_array($current_user->roles)) {
                    $matched = array_intersect($current_user->roles, $allowed);

                    return !empty($matched);
                }
            }
        } else {
            return $this->visitor;
        }
    }

    public function is_author_allowed() {
        if (is_user_logged_in() && !$this->author) {
            $author_id = 0;
            $item = gdrts_single()->item();

            if ($item->entity == 'posts') {
                $author_id = $item->data->object->post_author;
            } else if ($item->entity == 'comments') {
                $author_id = $item->data->object->comment_author;
            }

            $author_id = apply_filters('gdrts_rating_item_author_id', absint($author_id), $item);

            if ($author_id > 0 && $author_id == get_current_user_id()) {
                return false;
            }
        }

        return true;
    }

    public function has_voted() {
        $log = $this->log();

        $votes = isset($log['vote']) ? count($log['vote']) : 0;
        $revotes = isset($log['revote']) ? count($log['revote']) : 0;

        return $votes + $revotes > 0;
    }

    public function log() {
        if (isset($this->user->log[$this->item][$this->method])) {
            return $this->user->log[$this->item][$this->method];
        } else {
            return array();
        }
    }

    public function votes() {
        $log = $this->log();

        $votes = array();

        foreach ($log as $list) {
            foreach ($list as $id => $vote) {
                if ($vote->status == 'active') {
                    $votes[$id] = $vote;
                }
            }
        }

        return $votes;
    }

    public function previous_vote() {
        $log = $this->log();

        if (isset($log['revote'])) {
            $vote = reset($log['revote']);
            return $this->process_vote($vote);
        }

        if (isset($log['vote'])) {
            $vote = reset($log['vote']);
            return $this->process_vote($vote);
        }

        return null;
    }

    public function process_vote($vote) {
        return $vote;
    }

    public function count_votes() {
        $log = $this->log();

        return isset($log['vote']) ? count($log['vote']) : 0;
    }

    public function count_revotes() {
        $log = $this->log();

        return isset($log['revote']) ? count($log['revote']) : 0;
    }
}
