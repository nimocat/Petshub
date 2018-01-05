cgJsClass.slider.open = {
    init:function (classCGimage,real_picture_id) {


        // Achtung! Vorher diese Prüfung notwendig falls hide until vote und vote out gallery aktiviert sind! Der Klickevent von Children Element wird von der Jquery Bibliothek als erstes bearbeitet.
        // Auch wenn dieser weiter unten im Script passiert.

        if(cgJsClass.slider.vars.cg_FbLikeGallery==1){

        }




        var checkContainer = jQuery("#cg_hide_until_vote_actual").val();

        if(checkContainer!=1){


            jQuery('#cg_overlay').toggle();

            jQuery('#cg_overlay').css('opacity', '0.4');

            if(cgJsClass.slider.vars.cg_OriginalSourceLinkInSlider==0){
                jQuery( "#cg_slider_download_full_size_icon_div" ).remove();
            }

            if(cgJsClass.slider.vars.isMobile==true){
                jQuery( "#cg_slider_arrow_left" ).hide();
                jQuery( "#cg_slider_arrow_right" ).hide();
            }


            // Schließen des Comments Slider Div falls offen
            jQuery('#cg_comments_slider_div').css("display","none");
            jQuery('#close_slider_comments_button').css("display","none");


            // Echte ID wird vergeben
            if(real_picture_id==false){
                var real_picture_id = parseInt(jQuery("."+classCGimage+"").attr("data-cg_image_id"));
            }


            jQuery('#cg_actual_slider_img_id').val(real_picture_id);



            var classCGimageNumber = parseInt(classCGimage.substr(8));



            //Der aktuelle Slider Count wird gesetzt
            jQuery('#cg_actual_slider_class_value').val(classCGimage);

            // Funktion zur Ausführung der Berechnung

            // Aller Slider Boxen werden entfernt. Der Aufbau kann von vorne beginnen. Kann sein, dass vorher Resize wurde. Dann sollte der Aufbau von vorne starten.
            jQuery( ".cg_img_box" ).remove();


            jQuery("html").css("overflow","hidden");

            jQuery('#close_slider_button').css("display","inline");
            jQuery('#close_slider_button_img').css("display","inline");
            jQuery('#close_slider_comments_button_img').css("display","inline");
            jQuery('#cg_slider_arrow_left_img').css("display","inline");
            jQuery('#cg_slider_arrow_right_img').css("display","inline");
            jQuery('#cg_slider_arrow_fade_out_slider_selection_img').css("display","inline");

            //Viewport for slide und danach!!!
            // Unscalable definitiv einmal davor setzten
            if(cgJsClass.slider.vars.isMobile==true){
                jQuery("#cg_slider_main_div").css('display','none');
                //   jQuery("#cg_slider_main_div").css('visibility','hidden');
                cgJsClass.slider.vars.doNotResize = 1;

                if(cgJsClass.slider.vars.noViewport==true){
                    jQuery('head').prepend('<meta name="viewport" content="user-scalable = no">');
                    // Viewport lässt sich nur per Name entfernen

                }
                else{
                    var x = document.getElementsByName("viewport");
                    x[0].setAttribute('content','user-scalable = no');
                }


            }

            cgJsClass.slider.resize.openOrCloseCarrousel();


            cgJsClass.slider.slide.init(classCGimage,'');
            // cgJsClass.slider.resize.init();



            // Hier viewport nochmal ändern für Scale
            if(cgJsClass.slider.vars.isMobile==true){

                if(cgJsClass.slider.vars.noViewport==true){
                    // Den vorherigen Viewport der gesetzt wurde entfernen
                    jQuery("head meta[name=viewport]").remove();
                    // Viewport lässt sich nur per Name entfernen
                    jQuery('head').prepend('<meta name="viewport" content="width='+jQuery(window).width()+'">');
                }
                else{
                    var x = document.getElementsByName("viewport");
                    x[0].setAttribute('content','width='+jQuery(window).width()+'');
                }

                // Wieder auf 0 setzten falls vorher auf 1 gesetzt wurde
                jQuery("#cg_slider_main_div").css('display','block');

            }

            // Vermeiden von doppeltem Vorkommen des Rating buttons, weil ein anderes Bild mit angezeigt wird
            var cg_img_boxId = parseInt(jQuery("#cg_actual_slider_class_value").val().slice(8));// Aktuelles Bild wird ermittelt



            jQuery("#cg_img_box_"+cg_img_boxId+"").css("visibility","visible");
            jQuery("#cg_img_box_"+cg_img_boxId+"").children("div").css("visibility","visible");


            // Slider wurde geöffnet wird aktualisiert
            jQuery('#cg_vote_in_slider').val(1);

            jQuery('#cg_slider_main_div').fadeIn('slow');

            cgJsClass.slider.resize.hideCarrouselScrollbar();

            // Prüfe ob der User text fade in button zu verschwinden oder zu erscheinen hat
            cgJsClass.slider.resize.checkFadeInfo();


        }

        jQuery("#cg_hide_until_vote_actual").val(0);
        return false;

    },
    checkUrl:function(){

        // Show/Zeige ImgContainer für Slider


        // Wenn Picture ID Parameter mitgeschickt wird und gallery Script aktiviert ist


        // Zum Testen: https://www.contest-gallery.com/test-cg-1/?picture_id=100752#cg-begin

        // 1. Picture ID herausfiltern, ob mit # oder ohne geschickt wird
        // 2. Prüfen ob die mitgesendet ID aktiviert ist und an welchem Platz sich diese in der Reihenfolge befindet
        // 3. Sollte die ID auf einer Seite weiterhinten sein muss an diese weitergeleitet werden
        // 4. Click ID


        // Aktion wird aus geführt wenn User z.B. aus Ihren Bestätigungsmails klicken

        if(window.location.href.indexOf("?picture_id=") > -1) {
            //	alert(4);
            // 1. Picture ID herausfiltern, ob mit # oder ohne geschickt wird

            // Prüfung einbauen ob Raute beinhaltet! Und ohne Raute Verarbeitung auch einbauen.
            var site_str = window.location.href;
            //alert(test_str);
            var start_pos = site_str.indexOf('picture_id=') + 11;//+11 weil immer der erste buchstabe des searchvalue als position gilt
            //alert(start_pos);


            if(site_str.indexOf('#') === -1){
                //alert("no");

                var picture_id_to_get = parseInt(site_str.substring(start_pos));

            }

            else{

                var end_pos = site_str.indexOf('#',start_pos);
                var picture_id_to_get = parseInt(site_str.substring(start_pos,end_pos));

            }

            var realImgId = (parseInt(picture_id_to_get)-100000)/2-8;

            if ( jQuery( "#cg_check_all_acitvated_ids"+realImgId ).length ) {

                // 2. Prüfen ob die mitgesendet ID aktiviert ist und an welchem Platz sich diese in der Reihenfolge befindet
                var r=0;
                jQuery( ".cg_check_all_acitvated_images" ).each(function( i ) {

                    r++;

                    var check_id = jQuery(this).val();
                    check_id = parseInt(check_id);

                    if(check_id==realImgId){
                        return false;
                    }


                });

                // Maximale Anzahl der Bilder die angezeigt wird pro Gallerie Ansicht
                var check_step = jQuery("#cg_step").val();
                var quotient = r/check_step;

                var floorQuotioent = Math.floor(quotient);

                var check_step_on_site = check_step*floorQuotioent;

                // 3. Sollte die ID auf einer Seite weiterhinten sein muss an diese weitergeleitet werden

                if(quotient>1){

                    quotient = Math.floor(quotient);
                    var step = quotient * check_step;

                    var site_url = jQuery("#cg_siteURL").val();

                    var look = jQuery("#cg_look").val();
                    if(look=="thumb"){look=1;}
                    else if(look=="height"){look=2;}
                    else if(look=="row"){look=3;}
                    else{look=1;}

                    var order = jQuery("#cg_order").val();
                    if(order=="desc"){order=1;}
                    else if(order=="asc"){order=2;}
                    else{order=1;}


                    // Zwei Varianten von Seiten Urls sind möglich: mit und ohne ?
                    if(site_url.indexOf('?') === -1)
                    {
                        window.location.href = site_url+"?1="+look+"&2="+order+"&3="+check_step_on_site+"#img"+picture_id_to_get;
                    }
                    else{

                        window.location.href = site_url+"&1="+look+"&2="+order+"&3="+check_step_on_site+"#img"+picture_id_to_get;

                    }


                }

                // 4. Click ID
                else{

                    var uri = window.location.toString();
                    if (uri.indexOf("?") > 0) {
                        var clean_uri = uri.substring(0, uri.indexOf("?"));
                        window.history.replaceState({}, document.title, clean_uri);
                    }

                    var class_of_element = "cg_image"+jQuery("#cg_img_order"+realImgId).val();

                    this.init(class_of_element,realImgId);



                    var widthWindow = jQuery(window).width();
                    if(cgJsClass.slider.vars.cg_allow_rating>=1 && cgJsClass.slider.vars.widthWindow<=500){
                        cgJsClass.slider.resize.showFbNextLine();
                    }
                    if(cgJsClass.slider.vars.cg_allow_rating>=1 && cgJsClass.slider.vars.widthWindow>500){
                        jQuery('#imgContainer .cg_slider_facebook_div').removeAttr('style');
                        jQuery('#imgContainer .cg_user_input_container').removeAttr('style');
                    }

                }

            }

            else{
                // Div verschieben falls es sich noch in der Slider Div befindet
                if(jQuery('#cg_slider_main_div').has('#cg_comments_slider_div')){
                    jQuery('#cg_comments_slider_div').appendTo('body');
                }
                cgJsClass.slider.open.openCommentFrame();
                jQuery('#cg_comments_slider_div').clone().attr('id','cgIdNotExists').insertAfter('#cg_comments_slider_div');
                cgJsClass.slider.close.closeCommentFrame();
                cgJsClass.slider.open.openImageNotActive();

                return false;

            }





        }

        // Aktion wird aus geführt wenn User z.B. aus Ihren Bestätigungsmails klicken

        // Aktion wird ausgeführt User URLs dieser Art teilen #img


        else if (window.location.href.indexOf("#img") > -1) {

            // 1. Picture ID herausfiltern, ob mit # oder ohne geschickt wird

            // Prüfung einbauen ob Raute beinhaltet! Und ohne Raute Verarbeitung auch einbauen.
            var site_str = window.location.href;
            var start_pos = site_str.indexOf('#img') + 4;//+11 weil immer der erste buchstabe des searchvalue als position gilt

            var picture_id_to_get = parseInt(site_str.substring(start_pos));

            var realImgId = (parseInt(picture_id_to_get)-100000)/2-8;


            if ( jQuery( "#cg_check_all_acitvated_ids"+realImgId ).length >= 1) {
                // 2. Prüfen ob die mitgesendet ID aktiviert ist und an welchem Platz sich diese in der Reihenfolge befindet
                var r=0;
                jQuery( ".cg_check_all_acitvated_images" ).each(function( i ) {

                    r++;

                    var check_id = jQuery(this).val();
                    check_id = parseInt(check_id);

                    if(check_id==realImgId){
                        return false;
                    }


                });

                // Maximale Anzahl der Bilder die angezeigt wird pro Gallerie Ansicht
                var check_step = jQuery("#cg_step").val();

                var quotient = r/check_step;

                var floorQuotioent = Math.floor(quotient);

                var check_step_on_site = check_step*floorQuotioent;

                // 3. Sollte die ID auf einer Seite weiterhinten sein muss an diese weitergeleitet werden

                if(window.location.href.indexOf("picture_id") > -1) {

                    if(quotient>1){

                        quotient = Math.floor(quotient);
                        var step = quotient * check_step;

                        var site_url = jQuery("#cg_siteURL").val();

                        var look = jQuery("#cg_look").val();
                        if(look=="thumb"){look=1;}
                        else if(look=="height"){look=2;}
                        else if(look=="row"){look=3;}
                        else{look=1;}

                        var order = jQuery("#cg_order").val();
                        if(order=="desc"){order=1;}
                        else if(order=="asc"){order=2;}
                        else{order=1;}


                        // Zwei Varianten von Seiten Urls sind möglich: mit und ohne ?
                        if(site_url.indexOf('?') === -1)
                        {
                            window.location.href = site_url+"?1="+look+"&2="+order+"&3="+check_step_on_site+"#img"+picture_id_to_get;
                        }
                        else{

                            window.location.href = site_url+"&1="+look+"&2="+order+"&3="+check_step_on_site+"#img"+picture_id_to_get;

                        }


                    }

                }

                // 4. Click ID
                else{

                    //	var class_of_element = jQuery("#cg_image_id"+realImgId).attr("class");
                    var class_of_element = "cg_image"+jQuery("#cg_img_order"+realImgId).val();

                    this.init(class_of_element,realImgId);

                    var widthWindow = jQuery(window).width();
                    if(cgJsClass.slider.vars.cg_allow_rating>=1 && widthWindow<=500 && cgJsClass.slider.vars.cg_FbLike==1){
                        cgJsClass.slider.resize.showFbNextLine();
                    }
                    if(cgJsClass.slider.vars.cg_allow_rating>=1 && widthWindow>500 && cgJsClass.slider.vars.cg_FbLike==1){
                        jQuery('#imgContainer .cg_slider_facebook_div').removeAttr('style');
                        jQuery('#imgContainer .cg_user_input_container').removeAttr('style');
                    }

                }

            }

            else{
                // Div verschieben falls es sich noch in der Slider Div befindet
                if(jQuery('#cg_slider_main_div').has('#cg_comments_slider_div')){
                    jQuery('#cg_comments_slider_div').appendTo('body');
                }
                cgJsClass.slider.open.openCommentFrame();
                jQuery('#cg_comments_slider_div').clone().attr('id','cgIdNotExists').insertAfter('#cg_comments_slider_div');
                cgJsClass.slider.close.closeCommentFrame();
                cgJsClass.slider.open.openImageNotActive();

                return false;

            }





        }


        // Aktion wird ausgeführt User URLs dieser Art teilen #img --- ENDE

    },
    openCommentFrame:function (cg_picture_id) {


        // Div verschieben falls es sich noch in der Slider Div befindet
        if(jQuery('#cg_slider_main_div').has('#cg_comments_slider_div')){
            jQuery('#cg_comments_slider_div').appendTo('body');
        }

        var cg_slider_mouseup = jQuery("#cg_slider_mouseup").val();

        jQuery("#close_slider_button").css("display","none");

        var widthScreen = jQuery(window).width();
        var heightScreen = jQuery('#cg_overlay').height();


        var widthCommentContent = 800;


        if(widthScreen>widthCommentContent){ var leftSpace = (widthScreen-widthCommentContent)/2;}
        if(widthScreen<=widthCommentContent){ var leftSpace = 0; widthCommentContent=widthScreen;}

        // Horizontal kann in pixxel, vertical muss in % angegeben werden, damit overflow: scroll funktioniert
        jQuery('#cg_comments_slider_div').css('left', leftSpace);
        jQuery('#cg_comments_slider_div').css('top', "10%");
        jQuery('#cg_comments_slider_div').css('width', widthCommentContent);
        jQuery('#cg_comments_slider_div').css('height', "80%");
        jQuery('#cg_comments_slider_div').css('padding-right', "0");


        var newCommentsElementsWidth = widthCommentContent-100;


        jQuery('#cg_comments_slider_div').css("display","block");
        jQuery('#close_slider_comments_button').show();
        jQuery('#close_slider_comments_button_img').show();


        jQuery("#cg_user_input"+cg_picture_id+"").css("display","none");

        // Funktion zur Ausführung der Berechnung


        // Prüfen der Wordpress Session id
        var check = jQuery("#cg_check").val();


        // I am not a robot checkbox soll auftauchen
        if (jQuery("#"+check+"").length > 0){

            var onlyCheck = 1;

        }
        else{

            var cg_language_i_am_not_a_robot = jQuery('#cg_language_i_am_not_a_robot').val();
            jQuery("#cg_i_am_not_a_robot").empty();
            jQuery("#cg_i_am_not_a_robot").append("<input type='checkbox' value='"+check+"' class='"+check+"' id='cg_i_am_not_a_robot_checkbox' name='"+check+"' id='"+check+"' '><label for='cg_i_am_not_a_robot_checkbox'>"+cg_language_i_am_not_a_robot+"</label>");

        }

        cgJsClass.slider.vars.commentFrameOpened = true;
    },
    openImageNotActive: function () {
        jQuery('#cgIdNotExists').css('height','100px');
        jQuery('#cgIdNotExists *').css('display','none');
        jQuery('#cgIdNotExists > div').css('display','block');
        jQuery('#cgIdNotExists > div > div').css('display','block');
        jQuery('#cgIdNotExists > div > div > img').css('display','block');
        jQuery('#cgIdNotExists > div').remove();
        jQuery('#cgIdNotExists').append('<div id="cgIdNotExistsMessage">'+jQuery('#cg_language_imageIsNotActivated').val()+'</div>');

        jQuery(document).on('click','#cgIdNotExists > div > div',function () {
            jQuery('#cgIdNotExists').css('display','none');
        });

    }
}