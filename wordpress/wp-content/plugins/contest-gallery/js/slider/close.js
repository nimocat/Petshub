

cgJsClass.slider.close = {
    init:function (){

        if(cgJsClass.slider.vars.isMobile==true){

            if(cgJsClass.slider.vars.noViewport==true){
                // Den vorherigen Viewport der gesetzt wurde entfernen
                jQuery("head meta[name=viewport]").remove();
            }
            else{
                // Den ursprünglichen auf der Seite vorhanden viewPort wieder setzten!!!
                var x = document.getElementsByName("viewport");
                x[0].setAttribute('content',cgJsClass.slider.vars.viewportContent);
            }

        }

        jQuery("html").css("overflow","auto");

        //Erstmal das letzt genutzte Slider Facebook Like Iframe reloaden
        if(cgJsClass.slider.vars.cg_FbLikeGallery==1){

            var cg_fb_reload = jQuery("#cg_fb_reload"+jQuery("#cg_actual_slider_img_id").val()+"").val();

            if(cg_fb_reload=="413"){
                jQuery(window).load(function(){
                    var fbFrameDiv = document.getElementById("cg_fb_like_iframe_gallery"+jQuery("#cg_actual_slider_img_id").val()+"").contentWindow;
                    fbFrameDiv.postMessage("reload","*");
                    jQuery("#cg_slider_frame_reloaded").val(1);
                });

            }
            else{
                jQuery(window).load(function(){
                    //    alert(".cg_fb_like_iframe_gallery_order"+jQuery("#cg_actual_slider_img_id").val()+"");
                    //var cg_gallery_iframe_id = jQuery(".cg_fb_like_iframe_gallery_order"+jQuery("#cg_actual_slider_img_id").val()+"").attr("id");
                    var cg_gallery_iframe_id = "cg_fb_like_iframe_gallery"+jQuery("#cg_actual_slider_img_id").val()+"";
                    //alert(cg_gallery_iframe_id);
                    document.getElementById(cg_gallery_iframe_id).contentWindow.location.reload(true);
                    //document.getElementById("cg_fb_like_iframe_gallery"+jQuery("#cg_actual_slider_img_id").val()+"").contentWindow.location.reload;
                    jQuery("#cg_slider_frame_reloaded").val(1);
                });
            }

        }


// Hash soll entfernt werden beim schließen des Sldiers
        history.pushState("", document.title, window.location.pathname);




        // Werte der gevoteten Bilder sollen sich ändern
        // Plz vote language platzhalter
        var cg_plz_vote = jQuery('#cg_plz_vote').val();


        var r=0;

        jQuery( ".cg_show" ).each(function( i ) {

            r++;

            //eventuell alles nach diesem Height anpassen?
            //  alert(heightCGimgBox);

            var starOffUrl = jQuery("#cg_star_off_slider").val();

            var cg_pngCommentsIconImg = jQuery('#cg_pngCommentsIconImg').val();
            // alert(cg_pngCommentsIconImg);
            var realId = jQuery(this).find('.realId').val();
            var srcOriginalImg = jQuery(this).find('.srcOriginalImg').val();
            // alert(srcOriginalImg);
            var src1024width = jQuery(this).find('.src1024width').val();
            var src624width = jQuery(this).find('.src624width').val();
            var src300width = jQuery(this).find('.src300width').val();
            //  var hrefCGpic = jQuery(this).find('.hrefCGpic').val();

            var width = jQuery(this).find(".widthOriginalImg").val();
            var height = jQuery(this).find(".heightOriginalImg").val();

            var ratingImage = jQuery(this).find(".averageStarsRounded").val();
            var countRatingQuantity = jQuery(this).find("#countRatingQuantity"+realId+"").val();
            var countRatingSQuantity = jQuery(this).find("#countRatingSQuantity"+realId+"").val();
            var countCommentsQuantity = jQuery(this).find("#countCommentsQuantity"+realId+"").val();

            //Prüf ob wenn Hide Until Vote an ist schon gewotet wurde oder nicht
            //var cg_check_voted = jQuery(this).find(".cg_hide").find(".cg_check_voted").val();
            var cg_check_voted = jQuery("#cg_check_voted"+realId).val();
            //alert(cg_check_voted);

            var cgIMGsourceRelation = width/height;

            //	alert("cgIMGsourceRelation: "+cgIMGsourceRelation);
            //	alert("cgIMGcontainerRelation: "+cgIMGcontainerRelation);


            // Rating bestimmen

            // Cursor Style bestimmen, je nach dem ob es erlaubt ist aus der Gallerie zu voten oder nicht
            var cg_vote_in_gallery = jQuery("#cg_vote_in_gallery").val();

            if(cg_vote_in_gallery==1){
                var cg_ratingStar = "cg_rate_star"+realId+"";
                var cg_ratingStarCursorStyle = "cursor:pointer;";
            }
            else{
                var cg_ratingStar = "";
                var cg_ratingStarCursorStyle = "cursor:default;";
            }

            if(cgJsClass.slider.vars.cg_allow_rating==1){


                if(cg_check_voted==1 || cg_check_voted==2){
                    if(ratingImage>=1){var star1url = cgJsClass.slider.vars.starOnUrl;}
                    else{var star1url = cgJsClass.slider.vars.starOffUrl;}
                    if(ratingImage>=2){var star2url = cgJsClass.slider.vars.starOnUrl;}
                    else{var star2url = cgJsClass.slider.vars.starOffUrl;}
                    if(ratingImage>=3){var star3url = cgJsClass.slider.vars.starOnUrl;}
                    else{var star3url = cgJsClass.slider.vars.starOffUrl;}
                    if(ratingImage>=4){var star4url = cgJsClass.slider.vars.starOnUrl;}
                    else{var star4url = cgJsClass.slider.vars.starOffUrl;}
                    if(ratingImage>=5){var star5url = cgJsClass.slider.vars.starOnUrl;}
                    else{var star5url = cgJsClass.slider.vars.starOffUrl;}


                    // Cursor Style bestimmen, je nach dem ob es erlaubt ist aus der Gallerie zu voten oder nicht --- ENDE

                    var ratingBlock = '<div class="cg_gallery_rating_div_child" id="cg_gallery_rating_div_child'+realId+'"><input type="hidden" class="cg_check_voted" id="cg_check_voted'+realId+'" value="1">'+
                        "<div class='cg_gallery_rating_div_star'><img src='"+star1url+"' class='cg_slider_star1' style='float:left;"+cg_ratingStarCursorStyle+"' alt='1' id='"+cg_ratingStar+"'></div>"+
                        "<div class='cg_gallery_rating_div_star'><img src='"+star2url+"' class='cg_slider_star2' style='float:left;"+cg_ratingStarCursorStyle+"' alt='2' id='"+cg_ratingStar+"'></div>"+
                        "<div class='cg_gallery_rating_div_star'><img src='"+star3url+"' class='cg_slider_star3' style='float:left;"+cg_ratingStarCursorStyle+"' alt='3' id='"+cg_ratingStar+"'></div>"+
                        "<div class='cg_gallery_rating_div_star'><img src='"+star4url+"' class='cg_slider_star4' style='float:left;"+cg_ratingStarCursorStyle+"' alt='4' id='"+cg_ratingStar+"'></div>"+
                        "<div class='cg_gallery_rating_div_star cg_gallery_rating_image_five_star_last_child'><img src='"+star5url+"' class='cg_slider_star5' style='"+cg_ratingStarCursorStyle+"' alt='5' id='"+cg_ratingStar+"'></div>"+
                        "<div class='rating_cg cg_gallery_rating_div_count'>"+countRatingQuantity+"</div></div>";

                    var ratingDivStyle = "";

                }

                else if(cg_check_voted==0){
                    var ratingBlock = '<div class="cg_gallery_rating_div_child" id="cg_gallery_rating_div_child'+realId+'"><input type="hidden" class="cg_check_voted" id="cg_check_voted'+realId+'" value="0">'+
                        '<div class="cg_hide_until_vote_rate'+r+'">'+
                        "<div class='cg_gallery_rating_div_star'><img src='"+starOffUrl+"' class='cg_slider_star1' style='float:left;"+cg_ratingStarCursorStyle+"'  alt='1' id='"+cg_ratingStar+"'></div>"+
                        "<div class='cg_gallery_rating_div_star'><img src='"+starOffUrl+"' class='cg_slider_star2' style='float:left;"+cg_ratingStarCursorStyle+"'  alt='2' id='"+cg_ratingStar+"'></div>"+
                        "<div class='cg_gallery_rating_div_star'><img src='"+starOffUrl+"' class='cg_slider_star3' style='float:left;"+cg_ratingStarCursorStyle+"''  alt='3' id='"+cg_ratingStar+"'></div>"+
                        "<div class='cg_gallery_rating_div_star'><img src='"+starOffUrl+"' class='cg_slider_star4' style='float:left;"+cg_ratingStarCursorStyle+"'  alt='4' id='"+cg_ratingStar+"'></div>"+
                        "<div class='cg_gallery_rating_div_star cg_gallery_rating_image_five_star_last_child'><img src='"+starOffUrl+"' class='cg_slider_star5' style='"+cg_ratingStarCursorStyle+"'  alt='5' id='"+cg_ratingStar+"'></div></div>"+
                        //<u>'+cg_plz_vote+'...</u>
                        '</div>';
                }

                else{}

                // Rating bestimmen --- ENDE

            }

            if(cgJsClass.slider.vars.cg_allow_rating==2){
                //	alert(cg_check_voted);

                if(cg_check_voted==1 || cg_check_voted==2){
                    if(countRatingSQuantity>0){var star6url = cgJsClass.slider.vars.starOnUrl;}
                    else{var star6url = cgJsClass.slider.vars.starOffUrl;}


                    // Cursor Style bestimmen, je nach dem ob es erlaubt ist aus der Gallerie zu voten oder nicht --- ENDE

                    var ratingBlock = '<div class="cg_gallery_rating_div_child" id="cg_gallery_rating_div_child'+realId+'"><input type="hidden" class="cg_check_voted" id="cg_check_voted'+realId+'" value="1">'+
                        "<div class='cg_gallery_rating_div_star'><img src='"+star6url+"' class='cg_slider_star1' style='"+cg_ratingStarCursorStyle+"' alt='6' id='"+cg_ratingStar+"'></div>"+
                        "<div class='rating_cg cg_gallery_rating_div_count'>"+countRatingSQuantity+"</div></div>";


                }


                else if(cg_check_voted==0){
                    //	alert(cg_vote_in_gallery);


                    //alert(cg_ratingStar);
                    var ratingBlock = '<div class="cg_gallery_rating_div_child" id="cg_gallery_rating_div_child'+realId+'"><input type="hidden" class="cg_check_voted" id="cg_check_voted'+realId+'" value="0">'+
                        '<div class="cg_hide_until_vote_rate'+r+' cg_gallery_rating_div_star">'+
                        "<div class='cg_gallery_rating_div_count'><img src='"+cgJsClass.slider.vars.starOffUrlGallery+"' class='cg_slider_star' style='cursor:pointer;' alt='6' id='"+cg_ratingStar+"'></div>";
                    //"<u>'+cg_plz_vote+'...</u>"+
                    "</div></div>";
                }

                else{}

                // Rating bestimmen --- ENDE

            }


            // Reload von rating Div
            jQuery("#cg_gallery_rating_div"+realId).empty();
            jQuery("#cg_gallery_rating_div"+realId).append(ratingBlock);

            // Reload von rating Div --- ENDE

        });

        // Werte der gevoteten Bilder sollen sich ändern --- ENDE

        // Comments Slider soll ausgeblendet werden falls an
        jQuery('#cg_comments_slider_div').css("display","none");
        jQuery('#close_slider_comments_button').css("display","none");

        jQuery('#cg_slider_arrow_left').hide();
        jQuery('#cg_slider_arrow_right').hide();
        jQuery('#close_slider_button').hide();

        // Slider wurde geschlossen wird aktualisiert
        jQuery('#cg_vote_in_slider').val(0);

//   jQuery(this).css('display', 'hidden');
        jQuery('#cg_overlay').hide();
        //jQuery('div#imgContainer').css('display', 'hidden');
        //jQuery('#imgContainer').hide();
        jQuery('#cg_slider_main_div').hide();

        // Rating Bereich wird dadurch wieder gehovert
        var cg_vote_in_gallery = jQuery("#cg_vote_in_gallery").val();
        var cg_comment_in_gallery = jQuery("#cg_comment_in_gallery").val();

        if(cgJsClass.slider.vars.cg_hide_until_vote==1 && cg_vote_in_gallery==1){

            jQuery( '.rating_cg' ).hover(function() {
                jQuery('#cg_overlay').css("cursor","pointer");
            });

        }
     //   console.log(1);
// Wichtig! Für spätere Verarbeitung des Back-Buttons und checken hashchange mobile!!!
        document.location.hash = "#cg";


    },
    closeCommentFrame:function(){

        //var cg_slider_comment_real_id = jQuery('[id*=commentsCGslider]').find("#cg_slider_comment_real_id").val();

        var checkCGcontainerVisiblity = jQuery( "#imgContainer" ).is(':visible');

        if(checkCGcontainerVisiblity){
            jQuery("#close_slider_button").css("display","inline");
        }

        jQuery("#cg_slider_comment_hint_msg").empty();


        var actual_slider_comments_id = jQuery('#cg_slider_comment_picture_id').val();
        jQuery('#show_comments_slider').empty();
        //alert(actual_slider_comments_id);


        //jQuery('#cg_pngCommentsIcon'+actual_slider_comments_id+'').click();
        jQuery('#close_slider_comments_button').css("display","none");
        jQuery('#cg_comments_slider_div').css("display","none");

        //jQuery('.cg_pngCommentsIcon').click();

        cgJsClass.slider.vars.commentFrameOpened = false;
    }
}