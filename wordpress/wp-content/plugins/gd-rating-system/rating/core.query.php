<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_query {
    public $cache = null;

    public $args = array();
    public $votes = false;

    public $sql = '';

    public function __construct() { }

    public function run($args = array(), $cache = null) {
        $this->cache = $cache;

        $defaults = array(
            'method' => 'stars-rating',
            'series' => '',
            'entity' => 'posts',
            'name' => 'post',
            'id__in' => array(),
            'id__not_in' => array(),
            'orderby' => 'rating',
            'order' => 'DESC',
            'offset' => 0,
            'limit' => 5,
            'return' => 'objects',
            'rating_min' => 0,
            'votes_min' => 1,
            'period' => false,
            'source' => '',
            'object' => array(
                'status' => array('publish', 'closed', 'inherit'),
                'author' => array(),
                'meta' => array(),
                'terms' => array()
            )
        );

        $this->args = wp_parse_args($args, $defaults);

        $this->votes = apply_filters('gdrts_query_has_votes_'.$this->args['method'], $this->votes);

        if (!$this->votes && in_array($this->args['orderby'], array('votes', 'sum'))) {
            $this->args['orderby'] = 'rating';
        }

        if (gdrts_method_has_series($args['method'])) {
            if (empty($args['series']) || is_null($args['series'])) {
                $args['series'] = 'default';
            }
        }

        $this->build_query();

        return $this->_run();
    }

    protected function build_query() {
        $parts = $this->prepare_query_parts();

        $this->sql = "SELECT DISTINCT ".$parts['select']." FROM ".$parts['from'];

        if (!empty($parts['where'])) {
            $this->sql.= " WHERE ".join(' AND ', $parts['where']);
        }

        $this->sql.= $parts['group'].$parts['order'].$parts['limit'];

        $this->sql = apply_filters('gdrts_core_query_sql', $this->sql, $parts, $this->args);

        return $this->sql;
    }

    protected function prepare_query_parts() {
        $parts = array(
            'select' => 'i.*, CAST(m.`meta_value` AS DECIMAL(10,2)) as rating',
            'from'   => gdrts_db()->items." i INNER JOIN ".gdrts_db()->itemmeta." m".
                        " ON m.item_id = i.item_id AND m.meta_key = '".$this->_method()."_rating'",
            'where'  => array(
                        "i.`entity` = '".$this->args['entity']."'",
                        "i.`name` = '".$this->args['name']."'"),
            'group'  => '', 
            'order'  => '', 
            'limit'  => ''
        );

        if ($this->votes) {
            $parts['select'].= ", CAST(mv.`meta_value` AS UNSIGNED) as votes";
            $parts['from'].= " INNER JOIN ".gdrts_db()->itemmeta." mv ON mv.`item_id` = i.`item_id` AND mv.`meta_key` = '".$this->_method()."_votes'";

            if (is_numeric($this->args['votes_min']) && $this->args['votes_min'] > 0) {
                $parts['from'].= " AND mv.`meta_value` >= ".$this->args['votes_min'];
            }
        }

        if ($this->args['method'] == 'thumbs-rating') {
            $parts['select'].= ", CAST(mu.`meta_value` AS UNSIGNED) AS up";
            $parts['from'].= " INNER JOIN ".gdrts_db()->itemmeta." mu ON mu.`item_id` = i.`item_id` AND mu.`meta_key` = '".$this->args['method']."_up'";

            $parts['select'].= ", CAST(md.`meta_value` AS UNSIGNED) AS down";
            $parts['from'].= " INNER JOIN ".gdrts_db()->itemmeta." md ON md.`item_id` = i.`item_id` AND md.`meta_key` = '".$this->args['method']."_down'";
        }

        if (!empty($this->args['id__in'])) {
            $parts['where'][] = "i.`id` IN (".join(', ', $this->args['id__in']).")";
        } else if (!empty($this->args['id__not_in'])) {
            $parts['where'][] = "i.`id` NOT IN (".join(', ', $this->args['id__not_in']).")";
        }

        if (is_numeric($this->args['rating_min']) && $this->args['rating_min'] > 0) {
            $parts['where'][] = 'm.`meta_value` >= '.$this->args['rating_min'];
        }

        $parts = $this->parse_order($parts);
        $parts = $this->parse_object($parts);

        if ((is_numeric($this->args['offset']) && $this->args['offset'] > 0) || (is_numeric($this->args['limit']) && $this->args['limit'] > 0)) {
            $parts['limit'] = " LIMIT ".absint($this->args['offset']).", ".absint($this->args['limit']);
        }

        return apply_filters('gdrts_core_query_parts', $parts, $this->args);
    }

    protected function prepare_objects($items) {
        $list = array();

        $get = array();
        foreach ($items as $item_id => $obj) {
            gdrts_cache()->set('item_id', $obj->entity.'-'.$obj->name.'-'.$obj->id, $item_id);

            if (!gdrts_cache()->in('item', $item_id)) {
                $get[] = $item_id;
            }
        }

        if (!empty($get)) {
            $metas = gdrts_db()->get_items_meta($get);
        }

        $i = 1;
        foreach ($items as $item_id => $obj) {
            $data = (array)$obj;
            $data['meta'] = isset($metas[$item_id]) ? $metas[$item_id] : array();

            gdrts_cache()->add('item', $item_id, $data);

            $item = gdrts_get_rating_item_by_id($item_id);
            $item->ordinal = $i;
            $list[] = $item;

            $i++;
        }

        return $list;
    }

    protected function parse_order($q) {
        if ($this->args['orderby'] != '' && $this->args['orderby'] != 'none') {
            if ($this->args['orderby'] == 'rand') {
                $q['order'] = ' ORDER BY RAND()';
            } else {
                $q['order'] = ' ORDER BY ';

                $order = $this->_get_order();
                $orderby = $this->args['orderby'];

                $_default = 'rating ';
                if ($this->votes) {
                    $_default.= $order.', votes';
                }
                
                switch ($orderby) {
                    case 'item':
                    case 'item_id':
                        $q['order'].= 'i.`item_id`';
                        break;
                    case 'id':
                        $q['order'].= 'i.`id`';
                        break;
                    case 'latest':
                        $q['order'].= 'i.`latest`';
                        break;
                    case 'votes':
                        $q['order'].= 'votes';
                        break;
                    case 'percentage':
                        $q['select'].= ", CAST(mu.`meta_value` AS UNSIGNED) / CAST(mv.`meta_value` AS UNSIGNED) as percentage";
                        $q['order'].= 'percentage '.$order.', votes';
                        break;
                    case 'sum':
                        $q['select'].= ", ms.`meta_value` as sum";
                        $q['from'].= " INNER JOIN ".gdrts_db()->itemmeta." ms ON ms.`item_id` = i.`item_id` AND ms.`meta_key` = '".$this->_method()."_sum'";
                        $q['order'].= 'sum';
                        break;
                    case 'rating':
                        $q['order'].= $_default;
                        break;
                    default:
                        $q['order'].= apply_filters('gdrts_core_query_sort_orderby_'.$orderby, $_default, $this->args);
                        break;
                }

                $q['order'].= ' '.$order;
            }
        }

        return $q;
    }

    protected function parse_object($q) {
        $d = wp_parse_args($this->args['object'], array('author' => array(), 'meta' => array(), 'terms' => array()));

        $active = false;

        foreach ($d as &$value) {
            $value = (array)$value;

            if (!empty($value)) {
                $active = true;
            }
        }

        if ($active) {
            switch ($this->args['entity']) {
                case 'posts':
                    $q = $this->_parse_object_posts($q, $d);
                    break;
                case 'comments':
                    $q = $this->_parse_object_comments($q, $d);
                    break;
                case 'terms':
                    $q = $this->_parse_object_terms($q, $d);
                    break;
                case 'users':
                    $q = $this->_parse_object_users($q, $d);
                    break;
            }
        }

        return $q;
    }

    protected function _get_order() {
        $order = 'DESC';

        if (strtoupper($this->args['order']) == 'ASC') {
            $order = 'ASC';
        }

        return $order;
    }

    protected function _parse_object_posts($q, $d) {
        $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->posts.' op ON op.ID = i.id';

        $q['order'].= ', op.post_date '.$this->_get_order();

        if (!empty($d['author'])) {
            $q['where'][] = 'op.post_author IN ('.join(', ', $d['author']).')';
        }

        if (!empty($d['status'])) {
            $q['where'][] = "op.post_status IN ('".join("', '", $d['status'])."')";
        }

        if (!empty($d['meta'])) {
            $mid = 1;

            foreach ($d['meta'] as $key => $value) {
                $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->postmeta.' om'.$mid.' ON op.ID = om'.$mid.'.post_id';

                $q['where'][] = "om".$mid.".meta_key = '".esc_sql($key)."'";
                $q['where'][] = "om".$mid.".meta_value = '".esc_sql($value)."'";

                $mid++;
            }
        }

        if (!empty($d['terms'])) {
            $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->term_relationships.' otr ON op.ID = otr.object_id';
            $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->term_taxonomy.' ott ON ott.term_taxonomy_id = otr.term_taxonomy_id';

            $q['where'][] = 'ott.term_id IN ('.join(', ', $d['terms']).')';
        }

        return $q;
    }

    protected function _parse_object_comments($q, $d) {
        $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->comments.' oc ON oc.comment_ID = i.id';

        $q['order'].= ', oc.comment_date '.$this->_get_order();

        if (!empty($d['author'])) {
            $q['where'][] = 'oc.user_id IN ('.join(', ', $d['author']).')';
        }

        if (!empty($d['meta'])) {
            $mid = 1;

            foreach ($d['meta'] as $key => $value) {
                $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->commentmeta.' om'.$mid.' ON oc.comment_ID = om'.$mid.'.comment_id';

                $q['where'][] = "om".$mid.".meta_key = '".esc_sql($key)."'";
                $q['where'][] = "om".$mid.".meta_value = '".esc_sql($value)."'";

                $mid++;
            }
        }

        return $q;
    }

    protected function _parse_object_terms($q, $d) {
        $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->terms.' ot ON i.id = ot.term_id';
        $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->term_taxonomy.' ott ON ott.term_id = ot.term_id';

        $q['order'].= ', ott.term_id '.$this->_get_order();

        if (!empty($d['meta']) && GDRTS_WPV > 43) {
            $mid = 1;

            foreach ($d['meta'] as $key => $value) {
                $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->termmeta.' om'.$mid.' ON ott.term_id = om'.$mid.'.term_id';

                $q['where'][] = "om".$mid.".meta_key = '".esc_sql($key)."'";
                $q['where'][] = "om".$mid.".meta_value = '".esc_sql($value)."'";

                $mid++;
            }
        }

        return $q;
    }

    protected function _parse_object_users($q, $d) {
        $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->users.' ou ON ou.ID = i.id';

        $q['order'].= ', ou.user_registered '.$this->_get_order();

        if (!empty($d['meta'])) {
            $mid = 1;

            foreach ($d['meta'] as $key => $value) {
                $q['from'].= ' INNER JOIN '.gdrts_db()->wpdb()->usermeta.' om'.$mid.' ON ou.ID = om'.$mid.'.user_id';

                $q['where'][] = "om".$mid.".meta_key = '".esc_sql($key)."'";
                $q['where'][] = "om".$mid.".meta_value = '".esc_sql($value)."'";

                $mid++;
            }
        }

        return $q;
    }

    protected function _method() {
        $method = $this->args['method'];

        if (!empty($this->args['series'])) {
            $method.= '-'.$this->args['series'];
        }

        return $method;
    }

    protected function _use_cache() {
        if (is_null($this->cache)) {
            return gdrts_settings()->get('db_cache_on_query');
        }

        return $this->cache === true;
    }

    protected function _run() {
        $items = false;

        $db_cache_args = $this->args;
        $db_cache_args[] = $this->sql;

        if ($this->_use_cache()) {
            $items = gdrts_db_cache()->get('query', $this->_method(), $db_cache_args);
        }

        if ($items === false) {
            $raw = gdrts_db()->run_and_index($this->sql, 'item_id');

            if (empty($raw)) {
                $items = null;
            } else {
                switch ($this->args['return']) {
                    case 'realids':
                        $items = wp_list_pluck($raw, 'id');
                        break;
                    case 'ids':
                    case 'itemids':
                        $items = array_keys($raw);
                        break;
                    case 'quick':
                        $items = array_values($raw);
                        break;
                    default:
                    case 'objects':
                        $items = $this->prepare_objects($raw);
                        break;
                }
            }

            if ($this->_use_cache()) {
                gdrts_db_cache()->set('query', $this->_method(), $db_cache_args, $items);
            }
        }

        return is_null($items) || $items === false ? array() : $items;
    }
}

global $_gdrts_query;
$_gdrts_query = new gdrts_core_query();

/** @return gdrts_core_query */
function gdrts_query() {
    global $_gdrts_query;
    return $_gdrts_query;
}
