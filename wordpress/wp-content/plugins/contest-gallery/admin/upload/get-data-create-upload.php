<?php

// 1. Delete Felder in Entries, F_Input, F_Output
// 2. Swap Field_Order in Entries, F_Input, F_Output (bei post "done-upload" wird alles mitgegeben
// 3. Neue Felder hinzuf�gen in F_Input, Entries
// 4. // Auswahl zum Anzeigen gespeicherter Felder

// Empfangen von Galerie OptiOns ID 


$GalleryID = intval(@$_GET['option_id']);

global $wpdb;

// Tabellennamen bestimmen

$tablename = $wpdb->prefix . "contest_gal1ery";
$tablenameoptions = $wpdb->prefix . "contest_gal1ery_options";
$tablenameentries = $wpdb->prefix . "contest_gal1ery_entries";
$tablename_form_input = $wpdb->prefix . "contest_gal1ery_f_input";
$tablename_form_output = $wpdb->prefix . "contest_gal1ery_f_output";
$tablename_options_visual = $wpdb->prefix . "contest_gal1ery_options_visual";

$GalleryName = $wpdb->get_var("SELECT GalleryName FROM $tablenameoptions WHERE id = '$GalleryID'");

// Check if certain fieldnumber should be deleted

//
// Vorgehen: Zuerst Feld l�schen falls einz mitgeschickt wurde zum l�schen. Dann pr�fen welche IDs mitgeschickt wurden (beim erstellten Formular) und diese in f_output und f_entries eing�gen. Die alten
// die drin wahren durch die neuen ersetzten
// Dann pauschal existierendes f_input l�schen und die neuen mitgeschicktern werte komplett neu einf�gen "INSERT"

// L�schen Ddaten in Tablename entries
// L�schen Ddaten in Tablename f_input
// L�schen Ddaten in Tablename f_output

if(@$_POST['deleteFieldnumber']){
	


$deleteFieldnumber = intval(@$_POST['deleteFieldnumber']);

// echo "Delete IDs:<br>";
//print_r($deleteFieldnumber);
// echo "<br>";

		//$deleteQuery1 = "DELETE FROM $tablename_form_input WHERE GalleryID = '$GalleryID' AND id = '$deleteFieldnumber'";
		//$wpdb->query($deleteQuery1);
		
		//echo "<br>deleteQuery1: $deleteQuery1 <br>";
		
		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_form_input WHERE GalleryID = %d AND id = %d
			", 
				$GalleryID, $deleteFieldnumber
		 ));

		//$deleteQuery2 = "DELETE FROM $tablename_form_output WHERE GalleryID = $GalleryID AND f_input_id = $deleteFieldnumber";
		//$wpdb->query($deleteQuery2);
		
		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_form_output WHERE GalleryID = %d AND f_input_id = %d
			", 
				$GalleryID, $deleteFieldnumber
		 ));
		
		//echo "<br>deleteQuery2: $deleteQuery2 <br>";
		
		//$deleteQuery3 = "DELETE FROM $tablenameentries WHERE GalleryID = '$GalleryID' AND f_input_id = '$deleteFieldnumber'";
		//$wpdb->query($deleteQuery3);		
		
		//echo "<br>deleteQuery3: $deleteQuery3 <br>";
		
				$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablenameentries WHERE GalleryID = %d AND f_input_id = %d
			", 
				$GalleryID, $deleteFieldnumber
		 ));

}

// Check if certain fieldnumber should be deleted --- ENDE


// Update der aktuellen f_input_id id der aktuellen Define Output felder die genutzt werden. Gleich im n�chsten Schrite, siehe unten, wird die actuaID durch neue ersetzt.

		$lastFormInput = $wpdb->get_row("SHOW TABLE STATUS LIKE '$tablename_form_input'"); // Get the new id of the gallery options which will be created
		$nextIDformInput = $lastFormInput->Auto_increment; // Get the new id of the gallery options which will be created

		@$actualIDs = @$_POST['actualID'];
		
		// echo "Actual IDs:<br>";
	//	 print_r($actualIDs);
		// echo "<br>";
		
		if(@$actualIDs){

			foreach($actualIDs as $key => $IDvalue){
				
			//$changeIDformInput = $nextIDformInput+$key;
			if(is_numeric ($IDvalue)){
				
				$IDvalue = intval($IDvalue);
				//$querySET = "UPDATE $tablename_form_output  SET f_input_id = '$nextIDformInput' WHERE f_input_id = '$IDvalue' and GalleryID = '$GalleryID'";
				//$updateSQL = $wpdb->query($querySET);
				
					$wpdb->update( 
					"$tablename_form_output",
					array('f_input_id' => $nextIDformInput), 
					array('f_input_id' => $IDvalue,
					'GalleryID' => $GalleryID), 
					array('%d'),
					array('%d','%d')
					);
					
					
					$wpdb->update( 
					"$tablenameentries",
					array('f_input_id' => $nextIDformInput), 
					array('f_input_id' => $IDvalue,
					'GalleryID' => $GalleryID), 
					array('%d'),
					array('%d','%d')
					);
					
					
					
			}
			
			$nextIDformInput++;
			
			}

		}
		
		
