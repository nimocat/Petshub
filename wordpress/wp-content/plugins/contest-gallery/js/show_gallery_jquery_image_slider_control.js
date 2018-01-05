jQuery(document).ready(function($){
   // alert(cgJsClass.slider.vars.isMobile);
    if(cgJsClass.slider.vars.isMobile==false){

        $(document).on('click', '.cg_user_input', function(e){

            $(this).slideUp(1000, function() {
                // Animation complete.
                $(this).closest('.cg_slider_info_div').find('.cg_user_input_fade_in_arrow_container').css('display','block');

            });

        });

        $(document).on('click', '.cg_user_input_fade_in_arrow_container', function(e){

            $(this).css('display','none');
            $(this).closest('.cg_slider_info_div').find('.cg_user_input').slideDown(1000);
            //$(this).closest('.cg_slider_info_div').find('.cg_user_input_fade_in_arrow_container').css('display','block');
        });


    }




});

