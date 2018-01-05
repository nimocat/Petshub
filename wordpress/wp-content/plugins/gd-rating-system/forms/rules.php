<?php

if (!defined('ABSPATH')) { exit; }

include(GDRTS_PATH.'forms/shared/top.php');

$items = gdrts_list_all_entities();

$objects = array();

$values = array();
foreach (gdrts()->addons as $addon => $obj) {
    if (isset($obj['override']) && $obj['override'] && gdrts_is_addon_loaded($addon)) {
        $values[$addon] = $obj['label'];
    }
}

if (!empty($values)) {
    $objects[] = array('title' => __("Addons", "gd-rating-system"), 'values' => $values);
}

$values = array();
foreach (gdrts()->methods as $addon => $obj) {
    if (isset($obj['override']) && $obj['override'] && gdrts_is_method_loaded($addon)) {
        $values[$addon] = $obj['label'];
    }
}

if (!empty($values)) {
    $objects[] = array('title' => __("Methods", "gd-rating-system"), 'values' => $values);
}

$available = array();

foreach ($items as $group) {
    foreach ($group['values'] as $_item => $_label_item) {
        foreach ($objects as $objs) {
            foreach ($objs['values'] as $_objc => $_label_objc) {
                $key = $_item.'_'.$_objc.'_rule_active';
                $ava = gdrts_settings()->get($key, 'items');

                if (!is_null($ava)) {
                    $available[$key] = array(
                        'label' => '<strong>'.$_label_objc.'</strong> '.__("for", "gd-rating-system").' <strong>'.$_label_item.'</strong>',
                        'item' => $_item, 'obj' => $_objc, 'active' => $ava
                    );
                }
            }
        }
    }
}

?>

<div class="d4p-content-left">
    <div class="d4p-panel-scroller d4p-scroll-active">
        <div class="d4p-panel-title">
            <i aria-hidden="true" class="fa fa-star-o"></i>
            <h3><?php _e("Rules", "gd-rating-system"); ?></h3>
        </div>
        <div class="d4p-panel-info">
            <?php _e("You can create override rules for every rating content type registered with the plugin. Overrides can be added for rating methods and available addons.", "gd-rating-system"); ?>
        </div>
    </div>
</div>
<div class="d4p-content-right">
    <div class="d4p-group d4p-group-about">
        <h3><?php _e("Add new Rule", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <form method="post" action="">
                <?php settings_fields('gd-rating-system-newrule'); ?>
                <input type="hidden" value="postback" name="gdrts_handler" />

                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><?php _e("Item Type", "gd-rating-system"); ?></th>
                            <td>
                                <div class="d4p-setting-select">
                                    <?php d4p_render_grouped_select($items, array('name' => 'item', 'class' => 'widefat')); ?>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e("Object Type", "gd-rating-system"); ?></th>
                            <td>
                                <div class="d4p-setting-select">
                                    <?php d4p_render_grouped_select($objects, array('name' => 'object', 'class' => 'widefat')); ?>
                                </div>
                            </td>
                        </tr>
                        <tr valign="top"><td colspan="2"><div class="d4p-setting-hr"><hr></div></td></tr>
                        <tr valign="top">
                            <th scope="row"> </th>
                            <td>
                                <div class="d4p-setting-select">
                                    <input type="submit" class="button-primary" value="<?php _e("Override", "gd-rating-system"); ?>" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </form>
        </div>
    </div>

    <div class="d4p-group d4p-group-about">
        <h3><?php _e("Current Rules", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php

                if (empty($available)) {
                    _e("You don't have any custom rating rules created.", "gd-rating-system");
                } else {
                    foreach ($available as $av) {
                        $entity_name = explode('.', $av['item']);
                        $entity  = gdrts()->get_entity($entity_name[0]);

                        $icon = apply_filters('gdrts_admin_icon_'.$av['obj'], 'flash');

                        $_edit_nonce = wp_create_nonce('gdrts-remove-'.$av['item'].'-'.$av['obj']);

                        echo '<div class="gdrts-rule-block gdrts-rule-'.($av['active'] ? 'enabled' : 'disabled').'">';
                        echo '<span class="gdrts-rule-icons" aria-hidden="true"><i aria-hidden="true" class="fa fa-'.$icon.' fa-fw"></i> &middot; <i aria-hidden="true" class="fa fa-'.$entity['icon'].' fa-fw"></i></span>';
                        echo '<span class="gdrts-rule-title">'.$av['label'].'</span>';
                        echo '<span class="gdrts-rule-actions">';
                            echo '<a class="button-primary" href="'.self_admin_url('admin.php?page=gd-rating-system-rules&action=rule&item='.$av['item'].'&obj='.$av['obj']).'">'.__("edit", "gd-rating-system").'</a>';

                            if ($av['active']) {
                                echo '<a class="button-secondary" href="'.self_admin_url('admin.php?page=gd-rating-system-rules&gdrts_handler=getback&single-action=disable-rule&item='.$av['item'].'&obj='.$av['obj'].'&_wpnonce='.$_edit_nonce).'">'.__("disable", "gd-rating-system").'</a>';
                            } else {
                                echo '<a class="button-secondary" href="'.self_admin_url('admin.php?page=gd-rating-system-rules&gdrts_handler=getback&single-action=enable-rule&item='.$av['item'].'&obj='.$av['obj'].'&_wpnonce='.$_edit_nonce).'">'.__("enable", "gd-rating-system").'</a>';
                            }

                            echo '<a class="button-secondary gdrts-action-delete-rule" href="'.self_admin_url('admin.php?page=gd-rating-system-rules&gdrts_handler=getback&single-action=remove-rule&item='.$av['item'].'&obj='.$av['obj'].'&_wpnonce='.$_edit_nonce).'">'.__("delete", "gd-rating-system").'</a>';
                        echo '</span>';
                        echo '</div>';
                    }
                }

            ?>
        </div>
    </div>
</div>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');
include(GDRTS_PATH.'forms/dialogs/rules.php');
