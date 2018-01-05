<?php

if (!defined('ABSPATH')) { exit; }

abstract class gdrts_item_data {
    public $object = null;

    public $entity;
    public $name;
    public $id;

    public function __construct($entity, $name, $id) {
        $this->entity = $entity;
        $this->name = $name;
        $this->id = $id;
    }

    public function __get($name) {
        if (isset($this->object->$name)) {
            return $this->object->$name;
        } else {
            return null;
        }
    }

    public function is_valid() {
        return !is_null($this->object);
    }

    abstract public function get_title();
    abstract public function get_url();
    abstract public function get_date_published($format = 'c');
    abstract public function get_date_modified($format = 'c');
}

abstract class gdrts_user_rating {
    protected $log = array();
    protected $log_ids = array();

    public $ip = '';
    public $user_id = 0;

    public function __construct($user_id) {
        $this->ip = d4p_visitor_ip();
        $this->user_id = $user_id;
    }

    public function has_voted($args, $method = 'stars-rating', $series = null) {
        $item_id = $this->_get_item_id($args);

        $this->_load_log($item_id, $method, $series);

        if (is_null($series) || empty($series)) {
            $series = 'default';
        }

        if (gdrts_method_allows_multiple_votes($method)) {
            return isset($this->log[$item_id][$method][$series]['active']);
        } else {
            return isset($this->log[$item_id][$method][$series]['active']) ? $this->_get_first_active_log($item_id, $method, $series)->is_active_vote() : false;
        }
    }

    public function has_voted_for_item($item_id, $method = 'stars-rating', $series = null) {
        return $this->has_voted(array('item_id' => $item_id), $method, $series);
    }

    public function has_voted_for_post($post_id, $method = 'stars-rating', $series = null) {
        $post_type = get_post_type($post_id);

        return $this->has_voted(array(
            'entity' => 'posts', 
            'name' => $post_type, 
            'id' => $post_id), $method, $series);
    }

    public function has_voted_in_loop($method = 'stars-rating', $series = null) {
        global $post;

        return $this->has_voted(array(
            'entity' => 'posts', 
            'name' => $post->post_type, 
            'id' => $post->ID), $method, $series);
    }

    public function get_the_vote($args, $method = 'stars-rating', $series = null) {
        $item_id = $this->_get_item_id($args);

        $this->_load_log($item_id, $method, $series);

        if (is_null($series) || empty($series)) {
            $series = 'default';
        }

        if (gdrts_method_allows_multiple_votes($method)) {
            return isset($this->log[$item_id][$method][$series]['active']) ? $this->_get_first_active_log($item_id, $method, $series) : false;
        } else {
            return isset($this->log[$item_id][$method][$series]['active']) ? (
                   $this->_get_first_active_log($item_id, $method, $series)->is_active_vote() ? $this->_get_first_active_log($item_id, $method, $series) : false) : false;
        }
    }

    public function get_the_vote_for_item($item_id, $method = 'stars-rating', $series = null) {
        return $this->get_the_vote(array('item_id' => $item_id), $method, $series);
    }

    public function get_the_vote_for_post($post_id, $method = 'stars-rating', $series = null) {
        $post_type = get_post_type($post_id);

        return $this->get_the_vote(array(
            'entity' => 'posts', 
            'name' => $post_type, 
            'id' => $post_id), $method, $series);
    }

    public function get_the_vote_in_loop($method = 'stars-rating', $series = null) {
        global $post;

        return $this->get_the_vote(array(
            'entity' => 'posts', 
            'name' => $post->post_type, 
            'id' => $post->ID), $method, $series);
    }

    public function get_all_votes($args, $method = 'stars-rating', $series = null) {
        $item_id = $this->_get_item_id($args);

        $this->_load_log($item_id, $method, $series);

        if (is_null($series) || empty($series)) {
            $series = 'default';
        }

        return isset($this->log[$item_id][$method][$series]) ? $this->log[$item_id][$method][$series] : array();
    }

    public function get_all_votes_for_item($item_id, $method = 'stars-rating', $series = null) {
        return $this->get_all_votes(array('item_id' => $item_id), $method, $series);
    }

    public function get_all_votes_for_post($post_id, $method = 'stars-rating', $series = null) {
        $post_type = get_post_type($post_id);

        return $this->get_all_votes(array(
            'entity' => 'posts', 
            'name' => $post_type, 
            'id' => $post_id), $method, $series);
    }

    public function get_all_votes_in_loop($method = 'stars-rating', $series = null) {
        global $post;

        return $this->get_all_votes(array(
            'entity' => 'posts', 
            'name' => $post->post_type, 
            'id' => $post->ID), $method, $series);
    }

    protected function _get_item_id($args) {
        $item_id = false;

        $defaults = array(
            'entity' => null, 
            'name' => null,
            'item_id' => null,
            'id' => null
        );

        $atts = shortcode_atts($defaults, $args);

        if (is_null($atts['item_id']) || $atts['item_id'] == 0 || $atts['item_id'] === false) {
            $item_id = gdrts_cache()->get_item_id($atts['entity'], $atts['name'], $atts['id']);
        } else {
            $item_id = absint($atts['item_id']);
        }

        return $item_id;
    }

    protected function _load_log($item_id) {
        if (!isset($this->log[$item_id])) {
            $this->log[$item_id] = gdrts_db()->get_log_item_user($item_id, $this->user_id, $this->ip, $this->log_ids);
        }
    }

    private function _get_first_active_log($item_id, $method, $series) {
        return reset($this->log[$item_id][$method][$series]['active']);
    }
}