// Update der aktuellen f_input_id id der aktuellen Define Output felder die genutzt werden. Gleich im n�chsten Schrite, siehe unten, wird die actuaID durch neue ersetzt. --- ENDE





// Abspeichern von gesendeten Daten

if (@$_POST['submit']) {

	// Alte Werte erstmal l�schen
	

		
		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_form_input WHERE GalleryID = %d
			", 
				$GalleryID
		 ));
		
	// Alte Werte erstmal l�schen --- ENDE


$i=0; // Orientierungsvariable f�r den Gesamtdurchgang
$a=0; // Orientierungsvariable f�r den Einzeltypdurchgang


	
	
// Alte Formularfelder werden �berschrieben ---- ENDE



// �berpr�fung ob neue Field1IdGalleryView mit geschickt wird, wenn nicht, dann die alte auf "Null" oder "Leer" setzten
if(@$_POST['Field1IdGalleryView']){
$Field1IdGalleryView = @$_POST['Field1IdGalleryView'];

					foreach(@$Field1IdGalleryView as $key => $value){
						
						// ID wird ermittelt vom input Feld welches in der Gallerie als URL dienen soll
						// Es muss nicht unbedingt die existierende f_input_id mitgeschickt werden,
						// es kann auch eine Zahl mitgeschickt die bei einem neuen Feld durch JavaScript (auf + Klicken) mitgegeben wurde.
						// Wird sp�ter mit CompareID verglichen, die im Titel von jedem Formular-Feld-Block mitgegeben wurde 
						// (Diese kann es eben schon geben oder die wurde neu vom Javascript mitgegeben, die ist dann eben
						// beim Vergleichen egal)
						@$showInputID = $key;
						
					}
}
else{
	
	$wpdb->update( 
	"$tablename_options_visual",
	array('Field1IdGalleryView' => ''),
	array('GalleryID' => $GalleryID), 
	array('%s'),
	array('%d')
	);
	
}


// �berpr�fung ob neue Field1IdGalleryView mit geschickt wird, wenn nicht, dann die alte auf "Null" oder "Leer" setzten --- ENDE


// �berpr�fung ob neue Use_as_URL mit geschickt wird, wenn nicht, dann die alte auf "Null" oder "Leer" setzten
if(@$_POST['Use_as_URL']){
$Use_as_URL = @$_POST['Use_as_URL'];


					foreach(@$Use_as_URL as $key => $value){
						
						// ID wird ermittelt vom input Feld welches in der Gallerie als URL dienen soll
						// Es muss nicht unbedingt die existierende f_input_id mitgeschickt werden,
						// es kann auch eine Zahl mitgeschickt die bei einem neuen Feld durch JavaScript (auf + Klicken) mitgegeben wurde.
						// Wird sp�ter mit CompareID verglichen, die im Titel von jedem Formular-Feld-Block mitgegeben wurde 
						// (Diese kann es eben schon geben oder die wurde neu vom Javascript mitgegeben, die ist dann eben
						// beim Vergleichen egal)
						@$Use_as_URL = $key;
						
					}
		
}
else{
	
	$wpdb->update( 
	"$tablename_form_input",
	array('Use_as_URL' => ''),
	array('GalleryID' => $GalleryID), 
	array('%s'),
	array('%d')
	);
	
}


// �berpr�fung ob neue Use_as_URL mit geschickt wird, wenn nicht, dann die alte auf "Null" oder "Leer" setzten --- ENDE


// Neue Formularfelder werden eingef�gt

$newFormFields = @$_POST['upload'];

// Zur Pr�fung welche Felder im Slider gezeigt werden sollen
@$cg_f_input_id_show_slider = @$_POST[cg_f_input_id_show_slider];

//print_r($newFormFields);

