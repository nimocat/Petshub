<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>
<div id="pn_shortcode_template" class="pn_shortcode_template clearfix">

    <h3>Nivo Slider <i>by dev7studios.com</i></h3>

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Slide Width', 'inpost-gallery'),
            'shortcode_field'=>'slide_width',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('slide_width', 800),
            'description'=>''
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Slide Height', 'inpost-gallery'),
            'shortcode_field'=>'slide_height',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('slide_height', 600),
            'description'=>''
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Thumb Width', 'inpost-gallery'),
            'shortcode_field'=>'thumb_width',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('thumb_width', 75),
            'description'=>''
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Thumb Height', 'inpost-gallery'),
            'shortcode_field'=>'thumb_height',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('thumb_height', 75),
            'description'=>''
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Post id', 'inpost-gallery'),
            'shortcode_field'=>'post_id',
            'id'=>'',
            'help_button'=>'pn_post_id_help',
            'default_value'=>InpostGallery::set_default_value('post_id', $post_id),
            'description'=>__('ID of post with images. Current post ID is by default. For example you want to keep images in only one custom post type, but use everywhere!', 'inpost-gallery')
        ));
        ?>
    </div>


    <div class="one-half">

        <?php
        $skins = array(
            'light'=>__('light', "inpost-gallery"),
            'default'=>__('default', "inpost-gallery"),
            'dark'=>__('dark', "inpost-gallery"),
            'bar'=>__('bar', "inpost-gallery"),
        );
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('Skin', 'inpost-gallery'),
            'shortcode_field'=>'skin',
            'id'=>'',
            'options'=>$skins,
            'default_value'=>InpostGallery::set_default_value('skin', 'light'),
            'description'=>__('Skins', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        $transition_effects = array(
            'random'=>__('Random', "inpost-gallery"),
            'fold'=>__('Fold', "inpost-gallery"),
            'fade'=>__('Fade', "inpost-gallery"),
            'sliceDown'=>__('Slice Down', "inpost-gallery"),
            'sliceDownLeft'=>__('Slice Down Left', "inpost-gallery"),
            'sliceUpDown'=>__('Slice Up Down', "inpost-gallery"),
            'sliceUpDownLeft'=>__('Slice Up Down Left', "inpost-gallery"),
            'sliceUp'=>__('Slice Up', "inpost-gallery"),
            'sliceUpLeft'=>__('Slice Up Left', "inpost-gallery"),
            'slideInLeft'=>__('Slide In Left', "inpost-gallery"),
            'slideInRight'=>__('Slide In Right', "inpost-gallery"),
            'boxRandom'=>__('Box Random', "inpost-gallery"),
            'boxRain'=>__('Box Rain', "inpost-gallery"),
            'boxRainReverse'=>__('Box Rain Reverse', "inpost-gallery"),
            'boxRainGrow'=>__('Box Rain Grow', "inpost-gallery"),
            'boxRainGrowReverse'=>__('Box Rain Grow Reverse', "inpost-gallery"),
        );
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('Slide transition effect', 'inpost-gallery'),
            'shortcode_field'=>'transition_effect',
            'id'=>'',
            'options'=>$transition_effects,
            'default_value'=>InpostGallery::set_default_value('transition_effect', 'random'),
            'description'=>__('Slide transition effect', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Transition speed', 'inpost-gallery'),
            'shortcode_field'=>'transition_speed',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('transition_speed', 600),
            'description'=>__('Enter in the speed of slides transition in milliseconds.', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Autoslide', 'inpost-gallery'),
            'shortcode_field'=>'autoslide',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('autoslide', 5000),
            'description'=>__('Enter in the amount of milliseconds before the next transition or leave blank to disable auto slide.', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Show control navigation', 'inpost-gallery'),
            'shortcode_field'=>'control_nav',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('control_nav', 1),
            'description'=>__('1,2,3... navigation', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Use thumbnails for Control Nav', 'inpost-gallery'),
            'shortcode_field'=>'control_nav_thumbs',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('control_nav_thumbs', 0),
            'description'=>__('Use thumbnails for Control Nav', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Show navigation', 'inpost-gallery'),
            'shortcode_field'=>'direction_nav',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('direction_nav', 1),
            'description'=>__('Next &amp; Prev navigation', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Show navigation on hover only', 'inpost-gallery'),
            'shortcode_field'=>'direction_nav_hide',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('direction_nav_hide', 0),
            'description'=>__('Next &amp; Prev navigation only show on hover', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Show control navigation', 'inpost-gallery'),
            'shortcode_field'=>'controlNavThumbs',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('controlNavThumbs', 0),
            'description'=>__('Use thumbnails for Control Nav', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Random start', 'inpost-gallery'),
            'shortcode_field'=>'random_start',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('random_start', 0),
            'description'=>__('Start on a random slide', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Pause on hover', 'inpost-gallery'),
            'shortcode_field'=>'pause_on_hover',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('pause_on_hover', 1),
            'description'=>__('Pause on hover', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Show description', 'inpost-gallery'),
            'shortcode_field'=>'show_description',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('show_description', 1),
            'description'=>__('Show/hide slide description', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Box rows', 'inpost-gallery'),
            'shortcode_field'=>'box_rows',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('box_rows', 4),
            'description'=>__('For box animations', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Box cols', 'inpost-gallery'),
            'shortcode_field'=>'box_cols',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('box_cols', 8),
            'description'=>__('For box animations', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Slices', 'inpost-gallery'),
            'shortcode_field'=>'slices',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('slices', 15),
            'description'=>__('For box animations', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Starting Slide', 'inpost-gallery'),
            'shortcode_field'=>'start_slide',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('start_slide', 0),
            'description'=>__('Set starting Slide (0 index)', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->







    <hr />






    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('ID\'s', 'inpost-gallery'),
            'shortcode_field'=>'id',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('id', ''),
            'description'=>__('id=2,5,9,3 - out slides whith number (white number on thumb in admin). Works if group=0', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Random', 'inpost-gallery'),
            'shortcode_field'=>'random',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('random', 0),
            'description'=>__('random=-1 - random slides and out all. random=5 - shuffle slides and out 5 of them.', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        $groups = range(0, InpostGallery::$settings['max_thumb_groups']);
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('Group', 'inpost-gallery'),
            'shortcode_field'=>'group',
            'id'=>'',
            'options'=>$groups,
            'default_value'=>InpostGallery::set_default_value('group', 0),
            'description'=>__('You can categorize gallery images by groups', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->

    <hr />

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Show in popup', 'inpost-gallery'),
            'shortcode_field'=>'show_in_popup',
            'id'=>'pn_show_in_popup',
            'is_checked'=>InpostGallery::set_default_value('show_in_popup', 0),
            'description'=>__('Album grid layout with separate single image as cover and on click load all images inside individual popup', 'inpost-gallery'),
        ));

        $is_show_in_popup = InpostGallery::set_default_value('show_in_popup', 0);
        ?>

    </div><!--/ .one-half-->


    <div class="one-half pn_album_cover" <?php if(!$is_show_in_popup): ?>style="display: none;"<?php endif; ?>>
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'upload',
            'title'=>__('Album cover', 'inpost-gallery'),
            'shortcode_field'=>'album_cover',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('album_cover', ''),
            'description'=>''
        ));
        ?>
    </div>

    <div class="one-half pn_album_cover" <?php if(!$is_show_in_popup): ?>style="display: none;"<?php endif; ?>>
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Album cover Width', 'inpost-gallery'),
            'shortcode_field'=>'album_cover_width',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('album_cover_width', InpostGallery::$settings['thumb_default_width']),
            'description'=>''
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half pn_album_cover" <?php if(!$is_show_in_popup): ?>style="display: none;"<?php endif; ?>>
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Album cover Height', 'inpost-gallery'),
            'shortcode_field'=>'album_cover_height',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('album_cover_height', InpostGallery::$settings['thumb_default_height']),
            'description'=>''
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half pn_album_cover" <?php if(!$is_show_in_popup): ?>style="display: none;"<?php endif; ?>>
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Popup width', 'inpost-gallery'),
            'shortcode_field'=>'popup_width',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('popup_width', 800),
            'description'=>'',
        ));
        ?>

    </div><!--/ .one-half-->


     <div class="one-half pn_album_cover" <?php if(!$is_show_in_popup): ?>style="display: none;"<?php endif; ?>>
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Popup max height', 'inpost-gallery'),
            'shortcode_field'=>'popup_max_height',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('popup_max_height', 600),
            'description'=>'',
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="full-width pn_album_cover" <?php if(!$is_show_in_popup): ?>style="display: none;"<?php endif; ?>>
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Popup title', 'inpost-gallery'),
            'shortcode_field'=>'popup_title',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('popup_title', 'Gallery'),
            'description'=>'',
        ));
        ?>

    </div><!--/ .one-half-->


    <hr />

    <div class="full-width" id="pn_ext_shortcode_text">
        <h4 for="" class="label"><?php _e('Shortcode text', 'inpost-gallery') ?></h4>
        <div class="pn_ext_shortcode_text"></div>
        <i style="font-size: 10px;"><?php _e('Quickly click 3 times on shortcode text to select its.', 'inpost-gallery') ?></i>
    </div><!--/ .one-half-->




    <div style="display: none;">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('type', 'inpost-gallery'),
            'shortcode_field'=>'type',
            'id'=>'',
            'default_value'=>'nivo',
            'description'=>'',
        ));
        ?>

    </div><!--/ .one-half-->

</div>

<?php include InpostGallery::get_application_path() . 'views/shortcode_help_texts.php'; ?>
<!-- --------------------------  PROCESSOR  --------------------------- -->
<script type="text/javascript">
    var shortcode_name = "<?php echo basename(__FILE__, '.php'); ?>";
    jQuery(function() {
        pn_ext_shortcodes.changer(shortcode_name);
        jQuery('#pn_ext_shortcode_text .pn_ext_shortcode_text').html(pn_ext_shortcodes.get_html_from_buffer());
        jQuery("#pn_shortcode_template .js_shortcode_template_changer").life('keyup change', function() {
            jQuery('#pn_ext_shortcode_text .pn_ext_shortcode_text').html(pn_ext_shortcodes.changer(shortcode_name));
        });
        //+++
        jQuery('#pn_show_in_popup').life('click', function() {
            if (jQuery(this).is(":checked")) {
                jQuery('.pn_album_cover').show(200);
            } else {
                jQuery('.pn_album_cover').hide(200);
            }
        });
    });
</script>

