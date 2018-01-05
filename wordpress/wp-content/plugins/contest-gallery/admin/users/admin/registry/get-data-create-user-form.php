<?php

// 1. Delete Felder in Entries, F_Input, F_Output
// 2. Swap Field_Order in Entries, F_Input, F_Output (bei post "done-upload" wird alles mitgegeben
// 3. Neue Felder hinzufügen in F_Input, Entries
// 4. // Auswahl zum Anzeigen gespeicherter Felder

// Empfangen von Galerie OptiOns ID

$GalleryID = intval(@$_GET['option_id']);

global $wpdb;

// Tabellennamen bestimmen

$tablename = $wpdb->prefix . "contest_gal1ery";
$tablenameoptions = $wpdb->prefix . "contest_gal1ery_options";
$tablenameentries = $wpdb->prefix . "contest_gal1ery_entries";
$tablename_create_user_form = $wpdb->prefix . "contest_gal1ery_create_user_form";
$tablename_create_user_entries = $wpdb->prefix . "tablename_create_user_entries";
$tablename_form_output = $wpdb->prefix . "contest_gal1ery_f_output";
$tablename_options_visual = $wpdb->prefix . "contest_gal1ery_options_visual";


$GalleryName = $wpdb->get_var("SELECT GalleryName FROM $tablenameoptions WHERE id = '$GalleryID'");



// Check if certain fieldnumber should be deleted

//
// Vorgehen: Zuerst Feld löschen falls einz mitgeschickt wurde zum löschen. Dann prüfen welche IDs mitgeschickt wurden (beim erstellten Formular) und diese in f_output und f_entries eingügen. Die alten
// die drin wahren durch die neuen ersetzten
// Dann pauschal existierendes f_input löschen und die neuen mitgeschicktern werte komplett neu einfügen "INSERT"

// Löschen Ddaten in Tablename entries
// Löschen Ddaten in Tablename f_input
// Löschen Ddaten in Tablename f_output

//var_dump($_POST['deleteFieldnumber']);

if(@$_POST['deleteFieldnumber']){
	


$deleteFieldnumber = intval(@$_POST['deleteFieldnumber']);

		
		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_create_user_form WHERE GalleryID = %d AND id = %d
			", 
				$GalleryID, $deleteFieldnumber
		 ));


		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_create_user_entries WHERE GalleryID = %d AND f_input_id = %d
			", 
				$GalleryID, $deleteFieldnumber
		 ));	

}

// Check if certain fieldnumber should be deleted --- ENDE

// Abspeichern von gesendeten Daten

