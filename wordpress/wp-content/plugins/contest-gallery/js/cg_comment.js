jQuery(document).ready(function($){
jQuery( document ).on( 'click', '#cg_submit', function() {
	
	
	//alert("cg-comment");
	
	// var cg_given_rating=$(this).find('a').attr('alt');
	
		 var name = $("#cg_your_comment_name_single_view").val();
	 var comment = $("#cg_your_comment_comment_single_view").val();
	 var checkID = $("#cg_check").val();
	 var email = $("#email").val();
	 var timestamp = $("#timestamp").val();
	 var cg_galery_id = $('#cg_galeryID').val();
	 var cg_picture_id = $('#cg_picture_id').val();
	 //var cg_rate_value = cg_given_rating;
	 var cg_ThankYouComment = $('#cg_ThankYouComment').val();
	  var cg_check = $('#cg_check').val();
	// var cg_rating_picture_id = "#rating_cg-"+cg_picture_id+"";
	//alert(cg_check);
	 	  var loadingSource = $('#cg_loadingGifSource').val();
	 	 
	 //	var check = <?php echo json_encode($check);?>;
	 

	 //var pictureID = <?php echo json_encode($pictureID);?>;
	 //var galeryID = <?php echo json_encode($galeryID);?>;
	 var widthCommentArea = $('#widthCommentArea').val();
	 //var pathSetComment = $('#pathSetComment').val();
	 
	 //alert("check: "+check);
	 
	     // your existing submit code here
    // requesting my data..
	
	var nameLength = $("#cg_your_comment_name_single_view").val().length;
	var commentLength = $("#cg_your_comment_comment_single_view").val().length;
	


	// var allowSubmit = 0;
	 
	var allowSubmit = 1;
	
	
	//var post_id = jQuery(this).data('id');
	//var post_id = 657567;


	
	
		 if($('#'+checkID+'').is(':checked') ){
	 //alert(allowSubmit);
	 
	 $('#cg-hint-msg').empty();
	 
		if(nameLength<2){
			$('#cg_your_comment_name_single_view').val("");

		var cg_comment_name_characters = $('#cg_comment_name_characters').val();
		$('#cg-hint-msg').append("<br/<br/>"+cg_comment_name_characters+"<br>");
		}
		
		else if(commentLength<3){
			$('#cg_your_comment_comment_single_view').val("");
		var cg_comment_comment_characters = $('#cg_comment_comment_characters').val();
		$('#cg-hint-msg').append("<br/<br/>"+cg_comment_comment_characters+"<br>");
		}
	 
	 
		else if(allowSubmit==1){
			
			
			$('#show_comments').empty();
			$('#cg_your_comment_name_single_view').val("");
			$('#cg_your_comment_comment_single_view').val("");
			
	$("#show_new_comments").empty();
	 $("#show_new_comments").append("<img class='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");

	 $("#cg_loading_gif_img").load(function(){$(this).toggle();});
	 
	$("#"+cg_check+"").prop("checked",false);
			
	jQuery.ajax({
		url : post_cg_comment_wordpress_ajax_script_function_name.cg_comment_ajax_url,
		type : 'post',
		data : {
			action : 'post_cg_comment',
			action1 : name,
			action2 : comment,
			action3 : checkID,
			action4 : email,
			action5 : timestamp,
			action6 : cg_picture_id,
			action7 : cg_galery_id,
			action8 : widthCommentArea,
			action9 : cg_ThankYouComment
			
		},
		success : function( response ) {
			jQuery("#show_new_comments").html( response );
		}
	});
		
		// Um zu vielen requests vorzubeugen
		var allowSubmit = 0;
		setTimeout(function(){ allowSubmit = 1; }, 5000);
									
		}
		else{
		alert("Wait 5 seconds till you can send again");
		$('#cg-hint-msg').empty();
		$('#cg-hint-msg').append("<br/<br/>Wait 5 seconds till you can send again<br>");
		}
	}
	
	else{
	$('#cg-hint-msg').empty();
	
		if(nameLength<2){
		var cg_comment_name_characters = $('#cg_comment_name_characters').val();
		$('#cg-hint-msg').append("<br/<br/>"+cg_comment_name_characters+"<br>");
		}
		
		if(commentLength<3){
		var cg_comment_comment_characters = $('#cg_comment_comment_characters').val();
		$('#cg-hint-msg').append("<br/<br/>"+cg_comment_comment_characters+"<br>");
		}
	
	var cg_comment_not_a_robot = $('#cg_comment_not_a_robot').val();
	$('#cg-hint-msg').append("<br/<br/>"+cg_comment_not_a_robot+"<br>");
	}
	
	return false;

})
})