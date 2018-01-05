<?php

if (!defined('ABSPATH')) { exit; }

if (!function_exists('gdrts_prepare_list_of_users')) {
    function gdrts_prepare_list_of_users($users, $avatar = true, $avatar_size = 24) {
        $items = array();

        foreach ($users as $user) {
            $item = array(
                'id' => $user->user_id,
                'name' => $user->display_name,
                'email' => $user->user_email,
                'url' => $user->user_url
            );

            if (empty($item['url'])) {
                $item['url'] = get_author_posts_url($user->user_id);
            }

            if ($avatar) {
                $item['avatar'] = get_avatar($user->user_email, $avatar_size);
            }

            $items[$user->user_id] = $item;
        }

        return apply_filters('gdrts_prepare_list_of_users', $items, $user, $avatar, $avatar_size);
    }
}

if (!function_exists('gdrts_render_list_of_users')) {
    function gdrts_render_list_of_users($input, $args = array()) {
        $defaults = array('echo' => true, 'type' => 'plain', 'sep' => ', ', 'show_url' => true, 
            'show_name' => true, 'show_avatar' => true, 'avatar_size' => 16, 
            'class_user' => 'gdrts_user_single', 'class_wrapper' => 'gdrts_users_lists', 
            'show_more' => __(" and %s more...", "gd-rating-system"));

        $args = wp_parse_args($args, $defaults);

        $items = array();
        $users = gdrts_prepare_list_of_users($input['list'], $args['show_avatar'], $args['avatar_size']);

        foreach ($users as $user) {
            $tag = $args['type'] == 'list' ? 'li' : 'span';

            $item = '<'.$tag.' class="'.$args['class_user'].'">';

            if ($args['show_url']) {
                $item.= '<a href="'.$user['url'].'">';
            }

            if ($args['show_avatar']) {
                $item.= $user['avatar'];
            }

            if ($args['show_name']) {
                $item.= '<span>'.$user['name'].'</span>';
            }

            if ($args['show_url']) {
                $item.= '</a>';
            }

            $item.= '</'.$tag.'>';

            $items[] = $item;
        }

        $wrapper = $args['type'] == 'list' ? 'ul' : 'div';

        $render = '<div class="'.$args['class_wrapper'].'">';

        if ($args['type'] == 'list') {
            $render.= '<ul>';
        }

        $render.= join($args['sep'], $items);

        if ($args['type'] == 'list') {
            $render.= '<ul>';
        }

        if ($input['total'] > $input['count']) {
            $render.= '<span class="gdrts-show-more">'.sprintf($args['show_more'], $input['total'] - $input['count']).'</span>';
        }

        $render.= '</div>';

        if ($args['echo']) {
            echo $render;
        } else {
            return $render;
        }
    }
}
