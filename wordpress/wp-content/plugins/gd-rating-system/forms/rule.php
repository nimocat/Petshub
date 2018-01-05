<?php

if (!defined('ABSPATH')) { exit; }

include(GDRTS_PATH.'forms/shared/top.php');

$_edit_item = d4p_sanitize_key_expanded($_GET['item']);
$_edit_object = d4p_sanitize_key_expanded($_GET['obj']);

$_edit_nonce = wp_create_nonce('gdrts-remove-'.$_edit_item.'-'.$_edit_object);

$valid_items = array();
$valid_objects = array();

foreach (gdrts()->get_entities() as $entity => $obj) {
    $main = $obj['label'];

    $valid_items[$entity] = $main;
    
    foreach ($obj['types'] as $name => $label) {
        $valid_items[$entity.'.'.$name] = $main.' - '.$label;
    }
}

foreach (gdrts()->addons as $addon => $obj) {
    if (isset($obj['override']) && $obj['override']) {
        $valid_objects[$addon] = $obj['label'];
    }
}

foreach (gdrts()->methods as $addon => $obj) {
    if (isset($obj['override']) && $obj['override']) {
        $valid_objects[$addon] = $obj['label'];
    }
}

$title = isset($valid_objects[$_edit_object]) ? '<h4>'.$valid_objects[$_edit_object].'<span class="d4p-header-divider">'.__("for", "gd-rating-system").'</span>' : '';
$title.= isset($valid_items[$_edit_item]) ? $valid_items[$_edit_item].'</h4>' : '';

$panel = 'items';
$prekey = $_edit_item.'_'.$_edit_object;

$_active_name = 'gdrtsvalue[items]['.$prekey.'_rule_active]';
$_active_id = 'gdrtsvalue_items_'.$prekey.'_rule_active';

$_is_active = gdrts_settings()->get($prekey.'_rule_active', 'items');

?>

<form method="post" action="">
    <?php settings_fields('gd-rating-system-ruledit'); ?>
    <input type="hidden" value="postback" name="gdrts_handler" />

    <div class="d4p-content-left">
        <div class="d4p-panel-scroller d4p-scroll-active">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-star-o"></i>
                <h3><?php _e("Rules", "gd-rating-system"); ?></h3>
                <?php echo $title; ?>
            </div>
            <div class="d4p-panel-info">
                <?php _e("These settings will be used before default settings. You can temporarally disable this rule override or you can remove them.", "gd-rating-system"); ?>
            </div>
            <div class="d4p-panel-buttons">
                <input type="submit" value="<?php _e("Save Settings", "gd-rating-system"); ?>" class="button-primary">
            </div>
            <div class="d4p-return-to-top">
                <a href="#wpwrap"><?php _e("Return to top", "gd-rating-system"); ?></a>
            </div>
        </div>
    </div>
    <div class="d4p-content-right">
        <div class="d4p-group d4p-group-about">
            <h3><?php _e("Rule Control", "gd-rating-system"); ?></h3>
            <div class="d4p-group-inner">
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><?php _e("Status", "gd-rating-system"); ?></th>
                            <td>
                                <div class="d4p-setting-bool">
                                    <label for="<?php echo $_active_id; ?>">
                                        <input<?php echo $_is_active ? ' checked="checked"' : ''; ?> type="checkbox" class="widefat" id="<?php echo $_active_id; ?>" name="<?php echo $_active_name; ?>">Enabled
                                    </label>
                                    <em><?php _e("Rule will remain in the database, but it will not be used if it is not enabled.", "gd-rating-system"); ?></em>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top"><td colspan="2"><div class="d4p-setting-hr"><hr></div></td></tr>
                        <tr valign="top">
                            <th scope="row"> </th>
                            <td>
                                <div class="d4p-setting-select">
                                    <a class="button-primary gdrts-action-delete-rule" href="<?php echo self_admin_url('admin.php?page=gd-rating-system-rules&gdrts_handler=getback&single-action=remove-rule&item='.$_edit_item.'&obj='.$_edit_object.'&_wpnonce='.$_edit_nonce); ?>"><?php _e("Remove this rule", "gd-rating-system"); ?></a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php


        d4p_includes(array(
            array('name' => 'functions', 'directory' => 'admin'), 
            array('name' => 'settings', 'directory' => 'admin')
        ), GDRTS_D4PLIB);

        $groups = apply_filters('gdrts_admin_rule_'.$_edit_object, array(), 'items', $prekey);

        $render = new d4pSettingsRender($panel, $groups);
        $render->base = 'gdrtsvalue';
        $render->render();

        ?>

        <div class="clear"></div>
        <div style="padding-top: 15px; border-top: 1px solid #777; max-width: 800px;">
            <input type="submit" value="<?php _e("Save Settings", "gd-rating-system"); ?>" class="button-primary">
        </div>
    </div>
</form>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
include(GDRTS_PATH.'forms/dialogs/rules.php');
