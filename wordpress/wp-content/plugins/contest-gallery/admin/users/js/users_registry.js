jQuery(document).ready(function($){

    // Scroll Function here
    $.fn.cgGoTo = function() {
        $('html, body').animate({
            scrollTop: $(this).offset().top - 40+'px'
        }, 'fast');
        return this; // for chaining...
    }

    // Scroll Function here --- ENDE

		  $("#cg_user_registry_div").show();  // Document is ready
		  
		  // Objekt wird so festgelegt um später angezielt zu werden
		  var $mail = $(".cg_user_registry_instant_mail_check");

		$(".cg_main-mail").keyup(function() {
			 // Man könnte hier auch direkt die objektschreibweise $(".cg_main-mail") anwenden
			$mail.val( this.value );
		});


	
$(document).on('click', '#cg_users_registry_check', function(e){
	//alert(1);
	//e.preventDefault();
	
		

			//alert(cg_main_mail);
    $('.cg_form_div').removeClass('cg_form_div_error');
	 
	var check = 0;
	
	$('.cg_input_error').empty();
	

	

//  var emailRegex = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;   
  
  // new regEx RFC 5322
  var emailRegex = /^[a-zA-Z0-9_!#$%&’*+/=?`{|}~^.-]+@[a-zA-Z0-9.-]+$/;
  
  		  $( ".cg_check_f_checkbox" ).each(function( i ) {
			  //	alert(2);
				if(!$(this).prop('checked')){
					// alert('cf 1');
					//$( this ).parent().find('.cg_input_error p').remove();
					var cg_check_agreement = $("#cg_check_agreement").val();
					//alert(cg_check_agreement);
					$( this ).parent().find('.cg_input_error').append(cg_check_agreement);	
					check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					e.preventDefault();
				}
		  
		  }); 
		  

		  $( ".cg-main-mail" ).each(function( i ) {
		  
			  var cg_main_mail = $(this).val();			  

			 var realsize = $( this ).val().length;
			 
				 
				if(realsize == 0){		  
				   $( this ).parent().find('.cg_input_error').text(''+$('#cg_language_PleaseFillOut').val()+'');
				   check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
				   e.preventDefault();			  
				}	

				if(realsize > 0){
						if (!emailRegex.test(cg_main_mail)){

						 // $( this ).parent().find('.cg_input_error p').remove();
						  var cg_check_email_upload = $("#cg_check_email_upload").val();
						  $( this ).parent().find('.cg_input_error').append(cg_check_email_upload);
						  
						  check = 1;
                            $(this).closest('.cg_form_div').addClass('cg_form_div_error');
						  e.preventDefault();			  
					  
					  }	
			    }				 
				 
			 
			  

		  
		  }); 
		  
// Validate Emailadress --- ENDE



		$( ".cg-main-user-name" ).each(function( i ) {
			
			var cg_main_user_name = $(this).val();
			
			var cg_Real_Size = $( this ).val().length;
			var cg_Min_Char = $( this ).parent().find(".cg_Min_Char").val();
			var cg_Max_Char = $( this ).parent().find(".cg_Max_Char").val();

			var cg_min_characters_text = $("#cg_min_characters_text").val();
			var cg_max_characters_text = $("#cg_max_characters_text").val();
		 
				 
				 if(cg_Real_Size == 0){
				  
				   $( this ).parent().find('.cg_input_error').text(''+$('#cg_language_PleaseFillOut').val()+'');
					check = 1;
                     $(this).closest('.cg_form_div').addClass('cg_form_div_error');
				   e.preventDefault();
				  
				}
				 
		 		else if (cg_Real_Size<cg_Min_Char){
			 
					// $( this ).parent().find('.cg_input_error p').remove();

					 $( this ).parent().find('.cg_input_error').append(cg_min_characters_text+': '+cg_Min_Char);
					 
					check = 1;
                     $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					e.preventDefault();
						
				}						
				
				else if (cg_Real_Size>cg_Max_Char) {			 
			 
				//	$( this ).parent().find('.cg_input_error p').remove();	

					$( this ).parent().find('.cg_input_error').append(cg_max_characters_text+': '+cg_Max_Char);
					 
					check = 1;
                     $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					e.preventDefault();
						
				}
				
				else{
					
					
				}
				 
			 
		  
		   });		   
		   

		  
		 $( ".cg-user-comment-field" ).each(function( i ) {
		 
				var cg_Real_Size = $( this ).val().length;
				var cg_Min_Char = $( this ).parent().find(".cg_Min_Char").val();
				var cg_Max_Char = $( this ).parent().find(".cg_Max_Char").val();		 
			 
			 	var cg_min_characters_text = $("#cg_min_characters_text").val();
				var cg_max_characters_text = $("#cg_max_characters_text").val();				
				
				 checkIfNeed = $( this ).parent().find(".cg_form_required").val();

				 if(checkIfNeed=='1'){
					 
					if(cg_Real_Size == 0){		  
					   $( this ).parent().find('.cg_input_error').text(''+$('#cg_language_PleaseFillOut').val()+'');
					   check = 1;
                        $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					   e.preventDefault();			  
					}	

					else if (cg_Real_Size<cg_Min_Char) {				 
				 
						// $( this ).parent().find('.cg_input_error p').remove();

						 $( this ).parent().find('.cg_input_error').append(cg_min_characters_text+': '+cg_Min_Char);
						 
						check = 1;
                        $(this).closest('.cg_form_div').addClass('cg_form_div_error');
						e.preventDefault();
							
					}						
					
					else if (cg_Real_Size>cg_Max_Char) {				 
				 
					//	$( this ).parent().find('.cg_input_error p').remove();	

						$( this ).parent().find('.cg_input_error').append(cg_max_characters_text+': '+cg_Max_Char);
						 
						check = 1;
                        $(this).closest('.cg_form_div').addClass('cg_form_div_error');
						e.preventDefault();
							
					}
					
					else{
						
					}
					 
				 }

		   });
		   
		   
		$( ".cg-user-text-field" ).each(function( i ) {		 
		 	
			var cg_Real_Size = $( this ).val().length;
			var cg_Min_Char = $( this ).parent().find(".cg_Min_Char").val();
			var cg_Max_Char = $( this ).parent().find(".cg_Max_Char").val();		 
		 
			var cg_min_characters_text = $("#cg_min_characters_text").val();
			var cg_max_characters_text = $("#cg_max_characters_text").val();

			checkIfNeed = $( this ).parent().find(".cg_form_required").val();						
			 
			 if(checkIfNeed=='1'){				 
				 
				if(cg_Real_Size == 0){
				   $( this ).parent().find('.cg_input_error').text(''+$('#cg_language_PleaseFillOut').val()+'');
				   check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
				   e.preventDefault();			  
				}

				else if (cg_Real_Size<cg_Min_Char) {			 
			 
					// $( this ).parent().find('.cg_input_error p').remove();

					 $( this ).parent().find('.cg_input_error').append(cg_min_characters_text+': '+cg_Min_Char);
					 
					check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					e.preventDefault();
						
				}					
				
				else if (cg_Real_Size>cg_Max_Char) {		 
			 
					//$( this ).parent().find('.cg_input_error p').remove();	

					$( this ).parent().find('.cg_input_error').append(cg_max_characters_text+': '+cg_Max_Char);
					 
					check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					e.preventDefault();
						
				}
				
				else{
										
				}
			 
				 
			 }		 
		 
		   });
		   
		 
	   
		   
		   
// Passwort Felder überprüfen	
		
		var pw_count = 0;
		
		var cg_password_1='';
		var cg_password_2='';
		
		$( "[class*=cg-password]" ).each(function( i ) {
		//	alert(4);
		pw_count++;
			
		 //Überprüfen ob passwort oder password confirm typ ist		 
		 var cg_password_type = $(this).attr("class");
		 
		 if(pw_count==1){
			  cg_password_1 = $( this ).val();
		 }
		 
		 if(pw_count==2){
			  cg_password_2 = $( this ).val();
		 }	
			
		var cg_Real_Size = $( this ).val().length;
		var cg_Min_Char = $( this ).parent().find(".cg_Min_Char").val();
		var cg_Max_Char = $( this ).parent().find(".cg_Max_Char").val();	
		 
		 var cg_Real_Size = $( this ).val().length;		

		var cg_min_characters_text = $("#cg_min_characters_text").val();
		var cg_max_characters_text = $("#cg_max_characters_text").val();	
		 
				
				if(cg_Real_Size==0){					
					
				  $( this ).parent().find('.cg_input_error').text(''+$('#cg_language_PleaseFillOut').val()+'');
				   check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
				   e.preventDefault();					
					
				}


		 		else if (cg_Real_Size<cg_Min_Char) {
		 
				// $( this ).parent().find('.cg_input_error p').remove();			
				 //alert(1);
				 $( this ).parent().find('.cg_input_error').append(cg_min_characters_text+': '+cg_Min_Char);

				// alert("check: "+check);
				check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();
				
				}
						
				else if (cg_Real_Size>cg_Max_Char) {
				
				//alert(2);
				
				
				// $( this ).parent().find('.cg_input_error p').remove();			
				 
				 $( this ).parent().find('.cg_input_error').append(cg_max_characters_text+': '+cg_Max_Char);
				 
				// alert("check: "+check);
				check = 1;
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();

				}
				else{
					
				}				
				
				//alert(pw_count);
				if(pw_count==2){
					//alert("pw1 "+cg_password_1);
					//alert("pw2 "+cg_password_2);
					if(cg_password_1!=cg_password_2){check = 1; e.preventDefault();
						alert($("#cg_language_PasswordsDoNotMatch").val());
					}
				}			
			
			 });

		if(check==1){
            $(".cg_form_div_error").cgGoTo();
		}
		   

		// AJAX Action   
		if(check==0){
		//	e.preventDefault();
			
			$("#cg_registry_message").empty();

			var cg_site_url = $("#cg_site_url").val();	

			var cg_check = $("#cg_check").val();
			
			var cg_main_mail = $( ".cg-main-mail" ).val();
			var cg_main_user_name = $( ".cg-main-user-name" ).val();
			
		//	var cg_plugins_url = $( "#cg_plugins_url" ).val();
			

			 
		//	 var loadingSource = cg_plugins_url+"/admin/users/css/loading.gif";	 
			 
			// alert(loadingSource);
			  
			// $("#cg_registry_message").append("<img id='#cg_registry_message_img' src='"+loadingSource+"' width='22px' height='22px' style='display:inline;'>");
			 
			 //$("#cg_registry_message_img").load(function(){$(this).toggle();});

			 //$("#ipm_input_name").val('');
			//$("#ipm_input_check").prop('checked',false);
					
		
			jQuery.ajax({

				//url : cg_site_url+"/wp-admin/admin-ajax.php",
				url : post_cg_registry_wordpress_ajax_script_function_name.cg_registry_ajax_url,
				type : 'post',
				data : {
					action : 'post_cg_registry',
					action1 : cg_main_mail,
					action2 : cg_main_user_name,
					action3 : cg_check
					},
				async: false,	// Ganz wichtig! Führt die Operation live aus. Nicht asynchron!
				success : function( response ) {
					jQuery("#cg_registry_message").html( response );		
				}
			});
			
			if(document.readyState === "complete"){
			var cg_check_mail_name_value = $("#cg_check_mail_name_value").val();// Beim Ajax request wird eine 1 gesetzt wenn Name oder E-Mail schon existieren
			//alert(cg_check_mail_name_value);
			if(cg_check_mail_name_value==1){
				e.preventDefault();
				$("#cg_check_mail_name_value").val(0);
				}
			}
			
			
	
		}



});


});