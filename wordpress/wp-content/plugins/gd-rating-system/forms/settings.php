<?php

if (!defined('ABSPATH')) { exit; }

$panels = array(
    'index' => array(
        'title' => __("Settings Index", "gd-rating-system"), 'icon' => 'cogs', 
        'info' => __("All plugin settings are split into panels, and you access each starting from the right.", "gd-rating-system")),
    'extensions' => array(
        'title' => __("Extensions", "gd-rating-system"), 'icon' => 'puzzle-piece', 
        'break' => __("Basic Settings", "gd-rating-system"), 
        'info' => __("From this panel you can disable and enable individual plugin addons and rating methods.", "gd-rating-system")),
    'global' => array(
        'title' => __("Global", "gd-rating-system"), 'icon' => 'cog', 
        'info' => __("From this panel you control general plugin settings.", "gd-rating-system")),
    'security' => array(
        'title' => __("Security", "gd-rating-system"), 'icon' => 'lock', 
        'info' => __("From this panel you control security related plugin settings.", "gd-rating-system")),
    'debug' => array(
        'title' => __("Debug", "gd-rating-system"), 'icon' => 'bug', 
        'info' => __("From this panel you control debugger related plugin settings.", "gd-rating-system")),
    'cache' => array(
        'title' => __("Cache", "gd-rating-system"), 'icon' => 'cube', 
        'info' => __("From this panel you control cache related plugin settings.", "gd-rating-system")),
    'administration' => array(
        'title' => __("Administration", "gd-rating-system"), 'icon' => 'dashboard', 
        'info' => __("From this panel you control administration related settings.", "gd-rating-system")),
    'advanced' => array(
        'title' => __("Advanced", "gd-rating-system"), 'icon' => 'sliders', 
        'info' => __("From this panel you control advanced plugin settings.", "gd-rating-system"))
);

$panels = apply_filters('gdrts_admin_settings_panels', $panels);

$_methods = false;
$_addons = false;

foreach ($panels as $code => &$_obj) {
    if (!$_methods && isset($_obj['type']) && $_obj['type'] == 'method') {
        $_obj['break'] = __("Rating Methods", "gd-rating-system");
        $_methods = true;
    }

    if (!$_addons && isset($_obj['type']) && $_obj['type'] == 'addon') {
        $_obj['break'] = __("Rating Addons", "gd-rating-system");
        $_addons = true;
    }
}

include(GDRTS_PATH.'forms/shared/top.php');

?>

<form method="post" action="">
    <?php settings_fields('gd-rating-system-settings'); ?>
    <input type="hidden" value="postback" name="gdrts_handler" />

    <div class="d4p-content-left">
        <div class="d4p-panel-scroller d4p-scroll-active">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-cogs"></i>
                <h3><?php _e("Settings", "gd-rating-system"); ?></h3>
                <?php if ($_panel != 'index') { ?>
                <h4><?php echo d4p_render_icon($panels[$_panel]['icon'], 'i', true); ?> <?php echo $panels[$_panel]['title']; ?></h4>
                <?php } ?>
            </div>
            <div class="d4p-panel-info">
                <?php echo $panels[$_panel]['info']; ?>
            </div>
            <?php if ($_panel != 'index') { ?>
                <div class="d4p-panel-buttons">
                    <input type="submit" value="<?php _e("Save Settings", "gd-rating-system"); ?>" class="button-primary">
                </div>
                <div class="d4p-return-to-top">
                    <a href="#wpwrap"><?php _e("Return to top", "gd-rating-system"); ?></a>
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

                if (isset($obj['break'])) { ?>

                    <div style="clear: both"></div>
                    <div class="d4p-panel-break d4p-clearfix">
                        <h4><?php echo $obj['break']; ?></h4>
                    </div>
                    <div style="clear: both"></div>

                <?php } ?>

                <div class="d4p-options-panel">
                    <?php echo d4p_render_icon($obj['icon'], 'i', true); ?>
                    <h5><?php echo $obj['title']; ?></h5>
                    <div>
                        <?php if (isset($obj['type'])) { ?>
                        <span><?php echo $obj['type']; ?></span>
                        <?php } ?>
                        <a class="button-primary" href="<?php echo $url; ?>"><?php _e("Settings Panel", "gd-rating-system"); ?></a>
                    </div>
                </div>
        
                <?php
            }
        } else {
            require_once(GDRTS_PATH.'d4plib/admin/d4p.functions.php');
            require_once(GDRTS_PATH.'d4plib/admin/d4p.settings.php');

            include(GDRTS_PATH.'core/internal.php');

            $options = new gdrts_admin_settings();

            $panel = gdrts_admin()->panel;
            $groups = $options->get($panel);

            $render = new d4pSettingsRender($panel, $groups);
            $render->base = 'gdrtsvalue';
            $render->render();

            ?>

            <div class="clear"></div>
            <div style="padding-top: 15px; border-top: 1px solid #777; max-width: 800px;">
                <input type="submit" value="<?php _e("Save Settings", "gd-rating-system"); ?>" class="button-primary">
            </div>

            <?php

        }

        ?>
    </div>
</form>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
