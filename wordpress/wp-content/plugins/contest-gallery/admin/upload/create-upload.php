<?php
require_once('get-data-create-upload.php');
// Path to jquery Lightbox Script

// $pathJquery = plugins_url().'/contest-gallery/js/jquery.js';
//$pathPlugin1 = plugins_url().'/contest-gallery/js/lightbox-2.6.min.js';
//$pathPlugin2 = plugins_url().'/contest-gallery/css/lightbox.css';
//$pathPlugin3 = plugins_url().'/contest-gallery/css/star_off_48.png';
//$pathPlugin4 = plugins_url().'/contest-gallery/css/star_48.png';
//$pathCss = plugins_url().'/contest-gallery/css/style.css';
// $pathJqueryUI = plugins_url().'/contest-gallery/js/jquery-ui.js';
// $pathJqueryUIcss = plugins_url().'/contest-gallery/js/jquery-ui.css';
//$cssPng = content_url().'/plugins/contest-gallery/css/lupe.png';// URL for zoom pic


//add_action('wp_enqueue_scripts','my_scripts');

 
/*

echo <<<HEREDOC

<link href="$pathPlugin2" rel="stylesheet" />
<link href="$pathCss" rel="stylesheet" />
<link href="$pathPlugin6" rel="stylesheet" />


HEREDOC;

//echo $pathCss;


echo <<<HEREDOC

	<script src='$pathJquery'></script>
	<script src='$pathJqueryUI'></script>
	<script src='$pathJqueryUIcss'></script>

HEREDOC;*/



require_once(dirname(__FILE__) . "/../nav-menu.php");

function cg_cg_set_default_editor() {
    $r = 'html';
    return $r;
}
add_filter( 'wp_default_editor', 'cg_cg_set_default_editor' );

$tinymceStyle = '<style type="text/css">
				   .switch-tmce {display:none;}
				   .wp-editor-area{height:200px;}
				   </style>';

/*$timymceSettings = array(
    'plugins' => "preview",
    'menubar' => "view",
    'toolbar' => "preview",
    'plugin_preview_width'=> 650,
    'selector' => "textarea"
);*/

$settingsHTMLarea = array(
    "media_buttons"=>false,
    'editor_class' => 'html-active',
    'default_post_edit_rows'=> 10,
    "textarea_name"=>'upload[]',
    "teeny" => true,
    "dfw" => true,
    'editor_css' => $tinymceStyle
);
echo "<div id='cgTinymceCollection'>";
for($i=0;$i<=10;$i++){

    // TinyMCE Editor to take as copy for template
    echo "<div id='htmlEditorTemplateDiv$i' class='htmlEditorTemplateDiv' style='display:none;'>";

    $editor_id = "htmlFieldTemplate$i";

    $testVal = "";

    wp_editor($testVal, $editor_id, $settingsHTMLarea);

    echo "</div>";

}
echo "</div>";






echo '<div style="width:881px;padding-left:27px;padding-right:27px;background-color:white;border: 1px solid #DFDFDF;padding-top:20px;padding-bottom:20px;margin-bottom:15px;">';

//echo "<form name='defineUpload' enctype='multipart/form-data' action='?page=contest-gallery/index.php&optionID=$GalleryID&defineUpload=true' id='form' method='post'>";

$heredoc = <<<HEREDOC
	<select name="dauswahl" id="dauswahl">
		<optgroup label="User fields">
			<option  value="nf">Input</option>
			<option value="kf">Textarea</option>
			<option value="se">Select</option>
			<option value="ef">E-Mail</option>
			<option value="cb">Check agreement</option>
		 </optgroup>
		<optgroup label="Admin fields">
			<option  value="ht">HTML</option>
			<option  value="caRo">Captcha (I am not a robot)</option>
		 </optgroup>
	</select>
	<input id="cg_create_upload_add_field" type="button" name="plus" value="+" >
	</div>
HEREDOC;

echo $heredoc;

echo "<form name='defineUpload' enctype='multipart/form-data' action='?page=contest-gallery/index.php&option_id=$GalleryID&define_upload=true' id='form' method='post'>";
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
@$seCount = 70;
@$caRoCount = 80;

// Further IDs of the div boxes   
@$nfHiddenCount = 100;
@$kfHiddenCount = 200;
@$efHiddenCount = 300;
@$bhHiddenCount = 400;
@$htHiddenCount = 500;
@$cbHiddenCount = 600;
@$seHiddenCount = 700;
@$caRoHiddenCount = 800;

