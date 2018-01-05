<?php 

global $wpdb;

$tablename = $wpdb->prefix . "contest_gal1ery";

$GalleryID = $_GET['option_id'];

			//$querySETvalues = "UPDATE $tablename SET Informed='0'	WHERE Informed = '1' AND GalleryID = '$GalleryID'";
			//$wpdb->query($querySETvalues);
		
		
			$wpdb->update( 
			"$tablename",
			array('Informed' => '0'), 
			array(
			'Informed' => '1','GalleryID' => $GalleryID
			), 
			array('%d'),
			array('%d','%d')
			);
			
			

			
?>