<div class="d4p-group d4p-group-reset d4p-group-important">
    <h3><?php _e("Important", "gd-rating-system"); ?></h3>
    <div class="d4p-group-inner">
        <?php _e("This tool can remove plugin settings saved in the WordPress options table and all database tables added by the plugin.", "gd-rating-system"); ?><br/><br/>
        <?php _e("Deletion operations are not reversible, and it is highly recommended to create database backup before proceeding with this tool.", "gd-rating-system"); ?> 
        <?php _e("If you choose to remove plugin settings, once that is done, all settings will be reinitialized to default values if you choose to leave plugin active.", "gd-rating-system"); ?>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Remove plugin settings", "gd-rating-system"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdrtstools[remove][settings]" value="on" /> <?php _e("All Plugin Settings", "gd-rating-system"); ?>
        </label>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Remove database data and tables", "gd-rating-system"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdrtstools[remove][drop]" value="on" /> <?php _e("Remove plugins database tables and all data in them", "gd-rating-system"); ?>
        </label>
        <label>
            <input type="checkbox" class="widefat" name="gdrtstools[remove][truncate]" value="on" /> <?php _e("Remove all data from database tables", "gd-rating-system"); ?>
        </label><br/>
        <hr/>
        <p><?php _e("Database tables that will be affected", "gd-rating-system"); ?>:</p>
        <ul style="list-style: inside disc;">
            <li><?php echo gdrts_db()->items; ?></li>
            <li><?php echo gdrts_db()->itemmeta; ?></li>
            <li><?php echo gdrts_db()->logs; ?></li>
            <li><?php echo gdrts_db()->logmeta; ?></li>
        </ul>
    </div>
</div>

<div class="d4p-group d4p-group-tools d4p-group-reset">
    <h3><?php _e("Disable Plugin", "gd-rating-system"); ?></h3>
    <div class="d4p-group-inner">
        <label>
            <input type="checkbox" class="widefat" name="gdrtstools[remove][disable]" value="on" /> <?php _e("Disable plugin", "gd-rating-system"); ?>
        </label>
    </div>
</div>
