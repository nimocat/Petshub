<?php

if (!defined('ABSPATH')) { exit; }

/** @global wpdb $wpdb */
function gdrts_list_database_tables() {
    global $wpdb;

    return array(
        $wpdb->prefix.'gdrts_itemmeta' => 4,
        $wpdb->prefix.'gdrts_logmeta' => 4,
        $wpdb->prefix.'gdrts_items' => 5,
        $wpdb->prefix.'gdrts_logs' => 10,
        $wpdb->prefix.'gdrts_cache' => 6
    );
}

/** @global wpdb $wpdb */
function gdrts_install_database() {
    global $wpdb;

    $charset_collate = '';

    if (!empty($wpdb->charset)) {
        $charset_collate = "default CHARACTER SET $wpdb->charset";
    }

    if (!empty($wpdb->collate)) {
        $charset_collate.= " COLLATE $wpdb->collate";
    }

    $tables = array(
        'items' => $wpdb->prefix.'gdrts_items',
        'itemmeta' => $wpdb->prefix.'gdrts_itemmeta',
        'logs' => $wpdb->prefix.'gdrts_logs',
        'logmeta' => $wpdb->prefix.'gdrts_logmeta',
        'cache' => $wpdb->prefix.'gdrts_cache'
    );

    $query = "CREATE TABLE ".$tables['itemmeta']." (
meta_id bigint(20) unsigned NOT NULL auto_increment,
item_id bigint(20) unsigned NOT NULL default '0',
meta_key varchar(255) NULL default NULL,
meta_value longtext NULL,
PRIMARY KEY  (meta_id),
KEY item_id (item_id),
KEY meta_key (meta_key)) ".$charset_collate.";

CREATE TABLE ".$tables['logmeta']." (
meta_id bigint(20) unsigned NOT NULL auto_increment,
log_id bigint(20) unsigned NOT NULL default '0',
meta_key varchar(255) NULL default NULL,
meta_value longtext NULL,
PRIMARY KEY  (meta_id),
KEY log_id (log_id),
KEY meta_key (meta_key)
) ".$charset_collate.";

CREATE TABLE ".$tables['items']." (
item_id bigint(20) unsigned NOT NULL auto_increment,
entity varchar(32) NOT NULL default 'posts' COMMENT 'posts,comments,users,terms',
name varchar(64) NOT NULL default 'post',
id bigint(20) unsigned NOT NULL default '0',
latest datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'gmt',
PRIMARY KEY  (item_id),
UNIQUE KEY entity_name_id (entity,name,id),
KEY entity (entity),
KEY name (name),
KEY id (id)
) ".$charset_collate.";

CREATE TABLE ".$tables['logs']." (
log_id bigint(20) NOT NULL auto_increment,
item_id bigint(20) NOT NULL default '0' COMMENT 'from gdrs_items table',
user_id bigint(20) NOT NULL default '0',
ref_id bigint(20) NOT NULL default '0' COMMENT 'reference id for revotes from this same table',
action varchar(128) NOT NULL default 'vote' COMMENT 'vote,revote,queue',
status varchar(128) NOT NULL default 'active' COMMENT 'active,replaced',
method varchar(128) NOT NULL default 'stars-rating',
logged datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'gmt',
ip varchar(128) NOT NULL default '',
series varchar(128) NOT NULL default '' COMMENT 'set belonging to method',
PRIMARY KEY  (log_id),
KEY item_id (item_id),
KEY user_id (user_id),
KEY action (action),
KEY ref_id (ref_id),
KEY status (status),
KEY method (method),
KEY ip (ip),
KEY series (series)
) ".$charset_collate.";

CREATE TABLE ".$tables['cache']." (
cache_id bigint(20) unsigned NOT NULL auto_increment,
module varchar(32) NOT NULL default '' COMMENT 'aggregate,period',
method varchar(128) NOT NULL default '',
access varchar(32) NOT NULL default '',
store longtext NOT NULL,
expire int(10) unsigned NOT NULL default '0',
PRIMARY KEY  (cache_id),
KEY module (module),
KEY method (method)
) ".$charset_collate.";";

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');

    return dbDelta($query);
}

/** @global wpdb $wpdb */
function gdrts_check_database() {
    global $wpdb;

    $result = array();
    $tables = gdrts_list_database_tables();

    foreach ($tables as $table => $count) {
        if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
            $columns = $wpdb->get_results("SHOW COLUMNS FROM $table");

            if ($count != count($columns)) {
                $result[$table] = array("status" => "error", "msg" => __("Some columns are missing.", "gd-rating-system"));
            } else {
                $result[$table] = array("status" => "ok");
            }
        } else {
            $result[$table] = array("status" => "error", "msg" => __("Table missing.", "gd-rating-system"));
        }
    }

    return $result;
}

/** @global wpdb $wpdb */
function gdrts_truncate_database_tables() {
    global $wpdb;

    $tables = array_keys(gdrts_list_database_tables());

    foreach ($tables as $table) {
        $wpdb->query("TRUNCATE TABLE ".$table);
    }
}

/** @global wpdb $wpdb */
function gdrts_drop_database_tables() {
    global $wpdb;

    $tables = array_keys(gdrts_list_database_tables());

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS ".$table);
    }
}
