<?php

/*
Name:    d4pLib - Features - Geo IP
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

if (!class_exists('d4p_core_geoip')) {
    class d4p_core_geoip {
        public $error_code = null;
        public $error_message = null;

        public $ip = '';
        public $country_code = '';
        public $country_name = '';
        public $region_code = '';
        public $region_name = '';
        public $city = '';
        public $zip_code = '';
        public $time_zone = '';
        public $latitude = '';
        public $longitude = '';

        public $continent_code = '';

        public function __construct($input) {
            foreach ($input as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }

        public function is_error() {
            return is_array($this->error);
        }
    }
}

if (!class_exists('d4p_base_geoip')) {
    abstract class d4p_base_geoip {
        public $_active = true;
        public $_localhost = false;
        public $_error = '';

        public $_url = '';

        public $_ip = '';
        public $_expire = 14;

        public $_cache_hit = false;

        public $_user_agent = 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:56.0) Gecko/20100101 Firefox/56.0';

        /** @var d4p_core_geoip */
        public $_data = null;

        public function __construct($ip, $expire = 14) {
            $this->_ip = $ip;
            $this->_expire = intval($expire);

            if (in_array($ip, array('127.0.0.1', '::1'))) {
                $this->_active = false;
                $this->_localhost = true;
                $this->_error = __("Localhost IP's can't be geolocated.", "d4plib");
            } else {
                $this->init();
            }
        }

        public function __get($name) {
            if (isset($this->_data->$name)) {
                return $this->_data->$name;
            } else {
                return '';
            }
        }

        public function active() {
            return $this->_active;
        }

        public function error() {
            if (!$this->_active) {
                return $this->_error;
            } else {
                return false;
            }
        }

        public function location() {
            $location = '';

            if ($this->active() && isset($this->_data->country_name) && !empty($this->_data->country_name)) {
                $location.= $this->_data->country_name;

                if (isset($this->_data->city) && !empty($this->_data->city)) {
                    $location.= ', '.$this->_data->city;
                }
            }

            return $location;
        }

        public function flag($not_found = 'image') {
            if ($this->_active) {
                if ($this->country_code != '') {
                    $info = ucwords(strtolower($this->country_name));
                    if ($this->city != '') {
                        $info.= ', '.$this->city;
                    }

                    return '<img src="'.D4PLIB_URL.'resources/flags/blank.gif" class="flag flag-'.strtolower($this->country_code).'" title="'.$info.'" alt="" />';
                }
            } else if ($this->_localhost) {
                return '<img src="'.D4PLIB_URL.'resources/flags/blank.gif" class="flag flag-localhost" title="'.__("Localhost", "d4plib").'" alt="" />';
            }

            if ($not_found == 'image') {
                return '<img src="'.D4PLIB_URL.'resources/flags/blank.gif" class="flag" title="'.__("IP can't be geolocated.", "d4plib").'" alt="" />';
            } else if ($not_found == 'space') {
                return '';
            }
        }

        protected function init() {
            $code = array();
            $get = false;

            $key = 'd4pfg_'.$this->_ip;

            if ($this->_expire > 0) {
                $code = get_site_transient($key);

                if ($code === false || is_null($code) || empty($code)) {
                    $get = true;
                } else {
                    $this->_cache_hit = true;
                }
            } else {
                $get = true;
            }

            if ($get) {
                $raw = wp_remote_get($this->url(), array(
                    'httpversion' => '1.1',
                    'user-agent' => $this->_user_agent
                ));

                if (!is_wp_error($raw) && $raw['response']['code'] == '200') {
                    $code = $this->process($raw['body']);

                    if ($this->_expire > 0) {
                        set_site_transient($key, $code, $this->_expire * DAY_IN_SECONDS);
                    }
                } else {
                    $code = array(
                        'error_code' => $raw['response']['code'], 
                        'error_message' => $raw['response']['message']);
                }
            }

            $this->_data = new d4p_core_geoip($code);
        }

        abstract protected function url();

        abstract protected function process($body);
    }
}

if (!class_exists('d4p_freegeoip_geoip')) {
    class d4p_freegeoip_geoip extends d4p_base_geoip {
        public $_url = 'https://freegeoip.net/json/';

        public static function instance($ip = '', $expire = 14) {
            static $freegeoip_ips = array();

            if ($ip == '') {
                $ip = d4p_visitor_ip();
            }

            if (!isset($freegeoip_ips[$ip])) {
                $freegeoip_ips[$ip] = new d4p_freegeoip_geoip($ip, $expire);
            }

            return $freegeoip_ips[$ip];
        }

        protected function url() {
            return $this->_url.$this->_ip;
        }

        protected function process($body) {
            return (array)json_decode($body);
        }
    }
}

if (!class_exists('d4p_geoplugin_geoip')) {
    class d4p_geoplugin_geoip extends d4p_base_geoip {
        public $_url = 'http://www.geoplugin.net/json.gp?ip=';

        public static function instance($ip = '', $expire = 14) {
            static $geoplugin_ips = array();

            if ($ip == '') {
                $ip = d4p_visitor_ip();
            }

            if (!isset($geoplugin_ips[$ip])) {
                $geoplugin_ips[$ip] = new d4p_geoplugin_geoip($ip, $expire);
            }

            return $geoplugin_ips[$ip];
        }

        protected function url() {
            return $this->_url.$this->_ip;
        }

        protected function process($body) {
            $convert = array(
                'ip' => 'ip',
                'countryCode' => 'country_code',
                'countryName' => 'country_name',
                'regionCode' => 'region_code',
                'regionName' => 'region_name',
                'city' => 'city',
                'latitude' => 'latitude',
                'longitude' => 'longitude',
                'continentCode' => 'continent_code'
            );

            $raw = (array)json_decode($body);

            $code = array();

            foreach ($raw as $key => $value) {
                $ck = substr($key, 10);

                if (isset($convert[$ck])) {
                    $real = $convert[$ck];
                    $code[$real] = $value;
                }
            }

            return $code;
        }
    }
}

if (!function_exists('d4p_geoip')) {
    function d4p_geoip($ip = '', $expire = 14, $engine = 'freegeoip') {
        if ($engine == 'geoplugin') {
            return d4p_geoplugin_geoip::instance($ip, $expire);
        } else {
            return d4p_freegeoip_geoip::instance($ip, $expire);
        }
    }
}
