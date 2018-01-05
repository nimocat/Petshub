<?php if(!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
wp_enqueue_script('inpost_gallery_galleria', InpostGallery::get_application_uri() . 'js/sliders/galleria/galleria-1.2.9.min.js', array('jquery'));
wp_enqueue_script('inpost_gallery_galleria_fs1', InpostGallery::get_application_uri() . 'js/sliders/galleria/themes/fullscreen/galleria-fs-theme.js', array('jquery'));
wp_enqueue_script('inpost_gallery_galleria_fs2', InpostGallery::get_application_uri() . 'js/sliders/galleria/themes/fullscreen/galleria.fullscreen.js', array('jquery'));
wp_enqueue_style("inpost_gallery_galleria", InpostGallery::get_application_uri() . 'js/sliders/galleria/themes/fullscreen/galleria.fullscreen.css');
?>
<script type="text/javascript">

//+++GALLERIA OPTIONS
    var fsg_json = {};
    var fullscreen_galleria_postid = "";
    var fullscreen_galleria_attachment = true;
    var fsg_photobox = {};
    var fsg_last_post_id = "";
    var fsg_settings = {
        "transition": "slide",
        "overlay_time": "3000",
        "show_title": true,
        "auto_start_slideshow": false,
        "show_description": true,
        "show_camera_info": true,
        "show_thumbnails": true,
        "show_permalink": false,
        "show_attachment": true,
        "show_caption": false,
        "show_sharing": false,
        "image_nav": false,
        "true_fullscreen": false,
        "w3tc": false,
        "load_in_header": true
    };

</script>
<?php
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
//***
    $unique_id = uniqid();
    if(!isset($thumb_margin_bottom)) {
        $thumb_margin_bottom = 0;
    }
    if(!isset($thumb_margin_left)) {
        $thumb_margin_left = 3;
    }
    $styles_img = "margin: 0 0 " . $thumb_margin_bottom . "px " . $thumb_margin_left . "px !important;";
//****
    if(isset($thumb_border_radius)) {
        if(!empty($thumb_border_radius)) {
            $styles_img.= "border-radius:" . $thumb_border_radius . 'px !important;';
        }
    }

    if(isset($thumb_shadow)) {
        if(!empty($thumb_shadow)) {
            $styles_img.= "box-shadow:" . $thumb_shadow . ' !important;';
        }
    }


//***
    if(!empty($border)) {
        $styles_img.="border: " . $border . " !important;";
    }
    $counter = 0;
    ?>

    <?php if(!empty($slides)): ?>
        <div id="galleria"></div>
        <?php $counter = 0; ?>
        <?php foreach($slides as $gallery_item) : ?>
            <?php
            $slide_url = $gallery_item['imgurl'];
            $thumb_url = InpostGallery::get_image($gallery_item['imgurl'], $thumb_width . 'x' . $thumb_height);
            ?>
        <a target="_blank" title="<?php echo $gallery_item['title'] ?>" data-postid='fsg_group_<?php echo $unique_id ?>' data-imgid='<?php echo ($counter++) ?>' href='<?php echo $slide_url ?>'><img src="<?php echo $thumb_url ?>" style="<?php echo $styles_img ?>" alt="<?php echo $gallery_item['title'] ?>" /></a>
        <?php endforeach; ?>



        <script type="text/javascript">

            jQuery(function() {
                //+++
                fsg_json.fsg_group_<?php echo $unique_id ?> = [
        <?php $counter = 0; ?>
        <?php foreach($slides as $gallery_item) : ?>
            <?php
            $slide_url = $gallery_item['imgurl'];
            $thumb_url = InpostGallery::get_image($gallery_item['imgurl'], '145x145');
            ?>
                    {id: <?php echo ($counter++) ?>, image: '<?php echo $slide_url ?>', thumb: '<?php echo $thumb_url ?>', permalink: '', layer: '<?php if(!empty($gallery_item['title'])): ?><div class="galleria-infolayer"><div class="galleria-layeritem" style="padding-right: 20px;"><p class="galleria-info-description"><?php echo $gallery_item['title'] ?></p></div><?php endif; ?>'},
        <?php endforeach; ?>

                        ];
                    });
        </script>
    <?php endif; ?>
<?php endif; ?>


