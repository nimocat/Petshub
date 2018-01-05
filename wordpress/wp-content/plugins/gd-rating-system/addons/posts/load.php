<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_addon_posts extends gdrts_addon {
    public $prefix = 'posts';
    public $sorter = null;

    public function _load_admin() {
        require_once(GDRTS_PATH.'addons/posts/admin.php');
    }

    public function core() {
        if (is_admin()) return;

        require_once(GDRTS_PATH.'addons/posts/sort.php');

        $this->sorter = new gdrts_addon_posts_sorter();

        add_filter('the_content', array($this, 'content'));
    }

    public function content($content) {
        if (!is_front_page() && !is_home() && !is_posts_page() && is_main_query() && is_singular()) {
            global $post;

            $item = gdrts_get_rating_item_by_post($post);

            if ($item !== false) {
                $post_type = $post->post_type;
                $location = $item->get('posts-integration_location', 'default');
                $method = $item->get('posts-integration_method', 'default');

                if ($location == 'default') {
                    $location = $this->get($post_type.'_auto_embed_location');
                }

                if ($method == 'default') {
                    $method = $this->get($post_type.'_auto_embed_method');
                }

                $location = apply_filters('gdrts_posts_auto_embed_location', $location);
                $_method = apply_filters('gdrts_posts_auto_embed_method', $method);
                $_parts = explode('::', $_method, 2);
                $method = $_parts[0];
                $series = null;

                if (isset($_parts[1])) {
                    $series = $_parts[1];
                }

                if (gdrts_is_method_loaded($method)) {
                    if (!empty($location) && is_string($location) && in_array($location, array('top', 'bottom', 'both'))) {
                        $rating = gdrts_posts_render_rating(array(
                            'name' => $post_type, 
                            'id' => $post->ID, 
                            'method' => $method, 
                            'series' => $series
                        ));

                        if ($location == 'top' || $location == 'both') {
                            $content = $rating.$content;
                        }

                        if ($location == 'bottom' || $location == 'both') {
                            $content.= $rating;
                        }
                    }
                }
            }
        }

        return $content;
    }
}

global $_gdrts_addon_posts;
$_gdrts_addon_posts = new gdrts_addon_posts();

function gdrtsa_posts() {
    global $_gdrts_addon_posts;
    return $_gdrts_addon_posts;
}
