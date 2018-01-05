<h3><?php _e("Plugin Settings", "gd-rating-system"); ?></h3>
<?php

$_annonymous_verify = gdrts_settings()->get('annonymous_verify');
if ($_annonymous_verify == 'ip_andr_cookie') {
    gdrts_settings()->set('annonymous_verify', 'ip_and_cookie', 'settings', true);
}

_e("Plugin settings are updated.", "gd-rating-system");