if (@$_POST['submit']) {


/*
// Alte Werte erstmal löschen
	
    	$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_create_user_form WHERE GalleryID = %d
			", 
				$GalleryID
		 ));
		
// Alte Werte erstmal löschen --- ENDE
*/


// Neue Formularfelder werden eingefügt


$get_Field_Id = @$_POST['Field_Id'];
$get_Field_Type = @$_POST['Field_Type'];
$get_Field_Name = @$_POST['Field_Name'];
$get_Field_Content = @$_POST['Field_Content'];
$get_Min_Char = @$_POST['Min_Char'];
$get_Max_Char = @$_POST['Max_Char'];
$get_Necessary = @$_POST['Necessary'];

//var_dump($get_Field_Type);

//echo "Fieldtype: <br>";
//print_r($get_Field_Name);
//echo "<br>";
//print_r($get_Field_Content);

// Dient zur Orientierung zum Abarbeiten
$i=1;

foreach($get_Field_Type as $key => $value){
	
		// Das gelöschte Feld soll nicht nochmal kreiert werden. Unbedingt auf true prüfen! Ansonsten bei zwei leeren Werten ist die Bedingung auch erfüllt.
		if(@$_POST['deleteFieldnumber']==true && @$_POST['deleteFieldnumber']==@$get_Field_Id[$i]){continue;}
		
		if($value=="user-check-agreement-field"){
			
					$get_Field_Name[$i]=sanitize_text_field(htmlentities($get_Field_Name[$i], ENT_QUOTES, 'UTF-8'));
					$get_Field_Content[$i]=sanitize_textarea_field(htmlentities($get_Field_Content[$i], ENT_QUOTES, 'UTF-8'));
				
					if($get_Field_Id[$i]){
						$wpdb->update(
						"$tablename_create_user_form",
						array('Field_Order' => $i, 'Field_Name' => $get_Field_Name[$i],
						'Field_Content' => @$get_Field_Content[$i],'Min_Char' => @$get_Min_Char[$i],'Max_Char' => @$get_Max_Char[$i]),
						array('id' => $get_Field_Id[$i]),
						array('%d','%s',
						'%s','%s','%s'),
						array('%d')
						);					
					}
					else{						
						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_create_user_form
							( id, GalleryID, Field_Type, Field_Order,
							Field_Name,Field_Content,Min_Char,Max_Char,
							Required)
							VALUES ( %s,%d,%s,%d,
							%s,%s,%d,%d,
							%d)
						",
							'',$GalleryID,'user-check-agreement-field',$i,
							$get_Field_Name[$i],$get_Field_Content[$i],'','',
							1
						) );						
					}			

		}
	
			if($value=="user-comment-field"){
				
					$get_Field_Name[$i]=sanitize_text_field(htmlentities($get_Field_Name[$i], ENT_QUOTES, 'UTF-8'));
					$get_Field_Content[$i]=sanitize_textarea_field(htmlentities($get_Field_Content[$i], ENT_QUOTES, 'UTF-8'));
				
				if(@$get_Necessary[$i]=='on'){@$update_Necessary=1;}
				else{@$update_Necessary=0;}
			
					if(@$get_Field_Id[$i]){						
						$wpdb->update(
						"$tablename_create_user_form",
						array('Field_Order' => $i, 'Field_Name' => $get_Field_Name[$i],
						'Field_Content' => $get_Field_Content[$i],'Min_Char' => $get_Min_Char[$i],'Max_Char' => $get_Max_Char[$i], 'Required' => $update_Necessary),
						array('id' => $get_Field_Id[$i]),
						array('%d','%s',
						'%s','%s','%s','%d'),
						array('%d')
						);							
					}
					else{					
						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_create_user_form
							( id, GalleryID, Field_Type, Field_Order,
							Field_Name,Field_Content,Min_Char,Max_Char,
							Required)
							VALUES ( %s,%d,%s,%d,
							%s,%s,%d,%d,
							%d)
						",
							'',$GalleryID,'user-comment-field',$i,
							$get_Field_Name[$i],$get_Field_Content[$i],$get_Min_Char[$i],$get_Max_Char[$i],
							$update_Necessary
						) );						
					}
		
		}
	
		if($value=="user-text-field"){
			
				if(@$get_Necessary[$i]=='on'){@$update_Necessary=1;}
				else{@$update_Necessary=0;}
				
					$get_Field_Name[$i]=sanitize_text_field(htmlentities($get_Field_Name[$i], ENT_QUOTES, 'UTF-8'));
					$get_Field_Content[$i]=sanitize_textarea_field(htmlentities($get_Field_Content[$i], ENT_QUOTES, 'UTF-8'));
					
					if(@$get_Field_Id[$i]){
						$wpdb->update(
						"$tablename_create_user_form",
						array('Field_Order' => $i, 'Field_Name' => $get_Field_Name[$i],
						'Field_Content' => $get_Field_Content[$i],'Min_Char' => $get_Min_Char[$i],'Max_Char' => $get_Max_Char[$i], 'Required' => $update_Necessary),
						array('id' => @$get_Field_Id[$i]),
						array('%d','%s',
						'%s','%s','%s','%d'),
						array('%d')
						);							
					}
					else{
						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_create_user_form
							( id, GalleryID, Field_Type, Field_Order,
							Field_Name,Field_Content,Min_Char,Max_Char,
							Required)
							VALUES ( %s,%d,%s,%d,
							%s,%s,%d,%d,
							%d)
						",
							'',$GalleryID,'user-text-field',$i,
							$get_Field_Name[$i],$get_Field_Content[$i],$get_Min_Char[$i],$get_Max_Char[$i],
							$update_Necessary
						) );
						
					}
					

				
	}

	if($value=="main-user-name"){
		
					$get_Field_Name[$i]=sanitize_text_field(htmlentities($get_Field_Name[$i], ENT_QUOTES, 'UTF-8'));
					$get_Field_Content[$i]=sanitize_textarea_field(htmlentities($get_Field_Content[$i], ENT_QUOTES, 'UTF-8'));

						if($get_Field_Id[$i]){
							$wpdb->update(
							"$tablename_create_user_form",
							array('Field_Order' => $i, 'Field_Name' => $get_Field_Name[$i],
							'Field_Content' => $get_Field_Content[$i],'Min_Char' => $get_Min_Char[$i],'Max_Char' => $get_Max_Char[$i]),
							array('id' => $get_Field_Id[$i]),
							array('%d','%s',
							'%s','%s','%s'),
							array('%d')
							);
						}
						
		}
	
	if($value=="main-mail"){
		
					$get_Field_Name[$i]=sanitize_text_field(htmlentities($get_Field_Name[$i], ENT_QUOTES, 'UTF-8'));
					$get_Field_Content[$i]=sanitize_textarea_field(htmlentities($get_Field_Content[$i], ENT_QUOTES, 'UTF-8'));
					
					if($get_Field_Id[$i]){
							$wpdb->update(
							"$tablename_create_user_form",
							array('Field_Order' => $i, 'Field_Name' => $get_Field_Name[$i],
							'Field_Content' => @$get_Field_Content[$i],'Min_Char' => @$get_Min_Char[$i],'Max_Char' => @$get_Max_Char[$i]),
							array('id' => $get_Field_Id[$i]),
							array('%d','%s',
							'%s','%s','%s'),
							array('%d')
							);			
					}
		
						
	}
	
	if($value=="password"){
		
					$get_Field_Name[$i]=sanitize_text_field(htmlentities($get_Field_Name[$i], ENT_QUOTES, 'UTF-8'));
					$get_Field_Content[$i]=sanitize_textarea_field(htmlentities($get_Field_Content[$i], ENT_QUOTES, 'UTF-8'));
		
					if($get_Field_Id[$i]){
						
						$wpdb->update(
							"$tablename_create_user_form",
							array('Field_Order' => $i, 'Field_Name' => $get_Field_Name[$i],
							'Field_Content' => $get_Field_Content[$i],'Min_Char' => $get_Min_Char[$i],'Max_Char' => $get_Max_Char[$i]),
							array('id' => $get_Field_Id[$i]),
							array('%d','%s',
							'%s','%s','%s'),
							array('%d')
						);
						
						/*$wpdb->update(
							"$tablename_create_user_entries",
							array('f_input_id' => $get_Field_Id[$i]), 
							array('f_input_id' => $get_Field_Id[$i],
							'GalleryID' => $GalleryID), 
							array('%d'),
							array('%d','%d')
						);*/
						
					}
					
		}
	if($value=="password-confirm"){
		
					$get_Field_Name[$i]=sanitize_text_field(htmlentities($get_Field_Name[$i], ENT_QUOTES, 'UTF-8'));
					$get_Field_Content[$i]=sanitize_textarea_field(htmlentities($get_Field_Content[$i], ENT_QUOTES, 'UTF-8'));
		
					if($get_Field_Id[$i]){
						$wpdb->update(
						"$tablename_create_user_form",
						array('Field_Order' => $i, 'Field_Name' => $get_Field_Name[$i],
						'Field_Content' => $get_Field_Content[$i],'Min_Char' => $get_Min_Char[$i],'Max_Char' => $get_Max_Char[$i]),
						array('id' => $get_Field_Id[$i]),
						array('%d','%s',
						'%s','%s','%s'),
						array('%d')
						);							
					}			
		}

	if($value=="user-html-field"){

					$get_Field_Name[$i]=sanitize_text_field(htmlentities($get_Field_Name[$i], ENT_QUOTES, 'UTF-8'));
					$get_Field_Content[$i]=sanitize_textarea_field(htmlentities($get_Field_Content[$i], ENT_QUOTES, 'UTF-8'));

					if(@$get_Field_Id[$i]){

						$wpdb->update(
						"$tablename_create_user_form",
						array('Field_Order' => $i, 'Field_Name' => $get_Field_Name[$i],
						'Field_Content' => $get_Field_Content[$i],'Min_Char' => '','Max_Char' => '', 'Required' => ''),
						array('id' => $get_Field_Id[$i]),
						array('%d','%s',
						'%s','%s','%s','%d'),
						array('%d')
						);
						
					}
					else{			
						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_create_user_form
							( id, GalleryID, Field_Type, Field_Order,
							Field_Name,Field_Content,Min_Char,Max_Char,
							Required)
							VALUES ( %s,%d,%s,%d,
							%s,%s,%d,%d,
							%d)
						",
							'',$GalleryID,'user-html-field',$i,
							$get_Field_Name[$i],$get_Field_Content[$i],'','',
							''
						) );						
					}
					

				
		}

