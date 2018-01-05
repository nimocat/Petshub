var cgJsClass = cgJsClass || {};
cgJsClass.editUpload = {};


jQuery(document).ready(function($){

	//$('.wp-editor-wrap').removeClass('tmce-active');
//	$('.wp-editor-wrap').addClass('html-active');
//	$('.switch-html').click();

    // Ganz wichtig, damit später über allen elmenten überragt.
 //   htmlFieldTemplate_ifr = $('#htmlEditorTemplateDiv').clone();
//    $('#htmlEditorTemplateDiv').remove();
   // console.log(htmlFieldTemplate_ifr);
   // $('#htmlEditorTemplateDiv').remove();

/*    $('#htmlFieldTemplate_ifr').contents().find('html').empty();
    console.log(htmlFieldTemplate_ifr);
    $('#htmlEditorTemplateDiv').appendTo('#ausgabe1');
    $(htmlFieldTemplate_ifr).appendTo('#htmlFieldTemplate_ifr');*/

	$("#cg_changes_saved").fadeOut(3000);
	
	// Allow only to press numbers as keys in input boxes

  //called when key is pressed in textbox
  $(".Max_Char, .Min_Char").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        //$("#cg_options_errmsg").html("Only numbers are allowed").show().fadeOut("slow");
               return false;
    }
   });


// Allow only to press numbers as keys in input boxes --- END

	   
	  
    $( "#ausgabe1" ).sortable({ cursor: "move", placeholder: "ui-state-highlight",
        start:  function( event, ui ) {

            if(ui.item.hasClass('htmlField')){
              //  cgJsClass.editUpload.tinymce.setTinymceNames(ui.item);
            }

        },
		stop:  function( event, ui ) {


			if(ui.item.hasClass('htmlField')){
				//console.log('stop html');
				//cgJsClass.editUpload.tinymce.copyPasteTinymceIframeContent();
			}


if(document.readyState === "complete"){

var v = 0;
//var total = $('.formField').length;



		  $( ".formField" ).each(function( i ) {
		  
		v++;

		//$(this).find('.fieldnumber').val(v); 
		$(this).find('.changeUploadFieldOrder').val(v); 
		//$(this).find('.changeFieldOrderUsersEntries').val(v); 

		// alert(v);
		

				  
		   });  
		   
		   v = 0;
		   
		   }
  
   }


	});
	$('#ausgabe1').css('cursor', 'move');
	
	$( "#fuckoff" ).change(function () {
$( "#ausgabe1" ).append("<input type='text' id='testl'>");
});


  
  // Use as url for images
  
  
  var isChecked = 0;
  
    $( ".Use_as_URL" ).each(function() {
	  
		if($(this).is( ":checked" )){isChecked = 1;}

  });
  
  
   $(document).on('click', '.Use_as_URL', function(e){
  //	$(".cg_info_show_gallery").click(function(){
		
		
		if($(this).is( ":checked" ) && isChecked==1){isChecked = 0;}
		
		
	if(isChecked==1){
	  
	 
	 $(this).prop("checked",false); 
	   isChecked = 0;
  }
		
		
else{
  $( ".Use_as_URL" ).each(function() {
	  
		$(".Use_as_URL").prop("checked",false);

  });

  $(this).prop("checked",true);
  

	  isChecked = 1;
	  

		
}

	
});	 
  
  
  
  // Use as url for images --- ENDE
  
  
  
  // Show info in gallery
  
  
  var isChecked = 0;
  
    $( ".cg_info_show_gallery" ).each(function() {
	  
		if($(this).is( ":checked" )){isChecked = 1;}

  });
  
  
   $(document).on('click', '.cg_info_show_gallery', function(e){
  //	$(".cg_info_show_gallery").click(function(){
		
		
		if($(this).is( ":checked" ) && isChecked==1){isChecked = 0;}
		
		
	if(isChecked==1){
	  
	 
	 $(this).prop("checked",false); 
	   isChecked = 0;
  }
		
		
else{
  $( ".cg_info_show_gallery" ).each(function() {
	  
		$(".cg_info_show_gallery").prop("checked",false);

  });

  $(this).prop("checked",true);
  

	  isChecked = 1;
	  

		
}

/*
	if($("#ScaleSizesGalery").is( ":checked" )){
	
	$("#ScaleWidthGalery").prop("checked",false);
	$( "#ScaleSizesGalery1" ).attr("disabled",false);
	$( "#ScaleSizesGalery2" ).attr("disabled",false);
	$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
	$( "#ScaleSizesGalery2" ).css({ 'background': '#ffffff' });
	
	}
	
	else{

	$("#ScaleWidthGalery").prop("disabled",false);
	$( "#ScaleSizesGalery1" ).attr("disabled",true);
	$( "#ScaleSizesGalery2" ).attr("disabled",true);
	$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
	$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });
	
		if($("#ScaleWidthGalery").is( ":checked" )){}
		else{
		$( "#ScaleWidthGalery" ).prop("checked",true);
		$( "#ScaleSizesGalery1" ).attr("disabled",false);
		$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
		}
	
	}*/
	
});	 


  // Show info in gallery -- ENDE
  
  
