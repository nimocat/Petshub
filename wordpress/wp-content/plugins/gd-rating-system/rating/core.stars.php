<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_stars {
    public function __construct() { }

    public function stars($args = array()) {
        $defaults = array(
            'stars' => 5,
            'rating' => 0,
            'style_type' => 'font',
            'style_name' => 'star',
            'style_size' => 32,
            'style_class' => '',
            'font_color_empty' => '#dddddd',
            'font_color_current' => '#dd0000',
            'title' => ''
        );

        $atts = wp_parse_args($args, $defaults);

        $render = '';

        if ($atts['style_type'] == 'image') {
            $render = $this->_render_stars_image($atts);
        } else {
            $render = $this->_render_stars_font($atts);
        }

        return $render;
    }

    public function badge($args = array()) {
        $defaults = array(
            'rating' => '0.0',
            'style_type' => 'font',
            'style_name' => 'star',
            'style_size' => 140,
            'style_color' => '#ffff00',
            'font_size' => 32
        );

        $atts = wp_parse_args($args, $defaults);

        $render = '';

        if ($atts['style_type'] == 'image') {
            $render = $this->_render_badge_image($atts);
        } else {
            $render = $this->_render_badge_font($atts);
        }

        return $render;
    }

    private function _render_stars_image($atts = array()) {
        $current = 100 * ($atts['rating'] / $atts['stars']);

        $render = '<div title="'.$atts['title'].'" class="'.$this->_render_classes($atts).'" style="width: '.($atts['stars'] * $atts['style_size']).'px; height: '.$atts['style_size'].'px;">';
            $render.= '<span class="gdrts-stars-empty" style="background-size: '.$atts['style_size'].'px;">';
                $render.= '<span class="gdrts-stars-current" style="width: '.$current.'%; background-size: '.$atts['style_size'].'px;"></span>';
            $render.= '</span>';
        $render.= '</div>';

        return $render;
    }

    private function _render_stars_font($atts = array()) {
        $current = 100 * ($atts['rating'] / $atts['stars']);
        $thechar = gdrts()->get_font_star_char($atts['style_type'], $atts['style_name']);

        $render = '<div title="'.$atts['title'].'" data-max="'.$atts['stars'].'" data-type="'.$atts['style_type'].'" data-name="'.$atts['style_name'].'" data-char="'.$thechar.'" class="'.$this->_render_classes($atts).'" style="height: '.$atts['style_size'].'px;">';
            $render.= '<span class="gdrts-stars-empty" style="color: '.$atts['font_color_empty'].'; font-size: '.$atts['style_size'].'px; line-height: '.$atts['style_size'].'px;">';
                $render.= '<span class="gdrts-stars-current" style="color: '.$atts['font_color_current'].'; width: '.$current.'%"></span>';
            $render.= '</span>';
        $render.= '</div>';

        return $render;
    }

    private function _render_classes($atts = array()) {
        $list = array(
            'gdrts-custom-stars-block',
            'gdrts-'.$atts['style_type'].'-'.$atts['style_name'],
            'gdrts-stars-length-'.$atts['stars']
        );

        if ($atts['style_type'] == 'image') {
            $list[] = 'gdrts-with-image';
        } else {
            $list[] = 'gdrts-with-fonticon';
            $list[] = 'gdrts-fonticon-'.$atts['style_type'];
        }

        if (!empty($atts['style_class'])) {
            $list = array_merge($list, (array)$atts['style_class']);
        }

        return join(' ', $list);
    }

    private function _render_badge_image($atts = array()) {
        $render = '<div style="width: '.$atts['style_size'].'px; height: '.$atts['style_size'].'px;" class="gdrts-badge-wrapper gdrts-badge-image gdrts-image-'.$atts['style_name'].'">';
        $render.= '<div style="background-size: '.$atts['style_size'].'px auto; width: '.$atts['style_size'].'px; height: '.$atts['style_size'].'px;" class="gdrts-badge-icon">'.'</div>';
        $render.= '<div class="gdrts-badge-text" style="line-height: '.$atts['style_size'].'px;font-size: '.$atts['font_size'].'px">'.$atts['rating'].'</div>';
        $render.= '</div>';

        return $render;
    }

    private function _render_badge_font($atts = array()) {
        $render = '<div style="width: '.$atts['style_size'].'px; height: '.$atts['style_size'].'px;" class="gdrts-badge-wrapper gdrts-badge-font gdrts-fonticon-'.$atts['style_type'].'">';
        $render.= '<div class="gdrts-badge-icon" style="color: '.$atts['style_color'].'; font-size: '.$atts['style_size'].'px; line-height: '.$atts['style_size'].'px;">'.gdrts()->get_font_star_char($atts['style_type'], $atts['style_name']).'</div>';
        $render.= '<div class="gdrts-badge-text" style="line-height: '.$atts['style_size'].'px;font-size: '.$atts['font_size'].'px">'.$atts['rating'].'</div>';
        $render.= '</div>';

        return $render;
    }
}

function gdrts_render_custom_stars_block($atts = array()) {
    $_stars = new gdrts_core_stars();

    return $_stars->stars($atts);
}

function gdrts_render_custom_star_badge($atts = array()) {
    $_stars = new gdrts_core_stars();

    return $_stars->badge($atts);
}
