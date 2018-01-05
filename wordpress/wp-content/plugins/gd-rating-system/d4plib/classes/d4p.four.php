<?php

/*
Name:    d4pLib - Classes - Four Core
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

if (!class_exists('d4p_core_four')) {
    class d4p_core_four {
        public $plugins = array(
            'dev4press-updater/dev4press-updater.php' => '11',
            'gd-security-toolbox/gd-security-toolbox.php' => '50',
            'gd-rating-system/gd-rating-system.php' => '48',
            'gd-seo-toolbox/gd-seo-toolbox.php' => '49',
            'gd-topic-polls/gd-topic-polls.php' => '59',
            'gd-topic-prefix/gd-topic-prefix.php' => '56',
            'gd-social-sharing/gd-social-sharing.php' => '52',
            'gd-bbpress-toolbox/gd-bbpress-toolbox.php' => '29',
            'gd-swift-navigator/gd-swift-navigator.php' => '38',
            'gd-crumbs-navigator/gd-crumbs-navigator.php' => '39',
            'gd-knowledge-base/gd-knowledge-base.php' => '46',
            'gd-clever-widgets/gd-clever-widgets.php' => '36',
            'gd-webfonts-toolbox/gd-webfonts-toolbox.php' => '37',
            'gd-press-tools/gd-press-tools.php' => '1',
            'gd-products-center/gd-products-center.php' => '17',
            'gd-taxonomies-tools/gd-taxonomies-tools.php' => '4',
            'gd-content-tools/gd-content-tools.php' => '51'
        );

        public $base = 'https://www.dev4press.com/service/core/{endpoint}/';
        public $data = array(
            'type' => 'plugin',
            'name' => '',
            'version' => '',
            'build' => 0,
            'lic' => 'ND4PL',
            'api' => 'ND4UR',
            'multisite' => 'N',
            'bbpress' => 'N',
            'source' => 'ad',
            'plugins' => array(),
            'ip' => '',
            'url' => ''
        );

        public $ad = null;

        public function __construct($type, $name, $version, $build, $lic = '') {
            $this->data['type'] = $type;
            $this->data['name'] = $name;
            $this->data['version'] = $version;
            $this->data['build'] = $build;

            if ($lic != '') {
                $this->data['lic'] = $lic;
            }

            $this->data['multisite'] = is_multisite() ? 'Y' : 'N';
            $this->data['bbpress'] = $this->_has_bbpress() ? 'Y' : 'N';
            $this->data['url'] = parse_url(get_bloginfo('url'), PHP_URL_HOST);
            $this->data['ip'] = d4p_server_ip();

            if (function_exists('d4pupd_get_api_key')) {
                $this->data['api'] = d4pupd_get_api_key();
            }

            add_filter('http_request_args', array($this, 'request_headers'), 2000, 2);

            require_once(ABSPATH.'wp-admin/includes/plugin.php');
            $plugins = get_plugins();

            foreach (array_keys($plugins) as $plugin) {
                if (isset($this->plugins[$plugin])) {
                    $this->data['plugins'][] = $this->plugins[$plugin];
                }
            }

            $this->data['plugins'] = join(',', $this->data['plugins']);
        }

        public function request_headers($r, $url) {
            if (strpos($url, 'www.dev4press.com/service/core') !== false) {
                $r['headers']['X-Dev4press-Checkin-Product'] = $this->_header_product();
                $r['headers']['X-Dev4press-Checkin-Origin'] = $this->_header_origin();
                $r['headers']['X-Dev4press-Checkin-Validate'] = $this->_header_validate();
            }

            return $r;
        }

        public function ad() {
            $this->data['source'] = 'ad';
            $key = 'devad_'.$this->data['name'];

            $data = get_site_transient($key);

            if ($data === false) {
                $url = $this->_request_url('recommend');

                $data = $this->_request($url);

                if ($data !== false) {
                    set_site_transient($key, $data, DAY_IN_SECONDS * 7);
                }
            }

            $this->ad = $data;
        }

        public function ad_render($panel = 'update') {
            $render = '';

            if ($this->ad !== false && $this->ad->plugins !== false && is_array($this->ad->plugins) && !empty($this->ad->plugins)) {
                $plugins = (array)$this->ad->plugins;

                if ($plugins) {
                    $render = '<div id="dev4press-recommend">';
                    $render.= '<h5>Recommended Plugins</h5>';

                    foreach ($plugins as $plugin) {
                        $render.= '<div class="dev4press-recommend-plugin" style="border-color: '.$plugin->color_dark.'; background-color: '.$plugin->color.'">';
                            $render.= '<h6 style="background-color: '.$plugin->color_dark.';"><a target="_blank" href="'.$this->_url($plugin->url_home, $panel).'">'.$plugin->name.'</a></h6>';
                            $render.= '<div class="dev4press-plugin-inner">';
                                $render.= '<div class="dev4press-plugin-thumb">';
                                    $render.= '<a target="_blank" href="'.$this->_url($plugin->url_home, $panel).'"><img src="'.$plugin->url_icon.'" alt="'.$plugin->name.'" /></a>';
                                $render.= '</div>';
                                $render.= '<em>'.$plugin->description.'</em>';
                                $render.= '<div class="dev4press-plugin-links">';
                                    $render.= '<a target="_blank" class="button-primary dev4press-buynow" href="'.$this->_url($plugin->url_buy, $panel).'">Buy Now</a>';

                                    if ($plugin->url_micro != '') {
                                        $render.= '<a target="_blank" class="button-secondary" href="'.$this->_url($plugin->url_micro, $panel).'">Home Page</a>';
                                    }

                                    if ($plugin->url_demo != '') {
                                        $render.= '<a target="_blank" class="button-secondary" href="'.$this->_url($plugin->url_demo, $panel).'">View Demo</a>';
                                    }
                                $render.= '</div>';
                            $render.= '</div>';
                        $render.= '</div>';
                    }

                    $render.= '</div>';
                }
            }

            return $render;
        }

        protected function _url($url, $campaign = 'install') {
            $url = add_query_arg('utm_source', $this->data['url'], $url);
            $url = add_query_arg('utm_medium', 'web-'.$this->data['name'], $url);
            $url = add_query_arg('utm_campaign', $campaign.'-panel', $url);

            return $url;
        }

        protected function _request_url($endpoint) {
            return str_replace('{endpoint}', $endpoint, $this->base);
        }

        protected function _header_validate() {
            return $this->data['api'].'::'.
                   $this->data['lic'];
        }

        protected function _header_origin() {
            return $this->data['url'].'::'.
                   $this->data['multisite'];
        }

        protected function _header_product() {
            return $this->data['type'].'::'.
                   $this->data['name'].'::'.
                   $this->data['version'].'::'.
                   $this->data['build'];
        }

        protected function _has_bbpress() {
            if (function_exists('bbp_version')) {
                $version = bbp_get_version();
                $version = intval(substr(str_replace('.', '', $version), 0, 2));

                return $version > 22;
            } else {
                return false;
        }
        }

        private function _request($url) {
            $raw = $this->_post($url, $this->data);

            if (!is_wp_error($raw)) {
                return json_decode($raw['body']);
            }

            return false;
        }

        private function _post($url, $data) {
            global $wp_version;

            $options = array(
                'timeout' => 15, 
                'body' => json_encode($data), 
                'method' => 'POST',
                'user-agent' => 'WordPress/'.$wp_version.'; '.get_bloginfo('url')
            );

            return wp_remote_post($url, $options);
        }
    }
}
