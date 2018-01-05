<?php

global $post_ID;
$post_type = get_post_type($post_ID);

$tabs = apply_filters('gdrts_admin_metabox_tabs', array(), $post_ID, $post_type);

if (empty($tabs)) {
    _e("Nothing to show here.", "gd-rating-system");
} else {

?>

<div class="d4plib-metabox-wrapper">
    <ul class="wp-tab-bar">
        <?php

        $active = true;
        foreach ($tabs as $tab => $label) {
            echo '<li class="'.($active ? 'wp-tab-active' : '').'"><a href="#gdrts-meta-'.$tab.'">'.$label.'</a></li>';

            $active = false;
        }

        ?>
    </ul>
    <?php

    $active = true;
    foreach ($tabs as $tab => $label) {
        echo '<div id="gdrts-meta-'.$tab.'" class="wp-tab-panel '.($active ? 'tabs-panel-active' : 'tabs-panel-inactive').'">';

        do_action('gdrts_admin_metabox_content_'.$tab, $post_ID, $post_type);

        echo '</div>';

        $active = false;
    }

    ?>
</div>

<?php } ?>