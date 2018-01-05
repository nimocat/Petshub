<?php // GDRTS Template: Dynamic Loader // ?>

<div class="gdrts-dynamic-block">

    <?php gdrtsa_dynamic_load()->json(); ?>

    <div class="gdrts-rating-please-wait">
        <i class="rtsicon-spinner rtsicon-spin rtsicon-va rtsicon-fw"></i> <?php _e("Please wait...", "gd-rating-system"); ?>
    </div>

    <?php do_action('gdrts-template-rating-rich-snippet'); ?>

</div>