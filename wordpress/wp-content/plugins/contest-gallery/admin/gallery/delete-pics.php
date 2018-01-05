<?php


		$start = 0; // Startwert setzen (0 = 1. Zeile)
	$step =10;

	if (isset($_GET["start"])) {
	  $muster = "/^[0-9]+$/"; // reg. Ausdruck f�r Zahlen
	  if (preg_match($muster, @$_GET["start"]) == 0) {
		$start = 0; // Bei R�ckfall auf 0
	  } else {
		$start = intval(@$_GET["start"]);
	  }
	}
	
	if (isset($_GET["step"])) {
	  $muster = "/^[0-9]+$/"; // reg. Ausdruck f�r Zahlen
	  if (preg_match($muster, @$_GET["start"]) == 0) {
		$step = 10; // Bei Manipulation R�ckfall auf 0
	  } else {
		$step = intval(@$_GET["step"]);
	  }
	}

	global $wpdb;

	$GalleryID = @$_GET['option_id'];


	// Set table names
	$tablename = $wpdb->prefix . "contest_gal1ery";
	$tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";
	$tablenameEntries = $wpdb->prefix . "contest_gal1ery_entries";
	$tablenameComments = $wpdb->prefix . "contest_gal1ery_comments";
	
	// Set table names --- END
	
/*	$imageUnlinkOrigin = @$_POST['imageUnlinkOrigin'];
	$imageUnlink300 = @$_POST['imageUnlink300'];
	$imageUnlink624 = @$_POST['imageUnlink624'];
	$imageUnlink1024 = @$_POST['imageUnlink1024'];
	$imageUnlink1600 = @$_POST['imageUnlink1600'];
	$imageUnlink1920 = @$_POST['imageUnlink1920'];
	$imageFbHTMLUnlink = @$_POST['imageFbHTMLUnlink'];*/
	

	
	
// Pics vom Server l�schen

   // Wordpress Uploadordner bestimmen. Array wird zur�ck gegeben.
		$upload_dir = wp_upload_dir();	


		$deleteId1 = @$_POST['active'];		
	
		foreach($deleteId1 as $key => $value){
			
			
			/*echo '<input type="hidden" disabled name="imageUnlinkOrigin[]" value="/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'.'.$value->ImgType.'" class="image-delete">';
			echo '<input type="hidden" disabled name="imageUnlink300[]" value="/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'-300width.'.$value->ImgType.'" class="image-delete">';
			echo '<input type="hidden" disabled name="imageUnlink624[]" value="/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'-624width.'.$value->ImgType.'" class="image-delete">';
			echo '<input type="hidden" disabled name="imageUnlink1024[]" value="/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'-1024width.'.$value->ImgType.'" class="image-delete">';
			echo '<input type="hidden" disabled name="imageUnlink1600[]" value="/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'-1600width.'.$value->ImgType.'" class="image-delete">';
			echo '<input type="hidden" disabled name="imageUnlink1920[]" value="/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'-1920width.'.$value->ImgType.'" class="image-delete">';
			echo '<input type="hidden" disabled name="imageFbHTMLUnlink[]" value="/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'.html" class="image-delete">';*/
			
			
			@$imageData = $wpdb->get_row( "SELECT * FROM $tablename WHERE id = '$value' ");		
					
			if(file_exists($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic.".".$imageData->ImgType."")){
                @unlink($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic.".".$imageData->ImgType."");
			}
						
			if(file_exists($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-300width.".$imageData->ImgType."")){
                @unlink($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-300width.".$imageData->ImgType."");
			}
						
			if(file_exists($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-624width.".$imageData->ImgType."")){
                @unlink($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-624width.".$imageData->ImgType."");
			}
						
			if(file_exists($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-1024width.".$imageData->ImgType."")){
                @unlink($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-1024width.".$imageData->ImgType."");
			}
						
			if(file_exists($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-1600width.".$imageData->ImgType."")){
                @unlink($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-1600width.".$imageData->ImgType."");
			}
			
			if(file_exists($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-1920width.".$imageData->ImgType."")){
                @unlink($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."-1920width.".$imageData->ImgType."");
			}
			
/*			if(file_exists($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic.".html")){
                @unlink($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic.".html");
			}*/
			
			if(file_exists($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."413.html")){
				@unlink($upload_dir['basedir']."/contest-gallery/gallery-id-".$GalleryID."/".$imageData->Timestamp."_".$imageData->NamePic."413.html");
			}
			
			
					$deleteQuery = 'DELETE FROM ' . $tablename . ' WHERE';			
					$deleteQuery .= ' id = %d';
					
					$deleteEntries = 'DELETE FROM ' . $tablenameEntries . ' WHERE';
					$deleteEntries .= ' pid = %d';
					
					$deleteComments = 'DELETE FROM ' . $tablenameComments . ' WHERE';
					$deleteComments .= ' pid = %d';
					
					$deleteParameters = '';
					$deleteParameters .= $value;

					
					$wpdb->query( $wpdb->prepare(
						"
							$deleteQuery
						", 
							$deleteParameters
					 ));
					 
					 $wpdb->query( $wpdb->prepare(
						"
							$deleteEntries
						", 
							$deleteParameters
					));
		 
					$wpdb->query( $wpdb->prepare(
						"
							$deleteComments
						", 
							$deleteParameters
					 ));
					 
		}
	
	/*
	$deleteQuery = substr($deleteQuery,0,-3);	
	$deleteSQL = $wpdb->query($deleteQuery);
	
	//echo "<br>$deleteQuery<br>";
	
	$deleteEntries = substr($deleteEntries,0,-3);	
	$deleteSQL = $wpdb->query($deleteEntries);
	
	//echo "<br>$deleteEntries<br>";
	
	$deleteComments = substr($deleteComments,0,-3);	
	$deleteSQL = $wpdb->query($deleteComments);*/
	
//	echo "<br>$deleteComments<br>";

	
	
// DATEN L�schen und exit ENDE	

?>