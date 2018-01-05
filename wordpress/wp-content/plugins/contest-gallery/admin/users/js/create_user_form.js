jQuery(document).ready(function($){
	
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
	
  $(function() {
	  
	   
	  
    $( "#ausgabe1" ).sortable({ cursor: "move", placeholder: "ui-state-highlight", stop:  function( event, ui ) {

if(document.readyState === "complete"){

var v = 0;
//var total = $('.formField').length;



		  $( ".formField" ).each(function( i ) {
		  
		v++;

		//$(this).find('.fieldnumber').val(v); 
		$(this).find('.Field_Type').attr("name","Field_Type["+v+"]");
		$(this).find('.Field_Order').attr("name","Field_Order["+v+"]");
		$(this).find('.Field_Name').attr("name","Field_Name["+v+"]");		
		$(this).find('.Field_Id').attr("name","Field_Id["+v+"]");		
		$(this).find('.cg_actualID').attr("name","actualID["+v+"]");
		$(this).find('.Field_Content').attr("name","Field_Content["+v+"]");
		$(this).find('.Min_Char').attr("name","Min_Char["+v+"]");
		$(this).find('.Max_Char').attr("name","Max_Char["+v+"]");
		$(this).find('.necessary-check').attr("name","Necessary["+v+"]");
		$(this).find('.necessary-hidden').attr("name","Necessary["+v+"]");


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
	
$("#cg"+arg).remove();


});

/*
function fDeleteFieldOnly(arg){

// alert(arg);


}*/

// Delete field only --- ENDE

// Delete field and Data

function fDeleteFieldAndData(arg,arg1){

//alert("ARG: "+arg);
//alert("ARG1: "+arg1);



$("#"+arg+"").remove();
$( "#ausgabe1").append("<input type='hidden' name='deleteFieldnumber' value="+arg1+">");

	if(document.readyState === "complete"){
	// alert('READY!');
	//$('#submitForm').click();
	//alert("This option is not available in the Lite Version.");
	$('#submitForm').click();
	}



/*


if(document.readyState === "complete"){

var v = 0;
var total = $('.formField').length;

		  $( ".formField" ).each(function( i ) {
		  
		v++;

		$(this).find('.fieldnumber').val(v); 
		$(this).find('.changeUploadFieldOrder').val(v); 
		$(this).find('.changeFieldOrderUsersEntries').val(v); 


		alert("test"+v);
		

				  
		   });  
		   
		   v = 0;

	if(document.readyState === "complete"){
	alert('READY!');
	$('#submitForm').click();
	}
}*/

}

// Delete field and Data --- ENDE















// Überprüfen ob der Upload des Feldes im Frontend notwendig ist. Wenn Häckchen raus ist, erscheint ein zusätzliches Feld mit upload[] und Nummer der checkbox der Div zur späteren Feststellung.
/*
function checkNecessary(arg,arg1){


	if($("."+arg).val() == arg1){

	// ob Upload oder upload
	var checkName = $( "."+arg ).attr('name');
	alert(checkName);
	$( "."+arg ).remove();
	}

else{

	// ob Upload oder upload
	var checkName = $( "."+arg ).attr('name');
	alert(checkName);
	
	if(checkName.indexOf("upload") >= 0){
	$( "#"+arg ).append("<input type='hidden' name='upload[]' class='"+arg+"' value="+arg1+">");
	}

	else{
	$( "#"+arg ).append("<input type='hidden' name='upload[]' class='"+arg+"' value="+arg1+">");
	}

}

}*/

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
	





$(document).ready(function(){
	
	// Bestimmung der Anzahl der existierenden Div Felder in #ausgabe1 zur Bestiummung der Feldnummer in der Datenbank


    var i = $('.formField').length;
	
	// Bestimmung der Anzahl der existierenden Div Felder in #ausgabe1 zur Bestiummung der Feldnummer in der Datenbank  ---- ENDE

// Check Box

// 1 = Feldtyp
// 2 = Feldreihenfolge
// 3 = Feldname
// 4 = Feldinhalt
// 5 = Felderfordernis

  $("#cg_create_upload_add_field").click(function(){  
  
  
	if($('#dauswahl').val() == "cb") {
	
	i++;
	
	// alert(i);
	
	var cbCount = 10+$('input#cb').size();
	var cbHiddenCount = 100+$('input#cb').size();
	
	//alert(nfCount);
	
	if($('input#cb').size() == 3){
     alert("This field can be produced maximum 3 times.");
	}
	else{$("#ausgabe1").append("<div id='cg"+ cbCount +"' class='formField' style='width:855px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' class='Field_Type' name='Field_Type["+i+"]' value='user-check-agreement-field'>"+
	"<input type='hidden' class='cg_Field_Text_Type' >"+ // Zum Zählen von Text Feldern
	"<input type='hidden' class='Field_Order' name='Field_Order["+i+"]' >"+ // Nummer des neuen Feldes wird extra versendet
	//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ i +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
	//"<input type='hidden' class='fieldnumber' value='"+ i +"'>"+
	//"<input type='hidden' name='upload[]' value='"+ i +"' size='30' class='changeUploadFieldOrder' >"+// Feldreihenfolge
	"<input type='text' class='Field_Name' name='Field_Name["+i+"]' value='Check agreement' maxlength='200' size='30'>"+
	"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+ cbCount +"' ><br>"+
	"<input type='checkbox' disabled >"+
	"<input type='text' name='Field_Content["+i+"]' placeholder='HTML tags allowed' id='cb'  maxlength='1000' placeholder='HTML tags allowed' style='width:832px;'>"+
	"Required <input type='checkbox' class='necessary-check' name='upload[]' disabled checked >"+
	"<input type='hidden' name='upload[]' class='necessary-hidden' value='off' ><br/><br/></div>");}
	
	location.href = "#cg"+cbCount+"";
	
	
 }});




// NORMALES FELD

// 1 = Feldtyp
// 2 = Feldreihenfolge 
// 3 = Feldtitel
// 4 = Feldinhalt
// 5 = Feldkrieterium1
// 6 = Feldkrieterium2
// 7 = Felderfordernis

  $("#cg_create_upload_add_field").click(function(){  
  
  
	if($('#dauswahl').val() == "nf") {
	
	i++;
	
	// alert(i);
	
	var nfCount = 10+$('.cg_Field_Text_Type').size();
	var nfHiddenCount = 100+$('.cg_Field_Text_Type').size();
	

	
	//alert(nfCount);
	
	if($('input#nf').size() == 20){
     alert("This field can be produced maximum 20 times.");
	}
	else{$("#ausgabe1").append("<div id='cg"+nfCount+"' class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' class='Field_Type' name='Field_Type["+i+"]' value='user-text-field'>"+
	"<input type='hidden' class='cg_Field_Text_Type' >"+ // Zum Zählen von Text Feldern
	"<input type='hidden' class='Field_Order' name='Field_Order["+i+"]' >"+ // Nummer des neuen Feldes wird extra versendet
	//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ i +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank 
	//"<input type='hidden' class='fieldnumber' value='"+ i +"'>"+
	//"<input type='hidden' name='upload[]' value='"+ i +"' size='30' class='changeUploadFieldOrder'>"+// Feldreihenfolge
	"<input type='text' class='Field_Name' name='Field_Name["+i+"]' value='Name' maxlength='100' size='30'>"+	
	"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+ nfCount +"'><br/>"+
	"<input type='text' class='Field_Content' name='Field_Content["+i+"]'  placeholder='Placeholder' maxlength='1000' value='' style='width:855px;'><br/>"+
	"Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='Min_Char["+i+"]' value='3' size='7' maxlength='3'><br/>"+
	"Max. number of characters: <input type='text' class='Max_Char' name='Max_Char["+i+"]' value='100' size='7' maxlength='3' ><br/>"+
	"Necessary <input type='checkbox' class='necessary-check' name='Necessary["+i+"]' >"+
	"<input type='hidden' name='Necessary["+i+"]' class='necessary-hidden' value='off' ><br/><br/></div>");}
	
	//alert(nfCount);
	/*
	$('html, body').animate({
	scrollTop: $("#'"+nfCount+"'").offset().top
    }, 400);
	$("html, body").animate({ scrollTop: $("#12").scrollTop() }, 1000);*/
	
	location.href = "#cg"+nfCount+"";
	
 }});
 
// KOMMENTARFELD
  
  $("#cg_create_upload_add_field").click(function(){
  
	var kfCount = 20+$('.cg_Field_Comment_Type').size();
	var kfHiddenCount = 200+$('.cg_Field_Comment_Type').size();
  
	if($('#dauswahl').val() == "kf") {
	
		i++;
	
		// alert(i);
	
	
	if($('textarea#kf').size() == 10){
     alert("This field can be produced maximum 10 times.");
	}
	
	 
	else{$("#ausgabe1").append("<div id='cg"+kfCount+"' class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' class='Field_Type' name='Field_Type["+i+"]' value='user-comment-field'>"+
	"<input type='hidden' class='cg_Field_Comment_Type' >"+ // Zum Zählen von Text Feldern
	"<input type='hidden' class='Field_Order' name='Field_Order["+i+"]' >"+ // Nummer des neuen Feldes wird extra versendet
	//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ i +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank 
	//"<input type='hidden' class='fieldnumber' value='"+ i +"'>"+
	//"<input type='hidden' name='upload[]' value='"+ i +"' size='30' class='changeUploadFieldOrder'>"+// Feldreihenfolge
	"<input type='text' class='Field_Name' name='Field_Name["+i+"]' value='Comment' maxlength='100' size='30'>"+
	
	"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+ kfCount +"'><br/>"+
	"<textarea class='Field_Content' name='Field_Content["+i+"]' placeholder='Placeholder' maxlength='10000' style='width:856px;' rows='10'></textarea><br/>"+
	"Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='Min_Char["+i+"]' value='3' size='7' maxlength='3'><br/>"+
	"Max. number of characters: <input type='text' class='Max_Char' name='Max_Char["+i+"]' value='100' size='7' maxlength='4' ><br/>"+
	"Necessary <input type='checkbox' class='necessary-check' name='Necessary["+i+"]' >"+
	"<input type='hidden' name='Necessary["+i+"]' class='necessary-hidden' value='off' ><br/><br/></div>");}
	
	location.href = "#cg"+kfCount+"";
	
 }});
 
 // E-Mail
 
   $("#cg_create_upload_add_field").click(function(){
  
	if($('#dauswahl').val() == "ef") {
	
		i++;
	
		// alert(i);
	
	var efCount = 30+$('input#ef').size();
	var efHiddenCount = 300+$('input#ef').size();
	
	//alert(nfCount);
	
	if($('input#ef').size() == 1){
     alert("This field can be produced only 1 time");
	}
	else{$("#ausgabe1").append("<div id='cg"+ efCount +"' class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' name='upload[]' value='ef'>"+
	"<input type='hidden' value='"+ i +"' name='addField[]' class='fieldValue'>"+ // Nummer des neuen Feldes wird extra versendet
	"<input type='hidden' value='ef' name='addField[]'>"+
	//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ i +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
	//"<input type='hidden' class='fieldnumber' value='"+ i +"' class='changeUploadFieldOrder'>"+
	//"<input type='hidden' name='upload[]' value='"+ i +"' size='30'>"+// Feldreihenfolge
	"<input type='hidden' name='actualID[]' value='placeholder' >"+// Platzhalter statt aktueller Feld ID
	"<input type='text' name='upload[]["+efCount+"]' value='E-Mail' maxlength='100' size='30'>"+
	
	// Show input in gallery Bereich 
	"<div style='width:160px;float:right;text-align:right;'>Show info in gallery: &nbsp;"+
	"<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView["+efCount+"]' $checked>"+
	"</div>"+
	// Show input in gallery Bereich --- ENDE	
	
	// Show input in Slider Bereich 
	"<div style='width:160px;float:right;text-align:right;margin-right:12px;'>Show info in slider: &nbsp;"+
	"<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider["+efCount+"]' $checked>"+
	"</div>"+
	// Show input in Slider Bereich --- ENDE
	
	"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+ efCount +"'><br/>"+
	"<input type='text' name='upload[]' value='' id='ef' maxlength='100' style='width:855px;'><br/>"+
	"Necessary <input type='checkbox' class='necessary-check' name='upload[]' >"+
	"<input type='hidden' name='upload[]' class='necessary-hidden' ><br/><br/></div>");}
	
	location.href = "#cg"+efCount+"";
	
 }});
 
   $("#cg_create_upload_add_field").click(function(){
	 
	 
 
  	if($('#dauswahl').val() == "ht") {
		
			// 1 = Feldtyp
			// 2 = Feldtitel
			// 3 = Feldinhalt
	
	i++;
	
	// alert(countChildren);
	
	var htCount = 10+$('input#ht').size();
	var htHiddenCount = 100+$('input#ht').size();
	
	//alert(nfCount);
	
	if($('.cg_ht_field').size() >= 10){
     alert("This field can be produced maximum 10 times.");
	}
	else{
		
		$("#ausgabe1").append("<div id='"+ htCount +"' class='formField cg_ht_field' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>"+
		"<br/><input type='hidden' class='Field_Type' name='Field_Type["+i+"]' value='user-html-field'>"+
		"<input type='hidden' class='cg_Field_HTML_Type' >"+ // Zum Zählen von Text Feldern
		"<input type='hidden' class='Field_Order' name='Field_Order["+i+"]' >"+ // Nummer des neuen Feldes wird extra versendet
		//"<input type='hidden' value='"+ i +"' name='addField[]' class='fieldValue'>"+ // Nummer des neuen Feldes wird extra versendet
	//	"<input type='hidden' value='ht' name='addField[]'>"+
		//"<input type='hidden' name='upload[]' class='fieldnumber' value='"+ countChildren +"'>"+// Feldnummer wird vergeben zur Orientierung in der Datenbank
		//"<input type='hidden' class='fieldnumber' value='"+ countChildren +"'>"+
		//"<input type='hidden' name='upload[]' value='"+ countChildren +"' size='30' class='changeUploadFieldOrder'>"+// Feldreihenfolge
		"<input type='text' class='Field_Name' name='Field_Name["+i+"]' value='Title' maxlength='100' size='30'>"+
		
		//"<input type='hidden' name='actualID[]' value='placeholder' >"+// Platzhalter statt aktueller Feld ID
		"<input class='cg_delete_form_field_new' type='button' value='-' style='width:20px;' alt='"+htCount +"'> &nbsp; (HTML Field - Title is invisible)<br/>"+
		"<textarea class='Field_Content' name='Field_Content["+i+"]' maxlength='10000' rows='10' value='' style='width:857px;' placeholder='<p>Information here</p>' ></textarea><br/>"+
		"<br/></div>");
	
	
	}
	
	//alert(nfCount);
	/*
	$('html, body').animate({
	scrollTop: $("#'"+nfCount+"'").offset().top
    }, 400);
	$("html, body").animate({ scrollTop: $("#12").scrollTop() }, 1000);*/	
	
	location.href = "#"+htCount+"";
	
 }
 });
 
	
	
  /*$("#cg_create_upload_add_field").click(function(){
  
alert("This option is not available in the Lite Version.");
	
 });*/
 

 
});
 
 
  
});