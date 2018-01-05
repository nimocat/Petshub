<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_user_stars_rating extends gdrts_method_user {
    public $method = 'stars-rating';

    public function is_open($vote, $limit = 0) {
        $open = true;
        $log = $this->log();

        $votes = isset($log['vote']) ? count($log['vote']) : 0;
        $revotes = isset($log['revote']) ? count($log['revote']) : 0;

        switch ($vote) {
            case 'revote':
                $open = $limit == 0 || $limit > $revotes;
                break;
            case 'multi':
                $open = $limit == 0 || $limit > $votes + $revotes;
                break;
            default:
            case 'single':
                $open = $votes + $revotes == 0;
                break;
        }

        return apply_filters('gdrts_stars_rating_loop_is_open', $open, $vote, $limit);
    }
}
