<?php

/*
Name:    d4pLib_Access
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

if (!function_exists('d4p_is_ip_in_range')) {
    function d4p_is_ip_in_range($ip, $ip_range) {
        return d4p_core_ip::is_ipv4_in_range($ip, $range);
    }
}

if (!function_exists('d4p_is_ip6_in_range')) {
    function d4p_is_ip6_in_range($ip, $ip_range) {
        return d4p_core_ip::is_ipv6_in_range($ip, $range);
    }
}

if (!function_exists('d4p_is_cloudflare_ip')) {
    function d4p_is_cloudflare_ip($ip) {
        return d4p_core_ip::is_cloudflare_ip($ip);
    }
}

if (!function_exists('d4p_visitor_ip')) {
    function d4p_visitor_ip($no_local_or_protected = false) {
        return d4p_core_ip::get_visitor_ip($no_local_or_protected);
    }
}

if (!function_exists('d4p_validate_ip')) {
    function d4p_validate_ip($ip, $no_local_or_protected = false) {
        return d4p_core_ip::validate_ip($ip, $no_local_or_protected);
    }
}

if (!function_exists('d4p_ip_cleanup')) {
    function d4p_ip_cleanup($ip) {
        return d4p_core_ip::cleanup_ip($ip);
    }
}

if (!function_exists('d4p_server_ip')) {
    function d4p_server_ip() {
        return d4p_core_ip::get_server_ip();
    }
}

if (!function_exists('d4p_current_url_path')) {
    function d4p_current_url_path() {
        $uri = $_SERVER['REQUEST_URI'];
        return parse_url($uri, PHP_URL_PATH);
    }
}

if (!function_exists('d4p_current_url_request')) {
    function d4p_current_url_request() {
        $pathinfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        list($pathinfo) = explode('?', $pathinfo);
        $pathinfo = str_replace('%', '%25', $pathinfo);

        $request = explode('?', $_SERVER['REQUEST_URI']);
        $req_uri = $request[0];
        $req_query = isset($request[1]) ? $request[1] : false;
        $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
        $home_path_regex = sprintf('|^%s|i', preg_quote($home_path, '|'));

        $req_uri = str_replace($pathinfo, '', $req_uri);
        $req_uri = trim($req_uri, '/');
        $req_uri = preg_replace($home_path_regex, '', $req_uri);
        $req_uri = trim($req_uri, '/');

        $url_request = $req_uri;

        if ($req_query !== false) {
            $url_request.= '?'.$req_query;
        }

        return $url_request;
    }
}

if (!function_exists('d4p_current_url')) {
    function d4p_current_url($use_wp = true) {
        if ($use_wp) {
            return home_url(d4p_current_url_request());
        } else {
            $s = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
            $protocol = d4p_strleft(strtolower($_SERVER['SERVER_PROTOCOL']), '/').$s;
            $port = $_SERVER['SERVER_PORT'] == '80' || $_SERVER['SERVER_PORT'] == '443' ? '' : ':'.$_SERVER['SERVER_PORT'];

            return $protocol.'://'.$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
        }
    }
}

if (!function_exists('d4p_get_domain_name')) {
    function d4p_get_domain_name($url) {
        $host = parse_url($url, PHP_URL_HOST);
        $part = explode('.', $host);

        $build = array_slice($part, -2, 2);

        return $build[0].'.'.$build[1];
    }
}
