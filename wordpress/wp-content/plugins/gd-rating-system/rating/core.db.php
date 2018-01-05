<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_db extends d4p_wpdb_core {
    public $db_site = array();

    public $_prefix = 'gdrts';
    public $_tables = array(
        'cache',
        'items', 
        'itemmeta', 
        'logs', 
        'logmeta');
    public $_metas = array(
        'item' => 'item_id',
        'log' => 'log_id');

    public function get_item($item_id) {
        return $this->get_row($this->prepare("SELECT * FROM ".$this->items." WHERE item_id = %d", $item_id));
    }

    public function get_item_meta($item_id) {
        $raw = $this->run($this->prepare("SELECT * FROM ".$this->itemmeta." WHERE item_id = %d", $item_id));
        $data = array();

        foreach ($raw as $row) {
            $data[$row->meta_key] = maybe_unserialize($row->meta_value);
        }

        return $data;
    }

    public function get_items_meta($items) {
        $items = (array)$items;

        if (empty($items)) {
            return array();
        }

        $raw = $this->run("SELECT * FROM ".$this->itemmeta." WHERE item_id in (".join(', ', $items).")");
        $data = array();

        foreach ($raw as $row) {
            $data[$row->item_id][$row->meta_key] = maybe_unserialize($row->meta_value);
        }

        return $data;
    }

    public function get_item_id($entity, $name, $id) {
        $item_id = $this->get_var($this->prepare(
            "SELECT item_id FROM ".$this->items." 
             WHERE entity = %s 
             AND name = %s 
             AND id = %d",
                array($entity, $name, $id)
        ));

        if (is_null($item_id)) {
            $item_id = $this->add_new_item($entity, $name, $id);
        }

        return $item_id === false || is_null($item_id) ? false : intval($item_id);
    }

    public function get_log($log_id) {
        return $this->get_row($this->prepare("SELECT * FROM ".$this->logs." WHERE log_id = %d", $log_id));
    }

    public function get_log_meta($log_id) {
        $raw = $this->run($this->prepare("SELECT * FROM ".$this->logmeta." WHERE log_id = %d", $log_id));
        $data = array();

        foreach ($raw as $row) {
            $data[$row->meta_key] = maybe_unserialize($row->meta_value);
        }

        return $data;
    }

    public function get_logs_meta($logs) {
        $raw = $this->run("SELECT * FROM ".$this->logmeta." WHERE log_id in (".join(', ', $logs).")");
        $data = array();

        foreach ($raw as $row) {
            $data[$row->log_id][$row->meta_key] = maybe_unserialize($row->meta_value);
        }

        return $data;
    }

    public function get_log_entry($log_id) {
        $log = $this->get_log($log_id);

        if (is_object($log)) {
            $log->meta = $this->get_log_meta($log_id);
        }

        return $log;
    }

    public function get_log_latest_logged($filter = array()) {
        $sql = "SELECT l.`logged` FROM ".gdrts_db()->logs." l WHERE ".join(' AND ', $filter)." ORDER BY `logged` DESC LIMIT 0, 1";

        return $this->get_var($sql);
    }

    public function get_log_meta_filter($filter = array(), $return = 'm.*') {
        $sql = "SELECT ".$return." 
                FROM ".gdrts_db()->logmeta." m 
                INNER JOIN ".gdrts_db()->logs." l 
                ON l.log_id = m.log_id
                WHERE ".join(' AND ', $filter);

        $raw = $this->run($sql);
        $data = array();

        foreach ($raw as $row) {
            $data[$row->log_id][$row->meta_key] = maybe_unserialize($row->meta_value);
        }

        return $data;
    }

    public function add_new_item($entity, $name, $id) {
        if (!is_null($entity) && !is_null($name) && !is_null($id) && is_numeric($id)) {
            $result = $this->insert($this->items, array(
                'entity' => $entity,
                'name' => $name,
                'id' => $id
            ));

            if ($result !== false) {
                return $this->get_insert_id();
            }
        }

        return null;
    }

    public function get_log_item_user($item_id, $user_id, $ip = '', $log_ids = array()) {
        $log = array();

        if ($user_id == 0) {
            $verify = gdrts_settings()->get('annonymous_verify');

            if ($verify == 'cookie' && empty($log_ids)) {
                return array();
            }

            if (($verify == 'ip_or_cookie' || $verify == 'ip_and_cookie') && empty($log_ids)) {
                $verify = 'ip';
            }

            $query = $this->prepare("SELECT * FROM ".$this->logs." WHERE item_id = %d", array($item_id));

            switch ($verify) {
                case 'ip_or_cookie':
                    $query.= " AND (log_id IN (".join(', ', $log_ids).") OR ip = '".esc_sql($ip)."')";
                    break;
                case 'ip_and_cookie':
                    $query.= " AND log_id IN (".join(', ', $log_ids).") AND ip = '".esc_sql($ip)."'";
                    break;
                case 'cookie':
                    $query.= " AND log_id IN (".join(', ', $log_ids).")";
                    break;
                default:
                case 'ip':
                    $query.= " AND ip = '".esc_sql($ip)."'";
                    break;
            }

            if (gdrts_settings()->get('annonymous_same_ip')) {
                $query.= " AND user_id = 0";
            }
        } else {
            $query = $this->prepare("SELECT * FROM ".$this->logs." WHERE item_id = %d AND user_id = %d", array($item_id, $user_id));
        }

        $query.= ' ORDER BY log_id DESC';

        $raw = $this->run_and_index($query, 'log_id');

        foreach ($raw as &$r) {
            $r->meta = array();

            if (empty($r->series) || is_null($r->series)) {
                $r->series = 'default';
            }
        }

        if (!empty($raw)) {
            $metas = $this->run("SELECT * FROM ".$this->logmeta." WHERE log_id in (".join(', ', array_keys($raw)).")");

            foreach ($metas as $meta) {
                $raw[$meta->log_id]->meta[$meta->meta_key] = maybe_unserialize($meta->meta_value);
            }
        }

        foreach ($raw as $log_id => $r) {
            $log[$r->method][$r->series][$r->status][$log_id] = new gdrts_core_log_item((array)$r);
        }

        return $log;
    }

    public function get_log_item_user_method($item_id, $user_id, $method, $series = null, $ip = '', $log_ids = array()) {
        $log = array();
        $ids = array();

        if ($user_id == 0) {
            if (empty($ip)) {
                $ip = d4p_visitor_ip();
            }

            $verify = gdrts_settings()->get('annonymous_verify');

            if ($verify == 'ip_andr_cookie') {
                $verify = 'ip_and_cookie';
            }

            if ($verify == 'cookie' && empty($log_ids)) {
                return array();
            }

            if (($verify == 'ip_or_cookie' || $verify == 'ip_and_cookie') && empty($log_ids)) {
                $verify = 'ip';
            }

            $query = $this->prepare("SELECT * FROM ".$this->logs." WHERE item_id = %d AND method = %s", array($item_id, $method));

            switch ($verify) {
                case 'ip_or_cookie':
                    $query.= " AND (log_id IN (".join(', ', $log_ids).") OR ip = '".esc_sql($ip)."')";
                    break;
                case 'ip_and_cookie':
                    $query.= " AND log_id IN (".join(', ', $log_ids).") AND ip = '".esc_sql($ip)."'";
                    break;
                case 'cookie':
                    $query.= " AND log_id IN (".join(', ', $log_ids).")";
                    break;
                default:
                case 'ip':
                    $query.= " AND ip = '".esc_sql($ip)."'";
                    break;
            }

            if (gdrts_settings()->get('annonymous_same_ip')) {
                $query.= " AND user_id = 0";
            }
        } else {
            $query = $this->prepare("SELECT * FROM ".$this->logs." WHERE item_id = %d AND user_id = %d AND method = %s", array($item_id, $user_id, $method));
        }

        if (!is_null($series) && !empty($series)) {
            $query.= " AND series = '".esc_sql($series)."'";
        }

        $query.= ' ORDER BY log_id DESC';

        $raw = $this->run($query);

        foreach ($raw as $row) {
            $id = (int)$row->log_id;
            $action = $row->action;

            $ids[$id] = $action;

            $log[$action][$id] = $row;
            $log[$action][$id]->meta = new stdClass();
        }

        if (!empty($log)) {
            $raw = $this->run("SELECT * FROM ".$this->logmeta." WHERE log_id in (".join(', ', array_keys($ids)).")");

            foreach ($raw as $meta) {
                $id = (int)$meta->log_id;
                $key = $meta->meta_key;
                $action = $ids[$id];

                $log[$action][$id]->meta->$key = maybe_unserialize($meta->meta_value);
            }
        }

        return $log;
    }

    public function update_item_latest($item_id) {
        $this->update($this->items, array(
            'latest' => gdrts_db()->datetime()
        ), array(
            'item_id' => $item_id
        ));
    }

    public function add_to_log($item_id, $user_id, $method, $data = array(), $meta = array()) {
        $defaults = array(
            'action' => 'vote',
            'status' => 'active',
            'ip' => d4p_visitor_ip(),
            'logged' => $this->datetime(),
            'ref_id' => 0
        );

        $data = wp_parse_args($data, $defaults);

        $data['item_id'] = $item_id;
        $data['user_id'] = $user_id;
        $data['method'] = $method;

        $result = $this->insert($this->logs, $data);

        if ($result !== false) {
            $log_id = $this->get_insert_id();

            $meta = apply_filters('gdrts_db_add_vote_to_log', $meta, $data);

            $this->insert_meta_data($this->logmeta, 'log_id', $log_id, $meta);

            if ($data['ref_id'] > 0) {
                $this->update($this->logs, array('status' => 'replaced'), array('log_id' => $data['ref_id']));
            }

            do_action('gdrts_db_vote_logged', $log_id, $data, $meta);

            return $log_id;
        }

        return null;
    }

    public function get_users_who_voted_for_item($item_id, $method, $series = null, $args = array()) {
        $defaults = array('limit' => 24, 'offset' => 0, 'orderby' => 'logged', 'order' => 'DESC');

        $args = wp_parse_args($args, $defaults);

        if (!gdrts_is_method_valid($method)) {
            return new WP_Error('method_invalid', __("Method is invalid", "gd-rating-system"));
        }

        $item_id = intval($item_id);

        $args['offset'] = intval($args['offset']);
        $args['limit'] = intval($args['limit']);

        $SQL = array(
            'select' => 'l.log_id, l.user_id, m.meta_value as vote, u.user_login, u.user_email, u.user_url, u.display_name',
            'join' => $this->logs.' l INNER JOIN '.$this->logmeta.' m ON m.log_id = l.log_id INNER JOIN '.$this->users.' u ON u.ID = l.user_id',
            'where' => array('l.item_id = '.$item_id, "m.meta_key = 'vote'", "l.status = 'active'"),
            'orderby' => 'l.logged',
            'order' => $args['order'] == 'DESC' ? 'DESC' : 'ASC',
            'offset' => $args['offset'] > 0 ? $args['offset'] : 0,
            'limit' => $args['limit'] > 0 ? $args['limit'] : 0
        );

        $SQL['where'][] = "l.method = '".esc_sql($method)."'";

        if (!is_null($series) && !empty($series)) {
            $SQL['where'][] = "l.series = '".esc_sql($series)."'";
        }

        switch ($args['orderby']) {
            case 'user':
                $SQL['orderby'] = 'l.user_id';
                break;
            case 'rand':
                $SQL['orderby'] = 'rand()';
                $SQL['order'] = '';
                break;
        }

        $SQL = apply_filters('gdrts_get_users_who_voted_for_item_query', $SQL, $item_id, $method, $series, $args);

        if (!empty($SQL['where'])) {
            $SQL['where'] = ' WHERE '.join(' AND ', $SQL['where']);
        } else {
            $SQL['where'] = '';
        }

        $query = "SELECT SQL_CALC_FOUND_ROWS ".$SQL['select']." FROM ".$SQL['join'].$SQL['where'];
        $query.= " ORDER BY ".$SQL['orderby']." ".$SQL['order'];

        if ($SQL['limit'] > 0) {
            $query.= " LIMIT ".$SQL['offset'].", ".$SQL['limit'];
        }

        $list = gdrts_db()->run($query);
        
        return array(
            'list' => $list,
            'item_id' => $item_id,
            'method' => $method,
            'series' => $series,
            'count' => count($list),
            'total' => gdrts_db()->found_rows(),
            'offset' => $SQL['offset'],
            'limit' => $SQL['limit']
        );
    }
}
