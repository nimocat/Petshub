jQuery(document).ready(function($){
	/*
	$('head').append('<meta property="og:url" content="https://www.contest-gallery.com/test-cg-1/?picture_id=100586&1=2&2=1&3=0#cg-begin" />');
$('head').append("<meta property='og:type'          content='website' />");
$('head').append('<meta property="og:title"         content="12345" />');
	$('head').append('<meta property="og:description"   content="Your description" />'); 
	$('head').append('<meta property="og:image" content="https://www.contest-gallery.com/test-cg-1/wp-content/uploads/contest-gallery/gallery-id-8/1446277444_78contest-gallery_247172002-624width.jpg" />');*/

//	alert(123123);

	
	var cg_show_image_file = $("#cg_show_image_file").val();
	var cg_language_i_am_not_a_robot = $("#cg_language_i_am_not_a_robot").val();

    function centerImage(){
        var minusMarginLeftImage = ($("#cg_img_gallery").width()-$("#cg_img_div").width())/2;
        $("#cg_img_gallery").css('margin-left','-'+minusMarginLeftImage+'px');
    }



	if($("#cg_ScaleAndCut").val()==0){
        centerImage();
	}


	
	var IPcheck = $("#ip_check").val();
	
	
	//$(this).css("cursor: default");
	
	if(IPcheck==1){
		
		var starsBoxes = $("#cg_rate_stars_image_hidden").html();
		
		//$("#cg_rate_stars_image").css("cursor: default");
		//$("#cg_rate_stars_image").css("cursor: default");
		
		$("#cg_rate_stars_image").hover().css("cursor","pointer");
		
		$(document).on('click', '#cg_rate_stars_image', function(e){
	
	
//alert("action");

$("#cg_rate_stars_image").empty();
$("#cg_rate_stars_image").append(starsBoxes);
 

});
	
	
	}
	
	
	
		var check = $("#cg_check").val();
		
		
		// Prüfen ob es sich um show image file handelt das die selbe id auch im slider file zwecks Übersetzung
		if (typeof cg_show_image_file == 'undefined') {
		
		return false;
		
		}
		
		else{
			
			$("#cg_i_am_not_a_robot_single_page_image").prepend("<input type='checkbox' value='"+check+"' name='"+check+"' id='"+check+"' >");
			
		}



	  $( "#cg_arrow_left").hide();
	  $( "#cg_arrow_right").hide();
	  $( "#cg_arrow_random").hide();


	/*
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));*/


		 $( "#cg_arrow_left" ).mouseout(function() {
	  $( "#cg_arrow_left").hide();
	  $( "#cg_arrow_right").hide();
	  $( "#cg_arrow_random").hide();
	  });

	  $( "#cg_arrow_random" ).mouseout(function() {
	  $( "#cg_arrow_left").hide();
	  $( "#cg_arrow_right").hide();
	  $( "#cg_arrow_random").hide();
	  });
	  
	  		 $( "#cg_arrow_right" ).mouseout(function() {
	  $( "#cg_arrow_left").hide();
	  $( "#cg_arrow_right").hide();
	  $( "#cg_arrow_random").hide();
	  });

	  $( "#cg_img_gallery" ).mouseover(function() {
	  $( "#cg_arrow_left").toggle();
	  $( "#cg_arrow_right").toggle();
	  $( "#cg_arrow_random").toggle();
	  });
	  
		  $("#cg_arrow_left").mouseover(function() {
		  $("#cg_arrow_left").toggle();
          $("#cg_arrow_right").toggle();		  
          $("#cg_arrow_random").toggle();		  
		  });	

		  $("#cg_arrow_random").mouseover(function() {
		  $("#cg_arrow_left").toggle();
          $("#cg_arrow_right").toggle();		  
          $("#cg_arrow_random").toggle();		  
		  });
		  
		  $("#cg_arrow_right").mouseover(function() {
		  $("#cg_arrow_left").toggle();
          $("#cg_arrow_right").toggle();		  
          $("#cg_arrow_random").toggle();		  
		  });
		  
		 $( "#cg_img_gallery" ).mouseout(function() {
	  $( "#cg_arrow_left").hide();
	  $( "#cg_arrow_right").hide();
	  $( "#cg_arrow_random").hide();
	  }); 
	  
	  
	// Resizing if image bigger then screen, mobile view for example	


	function resizeImage(){

				var widthImageParent = $("#cg-start").parent().width();
			var widthImageCommentBoxesConfigured = $("#cg_width_gallery").val();
			var heightImageCommentBoxesConfigured = $("#cg_height_image").val();
			var widthImageCommentBoxes = $("#cg-start").val();
      //  console.log(widthImageCommentBoxesConfigured);
      //  console.log(widthImageParent);
       // console.log(widthImageCommentBoxesConfigured);


        if(widthImageParent > widthImageCommentBoxesConfigured){
            widthImageParent = widthImageCommentBoxesConfigured;
            var newWidthImageCommentBoxes = widthImageParent;
            $("#cg_img_gallery").css('width','auto');
            if($("#cg_ScaleAndCut").val()==1){
                var newHeightImgDiv = $("#cg_img_gallery").height();
                console.log(newHeightImgDiv);
             //   $("#cg_img_div").css('height','auto');
                $("#cg_img_div").height(newHeightImgDiv);
            }
		}
		else{
            var newWidthImageCommentBoxes = widthImageParent;
            $("#cg_img_gallery").css('width',newWidthImageCommentBoxes);
            if($("#cg_ScaleAndCut").val()==1){
                var newHeightImgDiv = $("#cg_img_gallery").height();
                $("#cg_img_div").height(newHeightImgDiv);
            }
		}


	//	if(widthImageParent < widthImageCommentBoxesConfigured || (widthImageParent < widthImageCommentBoxesConfigured && widthImageCommentBoxes <= widthImageCommentBoxesConfigured)){
		//	console.log(1);
			//var changeInPercent = widthImageParent/widthImageCommentBoxesConfigured;
			//alert(widthImageParent);
			//alert(changeInPercent);
			//var newHeightImgDiv = heightImageCommentBoxesConfigured*changeInPercent;


			//alert(newHeightImgDiv);
			

			//var newWidthImageCommentBoxes = widthImageParent-12-12;
			
			$("#cg-start").css('width',newWidthImageCommentBoxes);
			$("#cg_main_div").css('width',newWidthImageCommentBoxes);
			$("#cg_img_div").css('width',newWidthImageCommentBoxes);



			//var newHeightImgDiv = $("#cg_img_gallery").height();
			//alert(newHeightImgDiv);
	//		$("#cg_img_div").css('height',newHeightImgDiv+'px');
			$("#cg_img_gallery").css('left',0);
			$("#cg_img_gallery").css('right',0);
			$("#rating-div").css('width',newWidthImageCommentBoxes);
			//$("#cg_plz_vote_single_image_view").css('float','left');
			$("#cg_plz_vote_single_image_view").css('width','auto');
			$("#cg_plz_vote_single_image_view").css('display','block');
			//$("#show-pic-full-size").css('float','left');
			$("#show-pic-full-size").css('width','auto');
			$("#show-pic-full-size").css('display','block');



			centerImage();

			
			//alert(AllowComments);
			
			 var AllowComments = $("#cg_allow_comments").val();
			
				if(AllowComments==1){
					$("#cg-main-comments-div").css('width',newWidthImageCommentBoxes);
					$("#cg-comments-div").css('width',newWidthImageCommentBoxes);
				//	$("#show_comments").css('width',newWidthImageCommentBoxes);
					//$("#show_new_comments").css('width',newWidthImageCommentBoxes);
					
					var newWidthCGcommentBox = newWidthImageCommentBoxes-60;
					$("#cg_comment").css('width',newWidthCGcommentBox);
					$(".cg_comments_hr").css('width',newWidthCGcommentBox);
					
				}
		
	//	}
	
	}
	
			
			
			 var AllowComments = $("#cg_allow_comments").val();
//	alert(AllowComments);
	var widthImageParent = $("#cg-start").parent().width(); 
	var widthImageCommentBoxesConfigured = $("#cg_width_gallery").val();
	var widthImageCommentBoxes = $("#cg-start").val();
	
//	var heightLoadedImage = $("#cg_height_image").val();
	//$("#cg_height_image").val(heightLoadedImage);
	//alert(heightLoadedImage);

	//alert(widthImageCommentBoxes);
	
	
	if(widthImageParent < widthImageCommentBoxesConfigured){
	
	resizeImage();	
		
	}
	
	
	
	
	
	
	
	// Resizing if image bigger then screen, mobile view for example --- ENDE
	
	$( window ).resize(function() {
		
	resizeImage();	
		  
	});		  
		   
});