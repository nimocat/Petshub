<?php
require_once('get-data-create-user-form.php');


require_once(dirname(__FILE__) . "/../../../nav-menu.php");



	//<div style="display:block;padding:20px;padding-bottom:10px;background-color:white;width:897px;text-align:right;margin-top:10px;border: 1px solid #DFDFDF;height:40px;">

echo '<div style="width:881px;padding-left:27px;padding-right:27px;background-color:white;border: 1px solid #DFDFDF;padding-top:20px;padding-bottom:20px;margin-bottom:15px;">';
/*if ($checkDataFormOutput){
echo "<form method='POST' action='?page=contest-gallery/index.php&option_id=$GalleryID&define_output=true'><input type='submit' value='Single pic info' style='float:right;text-align:center;width:180px;'/></form>";
}*/
//echo "<form name='defineUpload' enctype='multipart/form-data' action='?page=contest-gallery/index.php&optionID=$GalleryID&defineUpload=true' id='form' method='post'>";

		//<option value="ef">E-Mail</option>
		//<option value="cb">Check agreement</option>

$heredoc = <<<HEREDOC
	<select name="dauswahl" id="dauswahl">
		<optgroup label="User fields">
			<option  value="nf">Text</option>
			<option value="kf">Comment</option>
			<option value="cb">Check agreement</option>
		</optgroup>
		<optgroup label="Admin fields">
			<option  value="ht">HTML</option>
		 </optgroup>
	</select>
	<input id="cg_create_upload_add_field" type="button" name="plus" value="+" style='width:20px;'>
	</div>
HEREDOC;

echo $heredoc;

echo "<form name='create_user_form' enctype='multipart/form-data' action='?page=contest-gallery/index.php&option_id=$GalleryID&create_user_form=true' id='form' method='post'>";
echo "<input type='hidden' name='option_id' value='$GalleryID'>";
?>
<div style="width:935px;background-color:#fff;border: 1px solid #DFDFDF;padding-bottom:15px;">
<div id="ausgabe1" style="display:table;width:875px;padding:10px;background-color:#fff;padding-left:29px;padding-right:20px;">
<br/>

<?php	
	

// ---------------- AUSGABE des gespeicherten Formulares

/*

	$deleteFieldnumber = @$_POST['deleteFieldnumber'];
	$changeFieldRow = @$_POST['changeFieldRow'];
	$addField = @$_POST['addField'];

	
	//echo 'deleteFieldnumber:<br/>';
	//print_r($deleteFieldnumber);echo '<br/>';
	//echo 'changeFieldRow:<br/>';
	//print_r($changeFieldRow);echo '<br/>';
	//echo 'addField:<br/>';
	//print_r($addField);
	//echo '<br/>';


// Jeder sechste wird ausgewertet, um festzustellen, um welche Feldart sich handelt
$i3 = 7;
$key = 0;

// Field type
$ft ='';*/

// IDs of the div boxes   
@$nfCount = 10;
@$kfCount = 20;
@$efCount = 30;
@$bhCount = 40;
@$htCount = 50;
@$cbCount = 60;
@$htCount = 70;

// Further IDs of the div boxes   
@$nfHiddenCount = 100;
@$kfHiddenCount = 200;
@$efHiddenCount = 300;
@$bhHiddenCount = 400;
@$htCount = 500;
@$cbHiddenCount = 600;
@$htHiddenCount = 700;

// FELDBENENNUNGEN

// 1 = Feldtyp
// 2 = Feldnummer
// 3 = Feldtitel
// 4 = Feldinhalt
// 5 = Feldkrieterium1
// 6 = Feldkrieterium2
// 7 = Felderfordernis 


//print_r($selectFormInput);

// Zum zählen von Feld Reihenfolge
$i = 1;

