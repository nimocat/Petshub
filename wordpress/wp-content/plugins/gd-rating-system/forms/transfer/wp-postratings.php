<div id="gdrts-remotecall-intro">
    <input type="hidden" class="gdrts-tr-plugin" value="wp-postratings">

    <div class="d4p-group d4p-group-import d4p-group-important">
        <h3><?php _e("Important", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php _e("This tool can import rating data from WP PostRatings plugin. There are some important things you need to know about import process.", "gd-rating-system"); ?>
            <ul style="list-style: inside disc; font-weight: normal;">
                <li><?php _e("This tool can transfer posts ratings and number of votes along with votes log data.", "gd-rating-system"); ?></li>
                <li><?php _e("Imported data will be marked with special import flag and will be skipped later if you decide to use this tool again.", "gd-rating-system"); ?></li>
                <li><?php _e("For import process to work, WP PostRatings database table 'wp_ratings' must be present.", "gd-rating-system"); ?></li>
                <li><?php _e("This tool will skip transfer for missing posts or pages. If you have ratings for posts or pages you deleted, rating for such posts will be skipped.", "gd-rating-system"); ?></li>
                <li><?php _e("WP PostRatings allowed for chaging number of rating stars, but it was not recalculating previous ratings if you made changes to number of stars. GD Rating Sytem will perform import based on max rating value you can specify in the transfer settings below.", "gd-rating-system"); ?></li>
                <li><?php _e("WP PostRatings plugin database table will not be modified in any way or deleted during this import process.", "gd-rating-system"); ?></li>
            </ul>
        </div>
    </div>

    <?php

    require_once(GDRTS_PATH.'core/transfer/wp-postratings.php');

    $transfer = new gdrts_transfer_wp_postratings();

    if (!$transfer->db_tables_exist()) {

    ?>

    <div class="d4p-group d4p-group-import d4p-group-important">
        <h3><?php _e("Import not possible", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php _e("Required database table not found. Import process can't proceed.", "gd-rating-system"); ?>
        </div>
    </div>

    <?php

    } else {
        $max_rating = intval(get_option('postratings_max', 5));

    ?>

    <div class="d4p-group d4p-group-import">
        <h3><?php _e("Import: Stars Rating", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php if (gdrts_is_method_loaded('stars-rating')) { ?>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e("Import", "gd-rating-system"); ?></th>
                        <td>
                            <div class="d4p-setting-bool">
                                <label>
                                    <input type="checkbox" class="widefat gdrts-tr-check" value="stars-rating"><?php _e("Enabled", "gd-rating-system"); ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("Max Ratings", "gd-rating-system"); ?></th>
                        <td>
                            <div class="d4p-setting-number">
                                <input type="text" class="widefat gdrts-tr-option gdrts-tr-checked-stars-rating" value="<?php echo $max_rating; ?>" name="max">
                                <em><strong><?php _e("Set this to the maximum rating value you used with WP PostRaings plugin for stars rating.", "gd-rating-system"); ?></strong> 
                                    <?php _e("When imported, ratings will be recalculated based on this value and currently set number of stars in GD Rating System.", "gd-rating-system"); ?> 
                                    <?php _e("If you want to keep same max rating, make sure to adjust the GD Rating System settings for this rating method before the transfer.", "gd-rating-system"); ?>
                                </em>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("How to import", "gd-rating-system"); ?></th>
                        <td>
                            <div class="d4p-setting-number">
                                <select class="widefat gdrts-tr-option gdrts-tr-checked-stars-rating" name="method">
                                    <option selected="selected" value="log"><?php _e("Rating based on ratings log only", "gd-rating-system"); ?></option>
                                    <option value="data"><?php _e("Rating results only", "gd-rating-system"); ?> (<?php _e("Not recommended", "gd-rating-system"); ?>)</option>
                                </select>
                                <em><?php _e("Rating log might contain incomplete list of ratings compared to rating results, but these ratings include more information and can be edited or deleted later.", "gd-rating-system"); ?><br/>
                                <strong><?php _e("If you import rating results only, you will not have votes distribution information since rating results are aggregated. To get votes distribution you must use log based import.", "gd-rating-system"); ?></strong></em>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php } else { ?>
            <p style="text-align: left"><?php _e("Rating method is not active.", "gd-rating-system"); ?></p>
            <?php } ?>
        </div>
    </div>

    <?php } ?>
</div>

<div id="gdrts-remotecall-process" style="display: none;">
    <?php include(GDRTS_PATH.'forms/shared/transfer-process.php'); ?>
</div>
