<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php
//http://nivo.dev7studios.com
wp_enqueue_script("nivoslider", InpostGallery::get_application_uri() . 'js/sliders/nivo/jquery.nivo.slider.pack.js', array('jquery'));
wp_enqueue_style("nivoslider", InpostGallery::get_application_uri() . 'js/sliders/nivo/nivo-slider.css');
wp_enqueue_style("nivoslider_theme", InpostGallery::get_application_uri() . 'js/sliders/nivo/themes/' . $skin . '/' . $skin . '.css');
//***
if(isset($show_in_popup) AND $show_in_popup):
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
    if(substr($post_id, 0, 1) == '{') {
        $slides = InpostGallery::get_posts_from_expression($post_id);
        $is_expression = true;
    } else {
        $slides = get_post_meta($post_id, 'inpost_gallery_data', true);
    }

    if(!$is_expression) {
        if(!is_array($slides) OR empty($slides)) {
            $slides = array();
        }

        $grouped = false;
        if(isset($group)) {
            if($group != 0) {//0 mean all
                $tmp_array = array();
                foreach($slides as $value) {
                    if($value['group'] == (int) $group) {
                        $tmp_array[] = $value;
                    }
                }
                $slides = $tmp_array;
                $grouped = true;
            }
        }

//***
        if(!$grouped) {
            if(isset($id) AND ! empty($id)) {
                $tmp_array = array();
                $ids_array = explode(',', $id);
                $counter = 1;
                foreach($slides as $value) {
                    if(in_array($counter, $ids_array)) {
                        $tmp_array[] = $value;
                    }
                    $counter++;
                }
                $slides = $tmp_array;
            }
        }

//*****
        if(isset($random)) {
            if($random != 0 AND ! empty($random)) {
                @shuffle($slides);
                if($random != -1) {
                    $slides = array_slice($slides, 0, $random);
                }
            }
        }
    }
    ?>

    <script type="text/javascript">
        jQuery(function() {
            jQuery('#slider_<?php echo $unique_id ?>').nivoSlider({
                animSpeed:<?php echo $transition_speed ?>, // Slide transition speed
                pauseTime:<?php echo $autoslide ?>, // How long each slide will show
                effect: '<?php echo $transition_effect; ?>', // Specify sets like: 'fold,fade,sliceDown'

                startSlide: <?php echo $start_slide ?>, // Set starting Slide (0 index)
                captionOpacity: 1,
                directionNav: <?php echo $direction_nav ?>, // Next & Prev navigation
                directionNavHide:<?php echo $direction_nav_hide ?>, // Only show on hover
                manualAdvance: false, // Force manual transitions
                keyboardNav: false, // Use left & right arrows


                slices: <?php echo $slices ?>, // For slice animations
                boxCols: <?php echo $box_cols ?>, // For box animations
                boxRows: <?php echo $box_rows ?>, // For box animations
                controlNav: <?php echo $control_nav ?>, // 1,2,3... navigation
                controlNavThumbs: <?php echo $control_nav_thumbs ?>, // Use thumbnails for Control Nav
                pauseOnHover: <?php echo $pause_on_hover ?>, // Stop animation while hovering
                prevText: '<?php _e('Prev', "inpost-gallery") ?>', // Prev directionNav text
                nextText: '<?php _e('Next', "inpost-gallery") ?>', // Next directionNav text
                randomStart: <?php echo $random_start ?>, // Start on a random slide
                beforeChange: function() {
                }, // Triggers before a slide transition
                afterChange: function() {
                }, // Triggers after a slide transition
                slideshowEnd: function() {
                }, // Triggers after all slides have been shown
                lastSlide: function() {
                }, // Triggers when last slide is shown
                afterLoad: function() {
                    jQuery(".slider-wrapper-<?php echo $unique_id ?>").show();
                } // Triggers when slider has loaded


            });
        });
    </script>


    <?php if(!empty($slides)): ?>

        <style type="text/css">
            .slider-wrapper-<?php echo $unique_id ?> a.nivo-control img{width: <?php echo $thumb_width ?>px !important;}
        </style>

        <div class="slider-wrapper theme-<?php echo $skin ?> slider-wrapper-<?php echo $unique_id ?>" style="width: <?php echo $slide_width ?>px; height:<?php echo $slide_height ?>px ;display: none;">

            <div id="slider_<?php echo $unique_id ?>" class="nivoSlider">
                <?php foreach($slides as $slide_num=> $slide) : ?>

                    <?php
                    $slide_url = InpostGallery::get_image($slide['imgurl'], $slide_width . 'x' . $slide_height);
                    $thumb_url = InpostGallery::get_image($slide['imgurl'], $thumb_width . 'x' . $thumb_height);
                    $description_title = "";
                    if($show_description) {
                        if(!empty($slide['title'])) {
                            $description_title = 'title="#htmlcaption_' . $slide_num . '_' . $unique_id . '"';
                        }
                    }
                    ?>

                    <img src="<?php echo $slide_url ?>" data-thumb="<?php echo $thumb_url ?>" alt="" <?php echo $description_title ?> />

                <?php endforeach; ?>
            </div>

            <?php if($show_description): ?>
                <?php foreach($slides as $slide_num=> $slide) : ?>
                    <?php if(!empty($slide['title'])): ?>
                        <div id="htmlcaption_<?php echo $slide_num ?>_<?php echo $unique_id ?>" class="nivo-html-caption"><?php echo $slide['title'] ?></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    <?php endif; ?>
<?php endif; ?>


