<?php

if (!defined('ABSPATH')) { exit; }

$panels = array();

include(GDRTS_PATH.'forms/shared/top.php');

$pages = gdrts_admin()->menu_items;

?>

<div class="d4p-front-left">
    <div class="d4p-front-title" style="height: auto;">
        <h1 style="font-size: 95px; line-height: 0.95; letter-spacing: -4px; text-align: right;">
            <span>GD RATING</span><span>SYSTEM</span>
            <span style="font-size: 48px; letter-spacing: 1px">
                <?php echo strtoupper(gdrts_settings()->info->edition); ?> 
                <em style="font-weight: 100; font-style: normal;"><?php _e("Edition", "gd-rating-system"); ?></em>
            </span>
        </h1>
        <h5><?php 

            _e("Version", "gd-rating-system");
            echo': '.gdrts_settings()->info->version.' - '.  gdrts_settings()->info->codename;

            if (gdrts_settings()->info->status != 'stable') {
                echo ' - <span style="color: red; font-weight: bold;">'.strtoupper(gdrts_settings()->info->status).'</span>';
            }
            
            ?></h5>
    </div>
    <div class="d4p-front-title" style="height: auto; margin-top: 15px; text-align: center; font-size: 18px; font-weight: bold;">
        <?php _e("Knowledge Base and Support Forums", "gd-rating-system"); ?>
        <p style="font-size: 15px; font-weight: normal; margin: 10px 0 0;">
            <?php echo sprintf(__("To learn more about the plugin, check out plugin %s articles and FAQ. To get additional help, you can use %s.", "gd-rating-system"),
                '<a target="_blank" href="https://support.dev4press.com/kb/product/gd-rating-system/">'.__("knowledge base", "gd-rating-system").'</a>',
                '<a target="_blank" href="https://support.dev4press.com/forums/forum/plugins/gd-rating-system/">'.__("support forum", "gd-rating-system").'</a>'); ?>
        </p>
    </div>
    <div class="d4p-front-title" style="height: auto; margin-top: 15px; text-align: center; font-size: 18px; font-weight: bold;">
        You can upgrade to GD Rating System Pro <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/">here</a>.
        <p style="font-size: 15px; font-weight: normal; margin: 10px 0 0;">To learn more about the features available in Pro version only, <br/>check out this <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/free-vs-pro-plugin/">FREE vs. PRO</a> comparison.</p>
    </div>
    <div class="d4p-front-dev4press">
        &copy; 2008 - 2016. Dev4Press - <a target="_blank" href="https://www.dev4press.com/">www.dev4press.com</a> | 
                                        <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/">plugins.dev4press.com/gd-rating-system</a>
    </div>
</div>
<div class="d4p-front-right">
    <?php

    foreach ($pages as $page => $obj) {
        if ($page == 'front') continue;

        $url = 'admin.php?page=gd-rating-system-'.$page;

        ?>

            <div class="d4p-options-panel">
                <i aria-hidden="true" class="fa fa-<?php echo $obj['icon']; ?>"></i>
                <h5><?php echo $obj['title']; ?></h5>
                <div>
                    <a class="button-primary" href="<?php echo $url; ?>"><?php _e("Open", "gd-rating-system"); ?></a>
                </div>
            </div>

        <?php
    }

    ?>
</div>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
