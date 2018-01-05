<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_comments_sorter {
    public $order = 'DESC';
    public $method = 'stars-rating';
    public $value = 'rating';
    public $scope = 'all';
    public $min = 0;

    public $meta_key = '';
    public $meta_key_votes = '';

    public function __construct() {
        add_action('pre_get_comments', array($this, 'pre_get_comments'));
    }

    public function pre_get_comments($cmm_query) {
        if (isset($cmm_query->query_vars['orderby']) && $cmm_query->query_vars['orderby'] == 'gdrts') {
            $this->prepare_rating($cmm_query->query_vars);

            add_filter('comments_clauses', array($this, 'comments_clauses'));
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

    public function comments_clauses($pieces) {
        remove_filter('comments_clauses', array($this, 'comments_clauses'));

        $_use_join = $this->scope == 'rated' ? " INNER JOIN " : " LEFT JOIN ";

        $pieces['join'].= $_use_join.gdrts_db()->items." gdrts_i ON gdrts_i.entity = 'comments' AND gdrts_i.id = ".gdrts_db()->wpdb()->comments.".comment_ID";
        $pieces['join'].= $_use_join.gdrts_db()->itemmeta." gdrts_m ON gdrts_i.item_id = gdrts_m.item_id AND gdrts_m.meta_key = '".$this->meta_key."'";

        if ($this->scope == 'rated') {
            if (isset($pieces['where']) && !empty($pieces['where'])) {
                $pieces['where'].= ' AND ';
            }

            $pieces['where'].= 'gdrts_m.meta_value >= '.$this->min;
        }

        $pieces['orderby'] = 'cast(gdrts_m.meta_value * 100 as unsigned) '.$this->order;

        if ($this->value == 'rating') {
            $pieces['join'].= $_use_join.gdrts_db()->itemmeta." gdrts_v ON gdrts_i.item_id = gdrts_v.item_id AND gdrts_v.meta_key = '".$this->meta_key_votes."'";

            $pieces['orderby'].= ', cast(gdrts_v.meta_value * 100 as unsigned) '.$this->order;
        }

        $pieces['orderby'].= ', '.$pieces['orderby'];

        return $pieces;
    }
}