$(document).on('click', '.cg_delete_form_field', function(e){
	
//var del = arg;
//var del1 = arg1;
 
 	var del = $(this).attr("alt");
	var del1 = $(this).attr("titel");

    if (confirm("Delete field? All user information connected to this field will be deleted.")) {
       // alert("Clicked Ok");
		//confirmForm();
		fDeleteFieldAndData(del,del1);
	    return true;
    } else {
        //alert("Field not deleted.");
		
		var test = $("#"+del).find('.fieldValue').val();
		
		// alert(test);  
		
		//alert("This option is not available in the Lite Version.");
		
        return false;
    }


});
 
 
 /*function checkMe(arg,arg1) {


}*/

// Delete field only

//arg="sdfgsdfgsd";

 $(document).on('click', '.cg_delete_form_field_new', function(e){

 var arg = $(this).attr("alt");

 if($(this).hasClass('deleteHTMLfield')){
	 $(this).closest('.formField').css('display','none').appendTo('#cgTinymceCollection');
 }
 else{
     $("#"+arg).remove();
 }



});


// Delete field only --- ENDE

// Delete field and Data

function fDeleteFieldAndData(arg,arg1){

//alert("ARG: "+arg);
//alert("ARG1: "+arg1);



$("#"+arg).remove();
$( "#ausgabe1").append("<input type='hidden' name='deleteFieldnumber' value="+arg1+">");

	if(document.readyState === "complete"){
	// alert('READY!');
	//$('#submitForm').click();
	//alert("This option is not available in the Lite Version.");
	$('#submitForm').click();
	}




}

// Delete field and Data --- ENDE

// Überprüfen ob der Upload des Feldes im Frontend notwendig ist. Wenn Häckchen raus ist, erscheint ein zusätzliches Feld mit upload[] und Nummer der checkbox der Div zur späteren Feststellung.--- END


// Ob das Feld notwendig ist oder nicht soll als on oder als off mit gesendet werden

	 // function checkNecessary(){
	 //$('.necessary-check').live('click', function() {
	 //$('.necessary-check').on('click', function() {
$(document).on('click', '.necessary-check', function(e){

	
	//$(".necessary-check").click(function(){

	if($(this).is( ":checked" )){
	
		$(this).parent().find('.necessary-hidden').remove();
		
		//alert(1);
	}
	
	else{
			//$(this).prop("checked",false);
			$(this).removeAttr('checked');
		$(this).parent().append("<input type='hidden' class='necessary-hidden'  name='upload[]' value='off' />");

	//alert(2);
	}


});

//}	

// Ob das Feld notwendig ist oder nicht soll als on oder als off mit gesendet werden --- ENDE
	




	// Bestimmung der Anzahl der existierenden Div Felder in #ausgabe1 zur Bestiummung der Feldnummer in der Datenbank


    var countChildren = $('#ausgabe1').find('.formField').length;
	
	// Bestimmung der Anzahl der existierenden Div Felder in #ausgabe1 zur Bestiummung der Feldnummer in der Datenbank  ---- ENDE

