<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_engine_list extends gdrts_base_engine {
    protected $_engine = 'list';

    protected $_sql;

    public $items;
    public $current_item = -1;
    public $items_count = 0;
    public $in_the_loop = false;

    public function have_items() {
        if ($this->current_item + 1 < $this->items_count) {
            return true;
        } else if ($this->current_item + 1 == $this->items_count && $this->items_count > 0 ) {
            $this->rewind_items();
        }

        $this->in_the_loop = false;

        return false;
    }

    public function rewind_items() {
        $this->current_item = -1;

        if ($this->items_count > 0) {
            $this->_item = $this->items[0];

            $this->update_method();
        }
    }

    public function the_item() {
        $this->in_the_loop = true;

        $this->next_item();
    }

    public function next_item() {
        $this->current_item++;

        $this->_item = $this->items[$this->current_item];

        $this->update_method();
    }

    public function update_method() {
        switch ($this->_args['method']) {
            case 'stars-rating':
                gdrtsm_stars_rating()->update_list_item();
                break;
            default:
                do_action('gdrts_loop_list_method_'.$this->_args['method'].'_update');
                break;
        }
    }

    public function render($args = array(), $method = array()) {
        $this->loop($args, $method);

        if ($this->abort) {
            return '';
        }

        $templates = apply_filters('gdrts_render_list_templates_pre', array(), $this->_args['entity'], $this->_args['name']);

        if (empty($templates)) {
            switch ($this->_args['method']) {
                case 'stars-rating':
                    $templates = gdrtsm_stars_rating()->loop()->templates_list($this->_args['entity'], $this->_args['name']);
                    break;
                default:
                    $templates = apply_filters('gdrts_render_list_templates_'.$this->_args['method'], array(), $this->_args['entity'], $this->_args['name']);
                    break;
            }
        }

        $templates = apply_filters('gdrts_render_list_templates', $templates, $this->_args['entity'], $this->_args['name']);

        $this->items = gdrts_query()->run($this->_args);
        $this->_sql = gdrts_query()->sql;

        $this->items_count = count($this->items);
        $this->current_item = -1;
        $this->in_the_loop = false;

        if (gdrts_debug_on()) {
            gdrts()->debug_queue($this->_sql, 'rating list, sql');
        }

        $render = gdrts_return_template($templates);

        $this->do_loop_status(false);

        if ($this->_args['echo']) {
            echo $render;
        }

        return $render;
    }

    public function loop($args = array(), $method = array()) {
        $defaults = apply_filters('gdrts_list_block_render_defaults', array(
            'echo' => false, 
            'entity' => null, 
            'name' => null,
            'method' => 'stars-rating',
            'series' => null
        ));

        $this->_args = wp_parse_args($args, $defaults);

        $this->abort = false;

        if (gdrts_is_method_loaded($this->_args['method'])) {
            $this->do_loop_status();

            $this->_user = new gdrts_core_user();

            switch ($this->_args['method']) {
                case 'stars-rating':
                    gdrtsm_stars_rating()->prepare_loop_list($method, $this->_args);
                    break;
                default:
                    do_action('gdrts_loop_list_method_'.$this->_args['method'].'_prepare', $method, $this->_args);
                    break;
            }
        } else {
            $this->abort = true;
        }
    }

    public function json() {
        $data = apply_filters('gdrts_loop_list_json_data', array(), $this->_args['method']);

        echo '<script class="gdrts-rating-data" type="application/json">';
        echo json_encode($data);
        echo '</script>';
    }
}

global $_gdrts_engine_list;
$_gdrts_engine_list = new gdrts_engine_list();

function gdrts_list() {
    global $_gdrts_engine_list;
    return $_gdrts_engine_list;
}
