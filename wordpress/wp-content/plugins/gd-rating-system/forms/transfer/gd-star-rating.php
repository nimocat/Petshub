<div id="gdrts-remotecall-intro">
    <input type="hidden" class="gdrts-tr-plugin" value="gd-star-rating">

    <div class="d4p-group d4p-group-import d4p-group-important">
        <h3><?php _e("Important", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php _e("This tool can import rating data from GD Star Rating plugin. Due to the vastly different strucutre of database tables between these plugins, there are some limitations to the import process.", "gd-rating-system"); ?>
            <ul style="list-style: inside disc; font-weight: normal;">
                <li><?php _e("This tool can transfer posts, pages and comments ratings and number of votes along with votes log data.", "gd-rating-system"); ?></li>
                <li><?php _e("Imported data will be marked with special import flag and will be skipped later if you decide to use this tool again.", "gd-rating-system"); ?></li>
                <li><?php _e("GD Star Rating settings, individual posts and comments rules or trend data can't be transfered or used in any way.", "gd-rating-system"); ?></li>
                <li><?php _e("Data for multi star rating can't be transfered at this time, since GD Rating System plugin doesn't have this rating method.", "gd-rating-system"); ?></li>
                <li><?php _e("For import process to work, all GD Star Rating database tables must be present.", "gd-rating-system"); ?></li>
                <li><?php _e("If there is data missing, or if it can't be matched correctly, the transfer might be incomplete.", "gd-rating-system"); ?></li>
                <li><?php _e("Old GD Star Rating plugin database tables will not be modified in any way or deleted during this import process.", "gd-rating-system"); ?></li>
            </ul>
            <?php _e("To make transfer faster, please follow these rules.", "gd-rating-system"); ?>
            <ul style="list-style: inside disc; font-weight: normal;">
                <li><?php _e("To improve the speed of transfer, convert GD Star Rating tables from MyISAM to InnoDB Engine.", "gd-rating-system"); ?></li>
                <li><?php _e("Make sure to run each rating method import individually. Transfering multiple rating methods at once, can slow down the process.", "gd-rating-system"); ?></li>
            </ul>
        </div>
    </div>

    <?php

    require_once(GDRTS_PATH.'core/transfer/gd-star-rating.php');

    $transfer = new gdrts_transfer_gd_star_rating();

    if (!$transfer->db_tables_exist()) {

    ?>

    <div class="d4p-group d4p-group-import d4p-group-important">
        <h3><?php _e("Import not possible", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php _e("Required database tables not found. Import process can't proceed.", "gd-rating-system"); ?>
        </div>
    </div>

    <?php

    } else {

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
                                <input type="text" class="widefat gdrts-tr-option gdrts-tr-checked-stars-rating" value="10" name="max">
                                <em><strong><?php _e("Set this to the maximum rating value you used with GD Star Rating plugin for stars rating.", "gd-rating-system"); ?></strong> 
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

    <div class="d4p-group d4p-group-import">
        <h3><?php _e("Import: Star Reviews", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner" style="text-align: left;">
            Stars Review rating method is available only in GD Rating System Pro. You can upgrade to GD Rating System Pro <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/">here</a>.
            <p style="font-weight: normal; margin: 10px 0 0;">To learn more about the features available in Pro version only, check out this <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/free-vs-pro-plugin/">FREE vs. PRO</a> comparison.</p>
        </div>
    </div>

    <div class="d4p-group d4p-group-import">
        <h3><?php _e("Import: Thumb Ratings", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner" style="text-align: left;">
            Thumbs Rating rating method is available only in GD Rating System Pro. You can upgrade to GD Rating System Pro <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/">here</a>.
            <p style="font-weight: normal; margin: 10px 0 0;">To learn more about the features available in Pro version only, check out this <a target="_blank" href="https://plugins.dev4press.com/gd-rating-system/free-vs-pro-plugin/">FREE vs. PRO</a> comparison.</p>
        </div>
    </div>

    <?php } ?>
</div>

<div id="gdrts-remotecall-process" style="display: none;">
    <?php include(GDRTS_PATH.'forms/shared/transfer-process.php'); ?>
</div>
