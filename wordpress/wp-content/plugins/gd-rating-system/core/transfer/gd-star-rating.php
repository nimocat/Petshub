<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_transfer_gd_star_rating {
    public $page = 500;
    public $post_types = array();

    public function __construct() {
        $this->page = gdrts_settings()->get('step_transfer');
    }

    public function round_to_page($number) {
        return ceil(intval($number) / $this->page) * $this->page;
    }

    public function db_tables_exist() {
        $tables = array(
            gdrts_db()->wpdb()->prefix.'gdsr_data_article',
            gdrts_db()->wpdb()->prefix.'gdsr_data_comment',
            gdrts_db()->wpdb()->prefix.'gdsr_votes_log'
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

    public function count_stars_rating($method = 'log') {
        $count = 0;

        switch ($method) {
            case 'data':
                $sql = "SELECT COUNT(*) FROM ".gdrts_db()->wpdb()->prefix."gdsr_data_article WHERE user_voters + visitor_voters > 0";
                $count+= $this->round_to_page(gdrts_db()->get_var($sql));

                $sql = "SELECT COUNT(*) FROM ".gdrts_db()->wpdb()->prefix."gdsr_data_comment WHERE user_voters + visitor_voters > 0";
                $count+= $this->round_to_page(gdrts_db()->get_var($sql));
                break;
            case 'log':
                $sql = "SELECT COUNT(*) FROM ".gdrts_db()->wpdb()->prefix."gdsr_votes_log r 
                WHERE r.vote_type in ('article', 'comment') AND r.record_id NOT IN 
                (SELECT CAST(meta_value as UNSIGNED) FROM ".gdrts_db()->logmeta." WHERE meta_key = 'gdsr-rating-import')";
                $count+= $this->round_to_page(gdrts_db()->get_var($sql));
                break;
        }

        return $count;
    }

    public function transfer_stars_rating($max = 10, $method = 'log', $offset = 0) {
        switch ($method) {
            case 'data':
                $this->_transfer_stars_rating_data_posts($max, $offset);
                $this->_transfer_stars_rating_data_comments($max, $offset);
                break;
            case 'log':
                $this->_transfer_stars_rating_log($max, $offset);
                break;
        }
    }

    public function _transfer_stars_rating_data_comments($max = 10, $offset = 0) {
        $sql = "SELECT r.comment_id, r.last_voted, r.user_voters + r.visitor_voters as `votes`, r.user_votes + r.visitor_votes as `sum`
                FROM ".gdrts_db()->wpdb()->prefix."gdsr_data_comment r WHERE r.user_voters + r.visitor_voters > 0
                ORDER BY r.post_id ASC LIMIT ".$offset.', '.$this->page;
        $raw = gdrts_db()->run($sql);

        if (!empty($raw)) {
            foreach ($raw as $rating) {
                gdrtsm_stars_rating()->_load_settings_rule('comments', 'comment');

                $args = array(
                    'entity' => 'comments', 
                    'name' => 'comment', 
                    'id' => $rating->comment_id
                );

                $item = gdrts_get_rating_item($args);
                $item->prepare_save();

                if ($item->get_meta('gdsr-rating-import', false) === false) {
                    $factor = gdrtsm_stars_rating()->get_rule('stars') / $max;

                    $votes = intval($item->get('stars-rating_votes', 0));
                    $sum = floatval($item->get('stars-rating_sum', 0));

                    $votes+= $rating->votes;
                    $sum+= $rating->sum * $factor;

                    $rate = round($sum / $votes, gdrts()->decimals());

                    $item->set('stars-rating_sum', $sum);
                    $item->set('stars-rating_max', gdrtsm_stars_rating()->get_rule('stars'));
                    $item->set('stars-rating_votes', $votes);
                    $item->set('stars-rating_rating', $rate);
                    $item->set('gdsr-rating-import', true);

                    $item->save();
                }
            }
        }
    }

    public function _transfer_stars_rating_data_posts($max = 10, $offset = 0) {
        $sql = "SELECT r.post_id, r.last_voted, r.user_voters + r.visitor_voters as votes, r.user_votes + r.visitor_votes as sum
                FROM ".gdrts_db()->wpdb()->prefix."gdsr_data_article r WHERE r.user_voters + r.visitor_voters > 0
                ORDER BY r.post_id ASC LIMIT ".$offset.', '.$this->page;
        $raw = gdrts_db()->run($sql);

        if (!empty($raw)) {
            foreach ($raw as $rating) {
                $post_type = get_post_type($rating->post_id);

                if ($post_type) {
                    gdrtsm_stars_rating()->_load_settings_rule('posts', $post_type);

                    $args = array(
                        'entity' => 'posts', 
                        'name' => $post_type, 
                        'id' => $rating->post_id
                    );

                    $item = gdrts_get_rating_item($args);

                    if ($item->get_meta('gdsr-rating-import', false) === false) {
                        $item->prepare_save();

                        $factor = gdrtsm_stars_rating()->get_rule('stars') / $max;

                        $votes = intval($item->get('stars-rating_votes', 0));
                        $sum = floatval($item->get('stars-rating_sum', 0));

                        $votes+= $rating->votes;
                        $sum+= $rating->sum * $factor;

                        $rate = round($sum / $votes, gdrts()->decimals());

                        $item->set('stars-rating_sum', $sum);
                        $item->set('stars-rating_max', gdrtsm_stars_rating()->get_rule('stars'));
                        $item->set('stars-rating_votes', $votes);
                        $item->set('stars-rating_rating', $rate);
                        $item->set('gdsr-rating-import', true);

                        $item->save();
                    }
                }
            }
        }
    }

    public function _transfer_stars_rating_log($max = 10, $offset = 0) {
        $sql = "SELECT r.record_id, r.id, r.vote_type, r.user_id, r.vote, r.voted, r.ip, r.comment_id
               FROM ".gdrts_db()->wpdb()->prefix."gdsr_votes_log r 
               WHERE r.vote_type in ('article', 'comment') AND r.record_id NOT IN 
               (SELECT CAST(meta_value as UNSIGNED) FROM ".gdrts_db()->logmeta." WHERE meta_key = 'gdsr-rating-import')
               ORDER BY r.record_id ASC LIMIT ".$this->page;
        $raw = gdrts_db()->run($sql);

        if (!empty($raw)) {
            $this->_load_post_types($raw);

            foreach ($raw as $rating) {
                $args = array();

                if ($rating->vote_type == 'article') {
                    $post_type = isset($this->post_types[$rating->id]) ? $this->post_types[$rating->id] : 'post';

                    gdrtsm_stars_rating()->_load_settings_rule('posts', $post_type);

                    $args = array(
                        'entity' => 'posts', 
                        'name' => $post_type, 
                        'id' => $rating->id
                    );
                } else {
                    gdrtsm_stars_rating()->_load_settings_rule('comments', 'comment');

                    $args = array(
                        'entity' => 'comments', 
                        'name' => 'comment', 
                        'id' => $rating->id
                    );
                }

                $item = gdrts_get_rating_item($args);

                $factor = gdrtsm_stars_rating()->get_rule('stars') / $max;

                $data = array(
                    'action' => 'vote',
                    'ip' => $rating->ip,
                    'logged' => $rating->voted
                );

                $meta = array(
                    'vote' => $rating->vote * $factor,
                    'max' => gdrtsm_stars_rating()->get_rule('stars'),
                    'gdsr-rating-import' => $rating->record_id
                );

                if ($rating->comment_id > 0) {
                    $meta['comment_id'] = $rating->comment_id;
                }

                gdrtsm_stars_rating()->calculate($item, 'vote', $meta['vote'], $meta['max']);

                gdrts_db()->add_to_log($item->item_id, $rating->user_id, gdrtsm_stars_rating()->method(), $data, $meta);
            }
        }
    }

    public function _load_post_types($input) {
        $this->post_types = array();
        $_list = array();

        foreach ($input as $row) {
            if ($row->vote_type == 'article' && !in_array($row->id, $_list)) {
                $_list[] = absint($row->id);
            }
        }

        if (!empty($_list)) {
            $sql = "SELECT ID, post_type FROM ".gdrts_db()->wpdb()->posts." WHERE ID IN (".join(',', $_list).");";
            $raw = gdrts_db()->run($sql);

            foreach ($raw as $r) {
                $this->post_types[$r->ID] = $r->post_type;
            }
        }
    }
}
