var cgJsClass = cgJsClass || {};
cgJsClass.slider = {};
cgJsClass.slider.open = {};
cgJsClass.slider.close = {};
cgJsClass.slider.slide = {};
cgJsClass.slider.resize = {};
cgJsClass.slider.vars = {};

jQuery(document).ready(function($){


    cgJsClass.slider.vars.windowHeight = $(window).height();
    cgJsClass.slider.vars.checkMobile();
    cgJsClass.slider.vars.checkPicsOnSite();
    cgJsClass.slider.open.checkUrl();

    var windowWidth = $(window).width();
    var windowHeight = $(window).height();

    if(windowWidth/windowHeight>=1){
        cgJsClass.slider.vars.actualWindowRatio = 'horizontal';
    }
    else{
        cgJsClass.slider.vars.actualWindowRatio = 'vertical';
    }




    var x = document.getElementsByName("viewport");
    if(x.length >= 1){
        //  alert(1);
        cgJsClass.slider.vars.viewportContent = x[0].getAttribute('content');
    }
    else{
        cgJsClass.slider.vars.noViewport = true;
    }


    $(window).on('hashchange', function() {

        var real_picture_id = $('#cg_actual_slider_img_id').val();
        var crypted_picture_id =(parseInt(real_picture_id)+8)*2+100000;

        cgJsClass.slider.vars.hashCountChangeGeneral = crypted_picture_id;
     //   console.log(cgJsClass.slider.vars.hash);
     //   console.log(3);

        if(cgJsClass.slider.vars.hash!=document.location.hash){
          //  console.log(2);
            cgJsClass.slider.close.init();
            cgJsClass.slider.vars.hash = document.location.hash;
        }

    });

    // alert(cgJsClass.slider.vars.cg_OnlyGalleryView);
    $(document).on('click', '[class*=cg_image]', function(e){

        if(cgJsClass.slider.vars.cg_activate_gallery_slider==1){
            var classCGimage = $(this).attr('class').split(' ')[0];
           // console.log(classCGimage);
            cgJsClass.slider.open.init(classCGimage,false);
            // Ganz wichtig return false hier! Ansonsten wird versucht single image view zu laden.
            return false;
        }
        // return false;
    });

    $( window ).resize(function() {

        // Wichtig für wenn url mit hash aufgerufen wurde, dann wird resize angewendet ohne dass soll, aber nur 1 mal
        if(cgJsClass.slider.vars.doNotResize==1){
            cgJsClass.slider.vars.doNotResize=0;
            return false;
        }
        else{
            cgJsClass.slider.vars.doNotResize=0;
        }
     //   alert(cgJsClass.slider.vars.isMobile);
        // Ist nur wichtig wenn mobile ist, damit die Tastatur nicht jedes Mal wegspring wenn man ein Feld gefocust hat
        if(cgJsClass.slider.vars.isMobile==true){

            var commentFrameOpened = cgJsClass.slider.vars.commentFrameOpened;

            // Achtung! Für Mobile vergleich mit innerHeight und innwerWidth arbeiten !!!!!
            var windowWidth = window.innerWidth;
           // $('#cg_overlay').css('position','static');
        //   $('#cg_overlay').css('overflow','visible');
            if(commentFrameOpened){
                var windowHeight = $('#cg_overlay').height();
            }
            else{
                var windowHeight = $(window).height();
            }


            if(windowWidth/windowHeight>=1){
                var windowRatioCheck = 'horizontal';
            }
            else{
                var windowRatioCheck = 'vertical';
            }

            if($('#cg_overlay').is(':visible')==false && commentFrameOpened){
                $('#cg_comments_slider_div').width(windowWidth);
            }

            //alert(commentFrameOpened);
            // Ein Mal resize hier falls sich ratio geändert hat!
            if(commentFrameOpened==true && windowRatioCheck!=cgJsClass.slider.vars.actualWindowRatio && cgJsClass.slider.vars.isMobile==true){
                cgJsClass.slider.resize.init();
            }
            // Wenn es Mobile ist, Commentar Frame offen ist und Window Verhältnis sich nicht geändert hat, dann braucht man nicht auszuführen
            if(commentFrameOpened==true && windowRatioCheck==cgJsClass.slider.vars.actualWindowRatio && cgJsClass.slider.vars.isMobile==true){

                return false;
            }
        }

        cgJsClass.slider.resize.init();



        if(cgJsClass.slider.vars.isMobile==true){
            var windowWidth = window.innerWidth;
            if(commentFrameOpened){
                var windowHeight = $('#cg_overlay').height();
            }
            else{
                var windowHeight = $(window).height();
            }

            if($('#cg_overlay').is(':visible')==false && commentFrameOpened){
                $('#cg_comments_slider_div').width(windowWidth);
            }


            if(windowWidth/windowHeight>=1){
                cgJsClass.slider.vars.actualWindowRatio = 'horizontal';
            }
            else{
                cgJsClass.slider.vars.actualWindowRatio = 'vertical';
            }
        }

    });



});