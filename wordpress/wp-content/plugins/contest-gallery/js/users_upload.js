jQuery(document).ready(function($){

	// Scroll Function here
    $.fn.cgGoTo = function() {
        $('html, body').animate({
            scrollTop: $(this).offset().top - 40+'px'
        }, 'fast');
        return this; // for chaining...
    }

	
	 var countChildren = $('#cg_upload_form_container').children().size()+1;
	 
	  $("#cg_upload_form_container").css('visibility','visible');   // Document is ready


	/*I am not a robot captcha here*/

    // Prüfen der Wordpress Session id
    var check = $("#cg_check").val();
    $("#cg_captcha_not_a_robot_field").prepend("<input type='checkbox' id='cg_"+check+"' >");


/*
Notwendige Formularprüfung

1. Prüfen der Bilder

- Prüfen ob Bild ausgewählt wurde >>> submit
- Prüfen ob der Größe der Bilder in MB >>> change und submit
- Prüfen ob das berechtigte Bildformat übergeben wurde >>> change und submit
- Prüfen ob die Auflösung der Bilder zu hoch ist >>> change und submit


2. Prüfen der Textfelder
- Prüfen ob E-Mail richtig geschrieben wurde >>> submit
- Prüfen wie viel Buchstaben eingegeben worden sind >>> submit


*/

// 1. Prüfen der Bilder

//- Prüfen ob das berechtigte Bildformat übergeben wurde
//- Funktion bilden

function checkPic(e) {

    $('.cg_form_div').removeClass('.cg_form_div_error');


var cg_ContestEnd = $("#cg_ContestEnd").val();


var cg_ContestEndTime = $("#cg_ContestEndTime").val();
if(cg_ContestEnd==1 && cg_ContestEndTime != 0){
	
	var ActualTimeSeconds = Math.round((new Date).getTime()/1000);
	//alert(actualTime);
	
	//alert(cg_ContestEndTime);
	
	if(ActualTimeSeconds>cg_ContestEndTime){
		
		var cg_photo_contest_over = $("#cg_photo_contest_over").val();	
		alert(cg_photo_contest_over);
		e.preventDefault();
		
	} 
	
}


		
		
		


//var filename = $('input[type=file]')[0].files[0].name;
var filename = $('input[type=file]#cg_input_image_upload_id').val().split('\\').pop();



if(!filename){

	var cg_no_picture_is_choosed = $("#cg_no_picture_is_choosed").val();
	$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_no_picture_is_choosed+'');
    $("#cg_upload_form_e_prevent_default").val(1);
    $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
	 e.preventDefault();
	 
}
else{
	
	$('.cg_input_error_image_upload').empty();
	 
	var fileType = document.getElementById('cg_input_image_upload_id').files[0].type;

	var testcheck = 1;

	var restrictJpg = $("#restrictJpg").val();
	var restrictPng = $("#restrictPng").val();
	var restrictGif = $("#restrictGif").val();


	if (restrictJpg==1) {var MaxResJPGwidth = $("#MaxResJPGwidth").val();var MaxResJPGheight = $("#MaxResJPGheight").val();}
	if (restrictPng==1) {var MaxResPNGwidth = $("#MaxResPNGwidth").val();var MaxResPNGheight = $("#MaxResPNGheight").val();}
	if (restrictGif==1) {var MaxResGIFwidth = $("#MaxResGIFwidth").val();var MaxResGIFheight = $("#MaxResGIFheight").val();}


			if (fileType != 'image/jpeg' && fileType != 'image/png' && fileType != 'image/gif') 
			{
				var cg_file_not_allowed = $("#cg_file_not_allowed").val();
				$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_file_not_allowed+'');
                $("#cg_upload_form_e_prevent_default").val(1);
                $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
				e.preventDefault();
			}
		
			
			
	//- Prüfen ob das berechtigte Bildformat übergeben wurde   --- ENDE

	//- Prüfen ob der Größe der Bilder in MB	



	// Überprüfen ob der File größer ist als vorgegebene Uploadgröße

			var post_max_size_php_ini = $("#post_max_size_php_ini").val();
			var post_max_size_user_config = $("#post_max_size_user_config").val();
	//alert(post_max_size_php_ini);
	//alert(post_max_size_user_config);
			// PHP ini configuration will be always prefered
			if(post_max_size_php_ini < post_max_size_user_config){	
				var post_max_size = post_max_size_php_ini;	
			}
			else{
				var post_max_size = post_max_size_user_config;
			}
			
			//alert(post_max_size);
			// Wenn Null dann sozusagen unlimited
			if(post_max_size==0){post_max_size=post_max_size_php_ini;}
	//alert(post_max_size);

			// alert("Post max size:"+post_max_size); 

			//alert(post_max_size);

				var file = $("#cg_input_image_upload_id")[0].files[0];
				
				// alert('file: '+file);
				var sizePic = file.size;
				
				//Umwandeln in MegaByte
				sizePic = sizePic/1000000;
				
				
				// alert(all_files.length);
				//alert("Aktuelle Größe: "+sizePic);
				
				
				if (sizePic >= post_max_size) {
				
				var cg_file_size_to_big = $("#cg_file_size_to_big").val();
				//var cg_post_size = $("#cg_post_size").val();
				
				$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_file_size_to_big+': '+post_max_size+'MB');
                    $("#cg_upload_form_e_prevent_default").val(1);
                    $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();
				
				}
		
	// Überprüfen ob der File größer ist als vorgegebene Uploadgröße --- ENDE 


	// Überprüfen ob Bulk Upload aktiviert ist und die Anzahl wieviel Bilder hochgeladen werden können

			var ActivateBulkUpload = $("#ActivateBulkUpload").val();		
			

			
			if(ActivateBulkUpload==1){
				
			var all_files = $("#cg_input_image_upload_id")[0].files;
				all_files = all_files.length;
			var BulkUploadQuantity = $("#BulkUploadQuantity").val();
			var BulkUploadMinQuantity = $("#BulkUploadMinQuantity").val();
			//alert(BulkUploadQuantity);
			// Wenn Null dann sozusagen unlimited
			if(BulkUploadQuantity==0){BulkUploadQuantity=1000;}
			if(BulkUploadMinQuantity==0){BulkUploadMinQuantity=1;}
			//alert("all_files "+all_files);
			//alert("BulkUploadQuantity "+BulkUploadQuantity);
			
			
				if(all_files>BulkUploadQuantity){
				
				var cg_language_BulkUploadQuantityIs = $("#cg_language_BulkUploadQuantityIs").val();
				$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_language_BulkUploadQuantityIs+': '+BulkUploadQuantity+'');
                    $("#cg_upload_form_e_prevent_default").val(1);
                    $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();
					
				}
				
				if(all_files<BulkUploadMinQuantity){
				
				var cg_language_BulkUploadLowQuantityIs = $("#cg_language_BulkUploadLowQuantityIs").val();
				$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_language_BulkUploadLowQuantityIs+': '+BulkUploadMinQuantity+'');
                    $("#cg_upload_form_e_prevent_default").val(1);
                    $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();
					
				}
				
				
			}


	// Überprüfen ob Bulk Upload aktiviert ist und die Anzahl wieviel Bilder hochgeladen werden können --- ENDE



	// überprüfen auflösung für jpg
	// Check the resolution of a pic
		
		if (fileType == 'image/jpeg' && restrictJpg == 1) {
		
		 //alert('test');
		
	var _URL = window.URL || window.webkitURL;

		var file, img;
		if (file = $("#cg_input_image_upload_id")[0].files[0]) {
			img = new Image();
			// Aufgrund onload findet diese Prüfung als aller letztens staat!
			img.onload = function () {
			//    alert("testRES"+this.width + " " + this.height);	

				 
			if (this.width > MaxResJPGwidth && MaxResJPGwidth!=0) {
		
			//var cg_to_high_resolution = $("#cg_to_high_resolution").val();
			//var cg_max_allowed_resolution_jpg = $("#cg_max_allowed_resolution_jpg").val();
			var cg_max_allowed_width_jpg = $("#cg_max_allowed_width_jpg").val();
			//alert(cg_max_allowed_width_jpg);
			$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_max_allowed_width_jpg+' '+MaxResJPGwidth+'px.');
			 $("#cg_upload_form_e_prevent_default").val(1);
                $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
			 e.preventDefault();
			
			 // alert('geklappt!res');	
			}
			
			  if (this.height > MaxResJPGheight && MaxResJPGheight!=0) {
			

			var cg_max_allowed_height_jpg = $("#cg_max_allowed_height_jpg").val();
			//alert(cg_max_allowed_height_jpg);
			$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_max_allowed_height_jpg+' '+MaxResJPGheight+'px.');
			 $("#cg_upload_form_e_prevent_default").val(1);
                  $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
			 e.preventDefault();
			
			 // alert('geklappt!res');	
			}

			};
			
		   img.src = _URL.createObjectURL(file);
		   
		}
		
		}

	// überprüfen auflösung für png	
	if (fileType == 'image/png' && restrictPng == 1) {
		
	var _URL = window.URL || window.webkitURL;

		var file, img;
		if (file = $("#cg_input_image_upload_id")[0].files[0]) {
			img = new Image();
			// Aufgrund onload findet diese Prüfung als aller letztens staat!
			img.onload = function () {
				//alert("testRES"+this.width + " " + this.height);	
				
				if (this.width > MaxResPNGwidth  && MaxResPNGwidth!=0) {
				
				var cg_max_allowed_width_png = $("#cg_max_allowed_width_png").val();
				$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_max_allowed_width_png+' '+MaxResPNGwidth+'px.');
				$("#cg_upload_form_e_prevent_default").val(1);
                $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();
				//alert('geklappt!res');
				
			
				}
				
				
				if (this.height > MaxResPNGheight  && MaxResPNGheight!=0) {
				
				var cg_max_allowed_height_png = $("#cg_max_allowed_height_png").val();
				$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_max_allowed_height_png+' '+MaxResPNGheight+'px.');
				$("#cg_upload_form_e_prevent_default").val(1);
                    $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();
				//alert('geklappt!res');			
			
				}

			};
			
		   img.src = _URL.createObjectURL(file);
		   
		}
		
		}
		
	// überprüfen auflösung für gif	
	if (fileType == 'image/gif' && restrictGif == 1) {
		
	var _URL = window.URL || window.webkitURL;

		var file, img;
		if (file = $("#cg_input_image_upload_id")[0].files[0]) {
			img = new Image();
			// Aufgrund onload findet diese Prüfung als aller letztens staat!
			img.onload = function () {
				//alert("testRES"+this.width + " " + this.height);	
				
				if (this.width > MaxResGIFwidth && MaxResGIFwidth!=0) {
				
				var cg_max_allowed_width_gif = $("#cg_max_allowed_width_gif").val();
				$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_max_allowed_width_gif+' '+MaxResGIFwidth+'px.');
				$("#cg_upload_form_e_prevent_default").val(1);
                    $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();
				//alert('geklappt!res');
				
			
				}
				
				
				if (this.height > MaxResGIFheight && MaxResGIFheight!=0) {
				
				var cg_max_allowed_height_gif = $("#cg_max_allowed_height_gif").val();
				$("#cg_input_image_upload_id").parent().find('.cg_input_error').text(''+cg_max_allowed_height_gif+' '+MaxResGIFheight+'px.');
				$("#cg_upload_form_e_prevent_default").val(1);
                    $("#cg_input_image_upload_id").closest('.cg_form_div').addClass('cg_form_div_error');
				 e.preventDefault();
				//alert('geklappt!res');
				
			
				}
				
				

			};
			
		   img.src = _URL.createObjectURL(file);
		   
		}
		
		}

	
	}	
	
    //var check = 1;
}






   	$('INPUT[type="file"]').change(function (e) {
	
	checkPic(e);


});

