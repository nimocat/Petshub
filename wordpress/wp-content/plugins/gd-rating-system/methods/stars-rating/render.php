<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_render_single_stars_rating extends gdrts_method_render_single {
    public function owner() {
        return gdrtsm_stars_rating();
    }

    public function classes($extra = '', $echo = true) {
        $classes = apply_filters('gdrts_stars_rating_loop_single_classes', $this->get_classes($extra));

        $render = join(' ', $classes);

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    public function text($atts = array(), $echo = true) {
        $defaults = array(
            'before' => '',
            'after' => '',
            'show_votes' => true,
            'show_max' => false,
            'tpl_rating' => null,
            'tpl_votes' => null
        );

        $atts = wp_parse_args($atts, $defaults);

        if (is_null($atts['tpl_rating'])) {
            $atts['tpl_rating'] = __("Rating", "gd-rating-system").': <strong>%1$s</strong>';

            if ($atts['show_max']) {
                $atts['tpl_rating'].= '/%2$s';
            }

            $atts['tpl_rating'].= '.';
        }

        $render = sprintf($atts['tpl_rating'], $this->owner()->value('rating', false), $this->owner()->value('stars', false));

        if ($atts['show_votes']) {
            $_votes = $this->owner()->value('votes', false);

            if (is_null($atts['tpl_votes'])) {
                $render.= ' '.sprintf(_n("From %s vote.", "From %s votes.", $_votes, "gd-rating-system"), $_votes);
            } else {
                $render.= ' '.sprintf(_n($atts['tpl_votes']['singular'], $atts['tpl_votes']['plural'], $_votes, "gd-rating-system"), $_votes);
            }
        }

        $render = $atts['before'].$render.$atts['after'];

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    public function user_vote($atts = array(), $echo = true) {
        $defaults = array(
            'show_time' => true,
            'show_remaining' => true,
            'tpl_voted' => null,
            'tpl_not_voted' => null,
            'tpl_remaining' => null
        );

        $atts = wp_parse_args($atts, $defaults);

        $render = '';

        if ($this->owner()->user()->has_voted()) {
            $vote = $this->owner()->user()->previous_vote();

            $tpl_voted = '';

            if (is_null($atts['tpl_voted'])) {
                switch ($this->owner()->calc('vote')) {
                    case 'single':
                        $tpl_voted = __("You have already voted", "gd-rating-system").' <strong>%1$s</strong>';
                        break;
                    case 'revote':
                        $tpl_voted = __("You voted", "gd-rating-system").' <strong>%1$s</strong>';
                        break;
                    case 'multi':
                        $tpl_voted = __("Your previous vote was", "gd-rating-system").' <strong>%1$s</strong>';
                        break;
                }

                if ($atts['show_time']) {
                    $tpl_voted.= ', %2$s '.__("ago", "gd-rating-system");
                }

                $tpl_voted.= '.';
            } else {
                switch ($this->owner()->calc('vote')) {
                    case 'single':
                        $tpl_voted = $atts['tpl_voted']['single'];
                        break;
                    case 'revote':
                        $tpl_voted = $atts['tpl_voted']['revote'];
                        break;
                    case 'multi':
                        $tpl_voted = $atts['tpl_voted']['multi'];
                        break;
                }
            }

            $render.= sprintf($tpl_voted, $vote->meta->vote, human_time_diff(strtotime($vote->logged)));

            if ($atts['show_remaining']) {
                $limit = $this->owner()->calc('vote_limit');

                switch ($this->owner()->calc('vote')) {
                    case 'revote':
                        $revotes = $this->owner()->user()->count_revotes();

                        if ($revotes < $limit) {
                            $remaining = $limit - $revotes;

                            if (!is_null($atts['tpl_remaining']) && is_array($atts['tpl_remaining']['revote'])) {
                                $render.= ' '.sprintf(_n($atts['tpl_remaining']['revote']['singular'], $atts['tpl_remaining']['revote']['plural'], $remaining, "gd-rating-system"), $remaining);
                            } else {
                                $render.= ' '.sprintf(_n("You can change your vote %s more time.", "You can change your vote %s more times.", $remaining, "gd-rating-system"), $remaining);
                            }
                        }
                        break;
                    case 'multi':
                        $votes = $this->owner()->user()->count_votes();

                        if ($votes < $limit) {
                            $remaining = $limit - $votes;

                            if (!is_null($atts['tpl_remaining']) && is_array($atts['tpl_remaining']['multi'])) {
                                $render.= ' '.sprintf(_n($atts['tpl_remaining']['multi']['singular'], $atts['tpl_remaining']['multi']['plural'], $remaining, "gd-rating-system"), $remaining);
                            } else {
                                $render.= ' '.sprintf(_n("You can vote %s more time.", "You can vote %s more times.", $remaining, "gd-rating-system"), $remaining);
                            }
                        }
                        break;
                }
            }
        } else {
            if (is_null($atts['tpl_not_voted'])) {
                $render = __("You have not voted yet.", "gd-rating-system");
            } else {
                $render = $atts['tpl_not_voted'];
            }
        }

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    public function stars($atts = array(), $echo = true) {
        $defaults = array(
            'input_name' => '',
            'input_value' => false,
            'passive_mode' => false,
            'allow_rating' => true,
            'show_rating' => 'rating'
        );

        $atts = wp_parse_args($atts, $defaults);

        $render = '';

        if ($this->owner()->args('style_type') == 'image') {
            $render = $this->_render_stars_image($atts);
        } else {
            $render = $this->_render_stars_font($atts);
        }

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    public function badge($atts = array(), $echo = true) {
        $defaults = array(
            'style_type' => $this->owner()->args('style_type'),
            'style_name' => $this->owner()->args('style_name'),
            'style_size' => 140,
            'style_color' => '#ffff00',
            'font_size' => 32
        );

        $atts = wp_parse_args($atts, $defaults);

        $atts['rating'] = $this->owner()->value('rating', false);

        $render = gdrts_render_custom_star_badge($atts);

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    public function symbol($atts = array(), $echo = true) {
        return $this->badge($atts, $echo);
    }

    public function distribution($atts = array(), $echo = true) {
        $defaults = array(
            'type' => $this->owner()->args('distribution'),
            'class' => 'gdrts-distribution-wrapper'
        );

        $atts = wp_parse_args($atts, $defaults);

        $distribution = gdrts_prepare_votes_distribution($this->owner()->calc('distribution'), $this->owner()->calc('max'), $atts['type']);

        $total = 0;
        foreach ($distribution as $d) {
            $total+= $d['votes'];
        }

        $render = '<div class="'.$atts['class'].'"><ul>';
        
        foreach ($distribution as $d) {
            $percentage = number_format((100 / $total) * $d['votes'], 2);

            $render.= '<li class="gdrts-clearfix">';

            $render.= '<div class="gdrts-distribution-stars">';
            $render.= '<strong>'.$d['stars'].'</strong> '._n("star", "stars", $d['stars'], "gd-rating-system");
            $render.= '</div>';
            
            $render.= '<div class="gdrts-distribution-line">';
            $render.= '<div title="'.$percentage.'%" class="gdrts-distribution-line-fill" style="width: '.$percentage.'%">'.$percentage.'%</div>';
            $render.= '</div>';

            $render.= '<div class="gdrts-distribution-votes">';
            $render.= $d['votes'].'<span> '._n("vote", "votes", $d['votes'], "gd-rating-system").'</span>';
            $render.= '</div>';

            $render.= '</li>';
        }

        $render.= '</ul></div>';

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    protected function _render_classes($active = true, $extras_classes = array()) {
        $list = array(
            'gdrts-rating-element',
            'gdrts-stars-rating',
            'gdrts-block-stars',
            $active ? 'gdrts-state-active' : 'gdrts-state-inactive',
            'gdrts-'.$this->owner()->args('style_type').'-'.$this->owner()->args('style_name'),
            'gdrts-stars-length-'.$this->owner()->calc('stars')
        );

        if ($this->owner()->args('style_type') == 'image') {
            $list[] = 'gdrts-with-image';
        } else {
            $list[] = 'gdrts-with-fonticon';
            $list[] = 'gdrts-fonticon-'.$this->owner()->args('style_type');
        }

        if (gdrts_single()->is_loop_save()) {
            $list[] = 'gdrts-loop-saving';
        }

        if (!empty($extras_classes)) {
            $list = array_merge($list, (array)$extras_classes);
        }

        return join(' ', $list);
    }

    protected function _render_stars_font($atts) {
        $active = $atts['allow_rating'] && $this->owner()->calc('allowed') && $this->owner()->calc('open');
        $current = $atts['show_rating'] == 'rating' ? $this->owner()->calc('current') : ($atts['show_rating'] == 'own' ? $this->owner()->calc('current_own') : 0);
        $input = $atts['input_value'] ? $this->owner()->calc('rating_own') : 0;
        $input_current = $atts['input_value'] ? $this->owner()->calc('current_own') : 0;
        $extra_classes = $atts['passive_mode'] ? 'gdrts-passive-rating' : '';

        $render = '<div class="'.$this->_render_classes($active, $extra_classes).'" style="height: '.$this->owner()->args('style_size').'px;">';
            if ($active) {
                $render.= $this->_accessibility_block($input);
            }

            $render.= '<input type="hidden" value="'.$input.'" name="'.$atts['input_name'].'" />';
            $render.= '<span aria-hidden="true" class="gdrts-stars-empty" style="color: '.$this->owner()->args('font_color_empty').'; font-size: '.$this->owner()->args('style_size').'px; line-height: '.$this->owner()->args('style_size').'px;">';
                $render.= '<span class="gdrts-stars-active" style="color: '.$this->owner()->args('font_color_active').'; width: '.$input_current.'%"></span>';
                $render.= '<span class="gdrts-stars-current" style="color: '.$this->owner()->args('font_color_current').'; width: '.$current.'%"></span>';
            $render.= '</span>';
        $render.= '</div>';

        return $render;
    }

    protected function _render_stars_image($atts) {
        $active = $atts['allow_rating'] && $this->owner()->calc('allowed') && $this->owner()->calc('open');
        $current = $atts['show_rating'] == 'rating' ? $this->owner()->calc('current') : ($atts['show_rating'] == 'own' ? $this->owner()->calc('current_own') : 0);
        $input = $atts['input_value'] ? $this->owner()->calc('rating_own') : 0;
        $input_current = $atts['input_value'] ? $this->owner()->calc('current_own') : 0;
        $extra_classes = $atts['passive_mode'] ? 'gdrts-passive-rating' : '';

        $render = '<div class="'.$this->_render_classes($active, $extra_classes).'" style="width: '.($this->owner()->calc('stars') * $this->owner()->args('style_size')).'px; height: '.$this->owner()->args('style_size').'px;">';
            if ($active) {
                $render.= $this->_accessibility_block($input);
            }

            $render.= '<input type="hidden" value="'.$input.'" name="'.$atts['input_name'].'" />';
            $render.= '<span aria-hidden="true" class="gdrts-stars-empty" style="background-size: '.$this->owner()->args('style_size').'px;">';
                $render.= '<span class="gdrts-stars-active" style="width: '.$input_current.'%; background-size: '.$this->owner()->args('style_size').'px;"></span>';
                $render.= '<span class="gdrts-stars-current" style="width: '.$current.'%; background-size: '.$this->owner()->args('style_size').'px;"></span>';
            $render.= '</span>';
        $render.= '</div>';

        return $render;
    }

    protected function _accessibility_block($input) {
        $render = '<div class="gdrts-sr-only"><label class="gdrts-sr-label">'.__("Rate this item", "gd-rating-system").':';
        $render.= '<select class="gdrts-sr-rating">';

        $stars = $this->owner()->calc('stars') * 100;
        $resolution = $this->owner()->calc('resolution');

        for ($i = $resolution; $i <= $stars; $i+= $resolution) {
            $value = number_format($i / 100, 2);
            $render.= '<option'.($input * 100 == $i ? ' selected="selected"' : '').' value="'.$value.'">'.$value.'</option>';
        }

        $render.= '</select></label>';
        $render.= '<button class="gdrts-sr-button">'.__("Submit Rating", "gd-rating-system").'</button></div>';

        return $render;
    }
}

class gdrts_render_list_stars_rating extends gdrts_method_render_list {
    public function owner() {
        return gdrtsm_stars_rating();
    }

    public function classes($extra = '', $echo = true) {
        $classes = apply_filters('gdrts_stars_rating_loop_list_classes', $this->get_classes($extra));

        $render = join(' ', $classes);

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    public function text($atts = array(), $echo = true) {
        $defaults = array(
            'before' => '',
            'after' => '',
            'show_votes' => true,
            'show_max' => false,
            'tpl_rating' => null,
            'tpl_votes' => null
        );

        $atts = wp_parse_args($atts, $defaults);

        if (is_null($atts['tpl_rating'])) {
            $atts['tpl_rating'] = __("Rating", "gd-rating-system").': <strong>%1$s</strong>';

            if ($atts['show_max']) {
                $atts['tpl_rating'].= '/%2$s';
            }

            $atts['tpl_rating'].= '.';
        }

        $render = sprintf($atts['tpl_rating'], $this->owner()->value('rating', false), $this->owner()->value('stars', false));

        if ($atts['show_votes']) {
            $_votes = $this->owner()->value('votes', false);

            if (is_null($atts['tpl_votes'])) {
                $render.= ' '.sprintf(_n("From %s vote.", "From %s votes.", $_votes, "gd-rating-system"), $_votes);
            } else {
                $render.= ' '.sprintf(_n($atts['tpl_votes']['singular'], $atts['tpl_votes']['plural'], $_votes, "gd-rating-system"), $_votes);
            }
        }

        $render = $atts['before'].$render.$atts['after'];

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    public function stars($atts = array(), $echo = true) {
        $defaults = array();

        $atts = wp_parse_args($atts, $defaults);

        $render = '';

        if ($this->owner()->args('style_type') == 'image') {
            $render = $this->_render_stars_image($atts);
        } else {
            $render = $this->_render_stars_font($atts);
        }

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    public function symbol($atts = array(), $echo = true) {
        $defaults = array(
            'style_type' => $this->owner()->args('style_type'),
            'style_name' => $this->owner()->args('style_name'),
            'style_size' => 64,
            'font_size' => 16
        );

        $atts = wp_parse_args($atts, $defaults);

        $render = '';

        if ($this->owner()->args('style_type') == 'image') {
            $render.= '<div style="width: '.$atts['style_size'].'px; height: '.$atts['style_size'].'px;" class="gdrts-symbol-wrapper gdrts-symbol-image gdrts-image-'.$atts['style_name'].'">';
            $render.= '<div style="background-size: '.$atts['style_size'].'px auto; width: '.$atts['style_size'].'px; height: '.$atts['style_size'].'px;" class="gdrts-symbol-icon">'.'</div>';
            $render.= '<div class="gdrts-symbol-text" style="line-height: '.$atts['style_size'].'px;font-size: '.$atts['font_size'].'px">'.$this->owner()->value('rating', false).'</div>';
            $render.= '</div>';
        } else {
            $render.= '<div class="gdrts-symbol-wrapper gdrts-symbol-font">';
            $render.= '<div class="gdrts-symbol-icon">'.'</div>';
            $render.= '<div class="gdrts-symbol-text">'.'</div>';
            $render.= '</div>';
        }

        if ($echo) {
            echo $render;
        } else {
            return $render;
        }
    }

    protected function _render_classes($extras_classes = array()) {
        $list = array(
            'gdrts-rating-element',
            'gdrts-stars-rating',
            'gdrts-block-stars',
            'gdrts-state-inactive',
            'gdrts-'.$this->owner()->args('style_type').'-'.$this->owner()->args('style_name'),
            'gdrts-stars-length-'.$this->owner()->calc('stars')
        );

        if ($this->owner()->args('style_type') == 'image') {
            $list[] = 'gdrts-with-image';
        } else {
            $list[] = 'gdrts-with-fonticon';
            $list[] = 'gdrts-fonticon-'.$this->owner()->args('style_type');
        }

        if (!empty($extras_classes)) {
            $list = array_merge($list, $extras_classes);
        }

        return join(' ', $list);
    }

    protected function _render_stars_font($atts) {
        $current = $this->owner()->calc('current');

        $render = '<div class="'.$this->_render_classes().'" style="height: '.$this->owner()->args('style_size').'px;">';
            $render.= '<span aria-hidden="true" class="gdrts-stars-empty" style="color: '.$this->owner()->args('font_color_empty').'; font-size: '.$this->owner()->args('style_size').'px; line-height: '.$this->owner()->args('style_size').'px;">';
                $render.= '<span class="gdrts-stars-current" style="color: '.$this->owner()->args('font_color_current').'; width: '.$current.'%"></span>';
            $render.= '</span>';
        $render.= '</div>';

        return $render;
    }

    protected function _render_stars_image($atts) {
        $current = $this->owner()->calc('current');

        $render = '<div class="'.$this->_render_classes().'" style="width: '.($this->owner()->calc('stars') * $this->owner()->args('style_size')).'px; height: '.$this->owner()->args('style_size').'px;">';
            $render.= '<span aria-hidden="true" class="gdrts-stars-empty" style="background-size: '.$this->owner()->args('style_size').'px;">';
                $render.= '<span class="gdrts-stars-current" style="width: '.$current.'%; background-size: '.$this->owner()->args('style_size').'px;"></span>';
            $render.= '</span>';
        $render.= '</div>';

        return $render;
    }
}