// Check Box

// 1 = Feldtyp
// 2 = Feldreihenfolge
// 3 = Feldname
// 4 = Feldinhalt
// 5 = Felderfordernis

  $(document).on('click',"#cg_create_upload_add_field",function(){
  
  // User Fields here  
  
	if($('#dauswahl').val() == "cb") {
	
	countChildren++;
	
	// alert(countChildren);
	
	var cbCount = 60+$('.checkAgreementField').length;
	var cbHiddenCount = 600+$('.checkAgreementField').length;
	
	//alert(nfCount);
	
	if($('.checkAgreementField').length == 3){
     alert("This field can be produced maximum 3 times.");
	}
	else{$("#ausgabe1").append("<div id='"+ cbCount +"' class='formField checkAgreementField' style='width:855px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' name='upload[]' value='cb'>"+
	"<input type='hidden' value='"+ countChildren +"' name='addField[]' class='fieldValue'>"+ // Nummer des neuen Feldes wird extra versendet
	"<input type='hidden' value='nf' name='addField[]'>"+
	//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ countChildren +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
	//"<input type='hidden' class='fieldnumber' value='"+ countChildren +"'>"+
	//"<input type='hidden' name='upload[]' value='"+ countChildren +"' size='30' class='changeUploadFieldOrder' >"+// Feldreihenfolge
	"<strong>Check agreement </strong><br/>"+
	"<input type='text' name='upload[]' value='Check agreement' maxlength='100' size='30'>"+
	"<input type='hidden' name='actualID[]' value='placeholder' >"+// Platzhalter statt aktueller Feld ID
	"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+ cbCount +"' ><br>"+
	"<input type='checkbox' disabled >"+
	"<input type='text' name='upload[]' placeholder='HTML tags allowed'  maxlength='1000' style='width:832px;'>"+
	"Required <input type='checkbox' class='necessary-check' name='upload[]' disabled checked >"+
	"<input type='hidden' name='upload[]' class='necessary-hidden' value='off' ><br/><br/></div>");

        location.href = "#"+cbCount+"";
	}
	



    }
	

	if($('#dauswahl').val() == "nf") {
	
	countChildren++;
	
	// alert(countChildren);
	
	var nfCount = 10+$('.inputField').length;
	var nfHiddenCount = 100+$('.inputField').length;
	
	//alert(nfCount);
	
	if($('.inputField').length == 20){
     alert("This field can be produced maximum 20 times.");
	}
	else{$("#ausgabe1").append("<div id='"+ nfCount +"' class='formField inputField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' name='upload[]' value='nf'>"+
	"<input type='hidden' value='"+ countChildren +"' name='addField[]' class='fieldValue'>"+ // Nummer des neuen Feldes wird extra versendet
	"<input type='hidden' value='nf' name='addField[]'>"+
	//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ countChildren +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
	//"<input type='hidden' class='fieldnumber' value='"+ countChildren +"'>"+
	//"<input type='hidden' name='upload[]' value='"+ countChildren +"' size='30' class='changeUploadFieldOrder'>"+// Feldreihenfolge
     "<strong>Input</strong><br/>"+
	"<input type='text' name='upload[]["+nfCount+"]' value='Name' maxlength='100' size='30'>"+
	
		// Show input in gallery Bereich 
	"<div style='width:160px;float:right;text-align:right;'>Show info in gallery: &nbsp;"+
	"<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView["+nfCount+"]'>"+
	"</div>"+
	// Show input in gallery Bereich --- ENDE
	
		// Show input in Slider Bereich 
	"<div style='width:160px;float:right;text-align:right;margin-right:12px;'>Show info in slider: &nbsp;"+
	"<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider["+nfCount+"]' $checked>"+
	"</div>"+
	// Show input in Slider Bereich --- ENDE
	
		// Das Feld soll als URL agieren 
	"<div style='width:210px;float:right;text-align:right;margin-right:10px;'>Use this field as images url: &nbsp;"+
	"<input type='checkbox' class='Use_as_URL' style='margin-top:0px;' name='Use_as_URL["+nfCount+"]'>"+
	"</div>"+
	// Das Feld soll als URL agieren --- ENDE	
	

	
	"<input type='hidden' name='actualID[]' value='placeholder' >"+// Platzhalter statt aktueller Feld ID
	"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+ nfCount +"'><br/>"+
	"<input type='text' name='upload[]' placeholder='Placeholder'  maxlength='1000' value='' style='width:855px;'><br/>"+
	"Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='upload[]' value='3' size='7' maxlength='4' value=' '><br/>"+
	"Max. number of characters: <input type='text' class='Max_Char' name='upload[]' value='100' size='7' maxlength='4' value=' '><br/>"+
	"Required <input type='checkbox' class='necessary-check' name='upload[]' >"+
	"<input type='hidden' name='upload[]' class='necessary-hidden' value='off' ><br/><br/></div>");

        location.href = "#"+nfCount+"";

	}
	
	//alert(nfCount);
	/*
	$('html, body').animate({
	scrollTop: $("#'"+nfCount+"'").offset().top
    }, 400);
	$("html, body").animate({ scrollTop: $("#12").scrollTop() }, 1000);*/	
	


    }
	

  
	var kfCount = 20+$('.textareaField').length;
	var kfHiddenCount = 200+$('.textareaField').length;
  
	if($('#dauswahl').val() == "kf") {
	
		countChildren++;
	
		// alert(countChildren);
	
	
	if($('.textareaField').length == 10){
     alert("This field can be produced maximum 10 times.");
	}


	 
	else{$("#ausgabe1").append("<div id='"+ kfCount +"' class='formField textareaField' style='width:718px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;width:856px;'><br/><input type='hidden' name='upload[]' value='kf'>"+
	"<input type='hidden' value='"+ countChildren +"' name='addField[]' class='fieldValue'>"+ // Nummer des neuen Feldes wird extra versendet
	"<input type='hidden' value='kf' name='addField[]'>"+
	//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ countChildren +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
	//"<input type='hidden' class='fieldnumber' value='"+ countChildren +"'>"+
	//"<input type='hidden' name='upload[]' value='"+ countChildren +"' size='30' class='changeUploadFieldOrder'>"+// Feldreihenfolge
     "<strong>Textarea</strong><br/>"+
	"<input type='text' name='upload[]["+kfCount+"]' size='30' maxlength='100' value='Comment'>"+

	
	// Show input in gallery Bereich 
	"<div style='width:160px;float:right;text-align:right;'>Show info in gallery: &nbsp;"+
	"<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView["+kfCount+"]' $checked>"+
	"</div>"+
	// Show input in gallery Bereich --- ENDE	
	
		// Show input in Slider Bereich 
	"<div style='width:160px;float:right;text-align:right;margin-right:12px;'>Show info in slider: &nbsp;"+
	"<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider["+kfCount+"]' $checked>"+
	"</div>"+
	// Show input in Slider Bereich --- ENDE
	
	"<input type='hidden' name='actualID[]' value='placeholder' >"+// Platzhalter statt aktueller Feld ID
	"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+ kfCount +"'><br/>"+
	"<textarea name='upload[]' maxlength='10000' rows='10' value='' style='width:857px;' placeholder='Placeholder' ></textarea><br/>"+
	"Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='upload[]' value='3' size='7' maxlength='4' value=' '><br/>"+
	"Max. number of characters: <input type='text' class='Max_Char' name='upload[]' value='1000' size='7' maxlength='4' value=' '><br/>"+
	"Required <input type='checkbox' class='necessary-check' name='upload[]' >"+
	"<input type='hidden' name='upload[]' class='necessary-hidden' value='off' ><br/><br/></div>");
        location.href = "#"+kfCount+"";

	}
	


    }
	


	var seCount = 70+$('.selectField').length;
	var seHiddenCount = 700+$('.selectField').length;

	if($('#dauswahl').val() == "se") {

		countChildren++;

	if($('.selectField').length == 10){
     alert("This field can be produced maximum 10 times.");
	}

	else{$("#ausgabe1").append("<div id='"+ seCount +"' class='formField selectField' style='width:718px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;width:856px;'><br/><input type='hidden' name='upload[]' value='se'>"+
	"<input type='hidden' value='"+ countChildren +"' name='addField[]' class='fieldValue'>"+ // Nummer des neuen Feldes wird extra versendet
	"<input type='hidden' value='se' name='addField[]'>"+
	//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ countChildren +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
	//"<input type='hidden' class='fieldnumber' value='"+ countChildren +"'>"+
	//"<input type='hidden' name='upload[]' value='"+ countChildren +"' size='30' class='changeUploadFieldOrder'>"+// Feldreihenfolge
    "<strong>Select</strong><br/>"+
	"<input type='text' name='upload[]["+seCount+"]' size='30' maxlength='100' placeholder='Title of your select box'>"+


	// Show input in gallery Bereich
	"<div style='width:160px;float:right;text-align:right;'>Show info in gallery: &nbsp;"+
	"<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView["+seCount+"]' $checked>"+
	"</div>"+
	// Show input in gallery Bereich --- ENDE

		// Show input in Slider Bereich
	"<div style='width:160px;float:right;text-align:right;margin-right:12px;'>Show info in slider: &nbsp;"+
	"<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider["+seCount+"]' $checked>"+
	"</div>"+
	// Show input in Slider Bereich --- ENDE

	"<input type='hidden' name='actualID[]' value='placeholder' >"+// Platzhalter statt aktueller Feld ID
	"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+ seCount +"'><br/>"+
	"<textarea name='upload[]' maxlength='10000' rows='10' value='' style='width:857px;' placeholder='Each row one value - Example: &#10;value1&#10;value2&#10;value3&#10;value4&#10;value5&#10;value6' ></textarea><br/>"+
	"<br/>"+
	"Required <input type='checkbox' class='necessary-check' name='upload[]' >"+
	"<input type='hidden' name='upload[]' class='necessary-hidden' value='off' ><br/><br/></div>");

        location.href = "#"+seCount+"";
	}



    }


	if($('#dauswahl').val() == "ef") {

        countChildren++;

        // alert(countChildren);

        var efCount = 30 + $('.emailField').length;
        var efHiddenCount = 300 + $('.emailField').length;

        //alert(nfCount);

        if ($('.emailField').length == 1) {
            alert("This field can be produced only 1 time");
        }
        else {
            $("#ausgabe1").append("<div id='" + efCount + "' class='formField emailField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><input type='hidden' name='upload[]' value='ef'>" +
                "<div style='margin-bottom:5px;'>Do not appear if user is registered and logged in</div>" +
                "<input type='hidden' value='" + countChildren + "' name='addField[]' class='fieldValue'>" + // Nummer des neuen Feldes wird extra versendet
                "<input type='hidden' value='ef' name='addField[]'>" +
                //"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ countChildren +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
                //"<input type='hidden' class='fieldnumber' value='"+ countChildren +"' class='changeUploadFieldOrder'>"+
                //"<input type='hidden' name='upload[]' value='"+ countChildren +"' size='30'>"+// Feldreihenfolge
                "<input type='hidden' name='actualID[]' value='placeholder' >" +// Platzhalter statt aktueller Feld ID
                "<strong>E-Mail</strong><br/>" +
                "<input type='text' name='upload[][" + efCount + "]' value='E-Mail' maxlength='100' size='30'>" +

                // Show input in gallery Bereich
                "<div style='width:160px;float:right;text-align:right;'>Show info in gallery: &nbsp;" +
                "<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView[" + efCount + "]' $checked>" +
                "</div>" +
                // Show input in gallery Bereich --- ENDE

                // Show input in Slider Bereich
                "<div style='width:160px;float:right;text-align:right;margin-right:12px;'>Show info in slider: &nbsp;" +
                "<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider[" + efCount + "]' $checked>" +
                "</div>" +
                // Show input in Slider Bereich --- ENDE

                "<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='" + efCount + "'><br/>" +
                "<input type='text' name='upload[]' value='' maxlength='100' placeholder='Placeholder'  style='width:855px;'><br/>" +
                "Required <input type='checkbox' class='necessary-check' name='upload[]' >" +
                "<input type='hidden' name='upload[]' class='necessary-hidden' ><br/><br/></div>");

            location.href = "#" + efCount + "";
        }



    }

 
  	if($('#dauswahl').val() == "ht") {
		
			// 1 = Feldtyp
			// 2 = Feldtitel
			// 3 = Feldinhalt
	
	countChildren++;
	
	// alert(countChildren);
	
	var htCount = 50+$('.htmlField').length;
	var htHiddenCount = 500+$('.htmlField').length;
	
	//alert(nfCount);
	
	if($('.htmlField').length >= 10){
     alert("This field can be produced maximum 10 times.");
	}
	else{

		$("#ausgabe1").append("<div id='"+ htCount +"' class='formField cg_ht_field htmlField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>"+
		"<br/><input type='hidden' name='upload[]' value='ht'>"+
        "<strong>HTML</strong><br/>"+
		"<input type='text' name='upload[]' value='Title' maxlength='100' size='30'>"+
		"<input type='hidden' name='actualID[]' value='placeholder' >"+// Platzhalter statt aktueller Feld ID
		"<input class='cg_delete_form_field_new deleteHTMLfield' type='button' value='-' style='width:20px;' alt='"+htCount +"'> &nbsp; (HTML Field - Title is invisible)<br/><hr>"+

		"</div>");

        $(".htmlEditorTemplateDiv").first().appendTo("#"+ htCount +"");
        $(".htmlEditorTemplateDiv").last().css('display','block');
		$("#"+ htCount +"").append("<br>");

        location.href = "#"+htCount+"";

	}


	//alert(nfCount);
	/*
	$('html, body').animate({
	scrollTop: $("#'"+nfCount+"'").offset().top
    }, 400);
	$("html, body").animate({ scrollTop: $("#12").scrollTop() }, 1000);*/	
	

  }

  	if($('#dauswahl').val() == "caRo") {

			// 1 = Feldtyp
			// 2 = Feldtitel
			// 3 = Feldinhalt

	countChildren++;

	// alert(countChildren);

	var caRoCount = 80+$('.htmlField').length;
	var caRoHiddenCount = 800+$('.htmlField').length;

	//alert(nfCount);

	if($('.captchaRoField').length >= 1){
     alert("This field can be produced maximum 1 time.");
	}
	else{

		$("#ausgabe1").append("<div id='"+ caRoCount +"' class='formField captchaRoField' style='width:855px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>"+
		"<br/><input type='hidden' name='upload[]' value='caRo'>"+
		"<input type='hidden' value='"+ countChildren +"' name='addField[]' class='fieldValue'>"+ // Nummer des neuen Feldes wird extra versendet
		"<input type='hidden' value='caRo' name='addField[]'>"+
		//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ countChildren +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
		//"<input type='hidden' class='fieldnumber' value='"+ countChildren +"'>"+
		//"<input type='hidden' name='upload[]' value='"+ countChildren +"' size='30' class='changeUploadFieldOrder'>"+// Feldreihenfolge
        "<strong>Captcha (I am not a robot)</strong><br/>"+
		"<input type='checkbox' disabled> "+
		"<input type='text' name='upload[]' value='I am not a robot' maxlength='100' size='30'>"+
		"<input type='hidden' name='actualID[]' value='placeholder' >"+// Platzhalter statt aktueller Feld ID
		"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+caRoCount +"'>"+
		"<br/>Required <input type='checkbox' class='necessary-check' disabled checked >"+
		"<br/><br/></div>");


        location.href = "#"+caRoCount+"";

	}

	//alert(nfCount);
	/*
	$('html, body').animate({
	scrollTop: $("#'"+nfCount+"'").offset().top
    }, 400);
	$("html, body").animate({ scrollTop: $("#12").scrollTop() }, 1000);*/



 }
 });
 
 
	
  /*$("#cg_create_upload_add_field").click(function(){
  
alert("This option is not available in the Lite Version.");
	
 });*/
 

 

 
 
  
});