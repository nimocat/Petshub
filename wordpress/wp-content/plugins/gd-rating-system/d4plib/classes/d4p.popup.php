<?php

/*
Name:    d4pLib - Features - Animated Popup
Version: v2.2.1
Author:  Milan Petrovic
Email:   milan@gdragon.info
Website: https://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2017 Milan Petrovic (email: milan@gdragon.info)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!class_exists('d4p_object_animated_popup')) {
    class d4p_object_animated_popup {
        public $url = '';

        public function __construct($url) {
            $this->url = $url;
        }

        public function enqueue_files() {
            wp_enqueue_script('jquery');

            wp_enqueue_style('d4p-animated-popup', $this->url.'animated-popup/popup.css', array(), D4P_VERSION);
            wp_enqueue_script('d4p-animated-popup', $this->url.'animated-popup/popup.js', array('jquery'), D4P_VERSION);
        }

        public function effects() {
            return array(
                'none' => 'No effect',
                'fade' => 'Fade',
                'scale' => 'Scale',
                'zoomfade' => 'Zoom and Fade',
                'slideinright' => 'Slide In From Right',
                'slideinleft' => 'Slide In From Left',
                'slideintop' => 'Slide In From Top',
                'slideinbottom' => 'Slide In From Bottom',
                'newspaper' => 'Newspaper',
                'fallcenter' => 'Fall Center',
                'fallleft' => 'Fall Left',
                'fallright' => 'Fall Right',
                'fliphorleft' => 'Flip Horizontal Left',
                'fliphorright' => 'Flip Horizontal Right',
                'flipvertop' => 'Flip Vertical Top',
                'flipverbottom' => 'Flip Vertical Bottom',
                'flipsign' => 'Flip Sign',
                'flipsignfront' => 'Flip Sign Front',
                'rotatebottom' => 'Rotate Bottom',
                'rotatetop' => 'Rotate Top',
                'rotateleft' => 'Rotate Left',
                'rotateright' => 'Rotate Right',
                'slit' => 'Slit Vertical',
                'slithor' => 'Slit Horizontal',
                'bounce' => 'Bounce',
                'roll' => 'Roll'
            );
        }
    }
}
