<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_admin_templates {
    public static function scan_for_templates() {
        $paths = array(
            GDRTS_PATH.'templates',
            TEMPLATEPATH,
            TEMPLATEPATH.'/gdrts',
        );

        if (STYLESHEETPATH != TEMPLATEPATH) {
            $paths[] = STYLESHEETPATH;
            $paths[] = STYLESHEETPATH.'/gdrts';
        }

        $paths = apply_filters('gdrts_templates_storage_paths', $paths);

        $templates = array();

        foreach ($paths as $path) {
            $path_tpls = gdrts_admin_templates::templates_from_folder($path);

            if (!empty($path_tpls) && is_array($path_tpls)) {
                foreach ($path_tpls as $method => $types) {
                    foreach ($types as $type => $tpls) {
                        foreach ($tpls as $key => $file) {
                            $templates[$method][$type][$key] = $file;
                        }
                    }
                }
            }
        }

        return $templates;
    }

    public static function templates_from_folder($path) {
        $templates = array();

        $files = d4p_scan_dir($path, 'files', array('php'));

        foreach ($files as $file) {
            $template = gdrts_admin_templates::template_from_file($path, $file);

            if ($template !== false) {
                $templates[$template[0]][$template[1]][$template[2]] = $template[3]; 
            }
        }

        return $templates;
    }

    public static function template_from_file($path, $file) {
        $_file = substr($file, 0, -4);

        if (substr($_file, 0, 7) == 'gdrts--') {
            $name = substr($_file, 7);
            $parts = explode('--', $name, 4);

            if (count($parts) > 2) {
                $method = $parts[0];
                $type = $parts[1];
                $template = $parts[2];
                $label = ucwords(str_replace('-', ' ', $template));

                $full_file = file(trailingslashit($path).$file);
                $line = $full_file[0];

                if (preg_match("/\/\/ GDRTS Template:(.+?)\/\//mi", $line, $match)) {
                    $label = trim($match[1]);
                }

                return array($method, $type, $template, $label);
            }
        }

        return false;
    }
}
