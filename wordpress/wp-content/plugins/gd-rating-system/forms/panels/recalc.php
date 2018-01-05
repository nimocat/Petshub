<div id="gdrts-recalc-intro">
    <div class="d4p-group d4p-group-reset d4p-group-important">
        <h3><?php _e("Important", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php _e("Recalculation tools will take existing log data and recalculate each object ratings based on the log.", "gd-rating-system"); ?><br/><br/>
            <ul style="list-style: inside disc; font-weight: normal;">
                <li><?php _e("If the log entries are not complete, this will affect the final ratings results.", "gd-rating-system"); ?></li>
                <li><?php _e("Once the process start, voting on the website will be disabled to avoid problems with data changing during the process.", "gd-rating-system"); ?></li>
                <li><?php _e("Recalculation page will show the progress, make sure not to close the page while the process is working.", "gd-rating-system"); ?></li>
                <li><?php _e("Process is not reversible, and it is highly recommended to create database backup before proceeding with this tool.", "gd-rating-system"); ?></li>
            </ul>
        </div>
    </div>

    <div class="d4p-group d4p-group-reset">
        <h3><?php _e("Stars Rating", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php _e("Rating", "gd-rating-system"); ?></th>
                        <td>
                            <div class="d4p-setting-bool">
                                <label for="gdrtstools_recalc_stars_rating_rating">
                                    <input type="checkbox" class="widefat gdrts-recalc-filter" id="gdrtstools_recalc_stars_rating_rating" value="stars-rating|rating"><?php _e("Recalculate average rating", "gd-rating-system"); ?>
                                </label>
                            </div>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php _e("Votes Distribution", "gd-rating-system"); ?></th>
                        <td>
                            <div class="d4p-setting-bool">
                                <label for="gdrtstools_recalc_stars_rating_distribution">
                                    <input type="checkbox" class="widefat gdrts-recalc-filter" id="gdrtstools_recalc_stars_rating_distribution" value="stars-rating|distribution"><?php _e("Recalculate votes distribution", "gd-rating-system"); ?>
                                </label>
                                <em><strong><?php _e("It is highly recommended to use both options for recalculation, or you will end up with potential discrapancy in results if you use votes distribution with average ratings.", "gd-rating-system"); ?></strong></em>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="gdrts-recalc-process" style="display: none;">
    <div class="d4p-group d4p-group-reset d4p-group-important">
        <h3><?php _e("Important", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <?php _e("Recalculation is in progress.", "gd-rating-system"); ?><br/><br/>
            <ul style="list-style: inside disc; font-weight: normal;">
                <li><?php _e("Do not close this page, it will stop the process. If that happens, rating maintenance mode will remain active.", "gd-rating-system"); ?></li>
                <li><?php _e("If the process stops responding for a long time (due to server related issues, or some other problem), visit Settings -> Advanced page to disable maintenance mode.", "gd-rating-system"); ?></li>
            </ul>
        </div>
    </div>

    <div class="d4p-group d4p-group-reset" id="gdrts-recalc-progress">
        <h3><?php _e("Processing progress", "gd-rating-system"); ?></h3>
        <div class="d4p-group-inner">
            <pre></pre>
        </div>
    </div>
</div>