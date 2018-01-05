<?php

if (!defined('ABSPATH')) { exit; }

$panels = array(
    'index' => array(
        'title' => __("Tools Index", "gd-rating-system"), 'icon' => 'wrench', 
        'info' => __("Show additional information related to plugin use.", "gd-rating-system")),
    'templates' => array(
        'title' => __("Templates List", "gd-rating-system"), 'icon' => 'file-text-o', 
        'button' => 'none', 'button_text' => '',
        'info' => __("List all available templates for each rating method.", "gd-rating-system"))
);

include(GDRTS_PATH.'forms/shared/top.php');

?>

<form method="post" action="" enctype="multipart/form-data">
    <?php settings_fields('gd-rating-system-tools'); ?>
    <input type="hidden" value="<?php echo $_panel; ?>" name="gdrtstools[panel]" />
    <input type="hidden" value="postback" name="gdrts_handler" />

    <div class="d4p-content-left">
        <div class="d4p-panel-scroller d4p-scroll-active">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-info-circle"></i>
                <h3><?php _e("Information", "gd-rating-system"); ?></h3>
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
                        <a class="button-primary" href="<?php echo $url; ?>"><?php _e("Information Panel", "gd-rating-system"); ?></a>
                    </div>
                </div>

                <?php
            }
        } else {
            include(GDRTS_PATH.'forms/panels/'.$_panel.'.php');
        }

        ?>
    </div>
</form>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
