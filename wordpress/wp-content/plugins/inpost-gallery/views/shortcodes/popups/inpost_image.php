<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div id="pn_shortcode_template" class="pn_shortcode_template clearfix">

    <h3>Simply Image</h3>

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'upload',
            'title' => __('Image', 'inpost-gallery'),
            'shortcode_field' => 'content',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('content', ''),
            'description' => ''
        ));
        ?>
    </div>


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'checkbox',
            'title' => __('Open in light box', 'inpost-gallery'),
            'shortcode_field' => 'open_in_lightbox',
            'id' => '',
            'is_checked' => InpostGallery::set_default_value('open_in_lightbox', 1),
            'description' => __('Open in light box by click', 'inpost-gallery')
        ));
        ?>
    </div>
    
    
    <div class="one-half">

        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'select',
            'title' => __('Align', 'inpost-gallery'),
            'shortcode_field' => 'align',
            'id' => '',
            'options' => array(
                'left'=>__('Left', 'inpost-gallery'),
                'right'=>__('Right', 'inpost-gallery'),
                'none'=>__('None', 'inpost-gallery'),
            ),
            'default_value' => InpostGallery::set_default_value('align', 'left'),
            'description' => __('Image align', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Image Width', 'inpost-gallery'),
            'shortcode_field' => 'width',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('width', 300),
            'description' => ''
        ));
        ?>

    </div><!--/ .one-half-->

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Image Height', 'inpost-gallery'),
            'shortcode_field' => 'height',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('height', 300),
            'description' => ''
        ));
        ?>

    </div><!--/ .one-half-->

    

    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Short description', 'inpost-gallery'),
            'shortcode_field' => 'description',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('description', ''),
            'description' => '',
        ));
        ?>

    </div><!--/ .one-half-->


    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Image Margin Bottom', 'inpost-gallery'),
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
            'title' => __('Image Margin Right', 'inpost-gallery'),
            'shortcode_field' => 'thumb_margin_right',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('thumb_margin_right', 0),
            'description' => __('px', 'inpost-gallery'),
        ));
        ?>

    </div><!--/ .one-half-->



    <div class="one-half">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'text',
            'title' => __('Image Margin Left', 'inpost-gallery'),
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
            'title' => __('Border', 'inpost-gallery'),
            'shortcode_field' => 'border',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('border', ''),
            'description' => __('set border around thumbnail image. Use CSS rule: solid 1px #fff', 'inpost-gallery'),
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
    
    
    <div class="full-width">
        <?php
        InpostGallery::draw_shortcode_option(array(
            'type' => 'textarea',
            'title' => __('Description', 'inpost-gallery'),
            'shortcode_field' => 'description',
            'id' => '',
            'default_value' => InpostGallery::set_default_value('description', ''),
            'description' => __('Write any description for the image if you want. It will appear when user click on it.', 'inpost-gallery'),
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
        jQuery("#pn_shortcode_template .js_shortcode_template_changer").on('click change keyup', function() {
            jQuery('#pn_ext_shortcode_text .pn_ext_shortcode_text').html(pn_ext_shortcodes.changer(shortcode_name));
        });
    });
</script>