$order=1;

    $actualStage = '';
  //  var_dump($newFormFields);

	if($newFormFields){  

		foreach($newFormFields as $key => $value){
			
			
		
			/*if ($value=='bh' or $actualStage=='bh'){
			
			$a++;
			
			// Feldtyp
			// Feldreihenfolge
			// FeldID	
			// 1 = Feldtitel
			
				// Variable werden gesetzt
				if($a==1){ $actualStage = 'bh'; $i++; }	

				// FeldID wird ermittelt
				if($a==2){ $id=$value; }	
				
				// 1. Feldtitel
				if($a==3){ $bhFieldsArray = array(); $bhFieldsArray['titel']=$value; 

				$bhFieldsArray = serialize($bhFieldsArray);
				
					$wpdb->insert( $tablename_form_input, array( 'id' => '', 'GalleryID' => $GalleryID, 'Field_Type' => 'image-f', "Field_Order" => $i, "Field_Content" => $bhFieldsArray ) ); 
					$a=0; $actualStage = ' ';			
					
				}
			}	*/
			

			
			
			
			if ($value=='bh' or @$actualStage=='bh'){
			
			$a++;
			
			// Feldtyp
			// 1 = Feldtitel
			
		
				// Variable werden gesetzt
				if($a==1){ $actualStage = 'bh'; $i++; }
				
				// 1. Feldtitel
				if($a==2){ $bhFieldsArray = array(); $bhFieldsArray['titel']= sanitize_text_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));
			
					$bhFieldsArray = serialize($bhFieldsArray);
				
					//$wpdb->insert( $tablename_form_input, array( 'id' => '', 'GalleryID' => $GalleryID, 'Field_Type' => 'image-f', "Field_Order" => $order, "Field_Content" => $bhFieldsArray ) ); 
					$a=0;$actualStage = ' ';$order++;

					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_input
						( id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%s,%d,%s )
					", 
						'',$GalleryID,'image-f',$order,$bhFieldsArray
				 ) );
				
				}
			}
			
			
			
			
			
			if ($value=='cb' or @$actualStage=='cb'){
			
			$a++;
			
			// Feldtyp
			// 1 = Feldname
			// 2 = Feldinhalt
			// 3 = Felderfordernis
			
		
				// Variable werden gesetzt
				if($a==1){ $actualStage = 'cb'; $i++; }
				
				// 2. Feldinhalt
				if($a==2){ $cbFieldsArray = array();  $cbFieldsArray['titel']=sanitize_text_field(htmlentities($value, ENT_QUOTES, 'UTF-8')); }
				
				// 2. Feldinhalt
				if($a==3){ $cbFieldsArray['content'] = sanitize_text_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));}

				// 3. Felderfordernis + Eingabe in die Datenbank
				if($a==4){ $cbFieldsArray['mandatory']=sanitize_text_field($value);

				$cbFieldsArray = serialize($cbFieldsArray);
				
					//$wpdb->insert( $tablename_form_input, array( 'id' => '', 'GalleryID' => $GalleryID, 'Field_Type' => 'check-f', "Field_Order" => $order, "Field_Content" => $cbFieldsArray ) ); 
					$a=0;$actualStage = ' ';$order++;
					
					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_input
						( id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%s,%d,%s )
					", 
						'',$GalleryID,'check-f',$order,$cbFieldsArray
				 ) );
					
				}
			}
			
			
		

			if ($value=='nf' or @$actualStage=='nf'){
			
			$a++;
			
			// Feldtyp
			// Feldreihenfolge
			// 1 = Feldtitel
			// 2 = Feldinhalt
			// 3 = Feldkrieterium1
			// 4 = Feldkrieterium2
			// 5 = Felderfordernis
			
				// Variable werden gesetzt
				if($a==1){ @$actualStage = 'nf'; $i++; }
				
				// 1. Feldtitel
				if($a==2){ $nfFieldsArray = array();
				//print_r($value);
					foreach($value as $key1 => $value1){	
					$nfFieldsArray['titel']=sanitize_text_field(htmlentities($value1, ENT_QUOTES, 'UTF-8'));
					$compareID = $key1;
					}
					
				}
				// 2. Feldinhalt
				if($a==3){ $nfFieldsArray['content'] = sanitize_text_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));}
				// 3. Feldkriterium 1
				if($a==4){ $nfFieldsArray['min-char']=intval($value); }
				// 4. Feldkriterium 2
				if($a==5){ $nfFieldsArray['max-char']=intval($value); }
				// 5. Felderfordernis + Eingabe in die Datenbank
				if($a==6){ $nfFieldsArray['mandatory']=sanitize_text_field($value);

				$nfFieldsArray = serialize($nfFieldsArray);
				
					//$wpdb->insert( $tablename_form_input, array( 'id' => '', 'GalleryID' => $GalleryID, 'Field_Type' => 'text-f', "Field_Order" => $order, "Field_Content" => $nfFieldsArray ) ); 
					$a=0;@$actualStage = ' ';$order++;
					
					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_input
						( id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%s,%d,%s )
					", 
						'',$GalleryID,'text-f',$order,$nfFieldsArray
				 ) );
				 

				 
				 // Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll			

					//	  echo "<br>showInputID: $showInputID<br>";
						//  echo "<br>compareID: $compareID<br>";
				// Es wird verglichen ob parallel dazu Field1IdGalleryView mitgeschickt wurde
				 if(@$showInputID==@$compareID){	

				$last = $wpdb->get_row("SHOW TABLE STATUS LIKE '$tablename_form_input'"); // Get the new id of the gallery options which will be created
				$nextIDgallery = $last->Auto_increment-1; // Get the new id of the gallery options which will be created				 
				//echo "<br>nextIDgallery: $nextIDgallery<br>";
								$wpdb->update( 
								"$tablename_options_visual",
								array('Field1IdGalleryView' => $nextIDgallery),
								array('GalleryID' => $GalleryID), 
								array('%d'),
								array('%d')
								);					
					
					}
					
					// Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll --- ENDE
					
					
		      // Pr�fen ob das Feld in der Galerie Ansicht als URL dienen soll  
			  
			  
			//  echo "<br>Use_as_URL: $Use_as_URL<br>";
			//  echo "<br>compareID: $compareID<br>";
			 // echo "<br>nextIDgallery: $compareID<br>";
				// Es wird verglichen ob parallel dazu Use_as_URL mitgeschickt wurde und dann eine 1 reingesetzt
				 if(@$Use_as_URL==@$compareID){	
				 
				 // Ermitteln der letzten ID des Tables um in diese dann User_asURL = 1 einzuf�gen
				 $lastID = $wpdb->get_var("SELECT id FROM $tablename_form_input ORDER BY id DESC LIMIT 1"); 


				//$last = $wpdb->get_var("SELECT id FROM $tablename_form_input ORDER BY id DESC LIMIT 1'"); // Get the new id of the gallery options which will be created
				//$nextIDgallery = $last->Auto_increment-1; // Get the new id of the gallery options which will be created		

					  //echo "nextIDgallery: $last";
					
								$wpdb->update( 
								"$tablename_form_input",
								array('Use_as_URL' => 1),
								array('id' => $lastID), 
								array('%d'),
								array('%d')
								);					
					
					}
					
				// Pr�fen ob das Feld in der Galerie Ansicht als URL dienen soll --- ENDE
				
				
				// Pr�fen ob das Feld im Slider als User Info dienen soll
				
				if($cg_f_input_id_show_slider){
					
					foreach($cg_f_input_id_show_slider as $userInfoSliderKey => $userInfoSliderValue){				
						
						
						if($userInfoSliderKey==@$compareID){
							
							
						$lastID = $wpdb->get_var("SELECT id FROM $tablename_form_input ORDER BY id DESC LIMIT 1"); 
									
									$wpdb->update( 
									"$tablename_form_input",
									array('Show_Slider' => 1),
									array('id' => $lastID), 
									array('%d'),
									array('%d')
									);								
							
						}				
															
						
					}						
					
				}	
					
				// Pr�fen ob das Feld im Slider als User Info dienen soll--- ENDE		
					
					
					
					
				}
			}
			
			
			if ($value=='ef' or @$actualStage=='ef'){
			
			//echo "EF SCHLEIFE L��UFT";
			
			$a++;
			
			// Feldtyp
			// Feldreihenfolge
			// 1 = Feldtitel
			// 2 = Feldinhalt
			// 3 = Felderfordernis
			
				// Variable werden gesetzt
				if($a==1){ $actualStage = 'ef'; $i++; }
				
				// 1. Feldtitel
				if($a==2){ $efFieldsArray = array(); 
				
					foreach($value as $key1 => $value1){	
					$efFieldsArray['titel']=sanitize_text_field(htmlentities($value1, ENT_QUOTES, 'UTF-8'));	
					$compareID = $key1;
					}
					
				}
				// 2. Feldinhalt
				if($a==3){ $efFieldsArray['content'] = sanitize_text_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));}
				// 3. Felderfordernis + Eingabe in die Datenbank
				if($a==4){ $efFieldsArray['mandatory']=sanitize_text_field($value);
				
				$efFieldsArray = serialize($efFieldsArray);
				
					//$wpdb->insert( $tablename_form_input, array( 'id' => '', 'GalleryID' => $GalleryID, 'Field_Type' => 'email-f', "Field_Order" => $order, "Field_Content" => $efFieldsArray ) ); 
					$a=0; $actualStage = ' ';$order++;
					
					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_input
						( id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%s,%d,%s )
					", 
						'',$GalleryID,'email-f',$order,$efFieldsArray
				 ) );
				 
				 
				 				 // Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll				 
				// Es wird verglichen ob parallel dazu Field1IdGalleryView mitgeschickt wurde
				 if(@$showInputID==@$compareID){	

				$last = $wpdb->get_row("SHOW TABLE STATUS LIKE '$tablename_form_input'"); // Get the new id of the gallery options which will be created
				$nextIDgallery = $last->Auto_increment-1; // Get the new id of the gallery options which will be created				 
					
								$wpdb->update( 
								"$tablename_options_visual",
								array('Field1IdGalleryView' => $nextIDgallery),
								array('GalleryID' => $GalleryID), 
								array('%d'),
								array('%d')
								);					
					
					}
					
					// Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll --- ENDE		

				// Pr�fen ob das Feld im Slider als User Info dienen soll
				
				if($cg_f_input_id_show_slider){
					
					foreach($cg_f_input_id_show_slider as $userInfoSliderKey => $userInfoSliderValue){				
						
						
						if($userInfoSliderKey==@$compareID){
							
							
						$lastID = $wpdb->get_var("SELECT id FROM $tablename_form_input ORDER BY id DESC LIMIT 1"); 
									
									$wpdb->update( 
									"$tablename_form_input",
									array('Show_Slider' => 1),
									array('id' => $lastID), 
									array('%d'),
									array('%d')
									);								
							
						}				
															
						
					}						
					
				}	
					
				// Pr�fen ob das Feld im Slider als User Info dienen soll--- ENDE						
					
					


				
				}
			
			}

			if ($value=='kf' or $actualStage=='kf'){
			
			$a++;
			
			// Feldtyp
			// Feldreihenfolge
			// 1 = Feldtitel
			// 2 = Feldinhalt
			
				// Variable werden gesetzt
				if($a==1){ $actualStage = 'kf'; $i++; }
				
				// 1. Feldtitel
				if($a==2){ $kfFieldsArray = array(); 
				
					foreach($value as $key1 => $value1){	
					$kfFieldsArray['titel']=sanitize_text_field(htmlentities($value1, ENT_QUOTES, 'UTF-8'));
					$compareID = $key1;
					}
					
				}
				// 2. Feldinhalt
				if($a==3){ $kfFieldsArray['content'] = sanitize_textarea_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));}
				// 3. Feldkriterium 1
				if($a==4){ $kfFieldsArray['min-char']=intval($value); }
				// 4. Feldkriterium 2
				if($a==5){ $kfFieldsArray['max-char']=intval($value); }
				// 5. Felderfordernis + Eingabe in die Datenbank
				if($a==6){ $kfFieldsArray['mandatory']=sanitize_text_field($value);

				$kfFieldsArray = serialize($kfFieldsArray);
				
					//$wpdb->insert( $tablename_form_input, array( 'id' => '', 'GalleryID' => $GalleryID, 'Field_Type' => 'comment-f', "Field_Order" => $order, "Field_Content" => $kfFieldsArray ) ); 
					$a=0;$actualStage = ' ';$order++;
					
					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_input
						( id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%s,%d,%s )
					", 
						'',$GalleryID,'comment-f',$order,$kfFieldsArray
				 ) );
				 
				 
				 				 // Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll				 
				// Es wird verglichen ob parallel dazu Field1IdGalleryView mitgeschickt wurde
				 if(@$showInputID==@$compareID){	

				$last = $wpdb->get_row("SHOW TABLE STATUS LIKE '$tablename_form_input'"); // Get the new id of the gallery options which will be created
				$nextIDgallery = $last->Auto_increment-1; // Get the new id of the gallery options which will be created				 
					
								$wpdb->update( 
								"$tablename_options_visual",
								array('Field1IdGalleryView' => $nextIDgallery),
								array('GalleryID' => $GalleryID), 
								array('%d'),
								array('%d')
								);					
					
					}
					
					// Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll --- ENDE
					
					
									// Pr�fen ob das Feld im Slider als User Info dienen soll
				
				if($cg_f_input_id_show_slider){
					
					foreach($cg_f_input_id_show_slider as $userInfoSliderKey => $userInfoSliderValue){				
						
						
						if($userInfoSliderKey==@$compareID){
							
							
						$lastID = $wpdb->get_var("SELECT id FROM $tablename_form_input ORDER BY id DESC LIMIT 1"); 
									
									$wpdb->update( 
									"$tablename_form_input",
									array('Show_Slider' => 1),
									array('id' => $lastID), 
									array('%d'),
									array('%d')
									);								
							
						}				
															
						
					}						
					
				}	
					
				// Pr�fen ob das Feld im Slider als User Info dienen soll--- ENDE	
					
				}
			}
			
			

			
			if ($value=='ht' or $actualStage=='ht'){


			
			$a++;
			
			// Feldtyp
			// Feldreihenfolge
			// 1 = Feldtyp
			// 2 = Feldtitel
			// 3 = Feldinhalt
			
				// Variable werden gesetzt
				// 1 = Feldtyp
				if($a==1){ $actualStage = 'ht'; $i++; }

				// 2. Feldtitel
				if($a==2){ $htFieldsArray = array(); $htFieldsArray['titel']=sanitize_text_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));}
				// 3. Feldinhalt
				if($a==3){ $htFieldsArray['content'] = sanitize_textarea_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));

					$htFieldsArray = serialize($htFieldsArray);
				
					//$wpdb->insert( $tablename_form_input, array( 'id' => '', 'GalleryID' => $GalleryID, 'Field_Type' => 'comment-f', "Field_Order" => $order, "Field_Content" => $kfFieldsArray ) ); 
					$a=0;$actualStage = ' ';$order++;
					
					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_input
						( id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%s,%d,%s )
					", 
						'',$GalleryID,'html-f',$order,$htFieldsArray
					) );		 
				 
					
				// Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll --- ENDE
				
				}
				
			}

        //

			if ($value=='caRo' or $actualStage=='caRo'){

			$a++;

			// Feldtyp
			// Feldreihenfolge
			// 1 = Feldtyp
			// 2 = Feldtitel
			// 3 = Feldinhalt

				// Variable werden gesetzt
				// 1 = Feldtyp

				if($a==1){ $actualStage='caRo'; $i++; }

				// 2. Feldtitel
				if($a==2){ $caFieldsArray = array(); $caFieldsArray['titel']=sanitize_text_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));

                    $caFieldsArray = serialize($caFieldsArray);

                    $a=0;$actualStage = ' ';$order++;

					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_input
						( id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%s,%d,%s )
					",
						'',$GalleryID,'caRo-f',$order,$caFieldsArray
					) );


				// Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll --- ENDE

				}

			}



			if ($value=='se' or $actualStage=='se'){


			$a++;

			// Feldtyp
			// Feldreihenfolge
			// 1 = Feldtyp
			// 2 = Feldtitel
			// 3 = Feldinhalt

				// Variable werden gesetzt
				// 1 = Feldtyp
				if($a==1){ $actualStage = 'se'; $i++; }
				// 2. Feldtitel
				//if($a==2){ $seFieldsArray = array(); $seFieldsArray['titel']=sanitize_text_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));}

                // 1. Feldtitel
                if($a==2){ $seFieldsArray = array();

                    foreach($value as $key1 => $value1){
                        $seFieldsArray['titel']=sanitize_text_field(htmlentities($value1, ENT_QUOTES, 'UTF-8'));
                        $compareID = $key1;
                    }

                }



				// 3. Feldinhalt
				if($a==3){ $seFieldsArray['content'] = sanitize_textarea_field(htmlentities($value, ENT_QUOTES, 'UTF-8'));}
                if($a==4){ $seFieldsArray['mandatory']=sanitize_text_field($value);

                    $seFieldsArray = serialize($seFieldsArray);

					//$wpdb->insert( $tablename_form_input, array( 'id' => '', 'GalleryID' => $GalleryID, 'Field_Type' => 'comment-f', "Field_Order" => $order, "Field_Content" => $kfFieldsArray ) );
					$a=0;$actualStage = ' ';$order++;

					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablename_form_input
						( id, GalleryID, Field_Type, Field_Order, Field_Content)
						VALUES ( %s,%d,%s,%d,%s )
					",
						'',$GalleryID,'select-f',$order,$seFieldsArray
					) );

				// Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll --- ENDE

                    // Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll
                    // Es wird verglichen ob parallel dazu Field1IdGalleryView mitgeschickt wurde
                    if(@$showInputID==@$compareID){

                        $last = $wpdb->get_row("SHOW TABLE STATUS LIKE '$tablename_form_input'"); // Get the new id of the gallery options which will be created
                        $nextIDgallery = $last->Auto_increment-1; // Get the new id of the gallery options which will be created

                        $wpdb->update(
                            "$tablename_options_visual",
                            array('Field1IdGalleryView' => $nextIDgallery),
                            array('GalleryID' => $GalleryID),
                            array('%d'),
                            array('%d')
                        );

                    }

                    // Pr�fen ob das Feld in der Galerie Ansicht gezeigt werden soll --- ENDE


                    // Pr�fen ob das Feld im Slider als User Info dienen soll

                    if($cg_f_input_id_show_slider){

                        foreach($cg_f_input_id_show_slider as $userInfoSliderKey => $userInfoSliderValue){


                            if($userInfoSliderKey==@$compareID){


                                $lastID = $wpdb->get_var("SELECT id FROM $tablename_form_input ORDER BY id DESC LIMIT 1");

                                $wpdb->update(
                                    "$tablename_form_input",
                                    array('Show_Slider' => 1),
                                    array('id' => $lastID),
                                    array('%d'),
                                    array('%d')
                                );

                            }


                        }

                    }

                    // Pr�fen ob das Feld im Slider als User Info dienen soll--- ENDE



				}

			}
			
			
			// Admin fields here --- ENDE
			
			
			
		}
		
	}
	