// Dient zur Orientierung zum Abarbeiten
$i++;
}



echo "<p id='cg_changes_saved' style='font-size:18px;'><strong>Changes saved</strong></p>";
	

} 

				
					

$selectFormInput = $wpdb->get_results("SELECT * FROM $tablename_create_user_form WHERE GalleryID = $GalleryID ORDER BY Field_Order ASC");


$checkDataFormOutput = $wpdb->get_results("SELECT * FROM $tablename_create_user_form WHERE GalleryID = $GalleryID and (Field_Type = 'comment-f' or Field_Type = 'text-f' or Field_Type = 'email-f')");
//print_r($checkDataFormOutput);


$rowVisualOptions = $wpdb->get_row("SELECT * FROM $tablename_options_visual WHERE GalleryID = '$GalleryID'");
@$Field1IdGalleryView = $rowVisualOptions->Field1IdGalleryView;


// Prüfen ob es ein Feld gibt welches als Images URL genutzt werden soll
@$Use_as_URL = $wpdb->get_var("SELECT Use_as_URL FROM $tablename_create_user_form WHERE GalleryID = '$GalleryID' AND Use_as_URL = '1'");
@$Use_as_URL_id = $wpdb->get_var("SELECT id FROM $tablename_create_user_form WHERE GalleryID = '$GalleryID' AND Use_as_URL = '1'");




?>