jQuery(document).ready(function($){

    $('.cg_title_icon').click( function() {

    	var post_title = $(this).parent().find('.post_title').val();
        if(post_title === '') {
            $(this).parent().find('.cg_image_title').val();
            $(this).parent().find('.cg_image_title').attr('placeholder','No WordPress title available');
        }
        else {
            $(this).parent().find('.cg_image_title').val(post_title);
		}

    });

    $('.cg_description_icon').click( function() {

    	var post_description = $(this).parent().find('.post_description').val();
        var post_description = post_description.replace(/(<([^>]+)>)/ig,"");


        if(post_description === '') {
            $(this).parent().parent().find('.cg_image_description').val();
            $(this).parent().parent().find('.cg_image_description').attr('placeholder','No WordPress description available');
        }
        else {
            $(this).parent().parent().find('.cg_image_description').val(post_description);
		}

    });

    $('.cg_excerpt_icon').click( function() {

    	var post_excerpt = $(this).parent().find('.post_excerpt').val();
        if(post_excerpt === '') {
            $(this).parent().parent().find('.cg_image_excerpt').val();
            $(this).parent().parent().find('.cg_image_excerpt').attr('placeholder','No WordPress excerpt available');
        }
        else {
            $(this).parent().parent().find('.cg_image_excerpt').val(post_excerpt);
		}

    });


// Add icon


$("#cg_changes_saved").fadeOut(3000);

$( "#cg_server_power_info" ).hover(function() {
		//alert(3);
   $('#cg_answerPNG').toggle();
    $(this).css('cursor','pointer');
});

$( "#cg_adding_images_info" ).hover(function() {
		//alert(3);
   $('#cg_adding_images_answer').toggle();
    $(this).css('cursor','pointer');
});

//Check if the current URL contains '#'



	
// Nicht löschen, wurde ursprünglich dazu markiert alle Felder auswählen zu lassen die im Slider gezeigt werden sollen, Logik könnte noch nützlich sein! --- ENDE	
	
	
	
	//alert(allFieldClasses);
	
      function countChar(val) {
        var len = val.value.length;
        if (len >= 1000) {
          val.value = val.value.substring(0, 1000);
        } else {
          $('#charNum').text(1000 - len);
        }
      };
	  
	  
	  // Verstecken weiterer Boxen

    $('.mehr').hide();
    $('.clickBack').hide();


    $('.clickMore').click( function() {
         // Zeigen oder Verstecken:

         $(this).next().slideDown('slow');
	     $(this).next(".mehr").next(".clickBack").toggle();
		 $(this).hide();		 

		 
    })
	
	    $('.clickBack').click( function() {
		 $(this).prev().slideUp('slow');
	     $(this).prev(".mehr").prev(".clickMore").toggle();
		 $(this).hide();		
		 
		 
    })

// Verstecken weiterer Boxen ---- ENDE

// Change Daten ändern oder löschen

$("#chooseAction1").change(function(){

	var chooseAction = $( "#chooseAction1 option:selected" ).val();


	if(chooseAction==3){

		$('.cg_image_checkbox:checked').each(function(){
			$(this).closest('.sortableDiv').addClass('highlightedSortable');
		});

	}
	else{

		$('.cg_image_checkbox:checked').each(function(){
			$(this).closest('.sortableDiv').removeClass('highlightedSortable');
		});

	}


});


//Change Daten ändern oder löschen -- END


/*
$("input[class*=deactivate]").change(function(){

//$( this ).parent( "div input .imageThumb" ).removeAttr("disabled");
//$( this ).closest( "input" ).removeAttr("disabled");
//$( this ).parent().find( "input .imageThumb" ).removeAttr("disabled"); 

if($(this).is(":checked")){
var platzhalter = 'keine Aktion';
$( this ).parent().find(".deactivate").remove();
$( this ).parent().find( ".image-delete" ).prop("disabled",false);
}

else{

var id = $(this).val();
$( this ).parent().append("<input type='hidden' name='deactivate[]' value='"+id+"' class='deactivate'>" );
$( this ).parent().find( ".image-delete" ).prop("disabled",true);
}


});*/

$(".cg_image_checkbox").change(function(){

    var chooseAction = $( "#chooseAction1 option:selected" ).val();

        if($(this).is(":checked")){
            if(chooseAction==3){
            	$(this).closest('.sortableDiv').addClass('highlightedSortable');
            }
        }
        else{
            	$(this).closest('.sortableDiv').removeClass('highlightedSortable');
		}

});


// Duplicate email to a hidden field for form


$(".email").change(function(){

var email = $( this ).val();

$( this ).parent().find( ".email-clone" ).val(email);

});

// Duplicate email to a hidden field for form -- END 


$("div input #activate").click(function() {
    $("input #inform").prop("disabled", this.checked);
});

/*function informAll(){

//alert(arg);
alert(arg1);

if($("#informAll").is( ":checked" )){
$( "input[class*=inform]").removeAttr("checked",true);
$( "input[class*=inform]").click();
}

else{
$( "input[class*=inform]").click();

}

}*/

// Alle Bilder auswählen 

var n = 0;

$("#chooseAll").click(function(){

	n++;
	$("#click-count").val(n);

    var chooseAction = $( "#chooseAction1 option:selected" ).val();


	if($("#chooseAll").is( ":checked" )){

		$(".cg_image_checkbox").prop("checked",true);

        if(chooseAction==3){

            $('.cg_image_checkbox:checked').each(function(){
                $(this).closest('.sortableDiv').addClass('highlightedSortable');
            });

        }

	}
	else{

		//$(".cg_image_checkbox").prop("checked",false);

		$('.cg_image_checkbox').each(function(){
			$(this).closest('.sortableDiv').removeClass('highlightedSortable');
			$(this).prop("checked",false);
        });

	}

});

// Alle Bilder auswählen --- END


//Sortieren der Galerie




	var v = 0;
	
	var $i = 0;
	
	var rowid = [];
	
		if($i==0){  
	
			$( ".sortableDiv" ).each(function( i ) {
					  
			var rowidValue =  $(this).find('.rowId').val(); 
			
			
			rowid.push(rowidValue);
							
			});
			
			$i++;

		}
	
	  
	  $(function() {
		$( "#sortable" ).sortable({cursor: "move",placeholder: "ui-state-highlight", stop: function( event, ui ) {

	if(document.readyState === "complete"){



			  $( ".sortableDiv" ).each(function( i ) {
			  

			  
			$(this).find('.rowId').val(rowid[v]); 

			
			v++;

					  
			   });  
			   
			   v = 0;
			   
			   }
			   
	   }	
		
		});
		$('#sortable').css('cursor', 'move');
	  });

//Sortieren der Galerie --- ENDE
	  

  
});