<?php

//echo "worked1";

		$optionID = @$_GET["option_id"];

		/*if(@$_GET['optionID']==true AND @$_GET['delete']==true AND @$_GET['yes']==false ) {
		
		echo '<p><br/>Sind sie sicher, dass Sie die Galerie l&ouml;schen m&ouml;chten? Alle Bilder, die hochgeladen wurden, 
		so wie alle Eintr&auml;ge in der Datenbank,<br/>werden <b>unwiederruflich gel&ouml;scht.</b></p>';
		
		echo '<p><a href="?page=contest-gallery/index.php&optionID=' . $optionID . '&delete=true&yes=true"><input type="submit" value="Ja, Galerie l&ouml;schen"></a</p><br/>';
		
		}*/
		
		if(@$_GET['option_id']==true AND @$_GET['delete']==true) {
		
		global $wpdb;
		
		$tablename = $wpdb->prefix . "contest_gal1ery";
		$tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";
		$tablenameEntries = $wpdb->prefix . "contest_gal1ery_entries";
		$tablenameComments = $wpdb->prefix . "contest_gal1ery_comments";
		$tablenameIp = $wpdb->prefix . "contest_gal1ery_ip";
		$tablenameMail = $wpdb->prefix . "contest_gal1ery_mail";
		$tablename_options_input = $wpdb->prefix . "contest_gal1ery_options_input";
		$tablename_form_input = $wpdb->prefix . "contest_gal1ery_f_input";
		$tablename_form_output = $wpdb->prefix . "contest_gal1ery_f_output";
		$tablename_options_visual = $wpdb->prefix . "contest_gal1ery_options_visual";
		$tablename_email_admin = $wpdb->prefix . "contest_gal1ery_mail_admin";
		
		$selectSQL = $wpdb->get_results( "SELECT * FROM $tablename WHERE GaleryNr = '$optionID'" );
		
		// alte files delte funktion
		/*foreach($selectSQL as $value){
		
		$ImageThumb = $value -> ImageThumb;
		$ImageGalery = $value -> ImageGalery;
		$ImageOrigin = $value -> ImageOrigin;
		
		unlink("../wp-content/uploads/$ImageThumb");
		unlink("../wp-content/uploads/$ImageGalery");
		unlink("../wp-content/uploads/$ImageOrigin");
		
		}*/
		
		// alte files delte funktion --- ENDE
		
		// neue delete Funktion
		
		$upload_dir = wp_upload_dir();
		//echo $upload_dir['basedir'];
		/*
		// delete cached sites
		
		$deleteCachedSiteFiles = $upload_dir['basedir'].'/contest-gallery/gallery-id-'.$optionID.'/cache/sites/*';
		// echo "<br>deleteCachedSiteFiles: $deleteCachedSiteFiles<br>";
		$deleteCachedSiteFiles = glob($deleteCachedSiteFiles); // get all file names
		foreach($deleteCachedSiteFiles as $file1){ // iterate files
		if(is_file($file1))
		unlink($file1); // delete file
		}
		
		// delete cached picture sites folder
		$deleteCachedSiteFilesFolder = $upload_dir['basedir'].'/contest-gallery/gallery-id-'.$optionID.'/cache/sites';
		rmdir($deleteCachedSiteFilesFolder);
		
		
		$deleteCachedGalleryFiles = $upload_dir['basedir'].'/contest-gallery/gallery-id-'.$optionID.'/cache/gallery/*';
		// echo "<br>deleteCachedGalleryFiles: $deleteCachedGalleryFiles<br>";

		$deleteCachedGalleryFiles = glob($deleteCachedGalleryFiles); // get all file names
		foreach($deleteCachedGalleryFiles as $file2){ // iterate files
		if(is_file($file2))
		unlink($file2); // delete file
		}
		
		// delete cached gallery folder
		$deleteCachedGalleryFilesFolder = $upload_dir['basedir'].'/contest-gallery/gallery-id-'.$optionID.'/cache/gallery';
		rmdir($deleteCachedGalleryFilesFolder);
		
		// delete cache folder
		$deleteCacheFolder = $upload_dir['basedir'].'/contest-gallery/gallery-id-'.$optionID.'/cache';
		rmdir($deleteCacheFolder);
		
		// delete cached sites --- END
		*/
		
		
		// delete pictures
		
		$deletePictureFiles = $upload_dir['basedir'].'/contest-gallery/gallery-id-'.$optionID.'/*';
		// echo "<br>deleteCachedGalleryFiles: $deleteCachedGalleryFiles<br>";

		$deletePictureFiles = glob($deletePictureFiles); // get all file names
		foreach($deletePictureFiles as $file3){ // iterate files
		if(is_file($file3))
		unlink($file3); // delete file
		}
		
		$deletePictureFilesFolder = $upload_dir['basedir'].'/contest-gallery/gallery-id-'.$optionID.'';
		
		//echo "<br>deletePictureFilesFolder: $deletePictureFilesFolder <br>";
		
		rmdir($deletePictureFilesFolder);
		
		
		/*$files = glob('path/to/temp/*');
		
		foreach($files as $file){
		if(is_file($file))
		unlink($file);
		
		}*/
		// neue delete Funktion --- ENDE
		
		/*
		$sql1 = "DELETE FROM $tablename WHERE GalleryID = '$optionID' ";
		$sql2 = "DELETE FROM $tablenameOptions WHERE id = '$optionID' ";
		$sql3 = "DELETE FROM $tablenameEntries WHERE GalleryID = '$optionID' ";
		$sql4 = "DELETE FROM $tablenameComments WHERE GalleryID = '$optionID' ";
		$sql5 = "DELETE FROM $tablenameIp WHERE GalleryID = '$optionID' ";
		$sql6 = "DELETE FROM $tablenameMail WHERE GalleryID = '$optionID' ";
		$sql7 = "DELETE FROM $tablename_options_input WHERE GalleryID = '$optionID' ";
		$sql8 = "DELETE FROM $tablename_form_input WHERE GalleryID = '$optionID' ";
		$sql9 = "DELETE FROM $tablename_form_output WHERE GalleryID = '$optionID' ";*/
		
		
		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename WHERE GalleryID = %d
			", 
				$optionID
		 ));
		 
		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablenameOptions WHERE id = %d
			", 
				$optionID
		 ));
		 
		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablenameEntries WHERE GalleryID = %d
			", 
				$optionID
		 ));
		 
		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablenameComments WHERE GalleryID = %d
			", 
				$optionID
		 ));
		 
		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablenameIp WHERE GalleryID = %d
			", 
				$optionID
		 ));
		 
		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablenameMail WHERE GalleryID = %d
			", 
				$optionID
		 ));
		 
		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_options_input WHERE GalleryID = %d
			", 
				$optionID
		 ));
		 
		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_form_input WHERE GalleryID = %d
			", 
				$optionID
		 ));
		 
		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_form_output WHERE GalleryID = %d
			", 
				$optionID
		 ));

		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_options_visual WHERE GalleryID = %d
			", 
				$optionID
		 ));	

		 		$wpdb->query( $wpdb->prepare(
			"
				DELETE FROM $tablename_email_admin WHERE GalleryID = %d
			", 
				$optionID
		 ));			 

		
		
		/*
		$wpdb->query($sql1);
		$wpdb->query($sql2);
		$wpdb->query($sql3);
		$wpdb->query($sql4);
		$wpdb->query($sql5);
		$wpdb->query($sql6);
		$wpdb->query($sql7);
		$wpdb->query($sql8);
		$wpdb->query($sql9);*/
		
		//$location = admin_url()."?page=contest-gallery/index.php";
		
		//@header("Location: $location");		
		
		}	

?>