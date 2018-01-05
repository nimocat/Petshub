<?php

if (!defined('ABSPATH')) { exit; }

include(GDRTS_PATH.'forms/shared/top.php');

?>

<div class="d4p-content-right d4p-content-full">
    <form method="get" action="">
        <input type="hidden" name="page" value="gd-rating-system-ratings" />
        <input type="hidden" value="getback" name="gdrts_handler" />

        <?php

        require_once(GDRTS_PATH.'core/grids/ratings.php');

        $_grid = new gdrts_grid_ratings();
        $_grid->prepare_items();
        $_grid->views();
        $_grid->display();

        ?>
    </form>
</div>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
include(GDRTS_PATH.'forms/dialogs/ratings.php');
