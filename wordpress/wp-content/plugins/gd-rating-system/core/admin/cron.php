<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_cron {
    public static function recalculate_max_changed() {
        foreach (gdrts()->get_entities() as $entity => $obj) {
            foreach (array_keys($obj['types']) as $type) {
                $object = array(
                    'entity' => $entity,
                    'name' => $type,
                    'stars-rating_max' => gdrts_settings()->get('stars-rating_stars', 'methods')
                );

                $ava_rating = gdrts_settings()->get($entity.'_stars-rating_rule_active', 'items');

                if (!is_null($ava_rating) && $ava_rating) {
                    $object['stars-rating_max'] = intval(gdrts_settings()->get($entity.'_stars-rating_stars', 'items'));
                }

                $ava_rating = gdrts_settings()->get($entity.'.'.$type.'_stars-rating_rule_active', 'items');

                if (!is_null($ava_rating) && $ava_rating) {
                    $object['stars-rating_max'] = intval(gdrts_settings()->get($entity.'.'.$type.'_stars-rating_stars', 'items'));
                }

                gdrts_admin_cron::recalculate_max_changed_single($object);
            }
        }
    }

    public static function recalculate_max_changed_single($obj) {
        $sql = "select i.item_id as id, m.meta_value as oldmax from ".gdrts_db()->items." i 
                inner join ".gdrts_db()->itemmeta." m on m.item_id = i.item_id and m.meta_key = 'stars-rating_max'
                where m.meta_value != ".$obj['stars-rating_max'];

        $items = gdrts_db()->run($sql);

        foreach ($items as $item) {
            $id = $item->id;
            $old_max = intval($item->oldmax);
            $factor = $obj['stars-rating_max'] / $old_max;

            $upd = "update ".gdrts_db()->itemmeta." set meta_value = ".$obj['stars-rating_max']." where meta_key = 'stars-rating_max' and item_id = ".$id;
            gdrts_db()->query($upd);

            $upd = "update ".gdrts_db()->itemmeta." set meta_value = round(meta_value * ".$factor.", 2) where meta_key in ('stars-rating_rating', 'stars-rating_sum') and item_id = ".$id;
            gdrts_db()->query($upd);
        }
    }

    public static function recalculate_statistics() {
        $results = array();

        foreach (gdrts()->get_entities() as $entity => $obj) {
            $results[$entity] = array();

            foreach (array_keys($obj['types']) as $type) {
                $results[$entity.'.'.$type] = array();
            }
        }

        $sql = "select i.entity, count(i.item_id) as items, sum(m1.meta_value) as votes, avg(m2.meta_value) as rating 
                from ".gdrts_db()->items." i 
                inner join ".gdrts_db()->itemmeta." m1 on i.item_id = m1.item_id and m1.meta_key = 'stars-rating_votes' 
                inner join ".gdrts_db()->itemmeta." m2 on i.item_id = m2.item_id and m2.meta_key = 'stars-rating_rating' 
                group by i.entity order by i.entity";

        $data = gdrts_db()->run($sql);

        foreach ($data as $row) {
            $results[$row->entity]['stars-rating'] = array(
                'items' => $row->items,
                'votes' => $row->votes,
                'rating' => $row->rating
            );
        }

        $sql = "select i.entity, i.name, count(i.item_id) as items, sum(m1.meta_value) as votes, avg(m2.meta_value) as rating 
                from ".gdrts_db()->items." i 
                inner join ".gdrts_db()->itemmeta." m1 on i.item_id = m1.item_id and m1.meta_key = 'stars-rating_votes' 
                inner join ".gdrts_db()->itemmeta." m2 on i.item_id = m2.item_id and m2.meta_key = 'stars-rating_rating' 
                group by i.entity, i.name order by i.entity, i.name";

        $data = gdrts_db()->run($sql);

        foreach ($data as $row) {
            $type = $row->entity.'.'.$row->name;

            $results[$type]['stars-rating'] = array(
                'items' => $row->items,
                'votes' => $row->votes,
                'rating' => $row->rating
            );
        }

        $valid_methods = array(
            'stars-rating' => array(
                'items', 'votes', 'rating'
            )
        );

        $old = gdrts_settings()->group_get('entities');

        foreach ($old as $key => &$data) {
            foreach ($valid_methods as $method => $valid_keys) {
                foreach ($valid_keys as $v) {
                    if (isset($results[$key][$method][$v])) {
                        $data[$method][$v] = $results[$key][$method][$v];
                    } else {
                        if (isset($data[$method][$v])) {
                            unset($data[$method][$v]);
                        }
                    }
                }
            }
        }

        gdrts_settings()->current['entities'] = $old;
        gdrts_settings()->save('entities');
    }
}
