<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_info {
    public $code = 'gd-rating-system';

    public $version = '2.3';
    public $codename = 'Phoebe';
    public $build = 795;
    public $edition = 'lite';
    public $status = 'stable';
    public $updated = '2017.12.06';
    public $url = 'https://plugins.dev4press.com/gd-rating-system/';
    public $author_name = 'Milan Petrovic';
    public $author_url = 'https://www.dev4press.com/';
    public $released = '2015.12.25';

    public $install = false;
    public $update = false;
    public $previous = 0;

    function __construct() { }

    public function to_array() {
        return (array)$this;
    }
}
