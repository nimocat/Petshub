<?php

if (!defined('ABSPATH')) { exit; }

$panels = array(
    'index' => array(
        'title' => __("About Plugin", "gd-rating-system"), 'icon' => 'info-circle', 
        'info' => __("Get more information about this plugin.", "gd-rating-system")),
    'changelog' => array(
        'title' => __("Changelog", "gd-rating-system"), 'icon' => 'file-text',
        'info' => __("Check out full changelog for this plugin.", "gd-rating-system")),
    'translations' => array(
        'title' => __("Translations", "gd-rating-system"), 'icon' => 'language',
        'info' => __("List of translations included for this plugin.", "gd-rating-system")),
    'resources' => array(
        'title' => __("Resources", "gd-rating-system"), 'icon' => 'archive',
        'info' => __("Acknowledgement of various resources used in this plugin.", "gd-rating-system")),
    'dev4press' => array(
        'title' => __("Dev4Press", "gd-rating-system"), 'icon' => 'd4p-dev4press',
        'info' => __("Check out other Dev4Press products.", "gd-rating-system"))
);

include(GDRTS_PATH.'forms/shared/top.php');

?>

<div class="d4p-content-left">
    <div class="d4p-panel-title">
        <i aria-hidden="true" class="fa fa-info-circle"></i>
        <h3><?php _e("About", "gd-rating-system"); ?></h3>
        <?php if ($_panel != 'index') { ?>
            <h4><?php echo d4p_render_icon($panels[$_panel]['icon'], 'i', true); ?> <?php echo $panels[$_panel]['title']; ?></h4>
        <?php } ?>
    </div>
    <div class="d4p-panel-info">
        <?php echo $panels[$_panel]['info']; ?>
    </div>
    <?php if ($_panel == 'index') { ?>
    <div class="d4p-panel-links">
        <a href="admin.php?page=gd-rating-system-about&panel=changelog"><i aria-hidden="true" class="fa fa-file-text fa-fw"></i> <?php _e("Changelog", "gd-rating-system"); ?></a>
        <a href="admin.php?page=gd-rating-system-about&panel=translations"><i aria-hidden="true" class="fa fa-language fa-fw"></i> <?php _e("Translations", "gd-rating-system"); ?></a>
        <a href="admin.php?page=gd-rating-system-about&panel=resources"><i aria-hidden="true" class="fa fa-archive fa-fw"></i> <?php _e("Resources", "gd-rating-system"); ?></a>
        <a href="admin.php?page=gd-rating-system-about&panel=dev4press"><i aria-hidden="true" class="d4pi d4p-dev4press d4pi-fw"></i> Dev4Press</a>
    </div>
    <?php } ?>
</div>
<div class="d4p-content-right">
    <?php

        if ($_panel == 'index') {
            include(GDRTS_PATH.'forms/panels/about.php');
        } else {
            include(GDRTS_PATH.'forms/panels/'.$_panel.'.php');
        }

    ?>
</div>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
