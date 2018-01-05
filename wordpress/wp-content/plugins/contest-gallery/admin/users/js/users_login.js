jQuery(document).ready(function($){

    // Scroll Function here
    $.fn.cgGoTo = function() {
        $('html, body').animate({
            scrollTop: $(this).offset().top - 30+'px'
        }, 'fast');
        return this; // for chaining...
    }

		  $("#cg_user_login_div").show();  // Document is ready
		  
		  // Objekt wird so festgelegt um später angezielt zu werden
		  /*var $mail = $(".cg_user_registry_instant_mail_check");

		$(".cg_main-mail").keyup(function() {
			 // Man könnte hier auch direkt die objektschreibweise $(".cg_main-mail") anwenden
			$mail.val( this.value );
		});*/


	
$(document).on('click', '#cg_user_login_check', function(e){

    $('.cg_form_div').removeClass('cg_form_div_error');

	
	$('#cg_append_login_name_mail_fail').empty();
	$('#cg_append_login_password_fail').empty();
	
	var check = 0;

		$( "#cg_login_name_mail" ).each(function( i ) {
			
			var cg_login_name_mail = $(this).val();			
			var cg_language_UsernameOrEmailRequired = $("#cg_language_UsernameOrEmailRequired").val();
			
		
				if(cg_login_name_mail.length==0){
				
					$(this).parent().find('#cg_append_login_name_mail_fail').append(cg_language_UsernameOrEmailRequired);					
					check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					e.preventDefault();
				}
			 
		});

		$( "#cg_login_password" ).each(function( i ) {
			
			var cg_login_password = $(this).val();			
			var cg_language_PasswordRequired = $("#cg_language_PasswordRequired").val();
				
				if(cg_login_password.length==0){
				
					$(this).parent().find('#cg_append_login_password_fail').append(cg_language_PasswordRequired);					
					check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					e.preventDefault();
				}
			 
		});

		if(check==1){
			$(".cg_form_div_error").cgGoTo();
		}


		// AJAX Action   
		if(check==0){
			
			$("#cg_message").empty();

			var cg_check = $("#cg_login_check").val();			
			var cg_login_name_mail = $( "#cg_login_name_mail" ).val();
			var cg_login_password = $( "#cg_login_password" ).val();
			var cg_gallery_id = $( "#cg_gallery_id" ).val();
			//alert(cg_check);
			//alert(cg_login_name_mail);
			//alert(cg_login_password);
			//var cg_plugins_url = $( "#cg_plugins_url" ).val();			 
		//	var loadingSource = cg_plugins_url+"/admin/users/css/loading.gif";	 
			 
			//$("#cg_message").append("<img id='#cg_message_img' src='"+loadingSource+"' width='22px' height='22px' style='display:inline;'>");
			 
		//	 $("#cg_message_img").load(function(){$(this).toggle();});

			 //$("#ipm_input_name").val('');
			//$("#ipm_input_check").prop('checked',false);
					
		//e.preventDefault();
			jQuery.ajax({

				//url : cg_site_url+"/wp-admin/admin-ajax.php",
				url : post_cg_login_wordpress_ajax_script_function_name.cg_login_ajax_url,
				type : 'post',			
				data : {
					action : 'post_cg_login',
					action1 : cg_login_name_mail,
					action2 : cg_login_password,
					action3 : cg_check,
					action4 : cg_gallery_id
					},
				async: false,	// Ganz wichtig! Führt die Operation live aus. Nicht asynchron!
				success : function( response ) {
					jQuery("#cg_message").html( response );		
				}
			});
			
			
			if(document.readyState === "complete"){
				var cg_check_mail_name_value = $("#cg_check_mail_name_value").val();// Wird auf 1 gesetzt wenn Login Daten unkorrekt
			//	alert(cg_check_mail_name_value);
				if(cg_check_mail_name_value==1){
					$("#cg_check_mail_name_value").val(0);
					e.preventDefault();
				}
				else{
					var cg_ForwardAfterLoginUrlCheck = $("#cg_ForwardAfterLoginUrlCheck").val();
					if(cg_ForwardAfterLoginUrlCheck==1){
						e.preventDefault();
						var cg_ForwardAfterLoginUrl = $("#cg_ForwardAfterLoginUrl").val();
						window.location.href = cg_ForwardAfterLoginUrl;
					}
				}
			}		
	
		}

});


});