<?php

if (!defined('ABSPATH')) { exit; }

include(GDRTS_PATH.'forms/shared/top.php');

$action = $_GET['action'] == 'edit' ? 'edit' : 'new';
$entity = $action == 'edit' && isset($_GET['entity']) ? d4p_sanitize_basic($_GET['entity']) : '';

$all = gdrts_settings()->get('custom_entities', 'early');
$obj = array('name' => '', 'label' => '', 'icon' => '', 'types' => array());

if (!empty($entity) && isset($all[$entity])) {
    $obj = $all[$entity];
} else if ($entity == 'custom') {
    $obj = array('name' => 'custom', 'label' => 'Custom', 'icon' => 'asterisk', 'types' => array());
}

?>

<form method="post" action="">
    <?php settings_fields('gd-rating-system-entityedit'); ?>
    <input type="hidden" value="postback" name="gdrts_handler" />

    <div class="d4p-content-left">
        <div class="d4p-panel-scroller d4p-scroll-active">
            <div class="d4p-panel-title">
                <i aria-hidden="true" class="fa fa-thumb-tack"></i>
                <h3><?php _e("Entity Editor", "gd-rating-system"); ?></h3>
            </div>
            <div class="d4p-panel-info">
                <?php _e("From this panel you can edit custom entities and types belonging to it.", "gd-rating-system"); ?>
            </div>
            <div class="d4p-panel-buttons">
                <input type="submit" value="<?php _e("Save Entity", "gd-rating-system"); ?>" class="button-primary">
            </div>
            <div class="d4p-return-to-top">
                <a href="#wpwrap"><?php _e("Return to top", "gd-rating-system"); ?></a>
            </div>
        </div>
    </div>
    <div class="d4p-content-right">
        <?php

        d4p_includes(array(
            array('name' => 'functions', 'directory' => 'admin'), 
            array('name' => 'settings', 'directory' => 'admin'), 
            array('name' => 'fontawesome', 'directory' => 'classes')
        ), GDRTS_D4PLIB);

        $fontawesome = new d4pFontawesome();

        $icons = array();

        foreach ($fontawesome->icons as $icon) {
            $icons[$icon] = $icon;
        }

        $groups = array(
            'entity_basic' => array('name' => __("Entity", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement('entity', 'name', __("Name", "gd-rating-system"), '', d4pSettingType::SLUG, $obj['name'], '', '', array('readonly' => $entity == 'custom')),
                new d4pSettingElement('entity', 'label', __("Label", "gd-rating-system"), '', d4pSettingType::TEXT, $obj['label'], '', '', array('readonly' => $entity == 'custom')),
                new d4pSettingElement('entity', 'icon', __("Icon", "gd-rating-system"), '', d4pSettingType::SELECT, $obj['icon'], 'array', $icons, array('readonly' => $entity == 'custom')),
            )),
            'entity_types' => array('name' => __("Types", "gd-rating-system"), 'settings' => array(
                new d4pSettingElement('entity', 'types', __("Types", "gd-rating-system"), '', d4pSettingType::EXPANDABLE_PAIRS, $obj['types'], '', '', array('label_key' => __("Name", "gd-rating-system"), 'label_value' => __("Label", "gd-rating-system"), 'label_button_add' => __("Add New Type", "gd-rating-system"), 'label_buttom_remove' => __("Remove", "gd-rating-system"))),
            ))
        );

        $render = new d4pSettingsRender('entity', $groups);
        $render->base = 'gdrtsvalue';
        $render->render();

        ?>

        <div class="clear"></div>
        <div style="padding-top: 15px; border-top: 1px solid #777; max-width: 800px;">
            <input type="submit" value="<?php _e("Save Entity", "gd-rating-system"); ?>" class="button-primary">
        </div>
    </div>
</form>

<?php 

include(GDRTS_PATH.'forms/shared/bottom.php');

