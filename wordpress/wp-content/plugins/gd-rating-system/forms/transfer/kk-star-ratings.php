<div id="gdrts-remotecall-intro">
    <input type="hidden" class="gdrts-tr-plugin" value="kk-star-ratings">

    <div class="d4p-group d4p-group-import d4p-group-important">
        <h3><?php _e("Important", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php _e("This tool can import rating data from WP PostRatings plugin. There are some important things you need to know about import process.", "gd-rating-system"); ?>
            <ul style="list-style: inside disc; font-weight: normal;">
                <li><?php _e("This tool can transfer posts ratings and number of votes. KK Star Ratings plugin doesn't have votes log, so you will only get rating results added, they can't be individually modified later, because KK Star Ratings only keeps overall ratings.", "gd-rating-system"); ?></li>
                <li><?php _e("KK Star Ratings includes rating results only, you will not have votes distribution information since rating results are aggregated.", "gd-rating-system"); ?></li>
                <li><?php _e("Imported data will be marked with special import flag and will be skipped later if you decide to use this tool again.", "gd-rating-system"); ?></li>
                <li><?php _e("This tool will skip transfer for missing posts or pages. If you have ratings for posts or pages you deleted, rating for such posts will be skipped.", "gd-rating-system"); ?></li>
                <li><?php _e("KK Star Ratings allowed for chaging number of rating stars, but it was not recalculating previous ratings if you made changes to number of stars. GD Rating Sytem will perform import based on max rating value you can specify in the transfer settings below.", "gd-rating-system"); ?></li>
                <li><?php _e("KK Star Ratings values in postmeta database table will not be modified in any way or deleted during this import process.", "gd-rating-system"); ?></li>
            </ul>
        </div>
    </div>

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
                                <input type="text" class="widefat gdrts-tr-option gdrts-tr-checked-stars-rating" value="5" name="max">
                                <em><strong><?php _e("Set this to the maximum rating value you used with KK STar Ratings plugin for stars rating.", "gd-rating-system"); ?></strong> 
                                    <?php _e("When imported, ratings will be recalculated based on this value and currently set number of stars in GD Rating System.", "gd-rating-system"); ?> 
                                    <?php _e("If you want to keep same max rating, make sure to adjust the GD Rating System settings for this rating method before the transfer.", "gd-rating-system"); ?>
                                </em>
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
</div>

<div id="gdrts-remotecall-process" style="display: none;">
    <?php include(GDRTS_PATH.'forms/shared/transfer-process.php'); ?>
</div>
