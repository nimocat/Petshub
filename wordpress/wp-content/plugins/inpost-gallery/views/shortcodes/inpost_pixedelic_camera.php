<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php
//http://www.pixedelic.com/plugins/camera/
wp_enqueue_script("easing", InpostGallery::get_application_uri() . 'js/jquery.easing.1.3.min.js', array('jquery'));
wp_enqueue_script("inpost_gallery_pixedelic_camera", InpostGallery::get_application_uri() . 'js/sliders/pixedelic_camera/camera.min.js', array('jquery'));
//wp_enqueue_script("inpost_gallery_pixedelic_camera_mob", InpostGallery::get_application_uri() . 'js/sliders/pixedelic_camera/jquery.mobile.customized.min.js', array('jquery'));
wp_enqueue_style("inpost_gallery_pixedelic_camera", InpostGallery::get_application_uri() . 'js/sliders/pixedelic_camera/css/styles.css');
//***
if (isset($show_in_popup) AND $show_in_popup):
    ?>
    <?php
    wp_enqueue_script('pn_ext_shortcodes_popup_js', self::get_application_uri() . 'js/pn_popup/pn_advanced_wp_popup.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable'));
    wp_enqueue_style('pn_ext_shortcodes_popup_css', self::get_application_uri() . 'js/pn_popup/styles_front.css');
    wp_enqueue_style('pn_inpost_front', self::get_application_uri() . 'css/front.css');
    wp_enqueue_script('pn_inpost_front', self::get_application_uri() . 'js/front.js', array('jquery'));
    ?>
    <a href="#" class="pn_inpost_gallery_in_popup" data-popup-title="<?php echo $popup_title ?>" data-popup-width="<?php echo $popup_width ?>" data-popup-max-height="<?php echo $popup_max_height ?>" data-shortcode-key="<?php echo $shortcode_key ?>" data-shortcode-attributes='<?php echo base64_encode(json_encode($attributes)); ?>'><img src="<?php echo InpostGallery::get_image($album_cover, $album_cover_width . 'x' . $album_cover_height) ?>" /></a>
<?php else: ?>


    <?php
    $unique_id = uniqid();
    $is_expression = false;
    //***
    if(isset($post_id)){
        $post_id=trim($post_id);
    }else{
        global $post;
        if(is_object($post)){
            $post_id=$post->ID;
        }
    }
    //***
    if (substr($post_id, 0, 1) == '{')
    {
        $slides = InpostGallery::get_posts_from_expression($post_id);
        $is_expression = true;
    } else
    {
        $slides = get_post_meta($post_id, 'inpost_gallery_data', true);
    }

    if (!$is_expression)
    {
        if (!is_array($slides) OR empty($slides))
        {
            $slides = array();
        }

        $grouped = false;
        if (isset($group))
        {
            if ($group != 0)
            {//0 mean all
                $tmp_array = array();
                foreach ($slides as $value)
                {
                    if ($value['group'] == (int) $group)
                    {
                        $tmp_array[] = $value;
                    }
                }
                $slides = $tmp_array;
                $grouped = true;
            }
        }

//***
        if (!$grouped)
        {
            if (isset($id) AND ! empty($id))
            {
                $tmp_array = array();
                $ids_array = explode(',', $id);
                $counter = 1;
                foreach ($slides as $value)
                {
                    if (in_array($counter, $ids_array))
                    {
                        $tmp_array[] = $value;
                    }
                    $counter++;
                }
                $slides = $tmp_array;
            }
        }

//*****
        if (isset($random))
        {
            if ($random != 0 AND ! empty($random))
            {
                @shuffle($slides);
                if ($random != -1)
                {
                    $slides = array_slice($slides, 0, $random);
                }
            }
        }
    }

//print_r($slides);
    ?>

    <script type="text/javascript">
        jQuery(function () {
            try {
                jQuery('#slider_<?php echo $unique_id ?>').camera({
                    alignment: '<?php echo $alignment; ?>',
                    autoAdvance:<?php echo $auto_advance; ?>,
                    thumbnails:<?php echo $thumbnails; ?>,
                    time:<?php echo $time; ?>,
                    transPeriod:<?php echo $transition_period; ?>,
                    barDirection: '<?php echo $bar_direction; ?>',
                    easing: '<?php echo $easing; ?>',
                    gridDifference:<?php echo $grid_difference; ?>,
                    hover:<?php echo $hover; ?>,
                    //loaderColor: '',
                    //loaderBgColor: '',
                    loaderOpacity: true,
                    loaderPadding: true,
                    navigation: true,
                    navigationHover: true,
                    opacityOnGrid: true,
                    overlayer: true,
                    pagination: <?php echo $pagination ?>,
                    playPause:<?php echo $play_pause_buttons ?>,
                    pauseOnClick:<?php echo $pause_on_click; ?>,
                    height: 'auto',
                    onStartLoading: function () {
                    },
                    onLoaded: function () {
                    },
                    onStartTransition: function () {
                    },
                    onEndTransition: function () {
                    }
                });
            } catch (e) {
                console.log(e);
            }
        });
    </script>
    <style type="text/css">
        #slider_<?php echo $unique_id ?> .camera_thumbs_cont{
            max-height:<?php echo $thumb_height + ($thumb_height * 0.21) ?>px;
        }
    </style>

            <?php if (!empty($slides)): ?>

        <div class="camera_wrap <?php echo $skin; ?>" id="slider_<?php echo $unique_id ?>" style="width: <?php echo $slide_width ?>px; height: <?php echo $slide_height ?>px;">
        <?php foreach ($slides as $slide_num => $slide) : ?>

                <?php
                $thumb_url = InpostGallery::get_image($slide['imgurl'], $thumb_width . 'x' . $thumb_height);
                $slide_url = InpostGallery::get_image($slide['imgurl'], $slide_width . 'x' . $slide_height);
                if (!isset($slide_effects))
                {
                    $slide_effects = "random";
                }
                ?>

                <div data-thumb="<?php echo $thumb_url ?>" data-src="<?php echo $slide_url ?>" data-alignment="<?php echo $slide['data_alignment'] ?>" data-fx="<?php echo $slide_effects ?>">

            <?php if (!empty($slide['title'])): ?>
                        <div class="camera_caption"><?php echo $slide['title'] ?></div>
            <?php endif; ?>

                </div>

        <?php endforeach; ?>
        </div>


    <?php endif; ?>
    <div style="clear: both;"></div>

<?php endif; ?>