// <<< Ende überprüfen der Change() Funktion



//$( "#cg_users_upload_submit" ).click(function() {
	
	 $(document).on('click', '#cg_users_upload_submit', function(e){
	
		$('.cg_form_div').removeClass('cg_form_div_error');
		$('.cg_input_error').empty();

	
	

	

 // var emailRegex = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;  
 
 // new regEx RFC 5322
  var emailRegex = /^[a-zA-Z0-9_!#$%&’*+/=?`{|}~^.-]+@[a-zA-Z0-9.-]+$/;  
  
  
  
  
  		  $( "#cg_upload_form_container .cg_check_agreement_class" ).each(function( i ) {
			  
				if(!$(this).prop('checked')){

					$( this ).parent().find('.cg_input_error').text(''+$("#cg_check_agreement").val()+'');
                    $("#cg_upload_form_e_prevent_default").val(1);
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					e.preventDefault();
				
				}



		  
		  });  
  

		  

		 $( "#cg_upload_form_container .cg_input_email_class" ).each(function( i ) {
			
		  
			 var email = $(this).val();

			 var checkIfNeed = $( this ).parent().find(".cg_form_required").val();
		
		  
		  if (checkIfNeed == 'on' || email.length > 0) {
			  
			  if(email.length == 0){
				  
				   $( this ).parent().find('.cg_input_error').text(''+$('#cg_language_PleaseFillOut').val()+'');
                  $("#cg_upload_form_e_prevent_default").val(1);
                  $(this).closest('.cg_form_div').addClass('cg_form_div_error');
				   e.preventDefault();
				  
			  }	
			  
			  if(email.length > 0){
				  
					  if (!emailRegex.test(email)){
						  
						  $( this ).parent().find('.cg_input_error').text(''+$("#cg_check_email_upload").val()+'');
                          $("#cg_upload_form_e_prevent_default").val(1);
                          $(this).closest('.cg_form_div').addClass('cg_form_div_error');
						  e.preventDefault();
						  
					  }				  
			  }
			  
		  }
		  
		  //alert('check: '+check);
		  
		  }); 
		  
// Validate Emailadress --- ENDE



// Überprüfen ob die Anzahl der Buchstaben in den Feldern stimmt
		  
		  $( "#cg_upload_form_container .cg_input_text_class" ).each(function( i ) {
		   // $(this).attr( "width", "200px" );
		   
		 var minsize = $( this ).parent().find(".minsize").val();
		 var maxsize = $( this ).parent().find(".maxsize").val();
		 var checkIfNeed = $( this ).parent().find(".cg_form_required").val();
		 
		 var realsize = $( this ).val().length;
		 
		 		// 	  alert('nf');
		 
		 		 var cg_min_characters_text = $("#cg_min_characters_text").val();
		 var cg_max_characters_text = $("#cg_max_characters_text").val();				 
		 if (checkIfNeed == 'on') {
			 

		 		 if (realsize<minsize) {
				 
			 
				 $( this ).parent().find('.cg_input_error').empty();

				 $( this ).parent().find('.cg_input_error').text(''+cg_min_characters_text+': '+minsize+'');

				 
                     $("#cg_upload_form_e_prevent_default").val(1);

                     $(this).closest('.cg_form_div').addClass('cg_form_div_error');

				e.preventDefault();
						}
						
				
				else if (realsize>maxsize) {
				 
			 
				 $( this ).parent().find('.cg_input_error').empty();
	

				 $( this ).parent().find('.cg_input_error').text(''+cg_max_characters_text+': '+maxsize+'');



                     $("#cg_upload_form_e_prevent_default").val(1);

                     $(this).closest('.cg_form_div').addClass('cg_form_div_error');

				e.preventDefault();
						}
		 
		 }
		 


   
		   });

		// Überprüfen ob leeres select field gewählt wurde

		  $( "#cg_upload_form_container .cg_input_select_class" ).each(function( i ) {
		   // $(this).attr( "width", "200px" );

		 var checkIfNeed = $( this ).parent().find(".cg_form_required").val();
		 var cg_language_youHaveNotSelected = $("#cg_language_youHaveNotSelected").val();


		 if (checkIfNeed == 'on') {

		 		$(this).val();

		 		 if ($(this).val()=='') {

					 $( this ).parent().find('.cg_input_error').empty();
					 $( this ).parent().find('.cg_input_error').text(''+cg_language_youHaveNotSelected+'');

						 $("#cg_upload_form_e_prevent_default").val(1);

						 $(this).closest('.cg_form_div').addClass('cg_form_div_error');

						e.preventDefault();

				}

		 }




		   });
		   
		   
// Überprüfen ob die Anzahl der Buchstaben in den Kommentar-Feldern stimmt
		  
		  $( "#cg_upload_form_container .cg_textarea_class" ).each(function( i ) {
		   // $(this).attr( "width", "200px" );
		   
		 var minsize = $( this ).parent().find(".minsize").val();
		 var maxsize = $( this ).parent().find(".maxsize").val();
		 var checkIfNeed = $( this ).parent().find(".cg_form_required").val();
		 
		 var realsize = $( this ).val().length;

		var cg_min_characters_text = $("#cg_min_characters_text").val();
		 var cg_max_characters_text = $("#cg_max_characters_text").val();	
		 //  alert(realsize);
		 			//  alert(maxsize);
			 				 
		 if (checkIfNeed == 'on') {
			 
				if(realsize == 0){
				  
				   $( this ).parent().find('.cg_input_error').text(''+$('#cg_language_PleaseFillOut').val()+'');
                    $("#cg_upload_form_e_prevent_default").val(1);
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
				   e.preventDefault();
				  
				}
			 
		 		else if (realsize<minsize) {
		 
					 $( this ).parent().find('.cg_input_error').text(''+cg_min_characters_text+': '+minsize+'');
                    $("#cg_upload_form_e_prevent_default").val(1);
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					 e.preventDefault();

				}
						
				else if (realsize>maxsize) {

					 $( this ).parent().find('.cg_input_error').text(''+cg_max_characters_text+': '+maxsize+'');
                    $("#cg_upload_form_e_prevent_default").val(1);
                    $(this).closest('.cg_form_div').addClass('cg_form_div_error');
					 e.preventDefault();

				}
				else{
					
				}
		 
		 }
		 
  
		   });

		  // Check captcha I am not a robot
         if($( "#cg_captcha_not_a_robot_field  input[type='checkbox']" ).length >= 1 && $( "#cg_captcha_not_a_robot_field  input[type='checkbox']" ).is(':checked')===false){
             e.preventDefault();
             var cg_language_pleaseConfirm = $("#cg_language_pleaseConfirm").val();
             $("#cg_captcha_not_a_robot_field").find('.cg_input_error').text(cg_language_pleaseConfirm);

		 }


		   
		   			//alert("check: "+check);
		   
		   		 // alert('end');
				  
				 //Funktion zum Überprüfen des Bildes
				checkPic(e);
 
		// Falls das Bild in der Funktion nicht zugelassen wird, wird da der Wert für prevent default auf 1 gesetzt.
		 var cg_upload_form_e_prevent_default = $("#cg_upload_form_e_prevent_default").val();



		 if(cg_upload_form_e_prevent_default==1){

/*             $("#cg_form_upload_submit_div .cg_input_error").text($("#cg_language_sendSubmitError").val());
			setTimeout(function(){
                $("#cg_form_upload_submit_div .cg_input_error").empty();
			},10000);*/

             //location.href = "#cg_input_image_upload_id";

             $(".cg_form_div_error").cgGoTo();

		 	e.preventDefault();
		 }
		   
// Überprüfen ob die Anzahl der Buchstaben in den Feldern stimmt --- ENDE 
  
		   /*if (check == 1) {
		   alert('Form has to be blocked');

		  
		  
		   e.preventDefault();
		   } */






//});

});




 });