// Neue Formularfelder werden eingef�gt ---- ENDE

echo "<p id='cg_changes_saved' style='font-size:18px;'><strong>Changes saved</strong></p>";
	

} 

/*

		// Alle Felder die in Slider gezeigt werden sollen zuerst auf NULL setzten


					$wpdb->update( 
					"$tablename_form_input",
					array('Show_Slider' => 0),
					array('GalleryID' => $GalleryID), 
					array('%d'),
					array('%d')
					);	
					
					$cg_f_input_id_show_slider = $_POST[cg_f_input_id_show_slider];
					
					if($cg_f_input_id_show_slider){
						
							foreach($cg_f_input_id_show_slider as $key => $value){								
														
									$wpdb->update( 
									"$tablename_form_input",
									array('Show_Slider' => 1),
									array('id' => $key), 
									array('%d'),
									array('%d')
									);	
								
							}
						
					}
					
*/					
					

$selectFormInput = $wpdb->get_results("SELECT * FROM $tablename_form_input WHERE GalleryID = $GalleryID ORDER BY Field_Order ASC");


$checkDataFormOutput = $wpdb->get_results("SELECT * FROM $tablename_form_input WHERE GalleryID = $GalleryID and (Field_Type = 'comment-f' or Field_Type = 'text-f' or Field_Type = 'email-f')");
//print_r($checkDataFormOutput);


