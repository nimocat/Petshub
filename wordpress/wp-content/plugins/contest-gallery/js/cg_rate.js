jQuery(document).ready(function($){

var cg_hide_until_vote = $("#cg_hide_until_vote").val();
var cg_vote_in_gallery = $("#cg_vote_in_gallery").val();
var cg_allow_rating = $("#cg_allow_rating").val();
var cg_user_login_check = $("#cg_user_login_check").val();
var cg_check_login = $("#cg_check_login").val();
var cg_VotesPerUser = $("#cg_VotesPerUser").val();

var cg_show_image_check = 0;
if($("#cg_show_image_check").length >=1){
    cg_show_image_check = 1;
}


if(cg_hide_until_vote==1 && cg_vote_in_gallery==1){

	/*$(document).on('click', '[class*=cg_hide_until_vote_rate]', function(e){
				
		if(cg_check_login==1 && cg_user_login_check==0){	
			alert($("#cg_OnlyRegisteredUsersCanVote").val());
			return false;
		}		
		else{				
						   
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
					
					if(cg_allow_rating==1){
					
					var ratingBlock = "<input type='hidden' class='cg_check_voted' value='0' id='cg_check_voted"+cg_real_id+"'>"+
					"<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star1' style='float:left;cursor:pointer;' alt='1' id='cg_rate_star"+cg_real_id+"'></div>"+
					  "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star2' style='float:left;cursor:pointer;' alt='2' id='cg_rate_star"+cg_real_id+"'></div>"+
					  "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star3' style='float:left;cursor:pointer;' alt='3' id='cg_rate_star"+cg_real_id+"'></div>"+
					  "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star4' style='float:left;cursor:pointer;' alt='4' id='cg_rate_star"+cg_real_id+"'></div>"+
					   "<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star5' style='float:left;cursor:pointer;' alt='5' id='cg_rate_star"+cg_real_id+"'></div>";
					   
					  }
					  
					if(cg_allow_rating==2){
					
					var ratingBlock = "<input type='hidden' class='cg_check_voted' value='0' id='cg_check_voted"+cg_real_id+"'>"+
					"<div style='display:inline-block;float:left;width:17px;height:17px;vertical-align: middle;'><img src='"+starOffUrl+"' class='cg_slider_star1' style='float:left;cursor:pointer;' alt='6' id='cg_rate_star"+cg_real_id+"'></div>";
					   
					 }
					   
				$("#cg_gallery_rating_div"+cg_real_id).empty();

				 $("#cg_gallery_rating_div"+cg_real_id).append(ratingBlock);

		}
	});*/



}



jQuery( document ).on( 'click', 'img[id*=cg_rate]', function(e) {
	
	if(cg_check_login==1 && cg_user_login_check==0){	
			alert($("#cg_OnlyRegisteredUsersCanVote").val());
			return false;
		}		
		else{	

	// Für vote out of gallery sollen elemente wenn bewertet wird angezeigt werden sobald bewertung abgegen wurde
	
		
	var cg_ShowAlways = $("#cg_ShowAlways").val();
	
	if($('#cg_overlay').is(':visible')) {
    var sliderVersion = 1;
	}
	else{
	// Ganz wichtig! Ansonsten kann man mobile nicht bewerten!	
	var sliderVersion = 0;	
	
	}
	
	
			var rateOutOfGallery = $('#cg_vote_in_gallery').val();
		var slider = $('#cg_vote_in_slider').val();

	
	
//	alert(slider);
	//alert(rateOutOfGallery);

	 var cg_given_rating = $(this).attr('alt');
	
	//alert(cg_given_rating);  
	
	 var cg_galery_id = $('#cg_galeryID').val();
	 var cg_picture_id = $('#cg_picture_id').val();
	 var cg_rate_value = cg_given_rating;
	 var cg_actual_value_id = $('#cg_actual_value_id').val();
	 var cg_rating_picture_id = "#rating_cgd-"+cg_picture_id+"";
	 
	 var cg_ContestEndTime = $('#cg_ContestEndTime').val();
	 var cg_ContestEnd = $('#cg_ContestEnd').val();
	var cg_AlreadyRatedText = $('#cg_AlreadyRated').val();
	 
	 //Variante für Slider (cg_rate_value ist auch so vorhanden)
	// alert(1); 
	 //if (typeof cg_galery_id == 'undefined') {var cg_galery_id = $("#cg_galeryID").val();}
	 //f (typeof cg_IpBlock == 'undefined') {var cg_IpBlock = 37;} 
	 
	  
	 //Variante für Slider --- ENDE
	 
	// alert("cg_galery_id"+cg_galery_id);
	// alert("cg_picture_id"+cg_picture_id);
	// alert("cg_rate_value"+cg_rate_value);
//	 alert("cg_AlreadyRatedText"+cg_AlreadyRatedText);
	// alert("cg_IpBlock"+cg_IpBlock);
	 	 
	// var cg_rating_picture_id = "#rating_cg-"+cg_picture_id+"";
	
	//alert(cg_ContestEndTime);
	var ActualTimeSeconds = Math.round((new Date).getTime()/1000);
	//alert(milliseconds);
	 
	 if((cg_ContestEndTime>ActualTimeSeconds && cg_ContestEnd == 1) || cg_ContestEnd != 1){
	 
	 var loadingSource = $('#cg_loadingGifSource').val();
	 
	// alert(loadingSource);
	 
	 // Slider Version
	 if (slider==1) {
		
		var cg_picture_id =  parseInt(this.id.substr(12));
$("#ratingCGslider"+cg_picture_id).empty();		
		//alert(2);
	 $("#ratingCGslider"+cg_picture_id+"").append("<img class='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
	 $("#cg_loading_gif_img").load(function(){$(this).toggle();});
	 var typeVoting = "slider";
	 // Zur späteren Wiedererkennung bei HideUntilVote ob würd dieses Bild schon gewertet wurde oder nicht
	 $("#cg_check_voted"+cg_picture_id).val(1);
		 }
		 
	 // Out of Gallery Version
	 else if (rateOutOfGallery==1) {
		$(this).parent().parent().empty();
		var cg_picture_id =  parseInt(this.id.substr(12));	
		//alert(2);
	 $("#cg_gallery_rating_div_child"+cg_picture_id+"").append("<img class='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
	 $("#cg_loading_gif_img").load(function(){$(this).toggle();});
	 var typeVoting = "gallery";
	 // Zur späteren Wiedererkennung bei HideUntilVote ob würd dieses Bild schon gewertet wurde oder nicht
	 $("#cg_check_voted"+cg_picture_id).val(1);
		 } 
		 
	 // Normale Version
	 else{
$("#cg_div_rate_stars_image").empty();
	 $("#cg_div_rate_stars_image").append("<img class='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
	 $("#rating_cg").empty();
	 $("#cg_loading_gif_img").load(function(){$(this).toggle();});
	 var typeVoting = "normal";
	// alert(typeVoting);
	 }
	 
	 
	// alert(typeVoting);
	//alert("cg_galery_id "+cg_galery_id);
//alert("cg_picture_id "+cg_picture_id);
	//alert("cg_rate_value "+cg_rate_value);
	//alert("typeVoting "+typeVoting);
	//alert("cg_AlreadyRatedText "+cg_AlreadyRatedText);
	//alert("cg_IpBlock "+cg_IpBlock);
//	alert("works");
	//alert(typeVoting);
	//var post_id = jQuery(this).data('id');
	//var post_id = 657567;
	

	
	//alert(post_cg_rate_wordpress_ajax_script_function_name);
	//alert(cg_rate_ajax_url);
	
	jQuery.ajax({
		url : post_cg_rate_wordpress_ajax_script_function_name.cg_rate_ajax_url,
		type : 'post',
		data : {
			action : 'post_cg_rate',
			action1 : cg_galery_id,
			action2 : cg_picture_id,
			action3 : cg_rate_value,
			action4 : typeVoting,
			action5 : cg_AlreadyRatedText,
			action6 : cg_show_image_check
		},
		success : function( response ) {
			
			// Prüfen ob Rating geklickt wurde und AJAX call lädt		
			$("#cg_rating_ajax_call").val(1);
			//alert("call ajax");
			 
			if(typeVoting=="slider"){
				
			//	alert("go1");
			//jQuery(this).parent().parent().html( response );
			jQuery("#ratingCGslider"+cg_picture_id+"").html( response );
			}
			else if(typeVoting=="gallery"){
			
				//alert("go2");
			//jQuery(this).parent().parent().html( response );
			jQuery("#cg_gallery_rating_div_child"+cg_picture_id+"").html( response );
			}		
			else{
				//alert("go3");
				jQuery("#cg_div_rate_stars_image").html( response );}
		}
	});

	return false;
	 }
	else{
		
		var cg_photo_contest_over = $("#cg_photo_contest_over").val();
		alert(cg_photo_contest_over);
		return false;
		
	}
}	
})

})