(function ($) {
    $(function () {
	   //All the backend js for the plugin 
       
       /*
       Settings Tabs Switching 
       */
       $('.ap-tabs-trigger').click(function(){
        $('.ap-tabs-trigger').removeClass('ap-active-tab');
        $(this).addClass('ap-active-tab');
        var board_id = 'board-'+$(this).attr('id');
        $('.ap-tabs-board').hide();
        $('#'+board_id).show();
       });
       
       /**
        * Captcha Type change
        */
       $('.ap-captcha-type').change(function(){
           var value = $(this).val();
           if(value=='google'){
               $('.ap-google-captcha-fields').show();
           }else{
               $('.ap-google-captcha-fields').hide();
           }
       });
       
	   
	});
}(jQuery));
