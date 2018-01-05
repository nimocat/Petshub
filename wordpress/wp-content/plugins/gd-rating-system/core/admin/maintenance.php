<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_maintenance {
    public static function delete_rating_item($item_id) {
        $item_id = (array)$item_id;

        $sql = "DELETE i, im, l, lm 
                FROM ".gdrts_db()->itemmeta." im 
                INNER JOIN ".gdrts_db()->items." i ON i.item_id = im.item_id 
                INNER JOIN ".gdrts_db()->logs." l ON l.item_id = i.item_id
                INNER JOIN ".gdrts_db()->logmeta." lm ON lm.log_id = l.log_id
                WHERE i.item_id in (".join(', ', $item_id).")";
        gdrts_db()->query($sql);

        $sql = "DELETE i, im 
                FROM ".gdrts_db()->itemmeta." im 
                INNER JOIN ".gdrts_db()->items." i ON i.item_id = im.item_id 
                WHERE i.item_id in (".join(', ', $item_id).")";
        gdrts_db()->query($sql);

        $sql = "DELETE FROM ".gdrts_db()->items." 
                WHERE item_id in (".join(', ', $item_id).")";
        gdrts_db()->query($sql);
    }

    public static function clear_rating_item_method($item_id, $method = '') {
        $item_id = (array)$item_id;

        $sql_items = "DELETE FROM ".gdrts_db()->itemmeta." WHERE item_id in (".join(', ', $item_id).")";
        $sql_logs = "DELETE l, m FROM ".gdrts_db()->logs." l INNER JOIN ".gdrts_db()->logmeta." m ON l.log_id = m.log_id WHERE l.item_id in (".join(', ', $item_id).")";

        if ($method != '') {
            $sql_items.= " AND meta_key LIKE '".$method."%'";
            $sql_logs.= " AND l.method = '".$method."'";
        }

        gdrts_db()->query($sql_items);
        gdrts_db()->query($sql_logs);
    }

    public static function clear_rating_item_method_limited($item_id, $method = '') {
        $item_id = (array)$item_id;

        $sql_items = "DELETE FROM ".gdrts_db()->itemmeta." WHERE item_id in (".join(', ', $item_id).")";

        if ($method != '') {
            $sql_items.= " AND meta_key LIKE '".$method."%'";
        }

        gdrts_db()->query($sql_items);
    }

    public static function delete_vote_log($log_id) {
        $row = gdrts_db()->get_log($log_id);

        if ($row) {
            if ($row->method != 'stars-review') {
                if ($row->status == 'active') {
                    switch ($row->method) {
                        case 'stars-rating':
                            gdrtsm_stars_rating()->remove_vote_by_log($row);
                            break;
                    }

                    gdrts_admin_maintenance::remove_vote_log($log_id);

                    if ($row->ref_id > 0) {
                        gdrts_db()->update(gdrts_db()->logs, array('status' => 'active'), array('log_id' => $row->ref_id));

                        $row = gdrts_db()->get_log($row->ref_id);

                        if ($row && $row->ref_id == 0) {
                            gdrts_db()->update(gdrts_db()->logs, array('action' => 'vote'), array('log_id' => $row->log_id));
                        }
                    }
                } else if ($row->status == 'replaced') {
                    gdrts_admin_maintenance::remove_vote_log($log_id);

                    gdrts_db()->update(gdrts_db()->logs, array('ref_id' => $row->ref_id), array('ref_id' => $log_id, 'method' => $row->method));
                }
            }
        }
    }

    public static function remove_vote_log($log_id) {
        $log_id = (array)$log_id;

        $sql = "DELETE l, lm 
                FROM ".gdrts_db()->logs." l
                INNER JOIN ".gdrts_db()->logmeta." lm ON lm.log_id = l.log_id
                WHERE l.log_id in (".join(', ', $log_id).")";
        gdrts_db()->query($sql);
    }

    public static function count_rating_objects() {
        return gdrts_db()->get_var("SELECT COUNT(*) FROM ".gdrts_db()->items);
    }

    public static function recalculate_rating_objects($offset, $limit, $settings = array()) {
        $objects = gdrts_db()->get_results("SELECT item_id FROM ".gdrts_db()->items." ORDER BY item_id ASC LIMIT ".$offset.", ".$limit);

        $results = array(
            'items' => 0,
            'processed' => 0,
            'saved' => 0,
            'cleared' => 0
        );

        foreach ($objects as $obj) {
            $item = gdrts_get_rating_item_by_id($obj->item_id);
            $results['items']++;

            foreach (array_keys($settings) as $method) {
                $results['processed']++;

                if ($method == 'stars-rating') {
                    $result = gdrts_admin_maintenance::recalculate_stars_rating($item, $settings[$method]);

                    if ($result) {
                        $results['saved']++;
                    } else {
                        $results['cleared']++;
                    }
                }
            }
        }
    }

    public static function recalculate_stars_rating($item, $settings) {
        gdrtsm_stars_rating()->_load_settings_rule($item->entity, $item->name);

        $sum = 0;
        $votes = 0;
        $rating = 0;
        $max = gdrtsm_stars_rating()->get_rule('stars');
        $distribution = gdrtsm_stars_rating()->distribution_array($max);

        $log = gdrts_db()->get_log_meta_filter(array("m.`meta_key` in ('max', 'vote')", "l.`status` = 'active'", "l.`method` = 'stars-rating'", "l.`item_id` = ".$item->item_id));
        $latest = gdrts_db()->get_log_latest_logged(array("l.`status` = 'active'", "l.`method` = 'stars-rating'", "l.`item_id` = ".$item->item_id));

        foreach ($log as $item_log) {
            if (isset($item_log['max']) && isset($item_log['vote'])) {
                $vmax = $item_log['max'];
                $vote = $item_log['vote'];
                $vote = $vote * ($max / $vmax);
                $sum+= $vote;
                $votes++;

                $dist = number_format(round($vote, 2), 2);

                if (!isset($distribution[$dist])) {
                    $distribution[$dist] = 0;
                }

                $distribution[$dist] = $distribution[$dist] + 1;
            }
        }

        if ($votes == 0) {
            gdrts_admin_maintenance::clear_rating_item_method_limited($item->item_id, 'stars-rating');

            return false;
        } else {
            krsort($distribution);

            $rating = round($sum / $votes, gdrts()->decimals());

            $item->prepare_save();

            if (in_array('rating', $settings)) {
                $item->set('stars-rating_sum', $sum);
                $item->set('stars-rating_max', $max);
                $item->set('stars-rating_votes', $votes);
                $item->set('stars-rating_latest', $latest);
                $item->set('stars-rating_rating', $rating);
            }

            if (in_array('distribution', $settings)) {
                $item->set('stars-rating_distribution', $distribution);
            }

            $item->save();

            return true;
        }
    }
}
