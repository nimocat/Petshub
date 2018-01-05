<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_transfer_wp_postratings {
    public $page = 500;

    public function __construct() {
        $this->page = gdrts_settings()->get('step_transfer');
    }

    public function round_to_page($number) {
        return ceil(intval($number) / $this->page) * $this->page;
    }

    public function db_tables_exist() {
        $tables = array(
            gdrts_db()->wpdb()->prefix.'ratings'
        );

        $ok = true;

        foreach ($tables as $table) {
            $rows = gdrts_db()->run("SHOW TABLES LIKE '".$table."'");

            if (count($rows) == 0) {
                $ok = false;
            }
        }

        return $ok;
    }

    public function count($method = 'log') {
        $count = 0;

        switch ($method) {
            case 'data':
                $sql = "SELECT COUNT(*) FROM ".gdrts_db()->wpdb()->postmeta." WHERE meta_key IN ('ratings_users', 'ratings_score')";
                $count+= $this->round_to_page(gdrts_db()->get_var($sql));
                break;
            case 'log':
                $sql = "SELECT COUNT(*) FROM ".gdrts_db()->wpdb()->prefix."ratings r WHERE r.rating_id NOT IN 
                (SELECT CAST(meta_value as UNSIGNED) FROM ".gdrts_db()->logmeta." WHERE meta_key = 'wppr-import')";
                $count+= $this->round_to_page(gdrts_db()->get_var($sql));
                break;
        }

        return $count;
    }

    public function transfer($max = 5, $method = 'log', $offset = 0) {
        switch ($method) {
            case 'data':
                $this->_transfer_data($max, $offset);
                break;
            case 'log':
                $this->_transfer_log($max, $offset);
                break;
        }
    }

    private function _transfer_log($max = 5, $offset = 0) {
        $sql = "SELECT r.rating_id, r.rating_postid AS post_id, r.rating_rating AS vote, 
                FROM_UNIXTIME(r.rating_timestamp) AS logged, r.rating_ip AS ip, r.rating_userid AS user_id 
                FROM ".gdrts_db()->wpdb()->prefix."ratings r WHERE r.rating_id NOT IN 
                (SELECT CAST(meta_value as UNSIGNED) FROM ".gdrts_db()->logmeta." WHERE meta_key = 'wppr-import') 
                ORDER BY r.rating_id ASC LIMIT ".$this->page;
        $raw = gdrts_db()->run($sql);

        if (!empty($raw)) {
            foreach ($raw as $rating) {
                $post_type = get_post_type($rating->post_id);

                if (!$post_type) {
                    $post_type = 'post';
                }

                gdrtsm_stars_rating()->_load_settings_rule('posts', $post_type);

                $args = array(
                    'entity' => 'posts', 
                    'name' => $post_type, 
                    'id' => $rating->post_id
                );

                $item = gdrts_get_rating_item($args);

                $factor = gdrtsm_stars_rating()->get_rule('stars') / $max;

                $data = array(
                    'action' => 'vote',
                    'ip' => $rating->ip,
                    'logged' => $rating->logged
                );

                $meta = array(
                    'vote' => $rating->vote * $factor,
                    'max' => gdrtsm_stars_rating()->get_rule('stars'),
                    'wppr-import' => $rating->rating_id
                );

                gdrtsm_stars_rating()->calculate($item, 'vote', $meta['vote'], $meta['max']);

                gdrts_db()->add_to_log($item->item_id, $rating->user_id, gdrtsm_stars_rating()->method(), $data, $meta);
            }
        }
    }

    private function _transfer_data($max = 5, $offset = 0) {
        $sql = "SELECT post_id as `id`, SUBSTR(meta_key, 9) as `key`, meta_value as `value` 
               FROM ".gdrts_db()->wpdb()->postmeta." WHERE meta_key IN ('ratings_users', 'ratings_score') 
               ORDER BY post_id ASC LIMIT ".$offset.', '.$this->page;
        $raw = gdrts_db()->run($sql);

        if (!empty($raw)) {
            $data = array();

            foreach ($raw as $r) {
                $id = intval($r->id);
                $data[$id][$r->key] = $r->value;
            }

            foreach ($data as $post => $rating) {
                $post_type = get_post_type($post);

                if ($post_type) {
                    gdrtsm_stars_rating()->_load_settings_rule('posts', $post_type);

                    $args = array(
                        'entity' => 'posts', 
                        'name' => $post_type, 
                        'id' => $post
                    );

                    $item = gdrts_get_rating_item($args);
                    $item->prepare_save();

                    if ($item->get_meta('wppr-import', false) === false) {
                        $factor = gdrtsm_stars_rating()->get_rule('stars') / $max;

                        $votes = intval($item->get('stars-rating_votes', 0));
                        $sum = floatval($item->get('stars-rating_sum', 0));

                        $votes+= inval($rating['users']);
                        $sum+= inval($rating['score']) * $factor;

                        $_rating = round($sum / $votes, gdrts()->decimals());

                        $item->set('stars-rating_sum', $sum);
                        $item->set('stars-rating_max', gdrtsm_stars_rating()->get_rule('stars'));
                        $item->set('stars-rating_votes', $votes);
                        $item->set('stars-rating_rating', $_rating);
                        $item->set('wppr-import', true);

                        $item->save();
                    }
                }
            }
        }
    }
}
