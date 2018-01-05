<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>
<div id="pn_shortcode_template" class="pn_shortcode_template clearfix">

    <h3>Camera <i>by Pixedelic.com</i></h3>

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
            'camera_amber_skin'=>'camera_amber_skin',
            'camera_ash_skin'=>'camera_ash_skin',
            'camera_azure_skin'=>'camera_azure_skin',
            'camera_beige_skin'=>'camera_beige_skin',
            'camera_black_skin'=>'camera_black_skin',
            'camera_blue_skin'=>'camera_blue_skin',
            'camera_brown_skin'=>'camera_brown_skin',
            'camera_burgundy_skin'=>'camera_burgundy_skin',
            'camera_charcoal_skin'=>'camera_charcoal_skin',
            'camera_chocolate_skin'=>'camera_chocolate_skin',
            'camera_coffee_skin'=>'camera_coffee_skin',
            'camera_cyan_skin'=>'camera_cyan_skin',
            'camera_fuchsia_skin'=>'camera_fuchsia_skin',
            'camera_gold_skin'=>'camera_gold_skin',
            'camera_green_skin'=>'camera_green_skin',
            'camera_grey_skin'=>'camera_grey_skin',
            'camera_indigo_skin'=>'camera_indigo_skin',
            'camera_khaki_skin'=>'camera_khaki_skin',
            'camera_lime_skin'=>'camera_lime_skin',
            'camera_magenta_skin'=>'camera_magenta_skin',
            'camera_maroon_skin'=>'camera_maroon_skin',
            'camera_orange_skin'=>'camera_orange_skin',
            'camera_olive_skin'=>'camera_olive_skin',
            'camera_pink_skin'=>'camera_pink_skin',
            'camera_pistachio_skin'=>'camera_pistachio_skin',
            'camera_pink_skin'=>'camera_pink_skin',
            'camera_red_skin'=>'camera_red_skin',
            'camera_tangerine_skin'=>'camera_tangerine_skin',
            'camera_turquoise_skin'=>'camera_turquoise_skin',
            'camera_violet_skin'=>'camera_violet_skin',
            'camera_white_skin'=>'camera_white_skin',
            'camera_yellow_skin'=>'camera_yellow_skin'
        );
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('Skin', 'inpost-gallery'),
            'shortcode_field'=>'skin',
            'id'=>'',
            'options'=>$skins,
            'default_value'=>InpostGallery::set_default_value('skin', 'camera_amber_skin'),
            'description'=>__('Slider skins', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">

        <?php
        $alignments = array(
            ''=>'',
            'topLeft'=>__('top Left', "inpost-gallery"),
            'topCenter'=>__('top Center', "inpost-gallery"),
            'topRight'=>__('top Right', "inpost-gallery"),
            'centerLeft'=>__('center Left', "inpost-gallery"),
            'center'=>__('center', "inpost-gallery"),
            'centerRight'=>__('center Right', "inpost-gallery"),
            'bottomLeft'=>__('bottom Left', "inpost-gallery"),
            'bottomCenter'=>__('bottom Center', "inpost-gallery"),
            'bottomRight'=>__('bottom Right', "inpost-gallery")
        );
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('Alignment', 'inpost-gallery'),
            'shortcode_field'=>'alignment',
            'id'=>'',
            'options'=>$alignments,
            'default_value'=>InpostGallery::set_default_value('alignment', ''),
            'description'=>__('Slider skins', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('Time', 'inpost-gallery'),
            'shortcode_field'=>'time',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('time', 7000),
            'description'=>__('milliseconds between the end of the sliding effect and the start of the nex one', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('transition Period', 'inpost-gallery'),
            'shortcode_field'=>'transition_period',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('transition_period', 1500),
            'description'=>__('length of the sliding effect in milliseconds', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">

        <?php
        $directions = array(
            'leftToRight'=>__('left To Right', "inpost-gallery"),
            'rightToLeft'=>__('right To Left', "inpost-gallery"),
            'topToBottom'=>__('top To Bottom', "inpost-gallery"),
            'bottomToTop'=>__('bottom To Top', "inpost-gallery")
        );
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('bar Direction', 'inpost-gallery'),
            'shortcode_field'=>'bar_direction',
            'id'=>'',
            'options'=>$directions,
            'default_value'=>InpostGallery::set_default_value('bar_direction', ''),
            'description'=>__('bar direction', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        $data_alignments = array(
            'topLeft'=>__('top Left', "slidermania"),
            'topCenter'=>__('top Center', "slidermania"),
            'topRight'=>__('top Right', "slidermania"),
            'centerLeft'=>__('center Left', "slidermania"),
            'center'=>__('center', "slidermania"),
            'centerRight'=>__('center Right', "slidermania"),
            'bottomLeft'=>__('bottom Left', "slidermania"),
            'bottomCenter'=>__('bottom Center', "slidermania"),
            'bottomRight'=>__('bottom Right', "slidermania")
        );
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('Data alignment', 'inpost-gallery'),
            'shortcode_field'=>'data_alignment',
            'id'=>'',
            'options'=>$data_alignments,
            'default_value'=>InpostGallery::set_default_value('data_alignment', ''),
            'description'=>__('Data alignment', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->




    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('Easing', 'inpost-gallery'),
            'shortcode_field'=>'easing',
            'id'=>'',
            'options'=>InpostGallery::$easing_effects,
            'default_value'=>InpostGallery::set_default_value('easing', 'swing'),
            'description'=>__('easing', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        $effects = array(
            'random'=>__('random', "slidermania"),
            'simpleFade'=>__('simple Fade', "slidermania"),
            'curtainTopLeft'=>__('curtain Top Left', "slidermania"),
            'curtainTopRight'=>__('curtain Top Right', "slidermania"),
            'curtainBottomLeft'=>__('curtain Bottom Left', "slidermania"),
            'curtainBottomRight'=>__('curtain Bottom Right', "slidermania"),
            'curtainSliceLeft'=>__('curtain Slice Left', "slidermania"),
            'curtainSliceRight'=>__('curtain Slice Right', "slidermania"),
            'blindCurtainTopLeft'=>__('blind Curtain Top Left', "slidermania"),
            'blindCurtainTopRight'=>__('blind Curtain Top Right', "slidermania"),
            'blindCurtainBottomLeft'=>__('blind Curtain Bottom Left', "slidermania"),
            'blindCurtainBottomRight'=>__('blind Curtain Bottom Right', "slidermania"),
            'blindCurtainSliceBottom'=>__('blind Curtain Slice Bottom', "slidermania"),
            'blindCurtainSliceTop'=>__('blind Curtain Slice Top', "slidermania"),
            'stampede'=>__('stampede', "slidermania"),
            'mosaic'=>__('mosaic', "slidermania"),
            'mosaicReverse'=>__('mosaic Reverse', "slidermania"),
            'mosaicRandom'=>__('mosaic Random', "slidermania"),
            'mosaicSpiral'=>__('mosaic Spiral', "slidermania"),
            'mosaicSpiralReverse'=>__('mosaic Spiral Reverse', "slidermania"),
            'topLeftBottomRight'=>__('top Left Bottom Right', "slidermania"),
            'bottomRightTopLeft'=>__('bottom Right Top Left', "slidermania"),
            'bottomLeftTopRight'=>__('bottom Left Top Right', "slidermania"),
            'bottomLeftTopRight'=>__('bottom Left Top Right', "slidermania"),
            'scrollLeft'=>__('scroll Left', "slidermania"),
            'scrollRight'=>__('scroll Right', "slidermania"),
            'scrollHorz'=>__('scroll Horz', "slidermania"),
            'scrollBottom'=>__('scroll Bottom', "slidermania"),
            'scrollTop'=>__('scroll Top', "slidermania")
        );
        //***
        InpostGallery::draw_shortcode_option(array(
            'type'=>'select',
            'title'=>__('Slide effects', 'inpost-gallery'),
            'shortcode_field'=>'slide_effects',
            'id'=>'',
            'options'=>$effects,
            'default_value'=>InpostGallery::set_default_value('slide_effects', 'swing'),
            'description'=>__('Slide effects', 'inpost-gallery'),
        ));
        ?>
    </div>


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'text',
            'title'=>__('grid Difference', 'inpost-gallery'),
            'shortcode_field'=>'grid_difference',
            'id'=>'',
            'default_value'=>InpostGallery::set_default_value('grid_difference', 250),
            'description'=>__('to make the grid blocks slower than the slices, this value must be smaller than transPeriod', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Thumbnails', 'inpost-gallery'),
            'shortcode_field'=>'thumbnails',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('thumbnails', 1),
            'description'=>__('To show thumbnails disable pagination', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Pagination', 'inpost-gallery'),
            'shortcode_field'=>'pagination',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('pagination', 0),
            'description'=>'',
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('auto Advance', 'inpost-gallery'),
            'shortcode_field'=>'auto_advance',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('auto_advance', 1),
            'description'=>'',
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('Hover', 'inpost-gallery'),
            'shortcode_field'=>'hover',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('hover', 1),
            'description'=>__('Pause on state hover. Not available for mobile devices', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('play Pause buttons', 'inpost-gallery'),
            'shortcode_field'=>'play_pause_buttons',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('play_pause_buttons', 1),
            'description'=>__('To display or not the play/pause buttons', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type'=>'checkbox',
            'title'=>__('pause On Click', 'inpost-gallery'),
            'shortcode_field'=>'pause_on_click',
            'id'=>'',
            'is_checked'=>InpostGallery::set_default_value('pause_on_click', 1),
            'description'=>__('It stops the slideshow when you click the sliders', 'inpost-gallery'),
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
            'default_value'=>'pixedelic_camera',
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

