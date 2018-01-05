jQuery(document).ready(function($){

    // Ganz wichtig, damit später über allen elmenten überragt.
    $('#cg_slider_main_div').appendTo('body');


    //------------------------------------------------------------------------------//
    // Bereich vor dem Slider --- Anfang
    //------------------------------------------------------------------------------//



// Komm600entar Pop Up im Slider



    $(document).on('click', '[id*=cg_pngCommentsIcon]', function(e){


        var cg_picture_id = $(this).attr("id");

        cg_picture_id = parseInt(cg_picture_id.substr(18));

        // Haupthidden Feld, dass aktuell geöffnete comment image id zeigt
        //  $('#cg_slider_comment_picture_id').val(cg_picture_id);
        $('#cg_slider_comment_picture_id').val(cg_picture_id);

        //	$("#cg_user_input"+cg_picture_id+"").css("display","none");
        //  alert(cg_picture_id);
        cgJsClass.slider.open.openCommentFrame(cg_picture_id);

        return false;

    });


// Kommentar Pop Up im Slider --- ENDE


    $(document).on('click', '#close_slider_button', function(e){
        //$("#cgViewPort").remove();
        // $("head meta[name=viewport]:contains(user-scalable = no)").remove();
        var x = document.getElementsByName("viewport");
        x[0].setAttribute('content',cgJsClass.slider.vars.viewportContent);
        cgJsClass.slider.close.init();



    });


    $(document).on('click', '#close_slider_comments_button', function(e){


        cgJsClass.slider.close.closeCommentFrame();


    });


    $(document).keydown(function(e) {

        //alert(2);

        if(e.which == 27) {


            if($('#close_slider_comments_button').is(':visible')) {

                cgJsClass.slider.close.closeCommentFrame();

            }

            else{


                if($('#close_slider_button').is(':visible')) {

                    cgJsClass.slider.close.init();

                }


            }


        }

    });


    // Wenn außerhalb des Slider Divs geklickt wird, dann schließen. Diese Funktion vorallem nützlich wenn "Comment out of gallery aktiviert ist" (release)
    $(document).mouseup(function (e)
    {
        var container = $("#cg_comments_slider_div");

        if (!container.is(e.target) // if the target of the click isn't the container...
            && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.hide();
        }
    });





// Schließen von Kommentar Fenster im Slider und in der Gallerie --- ENDE




    var cg_activate_gallery_slider = $("#cg_activate_gallery_slider").val();
    var cg_OnlyGalleryView = $("#cg_OnlyGalleryView").val();

    //Start Slider/Out of Gallery Script





//alert(cg_OnlyGalleryView);
    //------------------------------------------------------------------------------//
    // Bereich vor dem Slider --- ENDE
    //------------------------------------------------------------------------------//


    // Wenn nur gallery view aktiviert ist dann return false einfach immer
    /*    if(cg_OnlyGalleryView == 1){


            $(document).on('click', '[class*=cg_image]', function(e){

                //alert(14);
                return false;



            });

            return false;

        }*/



    if(cg_activate_gallery_slider == 1){

        //$(".cg_href_image").attr("href", "#")

        //alert(cg_activate_gallery_slider);
        /*
        ABLAUF
        1.  Durch klicken wird in der Hauptfunktion position berechnet
        2. Display none Elemente werden wieder visible
        3. Beim Wegklicken verschwinden Display none Elemente
        4. Beim Wiederklicken fängt es wieder bei 1 an

        Aufteilung insgesamt:

        1. Hovern
        2. Resize
        3. Hauptfunktion
        4. Click Events



        */

        // Zu tun: ins leere kicken comments block wird geschlossen, auf pfeil klicken comments block wird geschlossen, sliden bis zu gewisser stelle comments block wird geschlossen


        // Schließen (X) Buttons beim imgContainer und commentContainer einbauen

        // Beim Rating wird nicht gespeichert. Alle Infos wie bei Show_Image müssen gewonnen werden.

        // Wenn Bilder noch nicht geladen sind, dann soll Loading Gif erscheinen


        // Hidden input Feld wo aktuelles CG immer angezeigt wird, immer in Abhängigkeit von left.
        // Bei der Funktion r zur Orientierung nutzen
        // Formel beim Dragging berechnung
        // Bei Klicking dann später addieren und subtrahieren vom aktuellem

// Möglichkeiten die Bilder alle immer in gleicher größe als Thumb anzuzeigen

//Vollbild machen slider. Sowohl Verticale Pfeilfläche einbauen über die ganze Höhe. Wie auch Royal slider Funktion.


// Anzeigen, dass geklickt werden kann wenn man über Pfeile hovert

// Bewertungsmöglichkeit einbauen. Am besten da wo bewertungen in der Gallerie Ansicht drin sind, das Element klonen (clone).

// Als nächstes Elemente in der Box zentrieren dann kommentare einbauen

// Komplett ohne Slider, mit direktem bewerten und kommentieren

// Je nach dem bei welchem Bild man sich befindet sollte entsprechende Picture ID angezeigt werden


// Wichtige Variablen die man dauernd nutzt werden schon hier bestimmt
        var cg_hide_until_vote = $("#cg_hide_until_vote").val();
        var cg_hide_info = $("#cg_hide_info").val();

        var cg_hide_info_png = $('#cg_hide_info_png').val();
        var cg_show_info_png = $('#cg_show_info_png').val();


        var cg_slider_arrow_fade_in_user_input_src = $('#cg_slider_arrow_fade_in_user_input img').attr('src');
        var cg_slider_arrow_fade_out_user_input_src = $('#cg_slider_arrow_fade_out_user_input img').attr('src');

        // Cursor Style bestimmen, je nach dem ob es erlaubt ist aus der Gallerie zu voten oder nicht
        var cg_vote_in_gallery = $("#cg_vote_in_gallery").val();

        if(cg_hide_info==1){

            var cg_hide_info_class = "cg_hide_info_div_no";
            var cg_hide_info_img = cg_hide_info_png;
        }
        else{

            var cg_hide_info_class = "cg_hide_info_div_yes";
            var cg_hide_info_img = cg_show_info_png;
        }


        var cg_vote_in_gallery = $("#cg_vote_in_gallery").val();
        var cg_comment_in_gallery = $("#cg_comment_in_gallery").val();
//var cg_shown_images = $("[id*=cg_image_id]").length;
        var cg_shown_images = $(".cg_show").length;
        var cg_allow_comments = $("#cg_allow_comments").val();
        var cg_allow_rating = $("#cg_allow_rating").val();
//alert(cg_allow_rating);
        var cg_slider_mouseup = $("#cg_slider_mouseup").val();
        var cg_Use_as_URL = $("#cg_Use_as_URL").val();
        var cg_ForwardToURL = $("#cg_ForwardToURL").val();
        var cg_ForwardFrom = $("#cg_ForwardFrom").val();
        var cg_ForwardType = $("#cg_ForwardType").val();
        var cg_FbLike = $("#cg_FbLike").val();
        var cg_FbLikeGallery = $("#cg_FbLikeGallery").val();
        var cg_FbLikeGalleryVote = $("#cg_FbLikeGalleryVote").val();

        var starOnUrl = $("#cg_star_on_slider").val();
        var starOffUrl = $("#cg_star_off_slider").val();
        var starOnUrlGallery = $("#cg_star_on_gallery").val();
        var starOffUrlGallery = $("#cg_star_off_gallery").val();

        var cg_pngCommentsIconImg = $('#cg_pngCommentsIconImg').val();
        var cg_pngUrlIconImg = $('#cg_pngUrlIconImg').val();

        var cg_ShowAlwaysInfoSlider =  $("#cg_ShowAlwaysInfoSlider").val();


        var userBrowserLang = navigator.language || navigator.userLanguage;

        if(userBrowserLang.indexOf("en")==0){var widthFbDiv = 155;}
        else if(userBrowserLang.indexOf("de")==0){var widthFbDiv = 190;}
        else if(userBrowserLang.indexOf("fr")==0){var widthFbDiv = 182;}
        else if(userBrowserLang.indexOf("es")==0){var widthFbDiv = 207;}
        else if(userBrowserLang.indexOf("pt")==0){var widthFbDiv = 179;}
        else if(userBrowserLang.indexOf("nl")==0){var widthFbDiv = 195;}
        else if(userBrowserLang.indexOf("ru")==0){var widthFbDiv = 217;}
        else if(userBrowserLang.indexOf("zh")==0){var widthFbDiv = 136;}
        else if(userBrowserLang.indexOf("ja")==0){var widthFbDiv = 177;}
        else{var widthFbDiv = 152;}






        $( '[id*=cg_slider_arrow]' ).hover(function() {
            $(this).css("cursor","pointer");
        });

        if(cg_hide_until_vote==1 && cg_vote_in_gallery==1){

            $( '.rating_cg' ).hover(function() {
                $(this).css("cursor","pointer");
            });



        }

        if(cg_comment_in_gallery==1 && cg_vote_in_gallery==1){

            $( '[id*=rating_cgc]' ).hover(function() {
                $(this).css("cursor","pointer");
            });

        }



        // device detection, damit elemente beim klicken auf imgContainer nicht verschwinden und rating und comment auf mobile funktionieren
        var isMobile = false; //initiate as false


        // device detection, damit elemente beim klicken auf imgContainer nicht verschwinden und rating und comment auf mobile funktionieren
        if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
            || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;


        // Pfeile verstecken bei der Mobile Version

        if(isMobile==true){
            $( "#cg_slider_arrow_left" ).hide();
            $( "#cg_slider_arrow_right" ).hide();
        }











        $(document).on('click', '#imgContainer', function(e){

            $('#close_slider_button').css('display', 'block');

        });




// Reaktion auf touch
        /*
        $(document).on('touchstart', '#imgContainer', function(e) {
            //alert(1);
          var xPos = e.originalEvent.touches[0].pageX;
         // alert(xPos);
          //$("[id*=ratingCGslider]").css("display","inline");

        });*/

        /*
        document.addEventListener('touchmove', function(e) {

            //var start_x = e.pageX;

            //alert(start_x);

        //var xPos = e.originalEvent.touches[0].pageX;

        //	alert(xPos);
           // e.preventDefault();
            var touch = e.touches[0];
        //    alert(touch.pageX + " - " + touch.pageY);




        }, false);*/

//alert(3);












        $(document).on('touchstart', '#imgContainer', function(e) {

            //alert(44);
            var start_x = e.originalEvent.touches[0].pageX;



            // Zur Prüfung ob touch gestartet wurde
            $("#cg_slider_release_toch").val(1);

            // Bestimmung aktuelle ID des angezeiten Images
            var realId = $("#cg_actual_slider_img_id").val();


            $('.cg_slider_image').bind('contextmenu', function(e) {
                return false;
            });

            // Font Size der Input Felder je nach größe anpassen
            var widthScreen = $(window).width();


            if(widthScreen<=800){$("#cg_user_input"+realId+"").css("font-size","12px");}
            if(widthScreen>800 && widthScreen<=1280){$("#cg_user_input"+realId+"").css("font-size","14px");	}
            if(widthScreen>1280 && widthScreen<=1600){$("#cg_user_input"+realId+"").css("font-size","16px");	}
            if(widthScreen>1600 && widthScreen<=1900){$("#cg_user_input"+realId+"").css("font-size","18px");	}
            if(widthScreen>1900){$("#cg_user_input"+realId+"").css("font-size","20px");	}



            /*
                        if(cg_ShowAlwaysInfoSlider!=1){

                            setTimeout(function(){

                                // Nur dann anzeigen wenn immer noch gehalten wird
                                var cg_slider_release_toch = $("#cg_slider_release_toch").val();

                                if(cg_slider_release_toch==1){
                                    $("#cg_user_input"+realId+"").css("display","block");
                                }
                            }, 200);
                        }
            */



            //   var realId = $("#cg_actual_slider_img_id").val();
            // $("#cg_user_input"+realId+"").css("display","block");
            //alert(widthScreen);

            $("#cg_slider_touchstart").val(start_x);

        });


        document.addEventListener('touchmove', function(e) {
            //    e.preventDefault();
            var xPos = e.changedTouches[0].pageX;
            //   $("#cg_slider_main_div").css('visibility','hidden');
            //     $("#cg_slider_main_div div").css('visibility','hidden');

            $("#cg_slider_touchend").val(xPos);

            //    var realId = $("#cg_actual_slider_img_id").val();
            //      var height = $(window).height();
            // $("#cg_slider_image_"+realId+"").width('auto');
            //$("#cg_slider_image_"+realId+"").height(height);
            // $("#cg_slider_image_"+realId+"").wrap( "<div style='z-index:9900003 !important;width:100%;height:100%;background-color:black;position:fixed;background-color:black;visibility:visible;'></div>" );
            //     $("#cg_slider_image_"+realId+"").css("z-index","9900003 !important");
            //      $("#cg_slider_image_"+realId+"").css("position","fixed");
            //    $("#cg_slider_image_"+realId+"").css("visibility","visible");

            //    $("#cg_slider_image_"+realId+"").css("left","50%");
            //   $("#cg_slider_image_"+realId+"").css("display","block");



        }, false);

//target the entire page, and listen for touch events
        /*
                $('html, body').on('touchstart touchmove', function(e){
                    //prevent native touch activity like scrolling
                    e.preventDefault();
                });
        */


        $(document).on('touchend', '#imgContainer', function(e) {
            //    e.preventDefault();
            //    $("#cg_slider_image_"+realId+"").css("z-index","auto");
            //  $("#cg_slider_image_"+realId+"").css("position","relative");

            //  $("#cg_slider_main_div").css('visibility','visible');
            //  $("#cg_slider_main_div div").css('visibility','visible');

            var realId = $("#cg_actual_slider_img_id").val();
            //  alert(1);
            if($('#cg_user_input'+realId+'').is(':visible')){
                $("#cg_user_input"+realId+"").css("display","none");
            }
            else{

                $('#cg_user_input'+realId+'').css("display","block");
            }

            //    $("#cg_slider_image_"+realId+"").css("left"," auto");


            //    $("#cg_slider_main_div").css('display','block');
            $('#close_slider_button').css('display', 'block');


            //var end_x_2 = e.changedTouches[0].pageX;
            //var end_xd = e.changedTouches[0].pageX;

            //alert("end x 1"+end_xd);
//	alert(4);

            //  var realId = $("#cg_actual_slider_img_id").val();


            /* setTimeout(function(){

var realId = $("#cg_actual_slider_img_id").val();
$("#cg_user_input"+realId+"").css("display","none");

 }, 150);*/

            // Zur Prüfung ob losgelassen wurde
            $("#cg_slider_release_toch").val(0);
            var start_x = $("#cg_slider_touchstart").val();
            var end_x = $("#cg_slider_touchend").val();

            //alert("start "+start_x);
            //alert(end_x);
            //	alert("end "+end_x_2);

            //var touchDistance = end_x-start_x;


            var classCGimage = $('#cg_actual_slider_class_value').val();

            classCGimage = parseInt(classCGimage.substr(8));

            // Damit sofort wieder reagiert wenn man nach rechts klickt
            //	if(classCGimage<1){classCGimage=1;}

            //if (typeof end_x == 'undefined') {var touchDistance = 0;}
            if (end_x.length === 0) {var touchDistance = 0;}
            else{var touchDistance = end_x-start_x;}



            //	alert(start_x);
            //	alert(end_x);
            //	alert(touchDistance);

            // Beim Zug nach rechts wird die Klassen zurück geslidet
            if(touchDistance>70){
                classCGimage = classCGimage-1;
                var goSliderAnimation = true;

            }
            /*
            if(touchDistance>200&&touchDistance<=300){
            classCGimage = classCGimage-2;
            var goSliderAnimation = true;
            }

            if(touchDistance>300&&touchDistance<=400){
            classCGimage = classCGimage-3;
            var goSliderAnimation = true;
            }

            if(touchDistance>400&&touchDistance<=500){
            classCGimage = classCGimage-4;
            var goSliderAnimation = true;
            }

            if(touchDistance>500){
            classCGimage = classCGimage-4;
            var goSliderAnimation = true;
            }*/

            // Beim Zug nach links wird die Klassen nach vorne geslidet
            if(touchDistance<-70){



                classCGimage = classCGimage+1;
                var goSliderAnimation = true;
            }

            /*
            if(touchDistance<-200&&touchDistance>=-300){
            classCGimage = classCGimage+2;
            var goSliderAnimation = true;
            }

            if(touchDistance<-300&&touchDistance>=-400){
            classCGimage = classCGimage+3;
            var goSliderAnimation = true;
            }

            if(touchDistance<-400&&touchDistance>=-500){
            classCGimage = classCGimage+4;
            var goSliderAnimation = true;
            }

            if(touchDistance<-500){
            classCGimage = classCGimage+4;
            var goSliderAnimation = true;
            }
            */
            //alert(classCGimage);
            //alert(cg_shown_images);
            // Prüfen ob Slider am Ende oder am Anfang ist
            if(classCGimage<1){classCGimage=1;}
            if(classCGimage>cg_shown_images){classCGimage=cg_shown_images;}

            var real_picture_id = parseInt($(".cg_image"+classCGimage).attr("data-cg_image_id"));
            //alert(real_picture_id);


            classCGimage = "cg_image"+classCGimage;

            $('#cg_actual_slider_class_value').val(classCGimage);

            // Echte ID wird vergeben
            //var real_picture_id = $("cg_show"+classCGimage).attr("id");


            //real_picture_id = parseInt(real_picture_id.substr(7));
            $('#cg_actual_slider_img_id').val(real_picture_id);


            var allCGimages = $('.cg_show').length;

            if(classCGimage>1 || classCGimage<allCGimages ){
                // Vermeiden von doppeltem Vorkommen des Rating buttons, weil ein anderes Bild mit angezeigt wird
                /*                var cg_img_boxId = parseInt($("#cg_actual_slider_class_value").val().slice(8))-1;// Aktuelles Bild wird ermittelt

                                $(".cg_img_box").css("visibility","hidden");
                                $(".cg_img_box").children("div").css("visibility","hidden");

                                $("#cg_img_box_"+cg_img_boxId+"").css("visibility","visible");
                                $("#cg_img_box_"+cg_img_boxId+"").children("div").css("visibility","visible");*/
            }

            // Picture ID crypten und oben einfügen
            //  var crypted_picture_id =(real_picture_id+8)*2+100000;
            // document.location.hash = "img"+crypted_picture_id;


            //var isMobile = false; //initiate as false

            //alert(classCGimage);
            // device detection, damit elemente beim klicken auf imgContainer nicht verschwinden und rating und comment auf mobile funktionieren
            if(isMobile==true && goSliderAnimation == true){


                // Funktion zur Ausführung der Berechnung
                // !!! SEHR WICHTIG !!! SetTimeOut hat gesetzt zu werden damit click event auf rating und comment div reagiert!!!
                setTimeout(function(){  cgJsClass.slider.slide.init(classCGimage,''); }, 10);
            }



            // Muss wieder auf normal gesetzt werden. Ansonsten wiederholt sich die zuletzt ermittelte Position
            $("#cg_slider_touchend").val("");







        });





        /*
            $("#imgContainer").bind('touchstart', function(e){



            }).bind('touchend', function(e){


            });*/






// Reaktion auf touch --- ENDE


// CG Slider Full Size


        /*        $( window ).resize(function() {

                    if(isMobile==true){
                        // var widthCGoverlay = $(document).width();
                        var widthCGoverlay = $('#cg_slider_main_div').width();
                    }
                    else{
                        //var widthCGoverlay = $(window).width();
                        var widthCGoverlay = $('#cg_slider_main_div').width();
                    }

                    //alert(widthCGoverlay);

                    if(widthCGoverlay<=800){
                        exitFullSizeSlider();
                       // return false;
                    }
                    else{
                      //  return false;
                        fullSizeSlider();
                    }

                });*/


        $(document).on('click', '#cg_slider_full_size_view_icon_div', function(e){


            cgJsClass.slider.vars.fullSizeResized = true;
            cgJsClass.slider.resize.fullSizeSlider();
            cgJsClass.slider.resize.hideCarrousel();
            cgJsClass.slider.resize.init();

            //cgSliderResize();

        });

        $(document).on('click', '#cg_slider_exit_full_size_view_icon_div', function(e){


            cgJsClass.slider.vars.fullSizeResized = false;
            cgJsClass.slider.resize.exitFullSizeSlider();
            cgJsClass.slider.resize.showCarrousel();
            cgJsClass.slider.resize.init();

            //cgSliderResize();

            // Ansonsten wird die rechte Box kurz zu sehen sein
            $('.cg_img_box').css('visibility','hidden');
            var classCGimageNumber = $('#cg_actual_slider_class_value').val();
            classCGimageNumber = parseInt(classCGimageNumber.substr(8));
            $('#cg_img_box_'+classCGimageNumber+'').css('visibility','visible');


        });





// CG Slider Full Size --- ENDE










//---------------------- Mousedown Mouseup Events für Image Container --- ENDE --------------------------------
//----------------------------------------------------------------------------------------------------
//----------------------------------------------------------------------------------------------------



        // Funktion zur Ausführung der Berechnung


        // Funktion zur Ausführung der Berechnung --- ENDE






        // Funktion zur Ausführung der Berechnung --- ENDE













        // Wenn Picture ID Parameter mitgeschickt wird und gallery Script aktiviert ist --- ENDE


        function checkIfCarrouselHasToBeOpenedOrClosed(){



        }



        function cgOpenGallerySlider(classCGimage){



        }



// Show/Zeige ImgContainer für Slider   ---- ENDE



// Keypress animation



//var event = new KeyboardEvent('keydown');

        $(document).keydown(function(e) {

            // Facebook Felder zum Reloaden freigeben
            $("#cg_slider_frame_reloaded").val(0);
            $("#cg_gallery_frame_reloaded").val(0);

            if($('#imgContainer').is(':visible')) {

                if(e.which == 37) { // left

                    //Eintragen das linker Pfeil gerade geklickt wurde (Ausgetragen wird am Ende in der Funktion)
                    //$('#cg_check_arrow_left_click').val(1);

                    //alert(1);






                    var classCGimage = $('#cg_actual_slider_class_value').val();
                    //	alert(classCGimage);
                    classCGimage = parseInt(classCGimage.substr(8));
                    //Der vorherige Slider Count wird gesetzt
                    $('#cg_slider_class_value_before').val(classCGimage);

                    //	 alert(classCGimage);
                    if(classCGimage>1){

                        // Vermeiden von doppeltem Vorkommen des Rating buttons, weil ein anderes Bild mit angezeigt wird
                        var cg_img_boxId = parseInt($("#cg_actual_slider_class_value").val().slice(8))-1;// Aktuelles Bild wird ermittelt

                        $(".cg_img_box").css("visibility","hidden");
                        $(".cg_img_box").children("div").css("visibility","hidden");

                        $("#cg_img_box_"+cg_img_boxId+"").css("visibility","visible");
                        $("#cg_img_box_"+cg_img_boxId+"").children("div").css("visibility","visible");




                        // Nur dann Stop verursachen, wenn danach Animation ausgeführt wird, ansonsten bleibt das Fenster stecken bei zu schnellem öfteren drücken nach links oder rechts am Ende.
                        $( "#imgContainer" ).stop();

                        var allCGimages = $('.cg_show').length;
                        // Damit sofort wieder reagiert wenn man nach links klickt
                        if(classCGimage>allCGimages){classCGimage=allCGimages;}


                        classCGimage = classCGimage-1;

                        classCGimage = "cg_image"+classCGimage;

                        $('#cg_actual_slider_class_value').val(classCGimage);
                        //  alert(classCGimage);

                        // Echte ID wird vergeben
                        var real_picture_id = $("."+classCGimage+"").attr("id");
                        //	alert(real_picture_id);
                        real_picture_id = parseInt(real_picture_id.substr(11));
                        //	alert(real_picture_id);
                        $('#cg_actual_slider_img_id').val(real_picture_id);

                        // Picture ID crypten und oben einfügen
                        // var crypted_picture_id =(real_picture_id+8)*2+100000;
                        //  alert(crypted_picture_id);
                        //document.location.hash = "img"+crypted_picture_id;

                        cgJsClass.slider.slide.init(classCGimage,'');



                    }
                    else{
                        return false;
                    }


                }
                else if(e.which == 39) { // right
                    //alert(2);
                    //  return false;
                    //Eintragen das rechter Pfeil gerade geklickt wurde (Ausgetragen wird am Ende in der Funktion)
                    //$('#cg_check_arrow_right_click').val(1);




                    var classCGimage = $('#cg_actual_slider_class_value').val();
                    //    alert(classCGimage);
                    classCGimage = parseInt(classCGimage.substr(8));

                    $('#cg_slider_class_value_before').val(classCGimage);

                    var allCGimages = parseInt($('.cg_show').length);

                    // Aktuelle Anzahl der sichtbaren Bilder
                    // var cg_count = $('#cg_count').val();

                    // alert(allCGimages);
                    // alert("classCGimage: "+classCGimage);

                    if(classCGimage<allCGimages){


                        // Vermeiden von doppeltem Vorkommen des Rating buttons, weil ein anderes Bild mit angezeigt wird
                        var cg_img_boxId = parseInt($("#cg_actual_slider_class_value").val().slice(8))+1;// Aktuelles Bild wird ermittelt

                        $(".cg_img_box").css("visibility","hidden");
                        $(".cg_img_box").children("div").css("visibility","hidden");

                        $("#cg_img_box_"+cg_img_boxId+"").css("visibility","visible");
                        $("#cg_img_box_"+cg_img_boxId+"").children("div").css("visibility","visible");





                        // alert("action");
                        // Nur dann Stop verursachen, wenn danach Animation ausgeführt wird, ansonsten bleibt das Fenster stecken bei zu schnellem öfteren drücken nach links oder rechts am Ende.
                        $( "#imgContainer" ).stop();
                        //alert(classCGimage);
                        // Damit sofort wieder reagiert wenn man nach rechts klickt
                        //if(classCGimage==1){classCGimage=1;}

                        // alert(classCGimage);
                        classCGimage = classCGimage+1;

                        // alert(classCGimage);

                        classCGimage = "cg_image"+classCGimage;

                        $('#cg_actual_slider_class_value').val(classCGimage);

                        // Echte ID wird vergeben
                        var real_picture_id = $("."+classCGimage).attr("id");

                        real_picture_id = parseInt(real_picture_id.substr(11));
                        $('#cg_actual_slider_img_id').val(real_picture_id);


                        // Picture ID crypten und oben einfügen
                        //  var crypted_picture_id =(real_picture_id+8)*2+100000;

                        // document.location.hash = "img"+crypted_picture_id;

                        // Funktion zur Ausführung der Berechnung
                        // alert(classCGimage);
                        cgJsClass.slider.slide.init(classCGimage,'');



                    }
                    else{
                        //  alert(3);
                        return false;
                    }

                }

            }

        });



// Keypress animation --- ENDE













// Pfeilimages klicken rechts links

//var clicks = 0;



        $(function(){
            $("#cg_slider_arrow_left_img").on("click", function(e){

                //  if(clicks>0){return false;}

                // Facebook Felder zum Reloaden freigeben
                $("#cg_slider_frame_reloaded").val(0);
                $("#cg_gallery_frame_reloaded").val(0);

                // Vermeiden von doppeltem Vorkommen des Rating buttons, weil ein anderes Bild mit angezeigt wird
                var cg_img_boxId = parseInt($("#cg_actual_slider_class_value").val().slice(8))-1;// Aktuelles Bild wird ermittelt

                $(".cg_img_box").css("visibility","hidden");
                $(".cg_img_box").children("div").css("visibility","hidden");

                $("#cg_img_box_"+cg_img_boxId+"").css("visibility","visible");
                $("#cg_img_box_"+cg_img_boxId+"").children("div").css("visibility","visible");



                //Eintragen das linker Pfeil gerade geklickt wurde (Ausgetragen wird am Ende in der Funktion)
                //$('#cg_check_arrow_left_click').val(1);

                // Comments Slider soll ausgeblendet werden falls an
                $('#cg_comments_slider_div').css("display","none");
                $('#close_slider_comments_button_img').css("display","none");



                if( $("#imgContainer").is(':animated') ) {return false;}
                else{
                    $(this).fadeTo(100, 0.5).fadeTo(100, 1.0);
                }


                //clicks++;  //count clicks
//alert(clicks);
                //  if(clicks === 1) {



                var classCGimage = $('#cg_actual_slider_class_value').val();

                classCGimage = parseInt(classCGimage.substr(8));
                $('#cg_slider_class_value_before').val(classCGimage);






                if(classCGimage<1){
                    classCGimage=1;
                    $("#cg_slider_arrow_left_img").css("display","none");
                }


                classCGimage = classCGimage-1;

                classCGimage = "cg_image"+classCGimage;

                //alert(classCGimage);

                //     return false;

                // Echte ID wird vergeben
                $('#cg_actual_slider_class_value').val(classCGimage);

                // Echte ID wird vergeben
                var real_picture_id = $("."+classCGimage).attr("data-cg_image_id");
                //      alert(real_picture_id);
                // real_picture_id = parseInt(real_picture_id.substr(11));
                //     alert(real_picture_id);
                $('#cg_actual_slider_img_id').val(real_picture_id);

                cgJsClass.slider.slide.init(classCGimage,'');



            });

        });








        $(function(){
            $("#cg_slider_arrow_right_img").on("click", function(e){
                //alert(clicks);
                //if(clicks>0){return false;}
                //  $('#cg_slider_arrow_right_img').css('pointer-events','none;');

                // Facebook Felder zum Reloaden freigeben
                $("#cg_slider_frame_reloaded").val(0);
                $("#cg_gallery_frame_reloaded").val(0);

                // Vermeiden von doppeltem Vorkommen des Rating buttons, weil ein anderes Bild mit angezeigt wird
                var cg_img_boxId = parseInt($("#cg_actual_slider_class_value").val().slice(8))+1;// Aktuelles Bild wird ermittelt

                $(".cg_img_box").css("visibility","hidden");
                $(".cg_img_box").children("div").css("visibility","hidden");

                $("#cg_img_box_"+cg_img_boxId+"").css("visibility","visible");
                $("#cg_img_box_"+cg_img_boxId+"").children("div").css("visibility","visible");






                //Eintragen das rechter Pfeil gerade geklickt wurde (Ausgetragen wird am Ende in der Funktion)
                //$('#cg_check_arrow_right_click').val(1);


                if( $("#imgContainer").is(':animated') ) {return false;}
                else{
                    $(this).fadeTo(100, 0.5).fadeTo(100, 1.0);
                }

                //count clicks
//alert(clicks);
                //   if(clicks === 1) {



                var classCGimage = $('#cg_actual_slider_class_value').val();

                classCGimage = parseInt(classCGimage.substr(8));

                $('#cg_slider_class_value_before').val(classCGimage);

                // Damit sofort wieder reagiert wenn man nach rechts klickt
                //	alert(classCGimage);
                var allCGimages = $('.cg_show').length;
                // Damit sofort wieder reagiert wenn man nach links klickt
                if(classCGimage>allCGimages){
                    classCGimage=allCGimages;
                    $("#cg_slider_arrow_right_img").css("display","none");
                }


                classCGimage = classCGimage+1;


                classCGimage = "cg_image"+classCGimage;

                $('#cg_actual_slider_class_value').val(classCGimage);

                // Echte ID wird vergeben
                var real_picture_id = $("."+classCGimage).attr("data-cg_image_id");
                //console.log(classCGimage);
                //console.log(real_picture_id);
                // real_picture_id = parseInt(real_picture_id.substr(11));

                $('#cg_actual_slider_img_id').val(real_picture_id);

                // alert(classCGimage);

                // Picture ID crypten und oben einfügen
                // var crypted_picture_id =(real_picture_id+8)*2+100000;
                //document.location.hash = "img"+crypted_picture_id;

                // Funktion zur Ausführung der Berechnung

                //	clicks++;
                // setTimeout(function(){ clicks = 0; }, 500);
                cgJsClass.slider.slide.init(classCGimage,'');






                // Damit sich die Image Seite nicht öffnet
                //return false;
                //after action performed, reset counter


                /*  } else {

                      clearTimeout(timer);    //prevent single-click action
                                                        var classCGimage = $('#cg_actual_slider_class_value').val();

                                             classCGimage = parseInt(classCGimage.substr(8));

                                             // Damit sofort wieder reagiert wenn man nach rechts klickt
                                          if(classCGimage<1){classCGimage=1;}


                                          classCGimage = classCGimage+2;


                                          classCGimage = "cg_image"+classCGimage;

                                         $('#cg_actual_slider_class_value').val(classCGimage);

                                                 // Echte ID wird vergeben
                                              var real_picture_id = $("."+classCGimage).attr("id");
                                              real_picture_id = parseInt(real_picture_id.substr(11));
                                              $('#cg_actual_slider_img_id').val(real_picture_id);


                                              // Picture ID crypten und oben einfügen
                                                var crypted_picture_id =(real_picture_id+8)*2+100000;
                                                          document.location.hash = "img"+crypted_picture_id;

                                         // Funktion zur Ausführung der Berechnung

                                       cgSliderIMGdistanceCounting(classCGimage,'');

                                       // Damit sich die Image Seite nicht öffnet
                                      //return false;
                      clicks = 0;             //after action performed, reset counter
                  }

              })
              .on("dblclick", function(e){
                  e.preventDefault();  //cancel system double-click event*/
            });
        });









// Kommentar in Gallery

        if(cg_comment_in_gallery==1){


            $(document).on('click', '[id*=rating_cgc-]', function(e){


                //  alert(3);

                // Overlay sichtbar machen

                // $('#cg_overlay').css('display', 'block');
                // $('#cg_overlay').toggle();

                // $('#cg_overlay').css('opacity', '0.4');
//   $('#cg_slider_arrow_right').toggle();

                //var widthScreen = $('body').width();
                var widthScreen = $('#cg_slider_main_div').width();
                var marginLeftCommentDiv = (widthScreen-800)/2;

                $('#cg_comments_slider_div').css('left', marginLeftCommentDiv);


                $('#cg_comments_slider_div').toggle();
                var marginLeftCommentCloseDiv = (widthScreen-800)/2+727;

                // alert(4);
                // $('#close_slider_comments_button').css('left', marginLeftCommentCloseDiv);
                $('#cg_comments_slider_div').css('left', marginLeftCommentDiv);
                $('#close_slider_comments_button').toggle();
                var idImageForComment =  parseInt(this.id.substr(11));
                // Haupthidden Feld, dass aktuell geöffnete comment image id zeigt
                $('#cg_slider_comment_picture_id').val(idImageForComment);

                //$('#cg_actual_slider_class_value').val(idImageForComment);
                // Funktion zur Ausführung der Berechnung


                // Prüfen der Wordpress Session id
                var check = $("#cg_check").val();

                //alert(2);
                // I am not a robot checkbox soll auftauchen
                if ($("#"+check+"").length > 0){

                    //alert(3);


                    var onlyCheck = 1;



                }
                else{

                    var cg_language_i_am_not_a_robot = $('#cg_language_i_am_not_a_robot').val();
                    $("#cg_i_am_not_a_robot").append("<input type='checkbox' value='"+check+"' class='"+check+" id='cg_i_am_not_a_robot_checkbox' name='"+check+"' id='"+check+"' '><label for='cg_i_am_not_a_robot_checkbox'>"+cg_language_i_am_not_a_robot+"</label>");

                }


                $("#cg_open_slider_comment").click();

                //  $('#imgContainer').fadeIn('slow');
                // $('#imgContainer').toggle();




                return false;
            });

// Kommentar in Gallery --- ENDE

        }




// Rating Pop Up im Slider, bei hide until vote


        /*
        if(cg_hide_until_vote==1){

               $(document).on('click', '[id*=cg_plz_vote]', function(e){

        //var cg_real_id = $(this).find(".cg_real_id").val();
        var cg_real_id =  parseInt(this.id.substr(11));



        //alert(cg_real_id);

            //var starOnUrl = $("#cg_star_on_slider").val();
            var starOffUrl = $("#cg_star_off_slider").val();

                if(cg_allow_rating==1){

                    var ratingBlock = "<div style='display:inline-block;float:left;width:21px;height:21px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star1' style='float:left;cursor:pointer;' alt='1' id='cg_rate_star"+cg_real_id+"'></div>"+
                      "<div style='display:inline-block;float:left;width:21px;height:21px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star2' style='float:left;cursor:pointer;' alt='2' id='cg_rate_star"+cg_real_id+"'></div>"+
                      "<div style='display:inline-block;float:left;width:21px;height:21px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star3' style='float:left;cursor:pointer;' alt='3' id='cg_rate_star"+cg_real_id+"'></div>"+
                      "<div style='display:inline-block;float:left;width:21px;height:21px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star4' style='float:left;cursor:pointer;' alt='4' id='cg_rate_star"+cg_real_id+"'></div>"+
                      "<div style='display:inline-block;float:left;width:21px;height:21px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star5' style='float:left;cursor:pointer;' alt='5' id='cg_rate_star"+cg_real_id+"'></div>";

                }

                   if(cg_allow_rating==2){

                    var ratingBlock = "<input type='hidden' class='cg_check_voted' value='0' id='cg_check_voted"+cg_real_id+"'>"+
                    "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star1' style='float:left;cursor:pointer;' alt='6' id='cg_rate_star"+cg_real_id+"'></div>";

                }

                $("#ratingCGslider"+cg_real_id).empty();

         $("#ratingCGslider"+cg_real_id).append(ratingBlock);


        });



        }*/

        /*
        if(cg_hide_until_vote==1 && cg_vote_in_gallery==1){

               $(document).on('click', '[class*=cg_hide_until_vote_rate]', function(e){

        //var cg_real_id = $(this).find(".cg_real_id").val();
        var cg_real_id =  $(this).parent().attr("id");
            cg_real_id =  parseInt(cg_real_id.substr(21));

        var cg_ShowAlways =  $("#cg_ShowAlways").val();



        var	cg_real_row = $(this).attr("class");
            cg_real_row = parseInt(cg_real_row.substr(23));
            //alert(cg_vote_in_gallery);
            //alert(cg_real_row);
            //Show always vote, comments and info in gallery view:
            if(cg_ShowAlways!=1){
            $("#cg_hide"+cg_real_row).hide();
            }
        //alert(cg_real_row);



            //var starOnUrl = $("#cg_star_on_slider").val();
            var starOffUrl = $("#cg_star_off_slider").val();

            // Achtung! Vorher diese Prüfung notwendig falls hide until vote und vote out gallery aktiviert sind! Der Klickevent von Children Element wird von der Jquery Bibliothek als erstes bearbeitet.
          // Auch wenn dieser weiter unten im Script passiert.
            $("#cg_hide_until_vote_actual").val(1);




            var ratingBlock = "<input type='hidden' class='cg_check_voted' value='0' id='cg_check_voted"+cg_real_id+"'>"+
            "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star1' style='float:left;cursor:pointer;' alt='1' id='cg_rate_star"+cg_real_id+"'></div>"+
              "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star2' style='float:left;cursor:pointer;' alt='2' id='cg_rate_star"+cg_real_id+"'></div>"+
              "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star3' style='float:left;cursor:pointer;' alt='3' id='cg_rate_star"+cg_real_id+"'></div>"+
              "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star4' style='float:left;cursor:pointer;' alt='4' id='cg_rate_star"+cg_real_id+"'></div>"+
               "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star5' style='float:left;cursor:pointer;' alt='5' id='cg_rate_star"+cg_real_id+"'></div>";

                $("#cg_gallery_rating_div"+cg_real_id).empty();

         $("#cg_gallery_rating_div"+cg_real_id).append(ratingBlock);


        });



        }*/


// Rating Pop Up im Slider, bei hide until vote --- ENDE



// Schließen Layer





        $(document).on('mouseup', '[class*=cg_hide_info_div]', function(e){

            //alert(1);

            var cg_realId = $(this).attr("id");
            var cg_class_hide_show_info = $(this).attr("class");
            var cg_realId = parseInt(cg_realId.substr(16));

            // Achtung. visible check reagiert nur auf display none, nicht auf visiblity css
            if(cg_class_hide_show_info=="cg_hide_info_div_yes") {
                //alert(2);
                $(this).css("background-image","url("+cg_hide_info_png+")");
                $(this).attr("class","cg_hide_info_div_no");
                $("#cgFacebookDiv"+cg_realId+"").css("display","none");
                $("#commentsCGslider"+cg_realId+"").css("display","none");
                $("#ratingCGslider"+cg_realId+"").css("display","none");
                $("#cg_url_slider_div"+cg_realId+"").css("display","none");
                $("#cg_user_input"+cg_realId+"").css("display","none");

            }
            else{
                //alert(3);
                $(this).css("background-image","url("+cg_show_info_png+")");
                $(this).attr("class","cg_hide_info_div_yes");
                //$(this).find(".cg_hide_info_img").css("display","inline");
                $("#cgFacebookDiv"+cg_realId+"").css("display","inline");
                $("#commentsCGslider"+cg_realId+"").css("display","inline");
                $("#ratingCGslider"+cg_realId+"").css("display","inline");
                $("#cg_url_slider_div"+cg_realId+"").css("display","inline");
                $("#cg_user_input"+cg_realId+"").css("display","block");

            }



        });








        /*	   $(document).on('click', '#cg_overlay', function(e){




        });*/



// Schließen Layer --- ENDE







    }// End Slider/Out of Gallery Script


});