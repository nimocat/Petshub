<?php

if (!defined('ABSPATH')) { exit; }

class gdrts_core_user_rating extends gdrts_user_rating {
    public function __construct($user_id) {
        parent::__construct($user_id);
    }

    public static function get_instance($user_id) {
        $obj = gdrts_cache()->get('user', $user_id);

        if ($obj === false) {
            $obj = new gdrts_core_user_rating($user_id);

            gdrts_cache()->add('user', $user_id, $obj);
        }

        return $obj;
    }
}

class gdrts_core_visitor_rating extends gdrts_user_rating {
    public function __construct($user_id) {
        parent::__construct(0);

        $this->log_ids = gdrts_read_cookies();
    }

    public static function get_instance() {
        $obj = gdrts_cache()->get('visitor', 0);

        if ($obj === false) {
            $obj = new gdrts_core_visitor_rating(0);

            gdrts_cache()->add('visitor', 0, $obj);
        }

        return $obj;
    }
}
