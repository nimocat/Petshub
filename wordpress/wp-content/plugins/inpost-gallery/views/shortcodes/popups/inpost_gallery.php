<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div id="pn_shortcode_template" class="pn_shortcode_template clearfix">

    <h3>YoxView</h3>

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Thumb Width', 'inpost-gallery'),
            'shortcode_field' => 'thumb_width',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('thumb_width', InpostGallery::$settings['thumb_default_width']),
            'description' => ''
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Thumb Height', 'inpost-gallery'),
            'shortcode_field' => 'thumb_height',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('thumb_height', InpostGallery::$settings['thumb_default_height']),
            'description' => ''
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Post id', 'inpost-gallery'),
            'shortcode_field' => 'post_id',
            'id' => '',
            'help_button' => 'pn_post_id_help',
            'default_value' => InpostGallery::set_default_value('post_id', $post_id),
            'description' => __('ID of post with images. Current post ID is by default. For example you want to keep images in only one custom post type, but use everywhere!', 'inpost-gallery')
        ));
        ?>
    </div>
    
    
    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Thumb Margin Left', 'inpost-gallery'),
            'shortcode_field' => 'thumb_margin_left',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('thumb_margin_left', 0),
            'description' => __('px', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Thumb Margin Bottom', 'inpost-gallery'),
            'shortcode_field' => 'thumb_margin_bottom',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('thumb_margin_bottom', 0),
            'description' => __('px', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Thumb Border Radius', 'inpost-gallery'),
            'shortcode_field' => 'thumb_border_radius',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('thumb_border_radius', 2),
            'description' => __('px', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Thumb Shadow', 'inpost-gallery'),
            'shortcode_field' => 'thumb_shadow',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('thumb_shadow', '0 1px 4px rgba(0, 0, 0, 0.2)'),
            'description' => __('Use CSS rule: 0 8px 4px rgba(0, 0, 0, 0.5)', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Play Delay', 'inpost-gallery'),
            'shortcode_field' => 'js_play_delay',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('js_play_delay', 3000),
            'description' => __('Time of delay while playing. (ms)', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <hr />
    

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('ID\'s', 'inpost-gallery'),
            'shortcode_field' => 'id',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('id', ''),
            'description' => __('id=2,5,9,3 - out slides whith number (white number on thumb in admin). Works if group=0', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Random', 'inpost-gallery'),
            'shortcode_field' => 'random',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('random', 0),
            'description' => __('random=-1 - random slides and out all. random=5 - shuffle slides and out 5 of them.', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">

        <?php
        $groups = range(0, InpostGallery::$settings['max_thumb_groups']);
        InpostGallery::draw_shortcode_option(array(
            'type' => 'select',
            'title' => __('Group', 'inpost-gallery'),
            'shortcode_field' => 'group',
            'id' => '',
            'options' => $groups,
            'default_value' => InpostGallery::set_default_value('group', 0),
            'description' => __('You can categorize gallery images by groups', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Border', 'inpost-gallery'),
            'shortcode_field' => 'border',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('border', ''),
            'description' => __('set border around thumbnail image. Use CSS rule: solid 1px #fff', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->






    <div style="display: none;">       


        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('type', 'inpost-gallery'),
            'shortcode_field' => 'type',
            'id' => '',
            'default_value' => 'yoxview',
            'description' => '',
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

</div>

<?php include InpostGallery::get_application_path() . 'views/shortcode_help_texts.php'; ?>
<!-- --------------------------  PROCESSOR  --------------------------- -->
<script type="text/javascript">
    var shortcode_name = "<?php echo basename(__FILE__, '.php'); ?>";
    jQuery(function() {
        pn_ext_shortcodes.changer(shortcode_name);
        jQuery('#pn_ext_shortcode_text .pn_ext_shortcode_text').html(pn_ext_shortcodes.get_html_from_buffer());
        jQuery("#pn_shortcode_template .js_shortcode_template_changer").on('keyup change', function() {
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

