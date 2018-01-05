

cgJsClass.slider.slide = {
    init: function(CGclickedClass){
            //cgJsClass.slider.vars
            var cg_show_hide_info_button = 0;

            // 2 Varianten!!!!
            // 1ste: direkter Klick auf ein Image mit einer Klasse
            // 2ter: Resize. Übergabe von bereits getaner Verschiebung.

            // Ganz wichtig img davor stellen! Input Felder haben zwecks Klick event auch diese Klasse.
            var allCGimages = jQuery('.cg_show').length;

            //var classCGimageNumber = parseInt(CGclickedClass.substr(CGclickedClass.length - 1));
            var classCGimageNumber = parseInt(CGclickedClass.substr(8));

            var cgCountImages = jQuery('#cg_count').val();

            var heightCGoverlay = jQuery(window).height();
            var windowWidth = jQuery(window).width();

            cgJsClass.slider.vars.windowHeight = heightCGoverlay;
           // alert(3);
                //alert(cgJsClass.slider.vars.isMobile);
            if(cgJsClass.slider.vars.isMobile==true){


                if(windowWidth <= 800 || cgJsClass.slider.vars.picsOnSite<3 || heightCGoverlay/windowWidth>=1){
                    var widthCGoverlay = windowWidth;
                }
                else if(cgJsClass.slider.vars.cg_hide_carrousel == true){
                    var widthCGoverlay = jQuery(window).width();
                }
                else{
                    var widthCGoverlay = jQuery(window).width()/100*80;
                }


            }
            else{

                if(windowWidth <= 800 || cgJsClass.slider.vars.picsOnSite<3){
                    var widthCGoverlay = jQuery(window).width();
                }
                else if(cgJsClass.slider.vars.cg_hide_carrousel == true){
                    var widthCGoverlay = jQuery(window).width();
                }
                else{
                    var widthCGoverlay = jQuery(window).width()/100*80;
                }

            }

      //  alert("widthCGoverlay"+widthCGoverlay);


            // Arrows Breite anpassen
            if(widthCGoverlay<800){
                jQuery( "#cg_slider_arrow_left" ).css('width','3%');
                jQuery( "#cg_slider_arrow_right" ).css('width','3%');
            }

            if(widthCGoverlay<500){
                jQuery( "#cg_slider_arrow_left" ).css('width','4%');
                jQuery( "#cg_slider_arrow_right" ).css('width','4%');
            }


            if(widthCGoverlay>=800){
                jQuery( "#cg_slider_arrow_left" ).css('width','2%');
                jQuery( "#cg_slider_arrow_right" ).css('width','2%');
            }




            //Vorherige Breite zum Vergleich hernehmen
            var get_widthCGoverlay_old = jQuery("#widthCGoverlay_old").val();

            var widthBorder = 0;// 2px, jeweils innen und außen


            //Relation überprüfen von width height, wird später unten in der Schleife angwendet

            var heightDivBox = heightCGoverlay; // 2px, jeweils innen und außen
            var widthDivBox = widthCGoverlay-widthBorder; // 2px, jeweils innen und außen

            //  alert(widthDivBox);
            var cgIMGcontainerRelation = widthDivBox/heightDivBox;


            // Berechnung des Close Button position (close button befindet sich 30px rechts vom rechten arrow_image)
            var closeButtonPosition = widthCGoverlay-widthCGoverlay/100*3-20;

            // Berechnung des Close Button position (close button befindet sich 30px rechts vom rechten arrow_image)
            // var closeButtonCommentsPosition = widthCGoverlay-((widthCGoverlay-800)/2)-60;

            // Prüfen ob plz vote oder rating gezeigt werden sollen
            var cg_ip_check = jQuery('#cg_ip_check').val();

            // Plz vote language platzhalter
            var cg_plz_vote = jQuery('#cg_plz_vote').val();

            //Zum Anzeigen des GIFs before die Images geladen werden
            var loadingSource = jQuery('#cg_loadingGifSource').val();
            var margin_top_loadingSource = heightCGoverlay/2-12;//-9, damit es soch mittig wie möglich wird, wegen 19px breite und höhe des gifs
            var margin_left_loadingSource = widthCGoverlay/2-12;//-9, damit es soch mittig wie möglich wird, wegen 19px breite und höhe des gifs



            //var widthCGimgContainerAggregated = 0;


            var r = 0;
            var e = 0;


            var marginLeftSlider = 0;
            var aggregateWholeWidth = 0;


            var rLeft = classCGimageNumber-2; // Weniger als 2 nicht möglich bei der Logik
            var rRight = classCGimageNumber+2; // Weniger als 2 nicht möglich bei der Logik

            //Prüfen ob rechter oder Linker Pfeil geklickt wurde
            //Merke sobald Pfeil oder Bild im Slider geklickt wurde. Geht diese Funktion immer von vorne los!
            jQuery( ".cg_show" ).each(function( i ) {


                r++;



                cg_show_hide_info_button=0;


                if(rLeft<r && r<=rRight){

                    e++;

                    var count_cg_img_box = jQuery(".cg_img_box").length;

                    // Dies ist notwendige damit die richtige Gesamtwidth entsprechend der Anzahl der Inhaltsboxen geschaffen wird
                    if(count_cg_img_box<=6){
                        var cg_count_cg_img_box_multiplicator = 1;
                    }
                    else{
                        var cg_count_cg_img_box_multiplicator = 0;
                    }
                    var widthCGimgContainerAggregated = (widthDivBox+widthBorder)*(count_cg_img_box+cg_count_cg_img_box_multiplicator);

                    jQuery("#widthCGimgContainerAggregated").val(widthCGimgContainerAggregated);



                    // Extra Rechnung für Slider draggable. Wie wenn erstes Bild geklickt wäre.
                    var marginLeftSliderSaveValue = (widthCGoverlay-widthCGimgSliderBox-widthBorder)/2-marginSliderIMGleft;
                    jQuery("#cg_first_left_slider").val(marginLeftSliderSaveValue);

                    if(classCGimageNumber==r){

                        // r wird oben bestimmt
                        //*negative left muss bestimmt werden deswegen -1

                        if(r==1){

                            marginLeftSlider = 0;

                        }

                        else if(r==2){

                            marginLeftSlider = -1*widthDivBox;

                        }

                        else{

                            //Ausführen wenn im Slider nach rechts geklickt wurde

                            var cg_check_id_first_img_slider_box = jQuery(".cg_img_box").attr('id');
                            var cg_check_id_first_img_slider_box = parseInt(cg_check_id_first_img_slider_box.substr(11));
                            marginLeftSlider = widthDivBox*(classCGimageNumber-cg_check_id_first_img_slider_box)*-1;

                        }


                    }


                    //Hier muss geprüft werden ob outside von Galerie geratet wurde
                    var realId = jQuery(this).find('.realId').val();
                    var ratingImage = jQuery(this).find(".averageStarsRounded").val();
                    var countRatingQuantity = jQuery(this).find("#countRatingQuantity"+realId+"").val();
                    var countRatingSQuantity = jQuery(this).find("#countRatingSQuantity"+realId+"").val();
                    var countCommentsQuantity = jQuery(this).find("#countCommentsQuantity"+realId+"").val();
                    var cg_check_voted = jQuery("#cg_check_voted"+realId+"").val();

                    //Facebook button Seitenversion prüfen
                    var cg_fb_reload = jQuery("#cg_fb_reload"+realId+"").val();


                    if(get_widthCGoverlay_old==widthCGoverlay && cg_vote_in_gallery==1){

                        // Wenn der Werte Vergleich nicht übereinstimmt dann muss der inhalt des slider rating divs neu geladen werden

                        var cg_slider_rating_count_value = jQuery("#cg_slider_rating_count_value"+realId+"").val();
                        var cg_slider_rating_avarage_value = jQuery("#cg_slider_rating_avarage_value"+realId+"").val();

                    }


                    //Prüfen ob die Breite des Fensters sich überhaupt verändert hat, ob Boxen neu auftauchen müssen
                    if(get_widthCGoverlay_old!=widthCGoverlay || jQuery("#cg_img_box_"+r+"").length==0){


                        var width = jQuery(this).find(".widthOriginalImg").val();
                        var height = jQuery(this).find(".heightOriginalImg").val();


                        var cgIMGsourceRelation = width/height;

                        //	alert("cgIMGsourceRelation: "+cgIMGsourceRelation);
                        //	alert("cgIMGcontainerRelation: "+cgIMGcontainerRelation);

                        // hochformatigere Bilder
                        if(cgIMGsourceRelation<cgIMGcontainerRelation){

                            var newHeightSrc = heightDivBox;
                            var newHeightSrcPercentage = heightDivBox/height;

                            var newWidthSrc	= width*newHeightSrcPercentage;

                            var widthCGimgSliderBox = newWidthSrc;

                            var marginSliderIMGleft = (widthDivBox-newWidthSrc)/2;

                            // 97 wege 1,5% breite der Pfeile
                            if(widthCGimgSliderBox>widthCGoverlay/100*97){
                                widthCGimgSliderBox = widthDivBox-widthCGoverlay/100*4;
                                marginSliderIMGleft = marginSliderIMGleft+(widthCGoverlay/100*2)/2;
                            }


                        }

                        // breitformatigere Bilder
                        if(cgIMGsourceRelation>=cgIMGcontainerRelation){


                            var widthCGimgSliderBox = widthDivBox;

                            var marginSliderIMGleft = '';

                            var heightIMGdivBox = widthDivBox*height/width;

                            var paddingTop = (heightDivBox-heightIMGdivBox)/2;

                            if(heightIMGdivBox>heightCGoverlay/100*85){
                                widthCGimgSliderBox = widthCGimgSliderBox-77;
                            }

                        }


                        // Options relevante Werte
                        //eventuell alles nach diesem Height anpassen?


                        var srcOriginalImg = jQuery(this).find('.srcOriginalImg').val();

                        var src1920width = jQuery(this).find('.src1920width').val();
                        var src1600width = jQuery(this).find('.src1600width').val();
                        var src1024width = jQuery(this).find('.src1024width').val();
                        var src624width = jQuery(this).find('.src624width').val();
                        var src300width = jQuery(this).find('.src300width').val();



                        var urlForFacebook = jQuery(this).find(".urlForFacebook").val();

                        //Prüf ob wenn Hide Until Vote an ist schon gewotet wurde oder nicht




                        // Options relevante Werte	--- ENDE




                        // id's einfügen
                        var cg_img_box_id = "cg_img_box_"+r;

                        if (jQuery("#"+cg_img_box_id+"").length > 0){
                            jQuery("#"+cg_img_box_id+"").remove();
                        }

                        // Abstand bestimmen von rating und comments div und url div

                        // var marginLeftCGratingSlider = marginSliderIMGleft+37;
                        // +150 als breite von Rationg div und + 30 Abstand
                        // var marginLeftCGcommentsSlider = marginSliderIMGleft+37;
                        // var marginLeftcgFacebookDiv = marginSliderIMGleft+37;

                        if (typeof paddingTop == 'undefined') {

                            var paddingTop = 0;

                        }


                        // Hide Info Abstand nach Links
                        var marginLeftHideInfoDivSlider = marginSliderIMGleft;
                        var marginBottomHideInfoDivSlider = 0;


                        // Abstand bestimmen von rating div --- ENDE



                        //URL div bestimmen ob angezeigt werden soll oder nicht


                        // Prüfen ob überhaupt eine URL eingetragen wurde
                        var cg_img_url_entry_slider = jQuery("#cg_img_url"+realId+"").val();


                        if(cgJsClass.slider.vars.cg_Use_as_URL==1 && cgJsClass.slider.vars.cg_ForwardToURL==1 && cgJsClass.slider.vars.cg_ForwardFrom==1 && cg_img_url_entry_slider){


                            //Prüfen ob vom user ein http bei entries der url mit eingetragen wurde, wenn nicht dann hinzufügen
                            if (typeof cg_img_url_entry_slider === 'undefined') { }
                            else if(cg_img_url_entry_slider.indexOf("http") > -1) { cg_img_url_entry_slider = cg_img_url_entry_slider; }
                            else{ cg_img_url_entry_slider = "http://"+cg_img_url_entry_slider}


                            var cg_a_href_img = "href='"+cg_img_url_entry_slider+"'";


                            // Prüfung auf welche Art weitergeleitet werden soll
                            if(cgJsClass.slider.vars.cg_ForwardType==2){var targetStyle = "target = '_blank'";}
                            else{var targetStyle = "";}



                            var marginLeftUrlDiv = marginSliderIMGleft+37+150+30+90;

                            var urlDiv = "<div class='cg_url_slider_div' id='cg_url_slider_div"+realId+"'>"+
                                "<a href='"+cg_img_url_entry_slider+"' "+targetStyle+" ><img src='"+cg_pngUrlIconImg+"'></a></div>";

                            cg_show_hide_info_button = 1;

                        }

                        else{

                            var urlDiv = "";

                        }


                        //URL div bestimmen ob angezeigt werden soll oder nicht --- ENDE



                        // Rating bestimmen

                        // cg_allow_rating wird am Anfang der Datei bestimmt

                        if(cgJsClass.slider.vars.cg_allow_rating==1){

                            var starOnUrl = jQuery("#cg_star_on_slider").val();
                            var starOffUrl = jQuery("#cg_star_off_slider").val();


                            //if(cg_ip_check!=1 || (cg_ip_check==1 && cg_check_voted==1)){
                            if(cg_check_voted==1 || cg_check_voted==2){
                                if(ratingImage>=1){var star1url = starOnUrl;}
                                else{var star1url = starOffUrl;}
                                if(ratingImage>=2){var star2url = starOnUrl;}
                                else{var star2url = starOffUrl;}
                                if(ratingImage>=3){var star3url = starOnUrl;}
                                else{var star3url = starOffUrl;}
                                if(ratingImage>=4){var star4url = starOnUrl;}
                                else{var star4url = starOffUrl;}
                                if(ratingImage>=5){var star5url = starOnUrl;}
                                else{var star5url = starOffUrl;}

                                var ratingBlock = "<div class='cg_slider_rating_div_star' class='cg_slider_rating_div_star'><img src='"+star1url+"' class='cg_slider_star' alt='1' id='cg_rate_star"+realId+"'></div>"+
                                    "<div class='cg_slider_rating_div_star'><img src='"+star2url+"' class='cg_slider_star' alt='2' id='cg_rate_star"+realId+"'></div>"+
                                    "<div class='cg_slider_rating_div_star'><img src='"+star3url+"' class='cg_slider_star' alt='3' id='cg_rate_star"+realId+"'></div>"+
                                    "<div class='cg_slider_rating_div_star'><img src='"+star4url+"' class='cg_slider_star' alt='4' id='cg_rate_star"+realId+"'></div>"+
                                    "<div class='cg_slider_rating_div_star cg_gallery_rating_image_five_star_last_child'><img src='"+star5url+"' class='cg_slider_star' alt='5' id='cg_rate_star"+realId+"'></div>"+
                                    "<div id='rating_cg-"+realId+"' class='rating_cg_slider"+realId+" cg_slider_rating_div_count'>"+countRatingQuantity+"</div>";
                                //alert(ratingBlock);

                            }

                            else if(cg_ip_check==1 && cg_check_voted==0){
                                var ratingBlock = "<div id='cg_plz_vote"+realId+"' class='cg_slider_rating_div_star'>"+
                                    "<div class='cg_slider_rating_div_star'><img src='"+starOffUrl+"' class='cg_slider_star' alt='1' id='cg_rate_star"+realId+"'></div>"+
                                    "<div class='cg_slider_rating_div_star'><img src='"+starOffUrl+"' class='cg_slider_star' alt='2' id='cg_rate_star"+realId+"'></div>"+
                                    "<div class='cg_slider_rating_div_star'><img src='"+starOffUrl+"' class='cg_slider_star' alt='3' id='cg_rate_star"+realId+"'></div>"+
                                    "<div class='cg_slider_rating_div_star'><img src='"+starOffUrl+"' class='cg_slider_star' alt='4' id='cg_rate_star"+realId+"'></div>"+
                                    "<div class='cg_slider_rating_div_star cg_gallery_rating_image_five_star_last_child'><img src='"+starOffUrl+"' class='cg_slider_star' alt='5' id='cg_rate_star"+realId+"'></div>"+
                                    //"<u>"+cg_plz_vote+"...</u>
                                    "</div>";

                            }


                            // Rating bestimmen --- ENDE



                            // Rating div kreieren

                            var ratingDIV = "<div id='ratingCGslider"+realId+"' class='cg_slider_rating_div' >"+
                                "<input type='hidden' id='cg_slider_rating_count_value"+realId+"' value='"+countRatingQuantity+"'>"+
                                "<input type='hidden' id='cg_slider_rating_avarage_value"+realId+"' value='"+ratingImage+"'>"+
                                ratingBlock+
                                "<input type='hidden' class='cg_real_id' value='"+realId+"'>"+
                                "</div>";
                            // Rating div kreieren --- ENDE

                            cg_show_hide_info_button = 1;

                        }

                        else if(cgJsClass.slider.vars.cg_allow_rating==2){

                            var starOnUrl = jQuery("#cg_star_on_slider").val();
                            var starOffUrl = jQuery("#cg_star_off_slider").val();

                            //if(cg_ip_check!=1 || (cg_ip_check==1 && cg_check_voted==1)){
                            if(cg_check_voted==1 || cg_check_voted==2){
                                if(countRatingSQuantity>0){var star6url = starOnUrl;}
                                else{var star6url = starOffUrl;}
                                if(countRatingSQuantity>0){var countRatingSQuantity = countRatingSQuantity;}
                                else{var countRatingSQuantity = 0;}

                                var ratingBlock = "<div class='cg_slider_rating_div_star'><img src='"+star6url+"' class='cg_slider_star' alt='6' id='cg_rate_star"+realId+"'></div>"+
                                    "<div id='rating_cg-"+realId+"' class='rating_cg_slider"+realId+" cg_slider_rating_div_count'>"+countRatingSQuantity+"</div>";

                            }

                            else if(cg_ip_check==1 && cg_check_voted==0){

                                var ratingBlock = "<div id='cg_plz_vote"+realId+"' class='cg_slider_rating_div_star'>"+
                                    "<img src='"+starOffUrl+"' class='cg_slider_star' alt='6' id='cg_rate_star"+realId+"'>"+
                                    "</div>";
                            }



                            // Rating bestimmen --- ENDE

                            // Rating div kreieren

                            var ratingDIV = "<div id='ratingCGslider"+realId+"' class='cg_slider_rating_div' >"+
                                "<input type='hidden' id='cg_slider_rating_count_value"+realId+"' value='"+countRatingSQuantity+"'>"+
                                "<input type='hidden' id='cg_slider_rating_avarage_value"+realId+"' value='"+countRatingSQuantity+"'>"+
                                ratingBlock+
                                "<input type='hidden' class='cg_real_id' value='"+realId+"'>"+
                                "</div>";
                            // Rating div kreieren --- ENDE

                            cg_show_hide_info_button = 1;

                        }

                        else{
                            ratingDIV = "";
                        }


                        // cg_allow_comments wird am Anfang der Datei bestimmt
                        if(cgJsClass.slider.vars.cg_allow_comments==1){
                            var commentsDIV = "<div id='commentsCGslider"+realId+"' class='cg_slider_comments_div' >"+
                                "<div class='cg_slider_comments_div_icon'><img src='"+cgJsClass.slider.vars.cg_pngCommentsIconImg+"' id='cg_pngCommentsIcon"+realId+"' alt='"+realId+"'>"+
                                "<input type='hidden' class='countCommentsQuantity' value='"+countCommentsQuantity+"'></div>"+
                                "<div class='comments_cg_slider"+realId+" cg_slider_comments_div_count'>"+countCommentsQuantity+"</div>"+
                                "<input type='hidden' id='cg_slider_comment_real_id' value='"+realId+"' >"+
                                "</div>";

                            cg_show_hide_info_button = 1;

                        }

                        else{

                            commentsDIV = "";

                        }




                        // User input div kreieren

                        if (jQuery(this).find("p").length == 0){
                            var userInputDiv = '';
                            var cg_ShowInfoSliderDisplay = "display:none";


                        }

                        else{

                            // Hier erfolgt die Prüfung ob überhaupt irgendwelche Info in dem Fled ist. In der PHP Datei erfolgt die Prüfung der einzelnen eingegebenen values.
                            var cg_user_input = jQuery(this).find(".cg_user_input").html();
                            var cg_user_input_height = jQuery(this).find(".cg_user_input").height();
                            cg_user_input_height = cg_user_input_height-16;

                            //User info zeigen, welches versteckt ist in php


                            var cg_ShowInfoSliderDisplay = "display:block";

                            //User info zeigen, welches versteckt ist in php --- ENDE


                            var userInputDiv = "<div class='cg_user_input_container'><div class='cg_user_input_fade_in_arrow_container'><div class='cg_user_input_fade_in_arrow_div'>"+
                                "<img src='"+cgJsClass.slider.vars.cg_slider_arrow_fade_in_user_input_src+"' /></div></div>"+
                                "<div  id='cg_user_input"+realId+"' class='cg_user_input' style='"+cg_ShowInfoSliderDisplay+";'>"+
                                cg_user_input+"<div class='cg_user_input_fade_out_arrow_div' id='cg_user_input_fade_out_arrow_div_"+realId+"'>"+
                                "<img src='"+cgJsClass.slider.vars.cg_slider_arrow_fade_out_user_input_src+"' id='cg_user_input_bottom_border_"+realId+"'"+
                                " style='height:0;width:0;margin-bottom:-1px;' />"+
                                "<img src='"+cgJsClass.slider.vars.cg_slider_arrow_fade_out_user_input_src+"' />"+
                                "</div></div></div>";


                            cg_show_hide_info_button = 1;


                        }
                        // User input div kreieren --- ENDE

                        // Fb Like Div kreireren

                        if(cgJsClass.slider.vars.cg_FbLike==1){

                            // var marginLeftcgFacebookDiv = marginLeftCGcommentsSlider+100;
                            // var marginBottomcgFacebookDiv = marginBottomCGcommentsSlider+300;


                            if(cg_fb_reload!="413"){
                                widthFbDiv = 155;
                            }

                            var cgFacebookDivSlider = "<div id='cgFacebookDiv"+realId+"' class='cg_slider_facebook_div' >"+
                                "<iframe src='"+urlForFacebook+"'  scrolling='no'"+
                                "class='cg_fb_like_iframe_slider_order"+r+"' id='cg_fb_like_iframe_slider"+realId+"'  name='cg_fb_like_iframe_slider"+realId+"'></iframe>"+
                                "</div>";
                            cg_show_hide_info_button = 1;

                        }

                        else{

                            cgFacebookDivSlider = "";

                        }



                        // Fb Like Div kreireren --- ENDE


/*
                        if(cg_show_hide_info_button==1){
                            // Hide Info Div

                            var cg_hide_info_div = "<div class='"+cgJsClass.slider.vars.cg_hide_info_class+"' id='cg_hide_info_div"+realId+"'"+
                                "style='background-image:url("+cgJsClass.slider.vars.cg_hide_info_img+");background-position:center;background-repeat:no-repeat;width:21px;height:21px;'></div>";


                            // Hide Info Div --- ENDE
                        }
                        else{
                            var cg_hide_info_div = '';

                        }
*/



                        var r1 = r+1;

                        if (jQuery("#cg_img_box_"+r1+"").length>0){

                            if(widthCGimgSliderBox<=300){//alert("300");
                                jQuery("#imgContainer").prepend("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div'>"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");}
                            else if(widthCGimgSliderBox<=624){//alert("624");
                                jQuery("#imgContainer").prepend("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");}
                            else if(widthCGimgSliderBox<=1100){//alert("1024"); Puffer bis 1100 ist gegeben
                                jQuery("#imgContainer").prepend("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");}
                            else if(widthCGimgSliderBox<=1700){//alert("origin");

                                if(src1600width==0){src1600width = srcOriginalImg;}
                                //alert(src1600width);

                                jQuery("#imgContainer").prepend("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");

                            }

                            else if(widthCGimgSliderBox<=2120){//alert("origin");//Puffer bis 2120 ist gegeben von 1920 aus

                                if(src1920width==0){src1920width = srcOriginalImg;}
                                //alert(src1920width);
                                jQuery("#imgContainer").prepend("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");

                            }

                            else if(widthCGimgSliderBox>2120){//alert("origin");//Puffer von 200px noch erlaubt, damit nicht zu große Bilder geladen werden
                                jQuery("#imgContainer").prepend("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");

                            }



                        }

                        else{

                            if(widthCGimgSliderBox<=300){//alert("300");
                                jQuery("#imgContainer").append("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");}
                            else if(widthCGimgSliderBox<=624){//alert("624");
                                jQuery("#imgContainer").append("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");}
                            else if(widthCGimgSliderBox<=1100){//alert("1024"); Puffer bis 1100 ist gegeben
                                jQuery("#imgContainer").append("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");}
                            else if(widthCGimgSliderBox<=1700){//alert("origin");

                                if(src1600width==0){src1600width = srcOriginalImg;}
                                //alert(src1600width);

                                jQuery("#imgContainer").append("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div' >"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");

                            }

                            else if(widthCGimgSliderBox<=2120){//alert("origin");//Puffer bis 2120 ist gegeben von 1920 aus

                                if(src1920width==0){src1920width = srcOriginalImg;}
                                //alert(src1920width);
                                jQuery("#imgContainer").append("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div'>"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");

                            }

                            else if(widthCGimgSliderBox>2120){//alert("origin");//Puffer von 200px noch erlaubt, damit nicht zu große Bilder geladen werden
                                jQuery("#imgContainer").append("<div style='width:"+widthDivBox+"px;' class='cg_img_box' id='"+cg_img_box_id+"'>"+
                                    //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+loadingSource+"' width='24px' height='24px' style='z-index:1000006;position:absolute;margin-left:"+margin_left_loadingSource+"px;margin-top:"+margin_top_loadingSource+"px;display:inline;width:19px !important;height:19px !important;'>"+
                                    // jQuery("#show_comments_slider").append("<img class='cg_loading_gif_img' id='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
                                    // jQuery("#rating_cg").empty();
                                    //jQuery("#cg_loading_gif_img").load(function(){jQuery(this).toggle();});
                                    "<div class='cg_slider_image_div'>"+
                                    "<div class='cg_slider_info_div' style='padding-bottom:"+paddingTop+"px;'>"+
                                    ""+ratingDIV+""+
                                    ""+commentsDIV+""+
                                    ""+cgFacebookDivSlider+""+
                                    ""+urlDiv+""+
                                    ""+userInputDiv+""+
                                    "</div>"+
                                    "<img src='"+srcOriginalImg+"' style='visibility:hidden;margin-left:"+marginSliderIMGleft+"px;' width='"+widthCGimgSliderBox+"px' class='cg_slider_image' id='cg_slider_image_"+realId+"'  />"+
                                    "</div>"+
                                    "</div>");

                            }

                        }



                    }

                }

                //  console.log(jQuery('#cg_actual_slider_img_id').val());

            });


            var cg_actual_slider_img_id = jQuery('#cg_actual_slider_img_id').val();
            var srcOriginalImg = jQuery('#cg_show'+cg_actual_slider_img_id+' .srcOriginalImg').val();
            jQuery('#cg_slider_download_full_size_icon_div a').attr('href',srcOriginalImg);

            // Check if function was done after resizing

            var cg_slider_resize = jQuery('#cg_slider_resize').val();

            var widthCGimgContainerAggregated = jQuery("#widthCGimgContainerAggregated").val();
            //alert(widthCGimgContainerAggregated);


            if(cg_slider_resize==1){

                jQuery('#cg_slider_resize').val(0);

                jQuery('div#imgContainer').css('width', widthCGimgContainerAggregated);
                jQuery('div#imgContainer').css('left', marginLeftSlider);

/*                jQuery( "#imgContainer" ).animate({
                    width: widthCGimgContainerAggregated,
                    marginLeft: marginLeftSlider
                }, 200 );*/

            }
            else{

/*                jQuery( "#imgContainer" ).animate({
                    width: widthCGimgContainerAggregated,
                    marginLeft: marginLeftSlider
                }, 200 );*/

                jQuery( "div#imgContainer" ).css("width",widthCGimgContainerAggregated);
                jQuery( "div#imgContainer" ).css("left",marginLeftSlider);

            }






            if(cgJsClass.slider.vars.cg_FbLike==1){

                cg_fb_reload = jQuery("#cg_fb_reload"+jQuery("#cg_actual_slider_img_id").val()+"");


                //	  alert(cg_fb_reload);
                if(cg_fb_reload=="413"){


                    if(jQuery("#cg_slider_frame_reloaded").val()=="0"){
                        jQuery(window).load(function(){
                            // Einmal reload des aktuellen Frames
                            var fbFrameDiv = document.getElementById("cg_fb_like_iframe_slider"+jQuery("#cg_actual_slider_img_id").val()+"").contentWindow;
                            //var win = jQuery("#cg_fb_like_iframe_gallery17").contentWindow;
                            fbFrameDiv.postMessage("reload","*");
                            jQuery("#cg_slider_frame_reloaded").val(1);
                        });
                    }

                    if(cgJsClass.slider.vars.cg_FbLikeGallery==1){

                        if(jQuery("#cg_gallery_frame_reloaded").val()=="0"){
                            //alert("go");
                            // Einmal reload des vergangenen Frames in der Galerie-Ansicht
                            if (jQuery("#cg_slider_class_value_before").val() >= 1){
                                jQuery(window).load(function(){
                                    //alert("cg_fb_like_iframe_gallery_order"+jQuery("#cg_slider_class_value_before").val()+"");
                                    var fbFrameDiv = document.getElementsByClassName("cg_fb_like_iframe_gallery_order"+jQuery("#cg_slider_class_value_before").val()+"")[0].contentWindow;
                                    fbFrameDiv.postMessage("reload","*");
                                    jQuery("#cg_gallery_frame_reloaded").val(1);
                                });
                            }
                        }

                    }

                }

                else{

                    if(jQuery("#cg_slider_frame_reloaded").val()=="0"){
                        jQuery(window).load(function(){
                            // Einmal reload des aktuellen Frames
                           // alert(jQuery("#cg_actual_slider_img_id").val());
                            document.getElementById("cg_fb_like_iframe_slider"+jQuery("#cg_actual_slider_img_id").val()+"").contentWindow.location.reload;
                            jQuery("#cg_slider_frame_reloaded").val(1);
                        });
                    }

                    if(cgJsClass.slider.vars.cg_FbLikeGallery==1){

                        if(jQuery("#cg_gallery_frame_reloaded").val()=="0"){
                            //alert("go");
                            // Einmal reload des vergangenen Frames in der Galerie-Ansicht
                            if (jQuery("#cg_slider_class_value_before").val() >= 1){
                                jQuery(window).load(function(){
                                    //alert("cg_fb_like_iframe_gallery_order"+jQuery("#cg_slider_class_value_before").val()+"");
                                    //document.getElementsByClassName("cg_fb_like_iframe_gallery_order"+jQuery("#cg_slider_class_value_before").val()+"")[0].contentWindow.location.reload;
                                    //frames["cg_fb_like_iframe_gallery1076"].contentWindow.location.reload;
                                    var cg_gallery_iframe_id = jQuery(".cg_fb_like_iframe_gallery_order"+jQuery("#cg_slider_class_value_before").val()+"").attr("id");
                                    document.getElementById(cg_gallery_iframe_id).contentWindow.location.reload(true);
                                    jQuery("#cg_gallery_frame_reloaded").val(1);
                                });
                            }
                        }
                    }

                }

            }


            if(classCGimageNumber<2){

                jQuery("#cg_slider_arrow_left").hide();

            }


            else{

                if(cgJsClass.slider.vars.isMobile!=true){
                    jQuery("#cg_slider_arrow_left").show();
                }

            }

            if(classCGimageNumber>=allCGimages ){

                jQuery("#cg_slider_arrow_right").hide();

            }

            else{

                if(cgJsClass.slider.vars.isMobile!=true){
                    jQuery("#cg_slider_arrow_right").show();
                }
            }

            var real_picture_id = jQuery('#cg_actual_slider_img_id').val();
            var crypted_picture_id =(parseInt(real_picture_id)+8)*2+100000;
            document.location.hash = "img"+crypted_picture_id;


            cgJsClass.slider.vars.hash="#img"+crypted_picture_id;



            jQuery("#widthCGoverlay_old").val(widthCGoverlay);


            jQuery('#cg_img_box_'+classCGimageNumber+'').css("visibility","visible");
            jQuery('#cg_img_box_'+classCGimageNumber+' .cg_slider_image_div img').css("visibility","visible");





    }
}