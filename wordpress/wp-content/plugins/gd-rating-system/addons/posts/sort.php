<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_posts_sorter {
    public $order = 'DESC';
    public $method = 'stars-rating';
    public $value = 'rating';
    public $scope = 'all';
    public $min = 0;

    public $meta_key = '';
    public $meta_key_votes = '';

    public function __construct() {
        add_filter('query_vars', array($this, 'query_vars'));
        add_action('pre_get_posts', array($this, 'pre_get_posts'));
    }

    public function query_vars($vars) {
        $vars[] = 'gdrts_method';
        $vars[] = 'gdrts_value';
        $vars[] = 'gdrts_scope';
        $vars[] = 'gdrts_min';

        return $vars;
    }

    public function pre_get_posts($wp_query) {
        if (isset($wp_query->query_vars['orderby']) && $wp_query->query_vars['orderby'] == 'gdrts') {
            $this->prepare_rating($wp_query->query_vars);

            add_filter('posts_join', array($this, 'posts_join'));
            add_filter('posts_orderby', array($this, 'posts_orderby'));

            if ($this->scope == 'rated') {
                add_filter('posts_where', array($this, 'posts_where'));
            }
        }
    }

    public function prepare_rating($query_vars) {
        if (isset($query_vars['order'])) {
            $this->order = $query_vars['order'] == 'ASC' ? 'ASC' : 'DESC';
        }

        if (isset($query_vars['gdrts_method'])) {
            $_method = sanitize_text_field($query_vars['gdrts_method']);

            if (gdrts_is_method_loaded($_method)) {
                $this->method = $_method;
            }
        }

        if (isset($query_vars['gdrts_value'])) {
            $_value = sanitize_text_field($query_vars['gdrts_value']);

            if (in_array($_value, array('rating', 'votes'))) {
                $this->value = $_value;
            }
        }

        if (isset($query_vars['gdrts_scope'])) {
            $_scope = sanitize_text_field($query_vars['gdrts_scope']);

            if (in_array($_scope, array('all', 'rated'))) {
                $this->scope = $_scope;
            }
        }

        if (isset($query_vars['gdrts_min'])) {
            $this->min = intval($query_vars['gdrts_min']);
        } else {
            $this->min = 0;
        }

        $_key = $this->method;

        $this->meta_key = $_key.'_'.$this->value;
        $this->meta_key_votes = $_key.'_votes';
    }

    public function posts_where($where) {
        remove_filter('posts_where', array($this, 'posts_where'));

        if (!empty($where)) {
            $where.= ' AND ';
        }

        $where.= 'gdrts_m.meta_value >= '.$this->min;

        return $where;
    }

    public function posts_join($join) {
        remove_filter('posts_join', array($this, 'posts_join'));

        $_use_join = $this->scope == 'rated' ? " INNER JOIN " : " LEFT JOIN ";

        $join.= $_use_join.gdrts_db()->items." gdrts_i ON gdrts_i.entity = 'posts' AND gdrts_i.id = ".gdrts_db()->wpdb()->posts.".ID";
        $join.= $_use_join.gdrts_db()->itemmeta." gdrts_m ON gdrts_i.item_id = gdrts_m.item_id AND gdrts_m.meta_key = '".$this->meta_key."'";

        if ($this->value == 'rating') {
            $join.= $_use_join.gdrts_db()->itemmeta." gdrts_v ON gdrts_i.item_id = gdrts_v.item_id AND gdrts_v.meta_key = '".$this->meta_key_votes."'";
        }

        return $join;
    }

    public function posts_orderby($orderby) {
        remove_filter('posts_orderby', array($this, 'posts_orderby'));

        $orderby_new = 'cast(gdrts_m.meta_value * 100 as unsigned) '.$this->order;

        if ($this->value == 'rating') {
            $orderby_new.= ', cast(gdrts_v.meta_value * 100 as unsigned) '.$this->order;
        }

        $orderby_new.= ', '.$orderby;

        return $orderby_new;
    }
}
