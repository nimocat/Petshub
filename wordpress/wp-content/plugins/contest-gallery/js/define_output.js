jQuery(document).ready(function($){
	
	$("#cg_changes_saved").fadeOut(3000);
	
	
	  $(function() {
    $( "#ausgabe1" ).sortable({cursor: "move",placeholder: "ui-state-highlight"});
    //$( "#ausgabe1" ).disableSelection();
	$('#ausgabe1').css('cursor', 'move');
	
	$( "#fuckoff" ).change(function () {
$( "#ausgabe1" ).append("<input type='text' id='testl'>");
});

  });
	
	
/*$("*").click(function() {
  alert(this.id);
});*/
/*
	function fDelete(arg,arg1,arg2,arg3){

	//$("#"+arg).remove();

	$('#cg_define_output').append("<option value='"+arg3+"' class='"+arg2+"'>"+arg3+"</option>");

			if ($("#cg_define_output").length == 0) {
			//alert('works');
			$("#options").append("<select name='cg_define_output' id='cg_define_output' style='font-size:16px;width:100px;'><option id='"+arg1+"' name='"+arg1+"' value='' class='"+arg2+"'>"+arg3+"</option></select>");
			$("#plus").append("<input type='button' name='plus' value='+' style='width:20px;display:inline;'>");
			}

		var sorted = $.makeArray($("#cg_define_output option")).sort(function(a,b){
			return $(a).attr('id') > $(b).attr('id') ? 1:-1;
		});
		$("#cg_define_output").empty().append(sorted);

	}*/
	
	
	$(document).on('click', '.cg-delete', function(e){

	//alert("works")
	//$(".necessary-check").click(function(){

	var fieldType = $(this).attr("alt");
	var fieldTitel = $(this).attr("titel");
	var form_input_id = $(this).parent().find(".form_input_id").val();
	//alert(form_input_id);
	
		$(this).parent().remove();
		
		$("#cg_define_output").append("<option class='"+fieldType+"' value='"+fieldTitel+"' id='"+form_input_id+"'>"+fieldTitel+"</option>");
		
		//alert(1);
//<option id='$id' name='$i' value='$formvalue' class='nf'>$formvalue</option>
			if ($("#cg_define_output").length == 0) {
			//alert('works');
			$("#options").append("<select name='cg_define_output' id='cg_define_output' style='font-size:16px;width:100px;'><option value='"+fieldTitel+"' class='"+fieldType+"' id='"+form_input_id+"'>"+fieldTitel+"</option></select>");
			$("#plus").append("<input id='submit_define_output' type='button' name='plus' value='+' style='width:20px;'>");
			}

});
	
	
	
	

/*
function fPermitDelete(arg,arg1){

//alert(arg);
//alert(arg1);

if($("."+arg).val() == arg1){
$( "."+arg ).remove();
}

else{
$( "#"+arg ).append("<input type='hidden' name='upload[]' class='"+arg+"' value="+arg1+">");
}

}*/




//alert($('#cg_define_output option:selected').text());

//alert(3);

// 100+ = Name Fields
// 200+ = Comment Fields
// 300+ = E-mail Fields


//var countNf = "<?php echo $countNf;?>";
//var countKf = "<?php echo $countKf;?>";
//var countEf = "<?php echo $countEf;?>";


// NORMALES FELD

	$(document).on('click', '#submit_define_output', function(e){
  
  	var i = 0;
	
	var id = [];
	

	
			$( ".ausgabe1" ).each(function( i ) {
			
			i++;
					  
			var rowidValue =  $(this).find('.output').attr("id",i); 
			
			
			//rowid.push(rowidValue);
							
			});
			
	i=0;		

		
	var	n = $( ".output" ).length;
	
	// alert(n);
	
// NORMALES FELD

	if($('#cg_define_output .nf').is(':selected')) {
	
		//var nfIdCount = 100+$('input.nf').size();
		var fieldtype = "nf";
		var fieldtitelShow = $( "#cg_define_output option:selected" ).val();
		fieldtitel = '"'+fieldtitelShow+'"';
		
		n++;
		
		var id = $( "#cg_define_output option:selected" ).attr("id");
		
		// alert(fieldtype);
		// alert(fieldtitel);
		// alert(id);

		
		$("#ausgabe1").append("<div style='border: 1px solid #DFDFDF;padding:10px;margin-top:20px;margin-bottom:20px;' id='"+id+"'><div style='display:inline;float:left;margin-top:15px;font-size:16px;'>"+fieldtitelShow+":</div>"+
		"<input type='hidden' name='output[]' class='nf' value='nf'><input type='hidden' name='output[]' class='form_input_id' value='"+id+"'><input class='cg-delete' type='button' value='-' alt='"+fieldtype+"' titel='"+fieldtitelShow+"' style='width:20px;float:left;margin-top:10px;margin-bottom:10px;margin-left:10px;'><br/>"+
		"<input type='hidden' name='output[]' value='"+fieldtitelShow+"'><input type='text' value='' size='102' style='width:855px;' disabled style='background-color: white;'><br/><br/></div>");
  
 
	};

  
 
// KOMMENTARFELD

  if($('#cg_define_output .kf').is(':selected')) {
  
		//var nfIdCount = 100+$('input.nf').size();
		var fieldtype = "kf";
		var fieldtitelShow = $( "#cg_define_output option:selected" ).val();
		fieldtitel = '"'+fieldtitelShow+'"';
		
		n++;
		
		var id = $( "#cg_define_output option:selected" ).attr("id");
		
	// 	alert(fieldtype);
	// 	alert(fieldtitel);
	// 	alert(id);
		
		$("#ausgabe1").append("<div id='"+id+"' style='border: 1px solid #DFDFDF;padding:10px;margin-top:20px;margin-bottom:20px;'><div style='display:inline;float:left;margin-top:15px;font-size:16px;'>"+fieldtitelShow+":</div>"+
			"<input type='hidden' name='output[]' class='kf' value='kf'><input type='hidden' class='form_input_id' name='output[]' value='"+id+"'><input class='cg-delete' type='button' value='-' alt='"+fieldtype+"' titel='"+fieldtitelShow+"' style='width:20px;float:left;margin-top:10px;margin-bottom:10px;margin-left:10px;'><br/>"+
			"<input type='hidden' name='output[]' value='"+fieldtitelShow+"'><textarea maxlength='1000' style='width:855px;' rows='10' disabled style='background-color: white;'></textarea><br/><br/></div>");

			 
 };
 
// E-Mail
 
	if($('#cg_define_output .ef').is(':selected')) {
	
		//var nfIdCount = 100+$('input.nf').size();
		var fieldtype = "ef";
		var fieldtitelShow = $( "#cg_define_output option:selected" ).val();
		fieldtitel = '"'+fieldtitelShow+'"';
		
		n++;
		
		var id = $( "#cg_define_output option:selected" ).attr("id");
		
	// 	alert(fieldtype);
	// 	alert(fieldtitel);
	// 	alert(id); 	
	
		$("#ausgabe1").append("<div id='"+id+"' style='border: 1px solid #DFDFDF;padding:10px;margin-top:20px;margin-bottom:20px;'><div style='display:inline;float:left;margin-top:15px;font-size:16px;'>"+fieldtitelShow+":</div>"+
		"<input type='hidden' name='output[]' class='ef' value='ef'><input type='hidden' name='output[]' class='form_input_id' value='"+id+"'><input class='cg-delete' type='button' value='-' alt='"+fieldtype+"' titel='"+fieldtitelShow+"' style='width:20px;float:left;margin-top:10px;margin-bottom:10px;margin-left:10px;'><br/>"+
		"<input type='hidden' name='output[]' value='"+fieldtitelShow+"'><input type='text' value='' style='width:855px;' size='85' maxlength='110' disabled style='background-color: white;'><br/><br/></div>");

	 
	}
  

 
	
	//alert($("#cg_define_output > option").length);
	
	if($("#cg_define_output > option").length == 0){
		
		alert('There are no more fields defined in "Edit upload form"');
		
	}


 
 // ---- Entfernen vom ausgewählten aus Select
  
		if(document.readyState === "complete"){
		
		$('#cg_define_output option:selected').remove();

		//alert($('#cg_define_output option').size());
		
			//if ($('#cg_define_output option').size()==0) {
			//$('#cg_define_output').remove();
			//$('#submit').remove();
			//}
		
		}

 // Entfernen vom ausgewählten aus Select ---- ENDE	
 
	});
	

  
});