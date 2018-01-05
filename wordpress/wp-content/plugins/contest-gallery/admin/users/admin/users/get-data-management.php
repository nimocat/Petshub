<?php


/* UPDATE/INSERT VALUES */

$i=1;
//echo "<pre>";
//var_dump($_POST['Entry_Field_Type'] );
//echo "</pre>";
foreach($_POST['Entry_Field_Type'] as $key => $value){
	//@$selectWPusersEntries = $wpdb->get_row("SELECT * FROM $tablename_contest_gal1ery_create_user_entries WHERE f_input_id = '$f_input_id' AND wp_user_id = '$userId'");
	//echo $value;
	if($value!='user-check-agreement-field'){
		
		if($_POST['Entry_Field_Content'][$i]){
			
			//echo "$value<br>";
			if($_POST['Entry_Field_Content_Before'][$i]){
				//echo "Update".$_POST['Entry_Field_Content'][$i]."<br>";
				$wpdb->update(
					"$tablename_contest_gal1ery_create_user_entries",
					array('Field_Content' => sanitize_text_field(htmlentities($_POST['Entry_Field_Content'][$i], ENT_QUOTES, 'UTF-8'))),
					array('f_input_id' => $_POST['Entry_f_input_id'][$i], 'wp_user_id' => $_POST['Entry_wp_user_id'][$i]),
					array('%s'),
					array('%d','%d')
				);
			}
			else{
			//	echo "INSERT".$_POST['Entry_Field_Content'][$i]."<br>";
				$wpdb->query( $wpdb->prepare(
				"
					INSERT INTO $tablename_contest_gal1ery_create_user_entries
					( GalleryID, wp_user_id, f_input_id,
					Field_Type,Field_Content)
					VALUES ( %d,%d,%d,
					%s,%s)
				",
					$_POST['Entry_GalleryID'][$i],$_POST['Entry_wp_user_id'][$i],$_POST['Entry_f_input_id'][$i],
					$_POST['Entry_Field_Type'][$i],sanitize_text_field(htmlentities($_POST['Entry_Field_Content'][$i], ENT_QUOTES, 'UTF-8'))
				) );						
			}
			
		}			
	}
//echo $i;	
$i++;
}

/* UPDATE/INSERT VALUES --- END */







?>