if ($selectFormInput) {

	foreach ($selectFormInput as $key => $value) {
	

		if(@$value->Field_Type == 'main-mail'){
		
		// Feldtyp
		// Feldreihenfolge 
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$Min_Char = $value->Min_Char;
		$Max_Char = $value->Max_Char;
		$Field_Name = html_entity_decode(stripslashes($value->Field_Name));
		$Field_Content = html_entity_decode(stripslashes($value->Field_Content));
		$Field_Order = $value->Field_Order;
		$Field_Type = $value->Field_Type;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
	
		// Anfang des Formularteils
		echo "<div id='cg_main-mail'  class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>
		<br/><input type='hidden' class='Field_Type' name='Field_Type[$i]' value='$Field_Type'>";
		
		echo "<input type='hidden' class='Field_Order' value='$Field_Order'>";
		
				
		echo "<input type='hidden' class='Field_Id' name='Field_Id[$i]' value='$id' >";
				
		// Aktuelle Feld ID mitschicken. $i Aufzählung ist hier nicht notwendig. Wird für update entries verwendet.
		echo "<input type='hidden' class='cg_actualID' name='actualID[]' value='$id' >";		
					
					echo "<input type='text' class='Field_Name'  name='Field_Name[$i]' value='$Field_Name'  size='30' maxlength='100'> (Login user mail: Wordpress-Profile-Field)<br/>"; // Titel und Delete Möglichkeit die oben bestimmt wurde

					echo "<input type='text' class='Field_Content' name='Field_Content[$i]' value='$Field_Content' placeholder='Placeholder' id='main-mail' maxlength='1000' style='width:855px;'><br/>";
										
					
					echo "Required <input type='checkbox' class='necessary-check' name='Necessary[$i]' checked disabled ><br/><br/>";					
					
					echo "</div>";

		}	
					
		
		if(@$value->Field_Type == 'password'){
		
		// Feldtyp
		// Feldreihenfolge 
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$Min_Char = $value->Min_Char;
		$Max_Char = $value->Max_Char;
		$Field_Name = html_entity_decode(stripslashes($value->Field_Name));
		$Field_Content = html_entity_decode(stripslashes($value->Field_Content));
		$Field_Order = $value->Field_Order;
		$Field_Type = $value->Field_Type;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
		// Anfang des Formularteils
		echo "<div id='cg_password'  class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>
		<br/><input type='hidden' class='Field_Type' name='Field_Type[$i]' value='$Field_Type'>";
		
		echo "<input type='hidden' class='cg_actualID' class='Field_Order' value='$Field_Order'>";
		
				
		echo "<input type='hidden' class='Field_Id' name='Field_Id[$i]' value='$id' >";
				
		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";		
					
					echo "<input type='text' class='Field_Name'  name='Field_Name[$i]' value='$Field_Name'  size='30' maxlength='100'> (Login user password: Wordpress-Profile-Field)<br/>"; // Titel und Delete Möglichkeit die oben bestimmt wurde

					echo "<input type='text' class='Field_Content' name='Field_Content[$i]' value='$Field_Content' placeholder='Placeholder' id='password' maxlength='1000' style='width:855px;'><br/>";
										
					echo "Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='Min_Char[$i]' value='$Min_Char' size='7' maxlength='4' ><br/>";
										
					echo "Max. number of characters: <input type='text' class='Max_Char' name='Max_Char[$i]' value='$Max_Char' size='7' maxlength='4' ><br/>";
					
					echo "Required <input type='checkbox' class='necessary-check' name='Necessary[$i]' checked disabled ><br/><br/>";					
					
					echo "</div>";
	
		}
		
		if(@$value->Field_Type == 'password-confirm'){
		
		// Feldtyp
		// Feldreihenfolge 
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$Min_Char = $value->Min_Char;
		$Max_Char = $value->Max_Char;
		$Field_Name = html_entity_decode(stripslashes($value->Field_Name));
		$Field_Content = html_entity_decode(stripslashes($value->Field_Content));
		$Field_Order = $value->Field_Order;
		$Field_Type = $value->Field_Type;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
		// Anfang des Formularteils
		echo "<div id='cg_password-confirm'  class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>
		<br/><input type='hidden' class='Field_Type' name='Field_Type[$i]' value='$Field_Type'>";
		
		echo "<input type='hidden' class='cg_actualID' class='Field_Order' value='$Field_Order' >";
		
				
		echo "<input type='hidden' class='Field_Id' name='Field_Id[$i]' value='$id' >";
				
		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";		
					
					echo "<input type='text' name='Field_Name[$i]' class='Field_Name'  value='$Field_Name'  size='30' maxlength='100'> (Confirm login user password: Wordpress-Profile-Field)<br/>"; // Titel und Delete Möglichkeit die oben bestimmt wurde

					echo "<input type='text' class='Field_Content' name='Field_Content[$i]' value='$Field_Content' placeholder='Placeholder' id='password-confirm' maxlength='1000' style='width:855px;'><br/>";
										
					echo "Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='Min_Char[$i]' value='$Min_Char' size='7' maxlength='4' ><br/>";
										
					echo "Max. number of characters: <input type='text' class='Max_Char' name='Max_Char[$i]' value='$Max_Char' size='7' maxlength='4' ><br/>";
					
					echo "Required <input type='checkbox' class='necessary-check' name='Necessary[$i]' checked disabled ><br/><br/>";
					
					echo "</div>"; 


		}
		
		
		
		if(@$value->Field_Type == 'main-user-name'){
		
		// Feldtyp
		// Feldreihenfolge 
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis 
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$Min_Char = $value->Min_Char;
		$Max_Char = $value->Max_Char;
		$Field_Name = html_entity_decode(stripslashes($value->Field_Name));
		$Field_Content = html_entity_decode(stripslashes($value->Field_Content));
		$Field_Order = $value->Field_Order;
		$Field_Type = $value->Field_Type;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
		// Anfang des Formularteils
		echo "<div id='cg_main-user-name'  class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>
		<br/><input type='hidden' class='Field_Type' name='Field_Type[$i]' value='$Field_Type'>";
		
				
		echo "<input type='hidden' class='Field_Id' name='Field_Id[$i]' value='$id' >";
		
		echo "<input type='hidden' class='Field_Order' value='$Field_Order' >";

		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' class='cg_actualID' name='actualID[]' value='$id' >";		
					
					echo "<input type='text' class='Field_Name' name='Field_Name[$i]' value='$Field_Name'  size='30' maxlength='100'> (Login user name: Wordpress-Profile-Field)<br/>"; // Titel und Delete Möglichkeit die oben bestimmt wurde 

					echo "<input type='text' class='Field_Content' name='Field_Content[$i]' value='$Field_Content' placeholder='Placeholder' id='main-user-name' maxlength='1000' style='width:855px;'><br/>";
										
					echo "Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='Min_Char[$i]' value='$Min_Char' size='7' maxlength='4' ><br/>";
										
					echo "Max. number of characters: <input type='text' class='Max_Char' name='Max_Char[$i]' value='$Max_Char' size='7' maxlength='4' ><br/>";
					
					echo "Required <input type='checkbox' class='necessary-check' name='Necessary[$i]' checked disabled ><br/><br/>";					

					
					echo "</div>"; 
	
	
		}
		
		
		if(@$value->Field_Type == 'user-text-field'){
		
		// Feldtyp
		// Feldreihenfolge 
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis 
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$Min_Char = $value->Min_Char;
		$Max_Char = $value->Max_Char;
		$Field_Name = html_entity_decode(stripslashes($value->Field_Name));
		$Field_Content = html_entity_decode(stripslashes($value->Field_Content));
		$Field_Order = $value->Field_Order;
		$Field_Type = $value->Field_Type;
		$cg_Necessary = $value->Required;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
		// Anfang des Formularteils
		echo "<div id='cg$nfCount'  class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>
		<br/><input type='hidden' class='Field_Type' name='Field_Type[$i]' value='$Field_Type'>";
		
		echo "<input type='hidden' class='cg_Field_Text_Type' >"; // Zum Zählen von Text Feldern
		echo "<input type='hidden' class='Field_Order' value='$Field_Order' >";
		
				
		echo "<input type='hidden' class='Field_Id' name='Field_Id[$i]' value='$id' >";
		
		// Feld löschen Möglichkeit
		@$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$nfCount' titel='$id'><input type='hidden' name='originalRow[$key]' value='$fieldOrder'>";
				
		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' class='cg_actualID' name='actualID[]' value='$id' >";		
					
					echo "<input type='text' class='Field_Name' name='Field_Name[$i]' value='$Field_Name'  size='30' maxlength='100'>$deleteField<br/>"; // Titel und Delete Möglichkeit die oben bestimmt wurde

					echo "<input type='text' class='Field_Content' name='Field_Content[$i]' value='$Field_Content' placeholder='Placeholder' id='main-user-name' maxlength='1000' style='width:855px;'><br/>";
										
					echo "Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='Min_Char[$i]' value='$Min_Char' size='7' maxlength='4' ><br/>";
										
					echo "Max. number of characters: <input type='text' class='Max_Char' name='Max_Char[$i]' value='$Max_Char' size='7' maxlength='4' ><br/>";
					
					if($cg_Necessary==1){$cg_Necessary_checked="checked";}
					else{$cg_Necessary_checked="";}
					
					echo "Required <input type='checkbox' class='necessary-check' name='Necessary[$i]' $cg_Necessary_checked ><br/><br/>";					

					
					echo "</div>"; 

					$nfCount++;
					$nfHiddenCount++;			
	
		}
		
		
		
		if(@$value->Field_Type == 'user-comment-field'){
		
		// Feldtyp
		// Feldreihenfolge 
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis 
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$Min_Char = $value->Min_Char;
		$Max_Char = $value->Max_Char;
		$Field_Name = html_entity_decode(stripslashes($value->Field_Name));
		$Field_Content = html_entity_decode(stripslashes($value->Field_Content));
		$Field_Order = $value->Field_Order;
		$Field_Type = $value->Field_Type;
		$cg_Necessary = $value->Required;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
		// Anfang des Formularteils
		echo "<div id='cg$kfCount'  class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>
		<br/><input type='hidden' class='Field_Type' name='Field_Type[$i]' value='$Field_Type'>";
		
		echo "<input type='hidden' class='cg_Field_Comment_Type' >"; // Zum Zählen von Text Feldern
		echo "<input type='hidden' class='Field_Order' value='$Field_Order' >";		
		
		echo "<input type='hidden' class='Field_Id' name='Field_Id[$i]' value='$id' >";
		
		// Feld löschen Möglichkeit
		@$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$kfCount' titel='$id'><input type='hidden' name='originalRow[$key]' value='$fieldOrder'>";
				
		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' class='cg_actualID' name='actualID[]' value='$id' >";		
					
					echo "<input type='text' class='Field_Name' name='Field_Name[$i]' value='$Field_Name'  size='30' maxlength='100'>$deleteField<br/>"; // Titel und Delete Möglichkeit die oben bestimmt wurde

					echo "<textarea class='Field_Content' name='Field_Content[$i]' placeholder='Placeholder' maxlength='10000' style='width:856px;' rows='10'>$Field_Content</textarea><br/>";
										
					echo "Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='Min_Char[$i]' value='$Min_Char' size='7' maxlength='4' ><br/>";
										
					echo "Max. number of characters: <input type='text' class='Max_Char' name='Max_Char[$i]' value='$Max_Char' size='7' maxlength='4' ><br/>";
					
					if($cg_Necessary==1){$cg_Necessary_checked="checked";}
					else{$cg_Necessary_checked="";}
					
					echo "Required <input type='checkbox' class='necessary-check' name='Necessary[$i]' $cg_Necessary_checked ><br/><br/>";					

					
					echo "</div>"; 

					$kfCount++;
					$kfHiddenCount++;			
	
		}
		
		
		if(@$value->Field_Type == 'user-check-agreement-field'){
		
		// Feldtyp
		// Feldreihenfolge 
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis 
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$Min_Char = $value->Min_Char;
		$Max_Char = $value->Max_Char;
		$Field_Name = html_entity_decode(stripslashes($value->Field_Name));
		$Field_Content = html_entity_decode(stripslashes($value->Field_Content));
		$Field_Order = $value->Field_Order;
		$Field_Type = $value->Field_Type;
		$cg_Necessary = $value->Required;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
		// Anfang des Formularteils
		echo "<div id='cg$cbCount'  class='formField' style='width:855px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>
		<br/><input type='hidden' class='Field_Type' name='Field_Type[$i]' value='$Field_Type'>";
		
		echo "<input type='hidden' class='cg_Field_Check_Agreement_Type' >"; // Zum Zählen von Text Feldern
		echo "<input type='hidden' class='Field_Order' value='$Field_Order' >";
		
		echo "<input type='hidden' class='Field_Id' name='Field_Id[$i]' value='$id' >";
		
		// Feld löschen Möglichkeit
		@$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$cbCount' titel='$id'><input type='hidden' name='originalRow[$key]' value='$fieldOrder'>";
				
		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' class='cg_actualID' name='actualID[]' value='$id' >";		
					
					echo "<input type='text' class='Field_Name' name='Field_Name[$i]' value='$Field_Name'  size='30' maxlength='100'>$deleteField<br/>"; // Titel und Delete Möglichkeit die oben bestimmt wurde
				
					echo "<input type='checkbox' disabled><input type='text' class='Field_Content' name='Field_Content[$i]' placeholder='HTML tags allowed' value='$Field_Content' id='user-check-agreement-field' maxlength='1000' style='width:832px;'><br/>";

					echo "Required <input type='checkbox' class='necessary-check' checked disabled><br/><br/>";					

					
					echo "</div>"; 

					$cbCount++;
					$cbHiddenCount++;			
	
		}
		
		if(@$value->Field_Type == 'user-html-field'){
		
		// Feldtyp
		// Feldreihenfolge 
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis 
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$Min_Char = $value->Min_Char;
		$Max_Char = $value->Max_Char;
		$Field_Name = html_entity_decode(stripslashes($value->Field_Name));
		$Field_Content = html_entity_decode(stripslashes($value->Field_Content));
		$Field_Order = $value->Field_Order;
		$Field_Type = $value->Field_Type;
		$cg_Necessary = $value->Required;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
		// Anfang des Formularteils
		echo "<div id='cg$htfCount'  class='formField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>
		<br/><input type='hidden' class='Field_Type' name='Field_Type[$i]' value='$Field_Type'>";
		
		echo "<input type='hidden' class='cg_Field_Comment_Type' >"; // Zum Zählen von Text Feldern
		echo "<input type='hidden' class='Field_Order' value='$Field_Order' >";		
		
		echo "<input type='hidden' class='Field_Id' name='Field_Id[$i]' value='$id' >";
		
		// Feld löschen Möglichkeit
		@$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$htfCount' titel='$id'> &nbsp; (HTML Field - Title is invisible)<input type='hidden' name='originalRow[$key]' value='$fieldOrder'>";
				
		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' class='cg_actualID' name='actualID[]' value='$id' >";		
		
		echo "<input type='text' class='Field_Name' name='Field_Name[$i]' value='$Field_Name'  size='30' maxlength='100'>$deleteField<br/>"; // Titel und Delete Möglichkeit die oben bestimmt wurde

		echo "<textarea class='Field_Content' name='Field_Content[$i]' placeholder='Placeholder' maxlength='10000' style='width:856px;' rows='10'>$Field_Content</textarea><br/>";


		echo "</div>"; 

		$htfCount++;
		$htHiddenCount++;
	
		}
		
		
		// Zum zählen von Feld Reihenfolge
		$i++;
		
	}
}


?>
</div>

</div>

<div style="display:block;padding:20px;padding-bottom:10px;background-color:white;width:895px;text-align:right;margin-top:15px;border: 1px solid #DFDFDF;height:40px;">
<input id="submitForm" type="submit" name="submit" value="Save" style="text-align:center;width:180px;float:right;margin-right:10px;margin-bottom:10px;">
</div>
<br/>



<?php


// ---------------- AUSGABE des gespeicherten Formulares  --------------------------- ENDE

echo "<br/>";
?>
</form>