jQuery(document).ready(function($){	
	
	// Uploading files
	//var file_frame;
	//var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
	var cg_media_uploader_set_to_post_id = 0; // Set this

	  jQuery('.cg_upload_wp_images_button').live('click', function( event ){
	var file_frame;
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  // Set the post ID to what we want
		  file_frame.uploader.uploader.param( 'post_id', cg_media_uploader_set_to_post_id );
		  // Open frame
		  file_frame.open();
		  return;
		} else {
		  // Set the wp.media post id so the uploader grabs the ID we want when initialised
		  wp.media.model.settings.post.id = cg_media_uploader_set_to_post_id;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: jQuery( this ).data( 'uploader_title' ),
		  button: {
			text: jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: true  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() { // alert(2);
		  // We set multiple to false so only get one image from the uploader
		  attachment = file_frame.state().get('selection').toJSON();

		cgMediaUploadGallery(attachment);
		
			//$("#cg_wp_upload_ids").append("<input class='cg_wp_upload_id' value="+attachment.id+" />");	
	
	
		});

		// Finally, open the modal
		file_frame.open();
	  });
	  
	  // Restore the main ID when the add media button is pressed
	//  jQuery('a.add_media').on('click', function() {
		//wp.media.model.settings.post.id = wp_media_post_id;
	//	alert(wp_media_post_id);
	//  });
	
	/*	   $(document).on('click', '.media-modal-close', function(e){
		   
			
var file_frame;

return false;	

});*/
	
	function cgMediaUploadGallery(attachment){
		
		var cg_gallery_id = $("#cg_gallery_id").val();
		var cg_admin_url = $("#cg_admin_url").val();
		
		$("#cg_uploading_count").css("display","none");
		
		 if(attachment.length==0){return false;}
		 
		 $("#cg_uploading_gif").css("display","inline");
		 $("#cg_uploading_div").css("display","inline");
		 
		 var i = 0;
		 var attachmentIds = [];
		 while (i < attachment.length){

			attachmentIds.push(attachment[i].id);
			i++;
		 }

			jQuery.ajax({
				url : cg_admin_url+"admin-ajax.php",
				type : 'post',
				data : {
					action : 'cg_check_wp_admin_upload',
					action1 : attachmentIds,
					action2 : cg_gallery_id
				},
				success : function( response ) {
					jQuery("#cg_wp_upload_div").html( response );
					
						if (window.location.href.indexOf("wpmadd=true") >= 0){
							location.reload(true);
						}
						else{
							var reloadUrl = window.location.href;
							if (reloadUrl.indexOf("#img") >= 0){
								reloadUrl = reloadUrl.replace('#img','');				
							}
							reloadUrl = reloadUrl+"&wpmadd=true";							
							window.location.href = reloadUrl;
						}
				}
			});
			
			
			
	}


})