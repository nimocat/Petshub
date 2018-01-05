jQuery(document).ready(function($){
	
  $("#cg_changes_saved").fadeOut(3000);
  
$( "#questionUrl" ).hover(function() {
   $('#answerUrl').toggle();
    $(this).css('cursor','pointer');
});

$( "#questionLink" ).hover(function() {
   $('#answerLink').toggle(); 
    $(this).css('cursor','pointer');
});

  
});