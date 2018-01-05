<?php

$GalleryID = @$_GET['option_id'];
 
 global $wpdb;


// Tabellennamen bestimmen
$tablename_form_input = $wpdb->prefix . "contest_gal1ery_f_input";
$tablename_form_output = $wpdb->prefix . "contest_gal1ery_f_output";
$tablenameoptions = $wpdb->prefix . "contest_gal1ery_options";

$GalleryName = $wpdb->get_var("SELECT GalleryName FROM $tablenameoptions WHERE id = '$GalleryID'");

// Empfangen von Galerie Options ID



//echo "GalleryID: $GalleryID";

// ------------ Speichern des Outputs




if (@$_POST['submit-form-output']) {


	// Alte Werte erstmal löschen
	
	/*	$deleteQuery = "DELETE FROM $tablename_form_output WHERE GalleryID = '$GalleryID'";
	$deleteSQL = $wpdb->query($deleteQuery);*/
	
	
			$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_form_output WHERE GalleryID = %d
			", 
				$GalleryID
		 ));
		
	
		
	// Alte Werte erstmal löschen --- ENDE
		

	
	// Alte Werte erstmal löschen --- Ende
	
	$a = 0;
	$i = 0;
	
	$formOutput = @$_POST['output'];
	
	//print_r($formOutput);

	foreach($formOutput as $key => $value){
	
		if ($value=='bh' or @$actualStage=='bh'){
		
		$a++;
		
		// Feldtyp
		// Feldcontent

		
			// Variable werden gesetzt
			if($a==1){ @$actualStage = 'bh'; $i++; }
			// Form Upload ID ermitteln
			if($a==2){ $f_input_id = intval($value); }					
			// Feldcontent
			if($a==3){ 

					$value = sanitize_text_field($value);

				//$wpdb->insert( $tablename_form_output, array( 'id' => '', 'f_input_id' => $f_input_id, 'GalleryID' => $GalleryID, 'Field_Type' => 'image-f', "Field_Order" => $i, "Field_Content" => "$value" ) ); 
				
					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_output
						( id, f_input_id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%d,%s,%d,%s )
					", 
						'',$f_input_id,$GalleryID,'image-f',$i,$value
				 ) );
				
				
				
				$a=0; @$actualStage = ' ';			
				
			}
		}	
	

		if ($value=='nf' or $actualStage=='nf'){
		
		$a++;
		
		// Feldtyp
		// Feldcontent

		
			// Variable werden gesetzt
			if($a==1){ $actualStage = 'nf'; $i++; }
			// Form Upload ID ermitteln
			if($a==2){ $f_input_id = intval($value); }	
			// Feldcontent
			if($a==3){
				
				$value = sanitize_text_field($value);

				//$wpdb->insert( $tablename_form_output, array( 'id' => '', 'f_input_id' => $f_input_id, 'GalleryID' => $GalleryID, 'Field_Type' => 'text-f', "Field_Order" => $i, "Field_Content" => $value ) ); 
				$wpdb->query( $wpdb->prepare(
				"
					INSERT INTO $tablename_form_output
					( id, f_input_id, GalleryID, Field_Type, Field_Order, Field_Content)
					VALUES ( %s,%d,%d,%s,%d,%s )
				", 
					'',$f_input_id,$GalleryID,'text-f',$i,$value
			 ) );
				$a=0;$actualStage = ' ';
				
			}
		}
		
		
		if ($value=='ef' or $actualStage=='ef'){
		
		$a++;
		
		// Feldtyp
		// Feldcontent
		
			// Variable werden gesetzt
			if($a==1){ $actualStage = 'ef'; $i++; }
			// Form Upload ID ermitteln
			if($a==2){ $f_input_id = intval($value); }	
			// Feldcontent
			if($a==3){
			
				$value = sanitize_text_field($value);
				
				// Wenn nein dann neuen Datensatz erstellen			
		
				//$wpdb->insert( $tablename_form_output, array( 'id' => '', 'f_input_id' => $f_input_id, 'GalleryID' => $GalleryID, 'Field_Type' => 'email-f', "Field_Order" => $i, "Field_Content" => $value ) );
				$wpdb->query( $wpdb->prepare(
				"
					INSERT INTO $tablename_form_output
					( id, f_input_id, GalleryID, Field_Type, Field_Order, Field_Content)
					VALUES ( %s,%d,%d,%s,%d,%s )
				", 
					'',$f_input_id,$GalleryID,'email-f',$i,$value
			 ) );
				$a=0; $actualStage = ' ';
			
			
			}
		
		}

		if ($value=='kf' or $actualStage=='kf'){
		
		$a++;
		
		// Feldtyp
		// Feldcontent

		
			// Variable werden gesetzt
			if($a==1){ $actualStage = 'kf'; $i++; }
			// Form Upload ID ermitteln
			if($a==2){ $f_input_id = intval($value); }	
			// 1. Feldtitel
			if($a==3){
				
				$value1 = sanitize_text_field($value);

			// Wenn nein dann neuen Datensatz erstellen			
				
				//$wpdb->insert( $tablename_form_output, array( 'id' => '', 'f_input_id' => $f_input_id, 'GalleryID' => $GalleryID, 'Field_Type' => 'comment-f', "Field_Order" => $i, "Field_Content" => $value1 ) ); 
				$wpdb->query( $wpdb->prepare(
				"
					INSERT INTO $tablename_form_output
					( id, f_input_id, GalleryID, Field_Type, Field_Order, Field_Content)
					VALUES ( %s,%d,%d,%s,%d,%s )
				", 
					'',$f_input_id,$GalleryID,'comment-f',$i,$value
			 ) );
				$a=0;$actualStage = ' ';
			
			}
		}		
	}	
	
	 echo "<p id='cg_changes_saved' style='font-size:18px;'><strong>Changes saved</strong></p>";

}

