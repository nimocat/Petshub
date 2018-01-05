<?php

if (!defined('ABSPATH')) { exit; }

$panels = array(
    'index' => array(
        'title' => __("Transfer Data Index", "gd-rating-system"), 'icon' => 'exchange', 
        'info' => __("All transfer tools are split into several panels, and you access each starting from the right.", "gd-rating-system")),
    'gd-star-rating' => array(
        'title' => __("Import", "gd-rating-system").': GD Star Rating', 'icon' => 'cloud-download', 
        'button' => 'button', 'button_text' => __("Transfer", "gd-rating-system"),
        'info' => __("Import data from GD Star Rating plugin.", "gd-rating-system")),
    'wp-postratings' => array(
        'title' => __("Import", "gd-rating-system").': WP PostRatings', 'icon' => 'cloud-download', 
        'button' => 'button', 'button_text' => __("Transfer", "gd-rating-system"),
        'info' => __("Import data from WP PostRatings plugin.", "gd-rating-system")),
    'yet-another-stars-rating' => array(
        'title' => __("Import", "gd-rating-system").': YASR', 'icon' => 'cloud-download', 
        'button' => 'button', 'button_text' => __("Transfer", "gd-rating-system"),
        'info' => __("Import data from Yet Another Stars Rating plugin.", "gd-rating-system")),
    'kk-star-ratings' => array(
        'title' => __("Import", "gd-rating-system").': KK Star Ratings', 'icon' => 'cloud-download', 
        'button' => 'button', 'button_text' => __("Transfer", "gd-rating-system"),
        'info' => __("Import data from KK Star Ratings plugin.", "gd-rating-system"))
);

include(GDRTS_PATH.'forms/shared/top.php');

?>

<form method="post" action="">
    <div class="d4p-content-left">
        <div class="d4p-panel-scroller d4p-scroll-active">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-exchange"></i>
                <h3><?php _e("Transfer Data", "gd-rating-system"); ?></h3>
                <?php if ($_panel != 'index') { ?>
                <h4><?php echo d4p_render_icon($panels[$_panel]['icon'], 'i', true); ?> <?php echo $panels[$_panel]['title']; ?></h4>
                <?php } ?>
            </div>
            <div class="d4p-panel-info">
                <?php echo $panels[$_panel]['info']; ?>
            </div>
            <?php if ($_panel != 'index' && $panels[$_panel]['button'] != 'none') { ?>
                <div class="d4p-panel-buttons">
                    <input id="gdrts-tool-<?php echo $_panel; ?>" class="button-primary" type="<?php echo $panels[$_panel]['button']; ?>" value="<?php echo $panels[$_panel]['button_text']; ?>" />
                </div>
            <?php } ?>
            <div class="d4p-panel-info">
                <hr style="margin-top: 15px;" />
                <strong style="font-size: 12px"><?php _e("This feature is in Beta stage, and there is no guarantee that transfer will work as expected. More testing is needed.", "gd-rating-system"); ?></strong>
            </div>
        </div>
    </div>
    <div class="d4p-content-right">
        <?php

        if ($_panel == 'index') {
            foreach ($panels as $panel => $obj) {
                if ($panel == 'index') continue;

                $url = 'admin.php?page=gd-rating-system-'.$_page.'&panel='.$panel;

                ?>

                <div class="d4p-options-panel">
                    <?php echo d4p_render_icon($obj['icon'], 'i', true); ?>
                    <h5><?php echo $obj['title']; ?></h5>
                    <div>
                        <a class="button-primary" href="<?php echo $url; ?>"><?php _e("Transfer Panel", "gd-rating-system"); ?></a>
                    </div>
                </div>

                <?php
            }
        } else {
            include(GDRTS_PATH.'forms/transfer/'.$_panel.'.php');
        }

        ?>
    </div>
</form>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
