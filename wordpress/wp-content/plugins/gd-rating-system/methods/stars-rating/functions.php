<?php

if (!defined('ABSPATH')) { exit; }

function gdrts_prepare_votes_distribution($distribution, $max, $type = 'normalized') {
    $list = array();

    if ($type == 'exact') {
        foreach ($distribution as $key => $value) {
            if ($value > 0) {
                $list[] = array(
                    'stars' => floatval($key),
                    'votes' => $value
                );
            }
        }
    } else if ($type == 'normalized') {
        for ($i = $max; $i > 0; $i--) {
            $list[$i] = array(
                'stars' => $i,
                'votes' => 0
            );
        }

        foreach ($distribution as $key => $value) {
            $index = ceil(floatval($key));

            $list[$index]['votes']+= $value;
        }
    }

    return array_values($list);
}
