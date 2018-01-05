<?php

if (!defined('ABSPATH')) { exit; }

include(GDRTS_PATH.'forms/shared/top.php');

?>

<div class="d4p-content-left">
    <div class="d4p-panel-scroller d4p-scroll-active">
        <div class="d4p-panel-title">
            <i aria-hidden="true" class="fa fa-thumb-tack"></i>
            <h3><?php _e("Rating Types", "gd-rating-system"); ?></h3>
        </div>
        <div class="d4p-panel-info">
            <?php _e("Manage rating entities and types.", "gd-rating-system"); ?>
        </div>
        <div class="d4p-panel-buttons">
            <a class="button-primary" href="<?php echo admin_url('admin.php?page=gd-rating-system-types&action=new'); ?>"><?php _e("Add New Entity", "gd-rating-system"); ?></a>
        </div>
    </div>
</div>

<div class="d4p-content-right">
    <form method="get" action="">
        <input type="hidden" name="page" value="gd-rating-system-types" />
        <input type="hidden" value="getback" name="gdrts_handler" />

        <?php

        require_once(GDRTS_PATH.'core/grids/types.php');

        $_grid = new gdrts_grid_types();
        $_grid->prepare_items();
        $_grid->display();

        ?>
    </form>
</div>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
include(GDRTS_PATH.'forms/dialogs/types.php');
