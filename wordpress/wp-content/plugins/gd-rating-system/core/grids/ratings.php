<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_grid_ratings extends d4p_grid {
    public $_sanitize_orderby_fields = array('item_id', 'entity', 'id', 'latest');
    public $_checkbox_field = 'item_id';
    public $_table_class_name = 'gdrts-grid-ratings';

    public $_status = '';

    function __construct($args = array()) {
        parent::__construct(array(
            'singular'=> 'rating',
            'plural' => 'ratings',
            'ajax' => false
        ));

        $this->_status = isset($_GET['status']) && !empty($_GET['status']) ? $_GET['status'] : 'rated';
    }

    private function _log($args) {
        $base_url = 'admin.php?page=gd-rating-system-log';
        $url = $base_url.'&'.$args;

        $url.= '&_wpnonce='.wp_create_nonce('gdrts-admin-panel');
        $url.= '&_wp_http_referer='.wp_unslash(self_admin_url($base_url));

        return self_admin_url($url);
    }

    private function _self($args, $getback = false) {
        $base_url = 'admin.php?page=gd-rating-system-ratings';
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
        $url = 'admin.php?page=gd-rating-system-ratings';

        return array(
            'all' => '<a href="'.add_query_arg('status', 'all', $url).'" class="'.($this->_status == 'all' ? 'current' : '').'">'.__("All", "gd-rating-system").'</a>',
            'rated' => '<a href="'.add_query_arg('status', 'rated', $url).'" class="'.($this->_status == 'rated' ? 'current' : '').'">'.__("Rated", "gd-rating-system").'</a>',
            'notrated' => '<a href="'.add_query_arg('status', 'notrated', $url).'" class="'.($this->_status == 'notrated' ? 'current' : '').'">'.__("Not Rated", "gd-rating-system").'</a>'
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

            $all_entities = array_merge(array(
                array(
                    'title' => __("Global", "gd-rating-system"),
                    'values' => array('' => __("All Entities", "gd-rating-system"))
                )
            ), gdrts_list_all_entities());

            $_sel_entity = isset($_GET['filter-entity']) && !empty($_GET['filter-entity']) ? $_GET['filter-entity'] : '';
            $_sel_period = isset($_GET['filter-period']) && !empty($_GET['filter-period']) ? $_GET['filter-period'] : '';

            echo '<div class="alignleft actions">';
            d4p_render_grouped_select($all_entities, array('selected' => $_sel_entity, 'name' => 'filter-entity'));
            d4p_render_select($all_periods, array('selected' => $_sel_period, 'name' => 'filter-period'));

            do_action('gdrts_admin_grid_ratings_filter');

            submit_button(__("Filter", "gd-rating-system"), 'button', false, false, array('id' => 'gdrts-ratings-submit'));
            echo '</div>';
        }
    }

    public function list_all_months_dropdown() {
        global $wp_locale;

        $sql = "SELECT DISTINCT YEAR(latest) AS year, MONTH(latest) AS month FROM ".gdrts_db()->items." ORDER BY latest DESC";
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
    
    public function rows_per_page() {
        $user = get_current_user_id();
        $per_page = get_user_meta($user, 'gdrts_rows_per_page_ratings', true);

        if (empty($per_page) || $per_page < 1) {
            $per_page = 25;
        }

        return $per_page;
    }

    public function get_columns() {
	return apply_filters('gdrts_admin_grid_ratings_columns', array(
            'cb' => '<input type="checkbox" />',
            'item_id' => __("Item ID", "gd-rating-system"),
            'entity' => __("Rating Type", "gd-rating-system"),
            'id' => __("ID", "gd-rating-system"),
            'ratings' => __("Ratings", "gd-rating-system"),
            'latest' => __("Latest Vote", "gd-rating-system")
	));
    }

    public function get_sortable_columns() {
	$columns = array(
            'item_id' => array('item_id', false),
            'entity' => array('entity', false),
            'name' => array('name', false),
            'id' => array('id', false),
            'latest' => array('latest', false)
	);

        return $columns;
    }

    public function get_row_classes($item) {
        $classes = array();

        if (!$item->data->is_valid()) {
            $classes[] = 'gdrts-not-valid';
        }

        return $classes;
    }

    public function get_bulk_actions() {
        $bulk = array(
            'delete' => __("Delete", "gd-rating-system"),
            'clear' => __("Clear", "gd-rating-system"),
            'clear_stars-rating' => __("Clear Stars Ratings", "gd-rating-system")
        );

        return $bulk;
    }

    public function column_default($item, $column_name){
        $value = isset($item->$column_name) ? $item->$column_name : '';

        return apply_filters('gdrts_admin_grid_ratings_column_value', $value, $column_name, $item);
    }

    public function column_ratings($item) {
        $actions = array();

        $lines = apply_filters('gdrts_ratings_grid_ratings', array(), $item);

        foreach (array_keys($lines) as $key) {
            $lines[$key] = $lines[$key].'<span class="gdrts-row-actions row-actions"> &middot; <a class="gdrts-link-delete gdrts-action-clear-ratings" href="'.$this->_self('item_id='.$item->item_id.'&method='.$key.'&single-action=clear', true).'">'.__("clear", "gd-rating-system").'</a></span>';
        }

        $render = empty($lines) ? __("Empty", "gd-rating-system") : join('<br/>', $lines);

        $actions = apply_filters('gdrts_ratings_grid_actions_column_ratings', $actions, $item);

        return $render.$this->row_actions($actions);
    }

    public function column_entity($item) {
        $actions = array(
            'log' => '<a href="'.$this->_log('&filter-entity='.$item->entity.'.'.$item->name).'">'.__("Log", "gd-rating-system").'</a>'
        );

        $_entity  = gdrts()->get_entity($item->entity);

        $label = $_entity['label'].' :: ';

        if (isset($_entity['types'][$item->name])) {
            $label.= $_entity['types'][$item->name];
        } else {
            $label.= $item->name.' <strong style="color: red">('.__("missing", "gd-rating-system").')</strong>';
        }

        $render = apply_filters('gdrts_ratings_grid_content_column_entity', $label, $item);
        $actions = apply_filters('gdrts_ratings_grid_actions_column_entity', $actions, $item);

        return $render.$this->row_actions($actions);
    }

    public function column_id($item) {
        $actions = array(
            'log' => '<a href="'.$this->_log('filter-item_id='.$item->item_id).'">'.__("Log", "gd-rating-system").'</a>'
        );

        $title = __("Item not found", "gd-rating-system");

        if ($item->data->is_valid()) {
            $title = $item->title();
            $url = $item->url();

            if ($url != '') {
                $actions['view'] = '<a target="_blank" href="'.$url.'">'.__("View", "gd-rating-system").'</a>';
            }
        }

        $actions['delete'] = '<a class="gdrts-link-delete gdrts-action-delete-ratings" href="'.$this->_self('item_id='.$item->item_id.'&single-action=delete', true).'">'.__("Delete", "gd-rating-system").'</a>';

        $title = apply_filters('gdrts_ratings_grid_id_title', $title, $item);

        $render = apply_filters('gdrts_ratings_grid_content_column_id', '['.$item->id.'] '.$title, $item);
        $actions = apply_filters('gdrts_ratings_grid_actions_column_id', $actions, $item);

        return $render.$this->row_actions($actions);
    }

    public function column_latest($item) {
        if ($item->latest == '0000-00-00 00:00:00') {
            return __("No Votes", "gd-rating-system");
        } else {
            $timestamp = gdrts_timestamp_from_gmt_date($item->latest);

            return date('Y-m-d', $timestamp).'<br/>@ '.date('H:i:s', $timestamp);
        }
    }

    public function prepare_items() {
        $this->_column_headers = $this->get_column_info();

        $per_page = $this->rows_per_page();

        $select = "i.*";
        $join = gdrts_db()->items." i";
        $where = array();

        $status = $this->_status;

        $entity = isset($_GET['filter-entity']) && !empty($_GET['filter-entity']) ? $_GET['filter-entity'] : '';
        $last = isset($_GET['filter-period']) && !empty($_GET['filter-period']) ? $_GET['filter-period'] : '0';

        if ($status != '' && $status != 'all') {
            $join.= " LEFT JOIN (SELECT DISTINCT item_id FROM ".gdrts_db()->itemmeta.") m ON m.item_id = i.item_id";

            if ($status == 'notrated') {
                $where[] = "m.item_id IS NULL";
            } else {
                $where[] = "m.item_id IS NOT NULL";
            }
        }

        if ($entity != '') {
            $parts = explode('.', $entity, 2);

            $where[] = "i.`entity` = '".esc_sql($parts[0])."'";

            if (count($parts) == 2) {
                $where[] = "i.`name` = '".esc_sql($parts[1])."'";
            }
        }

        if ($last != '' && $last != 'all') {
            if (strlen($last) == 7) {
                $date = explode('-', $last);

                if (count($date) == 2) {
                    $where[] = "YEAR(i.`latest`) = ".intval($date[0]);
                    $where[] = "MONTH(i.`latest`) = ".intval($date[1]);
                }
            } else {
                $date = explode('-', $last);

                if ($date[0] == 'dy') {
                    $last = $date[1] * 24;
                }

                if ($last > 0) {
                    $where[] = "i.`latest` > DATE_SUB(NOW(), interval ".$last." hour)";
                }
            }
        }

        $orderby = !empty($_GET['orderby']) ? $this->sanitize_field('orderby', $_GET['orderby'], 'item_id') : 'item_id';
        $order = !empty($_GET['order']) ? $this->sanitize_field('order', $_GET['order'], 'DESC') : 'DESC';

        $paged = !empty($_GET['paged']) ? esc_sql($_GET['paged']) : '';
        if (empty($paged) || !is_numeric($paged) || $paged <= 0 ){
            $paged = 1;
        }

        $offset = intval(($paged - 1) * $per_page);
        
        $SQL = apply_filters('gdrts_admin_grid_ratings_query_parts', array('select' => $select, 
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

        $this->items = gdrts_db()->run_and_index($query, 'item_id');

        $total_rows = gdrts_db()->get_found_rows();

        $this->set_pagination_args(array(
            'total_items' => $total_rows,
            'total_pages' => ceil($total_rows / $per_page),
            'per_page' => $per_page,
        ));

        foreach (array_keys($this->items) as $item) {
            $this->items[$item]->meta = array();
            $this->items[$item]->data = gdrts_load_object_data($this->items[$item]->entity, $this->items[$item]->name, $this->items[$item]->id);

            $this->items[$item] = new gdrts_rating_item($this->items[$item]);
        }

        $ids = gdrts_db()->pluck($this->items, 'item_id');

        if (!empty($ids)) {
            $query_meta = "SELECT * FROM ".gdrts_db()->itemmeta." WHERE item_id in (".join(', ', $ids).")";
            $metas = gdrts_db()->run($query_meta);

            foreach ($metas as $meta) {
                $item = $meta->item_id;

                $this->items[$item]->meta[$meta->meta_key] = $meta->meta_value;
            }
        }
    }
}