// Speichern des Outputs ------------ ENDE
	
	
	// Formular Upload für User wird ermittelt
	$selectFormInput = $wpdb->get_results( "SELECT id, Field_Type, Field_Order, Field_Content FROM $tablename_form_input WHERE GalleryID = '$GalleryID' ORDER BY Field_Order ASC" );
	
	// Formular Output für User wird ermittelt
	$selectFormOutput = $wpdb->get_results( "SELECT f_input_id, Field_Type, Field_Order, Field_Content FROM $tablename_form_output WHERE GalleryID = '$GalleryID' ORDER BY Field_Order ASC" );
	
	// Ermitteln von IDs des User Uploads
	$selectUserFormIDs = $wpdb->get_results( "SELECT f_input_id FROM $tablename_form_output WHERE GalleryID = '$GalleryID'" );
	
	// Die Field_Content Felder werden jetzt schon usnerialized und in einem Array gespeichert um weniger Load zu erzeugen
	

	$selectContentFieldArray = array();
	
	// notwendig um rauszufinden ob nicht alle upload felder auch in output eingefügt wurden
	$onlyInputIDs = array();
	
	foreach ($selectFormInput as $value) {
		
		$id = 	$value->id;
		$selectFieldType = 	$value->Field_Type;
		$selectContentFieldArray[] = $selectFieldType;
		$selectContentFieldArray[] = intval($id);
		$selectContentField = unserialize($value->Field_Content);
		$selectContentFieldArray[] = $selectContentField["titel"];
		
		$onlyInputIDs[] = $id;
	
	}
	
	//echo "<br><br>";
	//print_r($selectUserFormIDs);
	
	// Die Field_Content Felder werden jetzt schon usnerialized und in einem Array gespeichert um weniger Load zu erzeugen ----- ENDE
	
	// Die IDs werden in einem einfachen Array zusammengefasst für späteren Array Search
	
	$selectUserFormIDsArray = array();
	
		foreach ($selectUserFormIDs as $value) {
		
			$id = 	$value->f_input_id;
			$selectUserFormIDsArray[] = $id;

		}
		
		//echo "<br>";
		
		//print_r($selectUserFormIDsArray);
		
		//echo "<br>";
		
			//	echo "<br>";
		
		//print_r($onlyInputIDs);
		
		//echo "<br>";
		
		// notwendig um rauszufinden ob nicht alle upload felder auch in output eingefügt wurden
		foreach ($onlyInputIDs as $key => $value) {
		
			$idExists = in_array($value, $selectUserFormIDsArray);
			if(!$idExists){ $freeOutputFields = true;}
			

		}
	
	// Die IDs werden in einem einfachen Array zusammengefasst für späteren Array Search  ---- ENDE
	
	


 
 
?>