<?php

if (!defined('ABSPATH')) { exit; }

function gdrts_is_rss() {
    return apply_filters('gdrts_feed_is_rss', is_feed());
}

function gdrts_is_amp() {
    $is = false;

    if (function_exists('is_amp_endpoint')) {
        $is = is_amp_endpoint();
    }

    return apply_filters('gdrts_feed_is_amp', $is);
}

function gdrts_is_fia() {
    $is = false;

    if (d4p_is_plugin_active('fb-instant-articles/facebook-instant-articles.php')) {
        if (defined('INSTANT_ARTICLES_SLUG') && is_feed(INSTANT_ARTICLES_SLUG)) {
            $is = true;
        }
    }

    return apply_filters('gdrts_feed_is_fia', $is);
}

function gdrts_is_anf() {
    $is = false;

    return apply_filters('gdrts_feed_is_anf', $is);
}