// FELDBENENNUNGEN

// 1 = Feldtyp
// 2 = Feldnummer
// 3 = Feldtitel
// 4 = Feldinhalt
// 5 = Feldkrieterium1
// 6 = Feldkrieterium2
// 7 = Felderfordernis 


//print_r($selectFormInput);




if ($selectFormInput) {

	foreach ($selectFormInput as $value) {
	

		if($value->Field_Type == 'image-f'){
		
		// Feldtyp
		// 1 = Feldtitel
		
		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";
		
		// Anfang des Formularteils
		echo "<div id='$bhCount' class='formField imageUploadField'  style='width:858px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' name='upload[]' value='bh'>";
		// Prim�re ID in der Datenbank
		//echo "<input type='hidden' name='upload[]' value='$fieldOrder' class='changeUploadFieldOrder'>";
		//SWAP Values
		//echo "<input type='hidden' name='changeFieldRow[$fieldOrder]' value='$fieldOrder' class='changeFieldOrderUsersEntries'>";

		// Formularfelder unserializen
		$fieldContent = unserialize($value->Field_Content);

		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";

			foreach($fieldContent as $key => $value){

					$value = html_entity_decode(stripslashes($value));

					// 1 = Feldtitel
					if($key=='titel'){
                    echo "<strong>Image upload </strong><br/>";
					echo "<input type='text' name='upload[]' value='$value' size='30'><br/>"; // Titel und Delete M�glichkeit die oben bestimmt wurde

					echo "<input type='file' id='bh' disabled /><input type='submit' value='Upload' disabled /><br/>";
					echo "Required <input type='checkbox' checked disabled /><br/><br/>"; // Bildupload ist so oder so immer notwendig



					echo "</div>";

					}

			}

		}


		if(@$value->Field_Type == 'check-f'){

		// Feldtyp
		// Feldreihenfolge
		// 1 = Feldinhalt
		// 2 = Felderfordernis

		//ermitteln der Feldnummer
		@$fieldOrder = $value->Field_Order;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";

		// Anfang des Formularteils
		echo "<div id='$cbCount'  class='formField checkAgreementField' style='width:858px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' name='upload[]' value='cb'>";
		// Prim�re ID in der Datenbank
		//echo "<input type='hidden' name='upload[]' value='$fieldOrder' class='changeUploadFieldOrder'>";
		//SWAP Values
		//echo "<input type='hidden' name='changeFieldRow[$fieldOrder]' value='$fieldOrder' class='changeFieldOrderUsersEntries'>";

		echo "<input type='hidden' value='$fieldOrder' class='fieldnumber'>";

		// Feld l�schen M�glichkeit
		$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$cbCount' titel='$id'>";



		// Formularfelder unserializen
		$fieldContent = unserialize($value->Field_Content);

		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";

			foreach($fieldContent as $key => $value){

					$value = html_entity_decode(stripslashes($value));

					// 2. Feldtitel
					if($key=='titel'){
                    echo "<strong>Check agreement</strong><br/>";
					echo "<input type='text' name='upload[]' value='$value' maxlength='100' size='30'>$deleteField<br/>"; // Titel und Delete M�glichkeit die oben bestimmt wurde
					}

					// 2. Feldinhalt
					if($key=='content'){
					echo "<input type='checkbox' disabled><input type='text' name='upload[]' class='cb'  maxlength='1000' style='width:832px;' placeholder='HTML tags allowed' value='$value'><br/>";
					}

					// 3. Felderfordernis
					if($key=='mandatory'){

					//$checked = ($value=='on') ? "checked" : "";

					echo "Required <input type='checkbox' class='necessary-check' name='upload[]' checked disabled /><br/><br/>";
					if ($value!='on') {echo "<input type='hidden' class='necessary-hidden'  name='upload[]' value='off' />";}
					echo "</div>";

					$cbCount++;
					@$cbHiddenCount++;


					}

			}

		}





		if(@$value->Field_Type == 'text-f'){

		// Feldtyp
		// Feldreihenfolge
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis

		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";

		// Anfang des Formularteils
		echo "<div id='$nfCount'  class='formField inputField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' name='upload[]' value='nf'>";
		// Prim�re ID in der Datenbank
		//echo "<input type='hidden' name='upload[]' value='$fieldOrder' class='changeUploadFieldOrder'>";
		//SWAP Values
		//echo "<input type='hidden' name='changeFieldRow[$fieldOrder]' value='$fieldOrder' class='changeFieldOrderUsersEntries'>";

		echo "<input type='hidden' value='$fieldOrder' class='fieldnumber'>";

		// Feld l�schen M�glichkeit
		@$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$nfCount' titel='$id'>";

		if($id==@$Field1IdGalleryView){$checked='checked';}
		else{$checked='';}

		//echo "<br>id: $id<br>";
		//echo "<br>Use_as_URL_id: $Use_as_URL_id<br>";
		if(@$Use_as_URL==1 and $id==@$Use_as_URL_id){$checkedURL='checked';}
		else{$checkedURL='';}

		@$Show_Slider = $wpdb->get_var("SELECT Show_Slider FROM $tablename_form_input WHERE id = '$id'");

		if(@$Show_Slider==1){$checkedShow_Slider='checked';}
		else{$checkedShow_Slider='';}

		echo "<div style='width:160px;float:right;text-align:right;'>";

		echo "Show info in gallery:  &nbsp;";
		//echo "<input type='text' value='Show info in gallery:' style='float:right;border: none;width:145px;'>";
		echo "<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView[$id]' $checked>";
		echo "</div>";

		echo "<div style='width:160px;float:right;text-align:right;margin-right:12px;'>";

		echo "Show info in slider: &nbsp;";
		//echo "<input type='text' value='Show info in gallery:' style='float:right;border: none;width:145px;'>";
		echo "<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider[$id]' $checkedShow_Slider>";
		echo "</div>";

		// Das Feld soll als URL agieren
		echo "<div style='width:210px;float:right;text-align:right;margin-right:10px;'>Use this field as images url: &nbsp;";
		echo "<input type='checkbox' class='Use_as_URL' style='margin-top:0px;' name='Use_as_URL[$id]' $checkedURL>";
		echo "</div>";
		// Das Feld soll als URL agieren --- ENDE


		// Formularfelder unserializen
		$fieldContent = unserialize($value->Field_Content);

		// Aktuelle Feld ID mitschicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";

			foreach($fieldContent as $key => $value){

					$value = html_entity_decode(stripslashes($value));

					if($key=='titel'){
					//ID wird dazu mitgegeben als compareID f�r sp�ter
                    echo "<strong>Input </strong><br/>";
					echo "<input type='text' name='upload[][$id]' value='$value'  size='30' maxlength='100'>$deleteField<br/>"; // Titel und Delete M�glichkeit die oben bestimmt wurde
					}

					if($key=='content'){

					echo "<input type='text' name='upload[]' value='$value' id='nf' maxlength='1000' placeholder='Placeholder'  style='width:855px;'><br/>";
					}

					if($key=='min-char'){
					echo "Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='upload[]' value='$value' size='7' maxlength='3' ><br/>";
					}

					if($key=='max-char'){
					echo "Max. number of characters: <input type='text' name='upload[]' class='Max_Char' value='$value' size='7' maxlength='3' ><br/>";
					}

					if($key=='mandatory'){

					$checked = ($value=='on') ? "checked" : "";

					echo "Required <input type='checkbox' class='necessary-check' name='upload[]' $checked ><br/><br/>";
					if ($value!='on') {echo "<input type='hidden' class='necessary-hidden'  name='upload[]' value='off' >";}
					echo "</div>";

					$nfCount++;
					$nfHiddenCount++;


					}

			}

		}


		if(@$value->Field_Type == 'email-f'){

		// Feldtyp
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Felderfordernis

		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";

		// Anfang des Formularteils
		echo "<div id='$efCount'  class='formField emailField' style='width:840px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><input type='hidden' name='upload[]' value='ef'>";
		// Prim�re ID in der Datenbank
		//echo "<input type='hidden' name='upload[]' value='$fieldOrder' class='changeUploadFieldOrder'>";
		//SWAP Values
		//echo "<input type='hidden' name='changeFieldRow[$fieldOrder]' value='$fieldOrder' class='changeFieldOrderUsersEntries'>";


		echo "<div style='margin-bottom:5px;'>";
        echo "<strong>E-Mail </strong><br/>";
        echo "Do not appear if user is registered and logged in</div>";

		echo "<input type='hidden' value='$fieldOrder' class='fieldnumber'>";

		// Feld l�schen M�glichkeit
		$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$efCount' titel='$id'>";

		if($id==@$Field1IdGalleryView){$checked='checked';}
		else{$checked='';}

		@$Show_Slider = $wpdb->get_var("SELECT Show_Slider FROM $tablename_form_input WHERE id = '$id'");

		if(@$Show_Slider==1){$checkedShow_Slider='checked';}
		else{$checkedShow_Slider='';}

		echo "<div style='width:160px;float:right;text-align:right;'>";

		echo "Show info in gallery: &nbsp;";
		//echo "<input type='text' value='Show info in gallery:' style='float:right;border: none;width:145px;'>";
		echo "<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView[$id]' $checked>";
		echo "</div>";

		echo "<div style='width:160px;float:right;text-align:right;margin-right:12px;'>";

		echo "Show info in slider: &nbsp;";
		//echo "<input type='text' value='Show info in gallery:' style='float:right;border: none;width:145px;'>";
		echo "<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider[$id]' $checkedShow_Slider>";
		echo "</div>";




		// Formularfelder unserializen
		$fieldContent = unserialize($value->Field_Content);

		// Aktuelle Feld ID mit schicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";

			foreach($fieldContent as $key => $value){

					$value = html_entity_decode(stripslashes($value));

					// 1 = Feldtitel
					if($key=='titel'){
					echo "<input  type='hidden'/>";
					//ID wird dazu mitgegeben als compareID f�r sp�ter
					echo "<input type='text' name='upload[][$id]' value='$value' size='30' maxlength='100'>$deleteField<br/>"; // Titel und Delete M�glichkeit die oben bestimmt wurde
					}

					// 2 = Feldinhalt
					if($key=='content'){

					echo "<input type='text' name='upload[]' value='$value' id='ef' style='width:858px;' placeholder='Placeholder'  maxlength='100'><br/>";
					}

					// 3. Felderfordernis
					if($key=='mandatory'){

					$checked = ($value=='on') ? "checked" : "";

					echo "Required <input type='checkbox' class='necessary-check' name='upload[]' $checked ><br/><br/>";
					if ($value!='on') {echo "<input type='hidden' class='necessary-hidden'  name='upload[]' value='off' >";}
					echo "</div>";

					$efCount++;
					$efHiddenCount++;


					}

			}

		}


		if(@$value->Field_Type == 'comment-f'){

		// Feldtyp
		// Feldreihenfolge
		// 1 = Feldtitel
		// 2 = Feldinhalt
		// 3 = Feldkrieterium1
		// 4 = Feldkrieterium2
		// 5 = Felderfordernis

		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";

		// Anfang des Formularteils
		echo "<div id='$kfCount'  class='formField textareaField' style='width:718px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'><br/><input type='hidden' name='upload[]' value='kf'>";
		// Prim�re ID in der Datenbank
		//echo "<input type='hidden' name='upload[]' value='$fieldOrder' class='changeUploadFieldOrder'>";
		//SWAP Values
		//echo "<input type='hidden' name='changeFieldRow[$fieldOrder]' value='$fieldOrder' class='changeFieldOrderUsersEntries'>";// Neuer Wert in der Datebank

		echo "<input type='hidden' value='$fieldOrder' class='fieldnumber'>";

		// Feld l�schen M�glichkeit
		$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$kfCount' titel='$id'>";



		if($id==@$Field1IdGalleryView){$checked='checked';}
		else{$checked='';}


		@$Show_Slider = $wpdb->get_var("SELECT Show_Slider FROM $tablename_form_input WHERE id = '$id'");

		if(@$Show_Slider==1){$checkedShow_Slider='checked';}
		else{$checkedShow_Slider='';}

		//echo "$id";

		echo "<div style='width:160px;float:right;text-align:right;'>";

		echo "Show info in gallery: &nbsp;";
		//echo "<input type='text' value='Show info in gallery:' style='float:right;border: none;width:145px;'>";
		echo "<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView[$id]' $checked>";
		echo "</div>";

				echo "<div style='width:160px;float:right;text-align:right;margin-right:12px;'>";

		echo "Show info in slider: &nbsp;";
		//echo "<input type='text' value='Show info in gallery:' style='float:right;border: none;width:145px;'>";
		echo "<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider[$id]' $checkedShow_Slider>";
		echo "</div>";

		// Formularfelder unserializen
		$fieldContent = unserialize($value->Field_Content);

		//echo "<br>";
		//print_r($fieldContent);
		//echo "<br>";

		// Aktuelle Feld ID mit schicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";

			foreach($fieldContent as $key => $value){

					$value = html_entity_decode(stripslashes($value));


					if($key=='titel'){
                    echo "<strong>Textarea </strong><br/>";
					//ID wird dazu mitgegeben als compareID f�r sp�ter
					echo "<input type='text' name='upload[][$id]' value='$value' size='30' maxlength='1000'/>$deleteField<br/>";// Titel und Delete M�glichkeit die oben bestimmt wurde
					}

					if($key=='content'){
					echo "<textarea name='upload[]' id='kf' maxlength='10000' style='width:858px;' placeholder='Placeholder'  rows='10'>$value</textarea><br/>";
					}

					if($key=='min-char'){
					echo "Min. number of characters:&nbsp; <input type='text' class='Min_Char' name='upload[]' value='$value' size='7' maxlength='3' ><br/>";
					}

					if($key=='max-char'){
					echo "Max. number of characters: <input type='text' class='Max_Char' name='upload[]' value='$value' size='7' maxlength='4' ><br/>";
					}

					if($key=='mandatory'){

					$checked = ($value=='on') ? "checked" : "";

					echo "Required <input type='checkbox' class='necessary-check' name='upload[]' $checked ><br/><br/>";
					if ($value!='on') {echo "<input type='hidden' class='necessary-hidden'  name='upload[]' value='off' >";}
					echo "</div>";

					$kfCount++;
					$kfHiddenCount++;

					}

			}

		}

		// Admin fields here

		if(@$value->Field_Type == 'html-f'){

		// Feldtyp
		// Feldreihenfolge
		// 1 = Feldtyp
		// 2 = Feldtitel
		// 3 = Feldinhalt


		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";

		// Anfang des Formularteils
		echo "<div id='$htCount'  class='formField cg_ht_field htmlField' style='width:858px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>";
		echo "<br/><input type='hidden' name='upload[]' value='ht'>";
		// Prim�re ID in der Datenbank
		//echo "<input type='hidden' name='upload[]' value='$fieldOrder' class='changeUploadFieldOrder'>";
		//SWAP Values
		//echo "<input type='hidden' name='changeFieldRow[$fieldOrder]' value='$fieldOrder' class='changeFieldOrderUsersEntries'>";// Neuer Wert in der Datebank

		echo "<input type='hidden' value='$fieldOrder' class='fieldnumber'>";

		// Feld l�schen M�glichkeit
		$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$htCount' titel='$id'> &nbsp; (HTML Field - Title is invisible)";


		// Formularfelder unserializen
		$fieldContent = unserialize($value->Field_Content);

		// Aktuelle Feld ID mit schicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";

			foreach($fieldContent as $key => $value){
						$value = html_entity_decode(stripslashes($value));
			if($key=='titel'){
                        echo "<strong>HTML </strong><br/>";
						echo "<input type='text' name='upload[]' value='$value' size='30' maxlength='1000'/>$deleteField<br/>";// Titel und Delete M�glichkeit die oben bestimmt wurde
                        echo "<hr>";
					}

					if($key=='content'){

                        // TinyMCE Editor
                        echo "<div>";

                        $editor_id = "htmlField$htCount";

                        wp_editor( $value, $editor_id, $settingsHTMLarea);
                        echo "</div>";
					}

			}

		echo "</div>";

            $htCount++;
            $htHiddenCount++;

		}


		if(@$value->Field_Type == 'caRo-f'){

		// Feldtyp
		// Feldreihenfolge
		// 1 = Feldtyp
		// 2 = Feldtitel
		// 3 = Feldinhalt


		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";

		// Anfang des Formularteils
		echo "<div id='$caRoCount'  class='formField captchaRoField' style='width:858px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>";
		echo "<br/><input type='hidden' name='upload[]' value='caRo'>";

		echo "<input type='hidden' value='$fieldOrder' class='fieldnumber'>";

		// Feld l�schen M�glichkeit
		$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$caRoCount' titel='$id'>";

		// Formularfelder unserializen
		$fieldContent = unserialize($value->Field_Content);

		// Aktuelle Feld ID mit schicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";

			foreach($fieldContent as $key => $value){
				$value = html_entity_decode(stripslashes($value));
                if($key=='titel'){
                            echo "<strong>Captcha (I am not a robot)</strong><br/>";
                            echo "<input type='checkbox' disabled> <input type='text' name='upload[]' value='$value' size='30' maxlength='1000'/>$deleteField";// Titel und Delete M�glichkeit die oben bestimmt wurde
                }

					echo "<br/>Required <input type='checkbox' class='necessary-check' disabled checked ><br><br>";

    		}

		echo "</div>";


            $caRoCount++;
            $caRoHiddenCount++;

		}

		// Admin fields here

		if(@$value->Field_Type == 'select-f'){

		// Feldtyp
		// Feldreihenfolge
		// 1 = Feldtyp
		// 2 = Feldtitel
		// 3 = Feldinhalt


		//ermitteln der Feldnummer
		$fieldOrder = $value->Field_Order;
		$fieldOrderKey = "$fieldOrder";
		$id = $value->id; // Unique ID des Form Feldes
		$idKey = "$id";

		// Anfang des Formularteils
		echo "<div id='$seCount'  class='formField cg_se_field selectField' style='width:718px;margin-bottom:20px;border: 1px solid #DFDFDF;padding-top:7px;padding-bottom:10px;display:table;padding:10px;'>";
		echo "<br/><input type='hidden' name='upload[]' value='se'>";
		// Prim�re ID in der Datenbank
		//echo "<input type='hidden' name='upload[]' value='$fieldOrder' class='changeUploadFieldOrder'>";
		//SWAP Values
		//echo "<input type='hidden' name='changeFieldRow[$fieldOrder]' value='$fieldOrder' class='changeFieldOrderUsersEntries'>";// Neuer Wert in der Datebank

		echo "<input type='hidden' value='$fieldOrder' class='fieldnumber'>";

		// Feld l�schen M�glichkeit
		$deleteField = "<input class='cg_delete_form_field' type='button' value='-' alt='$seCount' titel='$id'>";

        if($id==@$Field1IdGalleryView){$checked='checked';}
        else{$checked='';}


        @$Show_Slider = $wpdb->get_var("SELECT Show_Slider FROM $tablename_form_input WHERE id = '$id'");

        if(@$Show_Slider==1){$checkedShow_Slider='checked';}
        else{$checkedShow_Slider='';}

        //echo "$id";

        echo "<div style='width:160px;float:right;text-align:right;'>";

        echo "Show info in gallery: &nbsp;";
        //echo "<input type='text' value='Show info in gallery:' style='float:right;border: none;width:145px;'>";
        echo "<input type='checkbox' class='cg_info_show_gallery' style='margin-top:0px;' name='Field1IdGalleryView[$id]' $checked>";
        echo "</div>";

        echo "<div style='width:160px;float:right;text-align:right;margin-right:12px;'>";

        echo "Show info in slider: &nbsp;";
        //echo "<input type='text' value='Show info in gallery:' style='float:right;border: none;width:145px;'>";
        echo "<input type='checkbox' class='cg_info_show_slider' style='margin-top:0px;' name='cg_f_input_id_show_slider[$id]' $checkedShow_Slider>";
        echo "</div>";

		// Formularfelder unserializen
		$fieldContent = unserialize($value->Field_Content);

		// Aktuelle Feld ID mit schicken
		echo "<input type='hidden' name='actualID[]' value='$id' >";

			foreach($fieldContent as $key => $value){

                $value = html_entity_decode(stripslashes($value));
                if($key=='titel'){
                        echo "<strong>Select </strong><br/>";
                        echo "<input type='text' name='upload[][$id]' value='$value' size='30' maxlength='1000'/>$deleteField<br/>";// Titel und Delete M�glichkeit die oben bestimmt wurde
                }

                if($key=='content'){

                    //$value = nl2br(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
                    echo "<textarea name='upload[]' maxlength='10000' style='width:856px;' placeholder='Each row one value - Example: &#10;value1&#10;value2&#10;value3&#10;value4&#10;value5&#10;value6'  rows='10'>$value</textarea><br/>";

                }

                 if($key=='mandatory'){

                        $checked = ($value=='on') ? "checked" : "";

                        echo "Required <input type='checkbox' class='necessary-check' name='upload[]' $checked ><br/><br/>";
                        if ($value!='on') {echo "<input type='hidden' class='necessary-hidden'  name='upload[]' value='off' >";}


                        $seCount++;
                        $seHiddenCount++;

                  }

			}

		echo "</div>";

		}
		
		
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