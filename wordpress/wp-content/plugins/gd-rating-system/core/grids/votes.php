<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_grid_votes extends d4p_grid {
    public $_sanitize_orderby_fields = array('l.item_id', 'l.log_id', 'l.user_id', 'l.logged');
    public $_checkbox_field = 'log_id';
    public $_table_class_name = 'gdrts-grid-votes';

    public $_remove_log = false;

    public $_status = '';

    public $rating_objects = array();
    
    function __construct($args = array()) {
        $this->_remove_log = gdrts_settings()->get('admin_log_remove');

        $this->_status = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : '';

        parent::__construct(array(
            'singular'=> 'vote',
            'plural' => 'votes',
            'ajax' => false
        ));
    }

    private function _ratings($args) {
        $base_url = 'admin.php?page=gd-rating-system-ratings';
        $url = $base_url.'&'.$args;

        $url.= '&_wpnonce='.wp_create_nonce('gdrts-admin-panel');
        $url.= '&_wp_http_referer='.wp_unslash(self_admin_url($base_url));

        return self_admin_url($url);
    }

    private function _self($args, $getback = false) {
        $base_url = 'admin.php?page=gd-rating-system-log';
        $url = $base_url.'&'.$args;

        if ($this->_status != '') {
            $url.= '&status='.$this->_status;
        }

        if ($getback) {
            $url.= '&_wpnonce='.wp_create_nonce('gdrts-admin-panel');
            $url.= '&gdrts_handler=getback';
            $url.= '&_wp_http_referer='.wp_unslash(self_admin_url($base_url));
        }

        return self_admin_url($url);
    }

    public function get_views() {
        $url = 'admin.php?page=gd-rating-system-log';

        return array(
            'all' => '<a href="'.$url.'" class="'.($this->_status == '' ? 'current' : '').'">'.__("All", "gd-rating-system").'</a>',
            'active' => '<a href="'.add_query_arg('status', 'active', $url).'" class="'.($this->_status == 'active' ? 'current' : '').'">'.__("Active", "gd-rating-system").'</a>',
            'inactive' => '<a href="'.add_query_arg('status', 'replaced', $url).'" class="'.($this->_status == 'inactive' ? 'current' : '').'">'.__("Inactive", "gd-rating-system").'</a>'
        );
    }

    public function extra_tablenav($which) {
        if ($which == 'top') {
            $all_periods = array_merge(array(
                'all' => __("All Time", "gd-rating-system"),
                'hr-01' => __("Last hour", "gd-rating-system"),
                'hr-04' => __("Last 4 hours", "gd-rating-system"),
                'hr-08' => __("Last 8 hours", "gd-rating-system"),
                'hr-12' => __("Last 12 hours", "gd-rating-system"),
                'dy-01' => __("Last day", "gd-rating-system"),
                'dy-02' => __("Last 2 days", "gd-rating-system"),
                'dy-03' => __("Last 3 day", "gd-rating-system"),
                'dy-05' => __("Last 5 day", "gd-rating-system"),
                'dy-07' => __("Last 7 day", "gd-rating-system"),
                'dy-30' => __("Last 30 days", "gd-rating-system")
            ), $this->list_all_months_dropdown(''));

            $all_methods = array_merge(array(
                '-1' => __("All Methods", "gd-rating-system")
            ), gdrts_list_all_method(true));

            $all_entities = array_merge(array(
                array(
                    'title' => __("Global", "gd-rating-system"),
                    'values' => array('' => __("All Entities", "gd-rating-system"))
                )
            ), gdrts_list_all_entities());

            $_sel_entity = isset($_GET['filter-entity']) && !empty($_GET['filter-entity']) ? $_GET['filter-entity'] : '';
            $_sel_method = isset($_GET['filter-method']) && !empty($_GET['filter-method']) ? $_GET['filter-method'] : '';
            $_sel_item_id = isset($_GET['filter-item_id']) && !empty($_GET['filter-item_id']) ? intval($_GET['filter-item_id']) : '';
            $_sel_user_id = isset($_GET['filter-user_id']) && $_GET['filter-user_id'] !== '' ? intval($_GET['filter-user_id']) : '';
            $_sel_period = isset($_GET['filter-period']) && !empty($_GET['filter-period']) ? $_GET['filter-period'] : '';

            echo '<div class="alignleft actions">';
            d4p_render_grouped_select($all_entities, array('selected' => $_sel_entity, 'name' => 'filter-entity'));
            d4p_render_select($all_methods, array('selected' => $_sel_method, 'name' => 'filter-method'));
            d4p_render_select($all_periods, array('selected' => $_sel_period, 'name' => 'filter-period'));

            do_action('gdrts_admin_grid_votes_filter');

            echo '<input title="'.__("Item ID", "gd-rating-system").'" style="width: 100px;" type="number" placeholder="'.__("Item ID", "gd-rating-system").'" value="'.$_sel_item_id.'" name="filter-item_id" />';
            echo '<input title="'.__("User ID", "gd-rating-system").'" style="width: 100px;" type="number" placeholder="'.__("User ID", "gd-rating-system").'" value="'.$_sel_user_id.'" name="filter-user_id" />';
            submit_button(__("Filter", "gd-rating-system"), 'button', false, false, array('id' => 'gdrts-ratings-submit'));
            echo '</div>';
        }
    }

    public function list_all_months_dropdown() {
        global $wp_locale;

        $sql = "SELECT DISTINCT YEAR(logged) AS year, MONTH(logged) AS month FROM ".gdrts_db()->logs." ORDER BY logged DESC";
        $months = gdrts_db()->run($sql);

        $list = array();

        foreach ($months as $row) {
            if ($row->month > 0 && $row->year > 0) {
                $month = zeroise($row->month, 2);
                $year = $row->year;

                $list[$year.'-'.$month] = sprintf(__("%s %s", "gd-rating-system"), $wp_locale->get_month($month), $year);
            }
        }

        return $list;
    }

    function rows_per_page() {
        $user = get_current_user_id();
        $per_page = get_user_meta($user, 'gdrts_rows_per_page_votes', true);

        if (empty($per_page) || $per_page < 1) {
            $per_page = 25;
        }

        return $per_page;
    }

    public function get_columns() {
	return apply_filters('gdrts_admin_grid_votes_columns', array(
            'cb' => '<input type="checkbox" />',
            'log_id' => __("Log ID", "gd-rating-system"),
            'item' => __("Item", "gd-rating-system"),
            'method' => __("Method", "gd-rating-system"),
            'action' => __("Action", "gd-rating-system"),
            'vote' => __("Vote", "gd-rating-system"),
            'user' => __("User", "gd-rating-system"),
            'ip' => __("IP", "gd-rating-system"),
            'logged' => __("Logged", "gd-rating-system")
	));
    }

    public function get_sortable_columns() {
	$columns = array(
            'log_id' => array('l.log_id', false),
            'item' => array('l.item_id', false),
            'action' => array('l.action', false),
            'method' => array('l.method', false),
            'user' => array('l.user_id', false),
            'logged' => array('l.logged', false)
	);

        return $columns;
    }

    public function get_bulk_actions() {
        $bulk = array(
            'delete' => __("Delete vote", "gd-rating-system")
        );

        if ($this->_remove_log) {
            $bulk['remove'] = __("Remove from log", "gd-rating-system");
        }
        
        return $bulk;
    }

    public function column_default($item, $column_name){
        $value = isset($item->$column_name) ? $item->$column_name : '';

        return apply_filters('gdrts_admin_grid_votes_column_value', $value, $column_name, $item);
    }

    public function column_item($item) {
        $actions = array(
            'log' => '<a href="'.$this->_self('filter-item_id='.$item->item_id).'">'.__("Log", "gd-rating-system").'</a>'
        );

        $_entity  = gdrts()->get_entity($item->entity);

        $label = $_entity['label'].' :: ';

        if (isset($_entity['types'][$item->name])) {
            $label.= $_entity['types'][$item->name];
        } else {
            $label.= $item->name.' <strong style="color: red">('.__("missing", "gd-rating-system").')</strong>';
        }

        $title = '';
        $obj = $this->rating_objects[$item->item_id];
        if ($obj->data->is_valid()) {
            $title = wp_kses($obj->title(), array());
            $url = $obj->url();

            if ($url != '') {
                $actions['view'] = '<a target="_blank" href="'.$url.'">'.__("View", "gd-rating-system").'</a>';
            }
        }

        $label.= ' :: <attr title="'.$title.'">'.$item->id.'</attr>';

        $render = apply_filters('gdrts_votes_grid_content_column_item', '['.$item->item_id.'] '.$label, $item);
        $actions = apply_filters('gdrts_votes_grid_actions_column_item', $actions, $item);

        return $render.$this->row_actions($actions);
    }

    public function column_method($item) {
        $actions = array(
            'log' => '<a href="'.$this->_self('filter-method='.$item->method).'">'.__("Log", "gd-rating-system").'</a>'
        );

        $label = isset(gdrts()->methods[$item->method]) ? gdrts()->methods[$item->method]['label'] : ucwords(str_replace('-', ' ', $item->method));

        $render = apply_filters('gdrts_votes_grid_content_column_method', $label, $item);
        $actions = apply_filters('gdrts_votes_grid_actions_column_method', $actions, $item);

        return $render.$this->row_actions($actions);
    }

    public function column_action($item) {
        $render = apply_filters('gdrts_votes_grid_content_column_action', ucfirst($item->action), $item);
        $actions = apply_filters('gdrts_votes_grid_actions_column_action', array(), $item);

        return $render.$this->row_actions($actions);
    }

    public function column_vote($item) {
        $actions = array();

        if ($this->_remove_log) {
            $actions['remove'] = '<a class="gdrts-link-delete gdrts-action-remove-entry" href="'.$this->_self('log_id='.$item->log_id.'&single-action=remove', true).'">'.__("Remove from Log", "gd-rating-system").'</a>';
        }

        if ($item->method != 'stars-review') {
            $actions['delete'] = '<a class="gdrts-link-delete gdrts-action-delete-entry" href="'.$this->_self('log_id='.$item->log_id.'&single-action=delete', true).'">'.__("Delete Vote", "gd-rating-system").'</a>';
        }

        $vote = apply_filters('gdrts_votes_grid_vote_'.$item->method, '', $item);

        $render = apply_filters('gdrts_votes_grid_content_column_vote', $vote, $item);
        $actions = apply_filters('gdrts_votes_grid_actions_column_vote', $actions, $item);

        return $render.$this->row_actions($actions);
    }

    public function column_user($item) {
        $actions = array(
            'log' => '<a href="'.$this->_self('filter-user_id='.$item->user_id).'">'.__("Log", "gd-rating-system").'</a>'
        );

        $label = '';

        if ($item->user_id > 0) {
            $user = get_user_by('id', $item->user_id);

            if ($user) {
                $label = '<a href="'.get_edit_user_link($item->user_id).'" target="_blank">'.$user->display_name.'</a>';
            }
        }

        $render = apply_filters('gdrts_votes_grid_content_column_user', '['.$item->user_id.'] '.$label, $item);
        $actions = apply_filters('gdrts_votes_grid_actions_column_user', $actions, $item);

        return $render.$this->row_actions($actions);
    }

    public function column_ip($item) {
        $actions = array(
            'log' => '<a href="'.$this->_self('filter-ip='.$item->ip).'">'.__("Log", "gd-rating-system").'</a>'
        );

        $render = apply_filters('gdrts_votes_grid_content_column_ip', sprintf('<span>%s</span>', $item->ip), $item);
        $actions = apply_filters('gdrts_votes_grid_actions_column_ip', $actions, $item);

        return $render.$this->row_actions($actions);
    }

    public function column_latest($item) {
        $timestamp = gdrts_timestamp_from_gmt_date($item->logged);

        return date('Y-m-d', $timestamp).'<br/>@ '.date('H:i:s', $timestamp);
    }

    public function prepare_items() {
        $this->_column_headers = $this->get_column_info();

        $per_page = $this->rows_per_page();

        $select = "*";
        $join = gdrts_db()->logs." l INNER JOIN ".gdrts_db()->items." i ON l.item_id = i.item_id";
        $where = array();

        $status = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : '';
        $entity = isset($_GET['filter-entity']) && !empty($_GET['filter-entity']) ? $_GET['filter-entity'] : '';
        $method = isset($_GET['filter-method']) && !empty($_GET['filter-method']) ? $_GET['filter-method'] : '';
        $item_id = isset($_GET['filter-item_id']) && !empty($_GET['filter-item_id']) ? intval($_GET['filter-item_id']) : '';
        $user_id = isset($_GET['filter-user_id']) && $_GET['filter-user_id'] !== '' ? intval($_GET['filter-user_id']) : -1;
        $ip = isset($_GET['filter-ip']) && !empty($_GET['filter-ip']) ? d4p_ip_cleanup($_GET['filter-ip']) : '';
        $last = isset($_GET['filter-period']) && !empty($_GET['filter-period']) ? $_GET['filter-period'] : '0';

        if ($entity != '') {
            $parts = explode('.', $entity, 2);

            $where[] = "i.`entity` = '".esc_sql($parts[0])."'";

            if (count($parts) == 2) {
                $where[] = "i.`name` = '".esc_sql($parts[1])."'";
            }
        }

        if ($status != '') {
            $where[] = "l.`status` = '".esc_sql($status)."'";
        }

        if ($method != '') {
            $_m = explode('::', $method, 2);
            $_method = $_m[0];
            $_series = isset($_m[1]) ? $_m[1] : '';

            $where[] = "l.`method` = '".esc_sql($_method)."'";

            if ($_series != '') {
                $where[] = "l.`series` = '".esc_sql($_series)."'";
            }
        }

        if ($item_id != '') {
            $where[] = "l.`item_id` = '".esc_sql($item_id)."'";
        }

        if ($user_id > -1) {
            $where[] = "l.`user_id` = ".esc_sql($user_id);
        }

        if ($ip != '') {
            $where[] = "l.`ip` = '".esc_sql($ip)."'";
        }

        if ($last != '' && $last != 'all') {
            if (strlen($last) == 7) {
                $date = explode('-', $last);

                if (count($date) == 2) {
                    $where[] = "YEAR(l.`logged`) = ".intval($date[0]);
                    $where[] = "MONTH(l.`logged`) = ".intval($date[1]);
                }
            } else {
                $date = explode('-', $last);

                if ($date[0] == 'dy') {
                    $last = $date[1] * 24;
                }

                if ($last > 0) {
                    $where[] = "l.`logged` > DATE_SUB(NOW(), interval ".$last." hour)";
                }
            }
        }

        $orderby = !empty($_GET['orderby']) ? $this->sanitize_field('orderby', $_GET['orderby'], 'l.log_id') : 'l.log_id';
        $order = !empty($_GET['order']) ? $this->sanitize_field('order', $_GET['order'], 'DESC') : 'DESC';

        $paged = !empty($_GET['paged']) ? esc_sql($_GET['paged']) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0 ){
            $paged = 1;
        }

        $offset = intval(($paged - 1) * $per_page);

        $SQL = apply_filters('gdrts_admin_grid_votes_query_parts', array('select' => $select, 
            'join' => $join, 
            'where' => $where, 
            'orderby' => $orderby, 
            'order' => $order, 
            'offset' => $offset, 
            'per_page' => $per_page
        ));

        if (!empty($SQL['where'])) {
            $SQL['where'] = ' WHERE '.join(' AND ', $SQL['where']);
        } else {
            $SQL['where'] = '';
        }

        $query = "SELECT SQL_CALC_FOUND_ROWS ".$SQL['select']." FROM ".$SQL['join'].$SQL['where'];
        $query.= " ORDER BY ".$SQL['orderby']." ".$SQL['order']." LIMIT ".$SQL['offset'].", ".$SQL['per_page'];

        $this->items = gdrts_db()->run_and_index($query, 'log_id');

        $total_rows = gdrts_db()->get_found_rows();

        $this->set_pagination_args(array(
            'total_items' => $total_rows,
            'total_pages' => ceil($total_rows / $per_page),
            'per_page' => $per_page,
        ));

        foreach (array_keys($this->items) as $item) {
            $this->items[$item]->meta = array();

            $item_id = $this->items[$item]->item_id;

            if (!isset($this->rating_objects[$item_id])) {
                $this->rating_objects[$item_id] = gdrts_get_rating_item_by_id($item_id);
            }
        }

        $ids = gdrts_db()->pluck($this->items, 'log_id');

        if (!empty($ids)) {
            $query_meta = "SELECT * FROM ".gdrts_db()->logmeta." WHERE log_id in (".join(', ', $ids).")";
            $metas = gdrts_db()->run($query_meta);

            foreach ($metas as $meta) {
                $item = $meta->log_id;

                $this->items[$item]->meta[$meta->meta_key] = $meta->meta_value;
            }
        }
    }
}

