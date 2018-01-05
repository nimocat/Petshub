<h3><?php _e("Additional database tables", "gd-rating-system"); ?></h3>
<?php

    require_once(GDRTS_PATH.'core/admin/install.php');

    $list_db = gdrts_install_database();

    if (!empty($list_db)) {
        echo '<h4>'.__("Database Upgrade Notices", "gd-rating-system").'</h4>';
        echo join('<br/>', $list_db);
    }

    echo '<h4>'.__("Database Tables Check", "gd-rating-system").'</h4>';
    $check = gdrts_check_database();

    $msg = array();
    foreach ($check as $table => $data) {
        if ($data['status'] == 'error') {
            $_proceed = false;
            $_error_db = true;
            $msg[] = '<span class="gdpc-error">['.__("ERROR", "gd-rating-system").'] - <strong>'.$table.'</strong>: '.$data['msg'].'</span>';
        } else {
            $msg[] = '<span class="gdpc-ok">['.__("OK", "gd-rating-system").'] - <strong>'.$table.'</strong></span>';
        }
    }

    echo join('<br/>', $msg);

