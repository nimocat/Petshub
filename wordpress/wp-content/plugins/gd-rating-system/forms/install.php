<?php

if (!defined('ABSPATH')) { exit; }

$_classes = array('d4p-wrap', 'wpv-'.GDRTS_WPV, 'd4p-page-install');

?>
<div class="<?php echo join(' ', $_classes); ?>">
    <div class="d4p-header">
        <div class="d4p-plugin">
            GD Rating System
        </div>
    </div>
    <div class="d4p-content">
        <div class="d4p-content-left">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-magic"></i>
                <h3><?php _e("Installation", "gd-rating-system"); ?></h3>
            </div>
            <div class="d4p-panel-info">
                <?php _e("Before you continue, make sure plugin installation was successful.", "gd-rating-system"); ?>
            </div>
        </div>
        <div class="d4p-content-right">
            <div class="d4p-update-info">
                <?php

                    include(GDRTS_PATH.'forms/setup/templates.php');
                    include(GDRTS_PATH.'forms/setup/database.php');
                    include(GDRTS_PATH.'forms/setup/cache.php');

                    gdrts_settings()->set('install', false, 'info');
                    gdrts_settings()->set('update', false, 'info', true);

                ?>

                <h3><?php _e("All Done", "gd-rating-system"); ?></h3>
                <?php

                    _e("Installation completed.", "gd-rating-system");

                ?>
                <br/><br/><a class="button-primary" href="<?php echo d4p_current_url(); ?>"><?php _e("Click here to continue.", "gd-rating-system"); ?></a>
            </div>
        </div>
    </div>
</div>