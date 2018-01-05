<?php

/*
Name:    d4pLib - Classes - Debug / Static
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

if (!class_exists('d4p_debug')) {
    class d4p_debug {
        static function error_log($log, $title = '') {
            if (true === WP_DEBUG) {
                $print = '';

                if ($title != '') {
                    $print.= '<<<< '.$title."\r\n";
                }

                $print.= print_r($log, true);
                
                error_log($print);
            }
        }

        static function print_r($obj, $pre = true, $title = '', $before = '', $after = '') {
            echo $before.D4P_EOL;

            if ($pre) {
                echo '<pre style="padding: 5px; font-size: 12px; background: #fff; border: 1px solid #000; color: #000;">';

                if ($title != '') {
                    echo '&gt;&gt;&gt;&gt;&nbsp;<strong>'.$title.'</strong>&nbsp;&lt;&lt;&lt;&lt;&lt;<br/><br/>';
                }
            } else {
                if ($title != '') {
                    echo "<<<< ".$title." >>>>\r\n\r\n";
                }
            }

            print_r($obj);

            if ($pre) {
                echo '</pre>';
            }

            echo $after.D4P_EOL;
        }
        
        static function print_hooks($filter = false, $destination = 'print') {
            global $wp_filter;

            $skip = empty($filter);

            foreach ($wp_filter as $tag => $hook) {
                if ($skip || false !== strpos($tag, $filter)) {
                    self::print_hook($tag, $hook, $destination);
                }
            }
        }

        static function print_hook($tag, $hook, $destination = 'print') {
            ksort($hook);

            $print = array();

            foreach ($hook as $priority => $functions) {
                foreach ($functions as $function) {
                    $line = $priority.' : ';

                    $callback = $function['function'];

                    if (is_string($callback)) {
                        $line.= $callback;
                    } elseif (is_a($callback, 'Closure')) {
                        $closure = new ReflectionFunction($callback);
                        $line.= 'closure from '.$closure->getFileName(). '::'.$closure->getStartLine();
                    } elseif (is_string($callback[0])) {
                        $line.= $callback[0].'::'.$callback[1];
                    } elseif (is_object( $callback[0])) {
                        $line.= get_class($callback[0]).'->'.$callback[1];
                    }

                    if ($function['accepted_args'] == 1) {
                        $line.= " ({$function['accepted_args']})";
                    }

                    $print[] = $line;
                }
            }

            if ($destination == 'log') {
                self::error_log($print, $tag);
            } else {
                self::print_r($print, true, $tag);
            }
        }
    }
}
