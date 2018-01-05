<?php // GDRTS Template: Default with Distribution // ?>

<div class="<?php gdrts_loop()->render()->classes(); ?>">
    <div class="gdrts-inner-wrapper">

        <?php do_action('gdrts-template-rating-block-before'); ?>

        <?php gdrts_loop()->render()->stars(); ?>

        <div class="gdrts-rating-text">
            <?php

            if (gdrts_loop()->render()->has_votes()) {
                gdrts_loop()->render()->text();

                if (gdrts_loop()->render()->has_distribution()) {

                ?>

                <a class="gdrts-toggle-distribution" href="#" data-show="<?php _e("Show votes.", "gd-rating-system"); ?>" data-hide="<?php _e("Hide votes.", "gd-rating-system"); ?>"><?php _e("Show votes.", "gd-rating-system"); ?></a>

                <?php

                }

            } else {
                _e("No votes yet.", "gd-rating-system");
            }

            ?>
        </div>
        <?php

        if (gdrts_loop()->user()->has_voted()) {

        ?>

            <div class="gdrts-rating-user">
                <?php gdrts_loop()->render()->user_vote(); ?>
            </div>

        <?php

        }

        if (gdrts_loop()->is_save()) {

        ?>

            <div class="gdrts-rating-thanks">
                <?php _e("Thanks for your vote!", "gd-rating-system"); ?>
            </div>

        <?php

        } else {
            gdrts_loop()->method()->please_wait();
        }

        if (gdrts_loop()->render()->has_distribution()) {

            ?>

            <div class="gdrts-rating-distribution" style="display: none;">
                <?php gdrts_loop()->render()->distribution(); ?>
            </div>

            <?php

        }

        gdrts_loop()->json();

        do_action('gdrts-template-rating-block-after');
        do_action('gdrts-template-rating-rich-snippet');

        ?>

    </div>
</div>