$rowVisualOptions = $wpdb->get_row("SELECT * FROM $tablename_options_visual WHERE GalleryID = '$GalleryID'");
@$Field1IdGalleryView = $rowVisualOptions->Field1IdGalleryView;


// Pr�fen ob es ein Feld gibt welches als Images URL genutzt werden soll
@$Use_as_URL = $wpdb->get_var("SELECT Use_as_URL FROM $tablename_form_input WHERE GalleryID = '$GalleryID' AND Use_as_URL = '1'");
@$Use_as_URL_id = $wpdb->get_var("SELECT id FROM $tablename_form_input WHERE GalleryID = '$GalleryID' AND Use_as_URL = '1'");





//echo "<br>Field1IdGalleryView:$Field1IdGalleryView<br>";

	
	/*	// Swap Field_Order values of users in database if necessary
		
		if(@$_POST['changeFieldRow']){
		
		$changeFieldRow = @$_POST['changeFieldRow'];
		
		//print_r($changeFieldRow);
		
		
		// FELDBENENNUNGEN
		// 1 = Neuer Wert in der Datenbank
		// 2 = Alter Wert in der Datenbank
		
		$i = 2;
		
		$proveValue = array();
		
		
		
			foreach($changeFieldRow as $key => $value){
			
				if ($i == 0) {$i = 1;}
						
				if ($i==1) {
				
					$key = $oldFieldOrder;			
					$value = $newFieldOrder; 
					
					// Swap Field_Order in User-Entries-Tabelle
					$querySET = "UPDATE $tablenameentries  SET Field_Order = '$newRowKey' WHERE GalleryID = '$GalleryID' AND Field_Order = '$oldFieldOrder' ";
					$updateSQL = $wpdb->query($querySET);
					
					// Swap Field_Order in Form-Input-Tabelle
					$querySET = "UPDATE $tablename_form_input  SET Field_Order = '$newRowKey' WHERE GalleryID = '$GalleryID' AND Field_Order = '$oldFieldOrder' ";
					$updateSQL = $wpdb->query($querySET);
					
					// Swap Field_Order in Form-Output-Tabelle..... wenn vorhanden! �berpr�fung auf Existenz nicht notwendig.
					$querySET = "UPDATE $tablename_form_output  SET Field_Order = '$newRowKey' WHERE GalleryID = '$GalleryID' AND Field_Order = '$oldFieldOrder' ";
					$updateSQL = $wpdb->query($querySET);

				
				}
				
				
				
				
				
				if ($i==1) {$originKey = $value; // Alter Wert in der Datenbank
				
				//echo "<br/>rowKey: $rowKey<br/>";
				//echo "<br/>originKey: $originKey<br/>";
				
					if ($originKey != $rowKey) {	
						
						// Addition zu Wiedererkennung. Wird zwischengespeichert zur Differenzierng.
						$newRowKey = $rowKey+10;
						
						// �berpr�fng ob eines der urpsr�nglichen Werte zwischengespeichert wurden, aufgrund Erkennungsnotwendigkeit 
						$newOriginKey = $originKey+10;
						
						//echo "<br/>";
						//print_r($proveValue);
						//echo "<br/>";
						//echo "<br/>NeworiginKey: $newOriginKey<br/>";
						//echo "<br/>NeworiginKey: $newRowKey<br/>";
						
						
					
							if (array_search($newOriginKey, $proveValue)===false) {		
							
							// Zwischenspeicherung zur Differenzierung, wenn die Position nicht vorher gel�scht wurde
							if ($deleteFieldnumber != $rowKey) {							
							$querySET = "UPDATE $tablenameentries  SET Field_Order = '$newRowKey' WHERE GalleryID = '$GalleryID' AND Field_Order = '$rowKey' ";
							$updateSQL = $wpdb->query($querySET);
							}
							
							//echo "<br/>Zwischenspeicherung!<br/>";
							
							//echo "<br/>Originkey: <b>$originKey</b><br/>";
							//echo "<br/>rowKey: <b>$rowKey</b><br/>";
													
							// Neuen Wert �ndern in Alten (Wert der Reihenfolge)				
							$querySET = "UPDATE $tablenameentries  SET Field_Order = '$rowKey' WHERE GalleryID = '$GalleryID' AND Field_Order = '$originKey' ";
							$updateSQL = $wpdb->query($querySET);
							
							// Speicherung des neuen Zwischenwertes in ein Array. Wenn die Position nicht vorher gel�scht wurde.
							if ($deleteFieldnumber != $rowKey) {	
							$proveValue[] = $newRowKey; 
							}
							//	

							}
							
							else{
							
							
							// Zwischengespeicherte Werte neue Werte hinzuf�gen
							
							// Pr�fen ob ein Eintrag existiert, nicht dass er doppelt vorkommt
							$checkEntries = $wpdb->get_results("SELECT pid FROM $tablenameentries WHERE Field_Order = '$rowKey'");
								if ($checkEntries) {
								
								// Den doppelten sicherstellen, so dass man ihn wieder erkennen kann
								$querySET = "UPDATE $tablenameentries  SET Field_Order = '$newRowKey' WHERE GalleryID = '$GalleryID' AND Field_Order = '$rowKey' ";
								$updateSQL = $wpdb->query($querySET);
								
									if ($deleteFieldnumber==$rowKey) {
									
									// Auswahl der Bildnummern (pid)
									$selectPics = $wpdb->get_results("SELECT pid FROM $tablenameentries WHERE GalleryID = '$GalleryID' GROUP BY pid ASC");

									foreach($selectPics as $value){

									// Bestimmung der Feldnummer
									$pid = $value->pid;
									
									// Einsetzen des neuen Feldes in die Datenbank als leerer Datenbestand bei allen existierenden Bildern
									$wpdb->insert( $tablenameentries, array( 'id' => '', 'pid' => $pid, 'GalleryID' => $GalleryID, "Fieldtype" => $ft, 'fieldnumber' => $fn, 'ShortText' => '', 'LongText2' => '') );

									}									
									
									// Eintrag vornehmen nach dem abgesichert ist, dass es keine Doppelten geben kann
									$querySET = "UPDATE $tablenameentries  SET Fieldnumber = '$rowKey' WHERE GalleryID = '$GalleryID' AND Fieldnumber = '$newOriginKey' ";
									$updateSQL = $wpdb->query($querySET);	
									}
									else{
									// Eintrag vornehmen nach dem abgesichert ist, dass es keine Doppelten geben kann
									$querySET = "UPDATE $tablenameentries  SET Fieldnumber = '$rowKey' WHERE GalleryID = '$GalleryID' AND Fieldnumber = '$newOriginKey' ";
									$updateSQL = $wpdb->query($querySET);									
									}
								
								$proveValue[] = $newRowKey; 
								
								//echo "<br/>Update der zwischengespeicherten Werte! CheckEntries Wahr!<br/>";	
								
								}
								
								else{
								
								// Doppelte k�nnen nicht existieren. Einfach Eintrag vornehmen.
								$querySET = "UPDATE $tablenameentries  SET Fieldnumber = '$rowKey' WHERE GalleryID = '$GalleryID' AND Fieldnumber = '$newOriginKey' ";
								$updateSQL = $wpdb->query($querySET);
								
								}
								
													

							
							}
						

						
						//echo "<br/>Geklappt!<br/>";
						
						//echo "<br/>";
						
						//print_r($proveValue);						
						
						//echo "<br/>";
						
						
										
						//$querySET = "$originKey=(@temp:=$originKey), $originKey = $newKey, $newKey = @temp; UPDATE $tablenameentries SET Fieldnumber = '$originKey' WHERE GalleryID = '$GalleryID' AND Fieldnumber = '$newKey'";
						//$querySET = "UPDATE $tablenameentries SET Fieldnumber = (CASE WHEN Fieldnumber=$originKey THEN $newKey WHEN Fieldnumber=$newKey THEN $originKey END) WHERE Fieldnumber IN ($originKey, $newKey)";
						/*$querySET = "UPDATE $tablenameentries  SET Fieldnumber = '$originKey' WHERE GalleryID = '$GalleryID' AND Fieldnumber = '$newKey' ";
						$updateSQL = $wpdb->query($querySET);	
						
						echo "TEST!";*/
						
						/*$querySET = "UPDATE table1 SET Fieldnumber = CASE Fieldnumber   
                          WHEN $newKey THEN $originKey   
                          ELSE Fieldnumber  
                        END,   
					Fieldnumber = CASE Fieldnumber   
                          WHEN $originKey THEN $newKey   
                          ELSE Fieldnumber   
                        END  
					WHERE Fieldnumber IN ($newKey, $originKey)";*/ 

				/*	
					}
				
				}

			$i--;
				
			} 
		
		
		}	*/	
		// Swap Field_Order values of users in database if necessary --- ENDE
	
		
		// Add new fields in database if necessary 
		/*
		
		if(@$_POST['addField']){
		
		//echo "<br>AddFieldFunktionL�uft: <br>";
		
		$addField = @$_POST['addField'];
		
		//print_r($addField);
		
		
		// FELDBENENNUNGEN
		// 1 = Feldnummer in der Datenbank
		// 2 = Feldtyp in der Datenbank
		
		$i = 2;
		
			foreach($addField as $key => $value){
			
			if ($i == 0) {$i = 2;}
						
			if ($i==2) {$fn = $value;} // Bestimmung der Feldnummer in der Datenbank
			
			if ($i==1) {$ft = $value;
			
			//echo "<br>true</br>";
			//echo "Zusammenfassung: NextID: $nextID; GalleryID: $GalleryID; Fieldtype: $ft; Fieldnumber: $fn; ShortText: ;LongText: ;<br/>";
			
			// Ausw�hlen alle Bild IDs die in einer Galerie vorhanden sind
			$selectPicIds = $wpdb->get_results("SELECT id FROM $tablename WHERE GalleryID = '$GalleryID' GROUP BY id ASC");
			
			// Auswahl der Bildnummern (pid)
			if(!$selectPics){
			$selectPics = $wpdb->get_results("SELECT pid FROM $tablenameentries WHERE GalleryID = '$GalleryID' GROUP BY pid ASC");
			}
			
			//print_r($selectPicIds);

			foreach($selectPicIds as $value){ 

			// Bestimmung der Feldnummer
			$pid = $value->id;
			
			//echo "<br>pid:</br>";
			//echo "$pid";
			
			// Einsetzen des neuen Feldes in die Datenbank als leerer Datenbestand bei allen existierenden Bildern
			$wpdb->insert( $tablenameentries, array( 'id' => '', 'pid' => $pid, 'GalleryID' => $GalleryID, "Fieldtype" => $ft, 'fieldnumber' => $fn, 'ShortText' => '', 'LongText2' => '') );

			}
			
			
			} // Bestimmung des Feldtypes in der Datenbank
			
			$i--;
				
			}
		
		
		}	*/	
		// Add new fields in database if necessary --- ENDE





// Pfad definieren JS

//$pfad = plugins_url()."/f-einfach/js/jquer-1.6.2.js"; 


?>