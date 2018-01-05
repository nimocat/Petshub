jQuery(document).ready(function($){
jQuery( document ).on( 'click', '[id*=cg_pngCommentsIcon]', function() {

	
	// Haupthidden Feld, dass aktuell geöffnete comment image id zeigt
	//var cg_picture_id = $("#cg_slider_comment_picture_id").val();
	var cg_picture_id = $(this).attr("id");
	
	$("#show_comments_slider").empty();
	
	cg_picture_id = parseInt(cg_picture_id.substr(18));

	$("#cg_slider_comment_picture_id").val(cg_picture_id);	
	
	var countCommentsQuantity = $("#countCommentsQuantity"+cg_picture_id).val();

if(countCommentsQuantity>=1){
	$('#show_comments_slider').empty();
	$("#cg_slider_top_hr_div").remove();

 var loadingSource = $('#cg_loadingGifSource').val();
	 $("#show_comments_slider").append("<img class='cg_loading_gif_img' src='"+loadingSource+"' width='19px' height='19px' style='display:hidden;'>");
	// $("#rating_cg").empty(); 
	$("#cg_loading_gif_img").load(function(){$(this).toggle();}); 
			
	jQuery.ajax({
		url : post_cg_show_comments_slider_wordpress_ajax_script_function_name.cg_show_comments_slider_ajax_url,
		type : 'post',
		data : {
			action : 'post_cg_show_comments_slider',
			action1 : cg_picture_id
		},
		success : function( response ) {

			jQuery("#show_comments_slider").html( response );
		}
	});

}

})
})