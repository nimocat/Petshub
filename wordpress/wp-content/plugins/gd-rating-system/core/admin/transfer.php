<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_transfer {
    public static function count_objects($settings) {
        $plugin = $settings['plugin'];

        switch ($plugin) {
            case 'gd-star-rating':
                return gdrts_admin_transfer::_count_gd_star_rating($settings);
            case 'kk-star-ratings':
                return gdrts_admin_transfer::_count_kk_star_ratings($settings);
            case 'wp-postratings':
                return gdrts_admin_transfer::_count_wp_postratings($settings);
            case 'yet-another-stars-rating':
                return gdrts_admin_transfer::_count_yet_another_stars_rating($settings);
        }

        return 'ERROR';
    }

    public static function transfer_objects($offset, $step, $settings) {
        $plugin = $settings['plugin'];

        switch ($plugin) {
            case 'gd-star-rating':
                return gdrts_admin_transfer::_transfer_gd_star_rating($offset, $step, $settings);
            case 'kk-star-ratings':
                return gdrts_admin_transfer::_transfer_kk_star_ratings($offset, $step, $settings);
            case 'wp-postratings':
                return gdrts_admin_transfer::_transfer_wp_postratings($offset, $step, $settings);
            case 'yet-another-stars-rating':
                return gdrts_admin_transfer::_transfer_yet_another_stars_rating($offset, $step, $settings);
        }

        return 'ERROR';
    }

    public static function _count_gd_star_rating($settings) {
        require_once(GDRTS_PATH.'core/transfer/gd-star-rating.php');

        $transfer = new gdrts_transfer_gd_star_rating();

        $count = 0;

        foreach ($settings['data'] as $obj) {
            $obj = wp_parse_args($obj, array('rating' => '', 'max' => 0, 'method' => 'log'));

            switch ($obj['rating']) {
                case 'stars-rating':
                    $count+= $transfer->count_stars_rating($obj['method']);
                    break;
                case 'stars-review':
                    $count+= $transfer->count_stars_review();
                    break;
                case 'thumbs-rating':
                    $count+= $transfer->count_thumbs_rating($obj['method']);
                    break;
            }
        }

        return $count;
    }

    public static function _count_kk_star_ratings($settings) {
        require_once(GDRTS_PATH.'core/transfer/kk-star-ratings.php');

        $transfer = new gdrts_transfer_kk_star_ratings();

        $count = 0;

        foreach ($settings['data'] as $obj) {
            $obj = wp_parse_args($obj, array('rating' => '', 'max' => 0));

            switch ($obj['rating']) {
                case 'stars-rating':
                    $count+= $transfer->count();
                    break;
            }
        }

        return $count;
    }

    public static function _count_wp_postratings($settings) {
        require_once(GDRTS_PATH.'core/transfer/wp-postratings.php');
        
        $transfer = new gdrts_transfer_wp_postratings();

        $count = 0;

        foreach ($settings['data'] as $obj) {
            $obj = wp_parse_args($obj, array('rating' => '', 'max' => 0, 'method' => 'log'));

            switch ($obj['rating']) {
                case 'stars-rating':
                    $count+= $transfer->count($obj['method']);
                    break;
            }
        }

        return $count;
    }

    public static function _count_yet_another_stars_rating($settings) {
        require_once(GDRTS_PATH.'core/transfer/yet-another-stars-rating.php');

        $transfer = new gdrts_transfer_yet_another_stars_rating();

        $count = 0;

        foreach ($settings['data'] as $obj) {
            $obj = wp_parse_args($obj, array('rating' => '', 'max' => 0, 'method' => 'log'));

            switch ($obj['rating']) {
                case 'stars-rating':
                    $count+= $transfer->count_stars_rating($obj['method']);
                    break;
                case 'stars-review':
                    $count+= $transfer->count_stars_review();
                    break;
            }
        }

        return $count;
    }

    public static function _transfer_gd_star_rating($offset, $step, $settings) {
        require_once(GDRTS_PATH.'core/transfer/gd-star-rating.php');

        $transfer = new gdrts_transfer_gd_star_rating();

        foreach ($settings['data'] as $obj) {
            $obj = wp_parse_args($obj, array('rating' => '', 'max' => 0, 'method' => 'log'));

            switch ($obj['rating']) {
                case 'stars-rating':
                    $transfer->transfer_stars_rating($obj['max'], $obj['method'], $offset);
                    break;
                case 'stars-review':
                    $transfer->transfer_stars_review($obj['max'], $offset);
                    break;
                case 'thumbs-rating':
                    $transfer->transfer_thumbs_rating($obj['method'], $offset);
                    break;
            }
        }
    }

    public static function _transfer_kk_star_ratings($offset, $step, $settings) {
        require_once(GDRTS_PATH.'core/transfer/kk-star-ratings.php');

        $transfer = new gdrts_transfer_kk_star_ratings();

        foreach ($settings['data'] as $obj) {
            $obj = wp_parse_args($obj, array('rating' => '', 'max' => 0));

            switch ($obj['rating']) {
                case 'stars-rating':
                    $transfer->transfer($obj['max'], $offset);
                    break;
            }
        }
    }

    public static function _transfer_wp_postratings($offset, $step, $settings) {
        require_once(GDRTS_PATH.'core/transfer/wp-postratings.php');

        $transfer = new gdrts_transfer_wp_postratings();

        foreach ($settings['data'] as $obj) {
            $obj = wp_parse_args($obj, array('rating' => '', 'max' => 0, 'method' => 'log'));

            switch ($obj['rating']) {
                case 'stars-rating':
                    $transfer->transfer($obj['max'], $obj['method'], $offset);
                    break;
            }
        }
    }

    public static function _transfer_yet_another_stars_rating($offset, $step, $settings) {
        require_once(GDRTS_PATH.'core/transfer/yet-another-stars-rating.php');

        $transfer = new gdrts_transfer_yet_another_stars_rating();

        foreach ($settings['data'] as $obj) {
            $obj = wp_parse_args($obj, array('rating' => '', 'max' => 0, 'method' => 'log'));

            switch ($obj['rating']) {
                case 'stars-rating':
                    $transfer->transfer_stars_rating($obj['max'], $obj['method'], $offset);
                    break;
                case 'stars-review':
                    $transfer->transfer_stars_review($obj['max'], $offset);
                    break;
            }
        }
    }
}
