<?php

//$GalleryID = @$_GET['option_id'];
	
			
	echo "<table style='border: 1px solid #DFDFDF;background-color:#ffffff;margin-bottom:12px;' width='937px;'>";
	echo "<tr>";
	echo "<td style='padding-left:20px;width:353px;' colspan='2'>";
	echo "<br/>Allowed file types: <b>Jpg, Png, Gif</b><br/>";
	echo "Actual maximum upload size per file in your PHP configuration: <b>$max_post MB</b><br/>";
	echo "Memory limit provided from your server provider: ";
	if($memory_limit>=250){echo "<span style='color:green;font-weight:bold;'>$memory_limit MB</span>";}
	if($memory_limit<250 && $memory_limit>=120){echo "<span style='color:yellow;font-weight:bold;'>$memory_limit MB</span>";}
	if($memory_limit<120){echo "<span style='color:red;font-weight:bold;'>$memory_limit MB</span>";}
	
	echo "&nbsp;&nbsp;<a id='cg_server_power_info'><b><u>INFO</u></b></a></b>";
	?>
	<div id="cg_answerPNG" style="position: absolute; margin-left: 135px; margin-top: 10px;width: 460px; background-color: white; border: 1px solid; padding: 5px; display: none;">
	Higher memory allows you to upload bigger images with higher resolution.<br>
	If you receive an error during upload like "Allowed memory size of ... exhausted",
	then try to upload same image in minor resolution.<br>
	≈256 MB: good <br>
	≈128 MB: average <br>
	≈64 MB: poor <br></div>
	
	<?php

	
	/*echo <<<HEREDOC
<form action="?page=contest-gallery/index.php&option_id=$GalleryID&edit_gallery=true" method="POST" enctype="multipart/form-data">
<input type="hidden" name="upload_pics" value="true" />
	<input type="file" name="files[]" multiple />
	<input type="submit" value="Upload" style='text-align:center;width:95px;'/><br/><br/>
</form>
HEREDOC;
*/

//add_action( 'admin_enqueue_scripts', 'load_wp_media_files' );
$admin_url = admin_url();

echo '<input type="hidden" id="cg_gallery_id" value="'. $GalleryID .'">';
echo '<input type="hidden" id="cg_admin_url" value="'. $admin_url .'">';
echo "<div style='margin-top: 7px; height:35px;'>";


			if(!get_option("p_cgal1ery_count_uploads")){
				add_option( "p_cgal1ery_count_uploads", 50 );
			}
			
if(strpos(floatval(get_option("p_cgal1ery_reg_code"))/44,".") == false && floatval(get_option("p_cgal1ery_reg_code"))!=0 && floatval(get_option("p_cgal1ery_reg_code"))>=3281329700){
		?>

		<!--<input type="number" value="" class="regular-text process_custom_images" id="process_custom_images" name="" max="10" min="1" step="10">-->
		<div style="display:inline;float:left;width:100px;"><button class="cg_upload_wp_images_button button">Add Images</button></div>
		
		<?php
		
		$plugins_url = plugins_url();
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<img src='".$plugins_url."/contest-gallery/css/loading.gif' width='25px' style='display:none;' id='cg_uploading_gif'/>
		<div style='position:absolute;display:none;vertical-align:middle;height:28px !important;line-height:28px !important;' id='cg_uploading_div'>
		&nbsp;&nbsp;(adding images please wait)</div>";
		
}

else if(get_option("p_cgal1ery_count_uploads")>=1){
	
?>

    <!--<input type="number" value="" class="regular-text process_custom_images" id="process_custom_images" name="" max="10" min="1" step="10">-->
    <div style="display:inline;float:left;width:100px;"><button class="cg_upload_wp_images_button button">Add Images</button></div>
    
<?php

echo '<div style="display:inline;float:left;width:250px;padding-top:5px;"  id="cg_uploading_count"><b>Light version:</b> '.get_option("p_cgal1ery_count_uploads").' Uploads left <br>
<a href="https://www.contest-gallery.com/pro-version/" title="Get PRO version for unlimited uploads" target="_blank">PRO version</a>
</div>';

$plugins_url = plugins_url();
echo "&nbsp;&nbsp;&nbsp;&nbsp;<img src='".$plugins_url."/contest-gallery/css/loading.gif' width='25px' style='display:none;' id='cg_uploading_gif'/>
<div style='position:absolute;display:none;vertical-align:middle;height:28px !important;line-height:28px !important;' id='cg_uploading_div'>
&nbsp;&nbsp;(adding images please wait)</div>";	
}
else if(get_option("p_cgal1ery_count_uploads")<1){
	echo "<a href='https://www.contest-gallery.com/pro-version/' title='Get PRO and upload unlimited amount of images' target='_blank'>Get PRO version to upload unlimited amount of images</a>";
}
else{
	
	echo '';
	
}

echo "</div>";	

echo "<div style='display:none;' id='cg_wp_upload_ids'></div>";
echo "<div id='cg_wp_upload_div'></div>";

	echo "<div style='margin-bottom:15px;margin-top:15px;clear:both;'>What happens when adding images?&nbsp;<a id='cg_adding_images_info'><b><u>Read here...</u></b></a></b>";
	?>
	<div id="cg_adding_images_answer" style="position: absolute; margin-left: 40px; margin-top: 10px;width: 510px; background-color: white; border: 1px solid; padding: 5px; display: none;z-index:500;">
	Every image will be converted to five different resolutions. From 300pixel to 1920pixel width.
	<br>Depending on screen width a suitable image will be selected by algorithm.
	<br>It brings faster loading performance for frontend users viewing your gallery.
	<br><br>Converting images can take some time, especially for images higher then 3MB.
	<br>In general it is recommended not to add more then 10 images at one go. </div>
	
	<?php
	echo "</div>";


	echo "</td>";	
	
	echo "<td align='center'><div>";
	
//	print_r( @$_POST['informId']);
	
	//echo "teasdtfsdf";
	
		//print_r(@$_POST['create_data_csv']);
		//echo @$_POST['create_data_csv'];
		//echo @$_POST['create_zip'];
	
	
	//echo "works0";	
	
	
	
if(@$_POST['create_data_csv']  or (@$_POST['chooseAction1'] == 4 and (@$_POST['informId']==true or @$_POST['resetInformId']==true))){
	//echo "works";	

//print_r($selectContentFieldArray);

$getFormFieldNames = 0;

	$selectSQLall = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID = '$GalleryID' ORDER BY rowid DESC");
	
		$csvData = array();
		
		$i=0;
		$r=0;
		$n=0;
		
		$GalleryID1="GalleryID";
		$id1="id";//ACHTUNG! Darf nicht Anfangen mit ID(Großgeschrieben I oder D am Anfang) in einer csv Datei, ansonsten ungültige SYLK Datei!
		$rowid1="rowid";
		$UploadDate1="UploadDate";
		$NamePic1="NamePic";
		$DownloadURL1="DownloadURL";
		$CountComments1="CountComments";
		$CountRatingOneStar ="CountRatingOneStar";
		$CountRatingFiveStars="CountRatingFiveStars";
		$CumulatedRatingFiveStars="CumulatedRatingFiveStars";
		$AvarageRating1="AvarageRating";
		$Active1="Active";
		$Informed1="Informed";
		
			$csvData[$i][$r]=$GalleryID1;
			$r++;		
			$csvData[$i][$r]=$id1;
			$r++;
			$csvData[$i][$r]=$rowid1;
			$r++;				
			$csvData[$i][$r]=$UploadDate1;	
			$r++;
			$csvData[$i][$r]=$NamePic1;
			$r++;
			$csvData[$i][$r]=$DownloadURL1;
			$r++;
			$csvData[$i][$r]=$CountComments1;
			$r++;
			$csvData[$i][$r]=$CountRatingOneStar;
			$r++;
			$csvData[$i][$r]=$CountRatingFiveStars;
			$r++;
			$csvData[$i][$r]=$CumulatedRatingFiveStars;
			$r++;
			$csvData[$i][$r]=$AvarageRating1;
			$r++;			
			$csvData[$i][$r]=$Active1;
			$r++;
			$csvData[$i][$r]=$Informed1;	
			$r++;
			
		
		
		
		//Bestimmung der Spalten Namen
		
	
	
	if($n==0){
		
			foreach($selectContentFieldArray as $key => $formvalue){
		
			//	echo "<br>$i<br>";
			
				// 1. Feld Typ
				// 2. ID des Feldes in F_INPUT
				// 3. Feld Reihenfolge
				// 4. Feld Content 
				
				
										
				if(@$formvalue=='text-f'){$fieldtype="nf"; $n=1; continue;}	
				if(@$fieldtype=="nf" AND $n==1){$formFieldId=$formvalue; $n=2; continue;}	
				if(@$fieldtype=="nf" AND $n==2){$fieldOrder=$formvalue; $n=3; continue;}	
				if (@$fieldtype=="nf" AND $n==3) {

				$csvData[$i][$r]="$formvalue";	
				$r++;
				
										/*	$getEntries = $wpdb->get_var( $wpdb->prepare(
							"
								SELECT Short_Text
								FROM $tablenameentries 
								WHERE pid = %d and f_input_id = %d
							", 
							$id,$formFieldId
							) );*/
				
				$n=0;
				
				}
				
				if(@$formvalue=='email-f'){$fieldtype="ef";  $n=1; continue;}	
				if(@$fieldtype=="ef" AND $n==1){$formFieldId=$formvalue; $n=2; continue;}	
				if(@$fieldtype=="ef" AND $n==2){$fieldOrder=$formvalue; $n=3; continue;}	
				if (@$fieldtype=='ef' AND $n==3) {

				$csvData[$i][$r]="$formvalue";				
				$r++;
				$n=0;
				}
				
				if(@$formvalue=='comment-f'){$fieldtype="kf"; $n=1; continue;}	
				if(@$fieldtype=="kf" AND $n==1){$formFieldId=$formvalue; $n=2; continue;}	
				if(@$fieldtype=="kf" AND $n==2){$fieldOrder=$formvalue; $n=3; continue;}	
				if (@$fieldtype=='kf' AND $n==3) {

				$csvData[$i][$r]="$formvalue";
				$r++;
				$n=0;
				}

										
			}
		
		}
		
	$getFormFieldNames++;
		// Bestimmung der Feld-Inhalte
	
		$i++;
		foreach($selectSQLall as $value){
			
			$csvData[$i][$r]=$value->GalleryID;	
			$r++;			
			$csvData[$i][$r]=$value->id;
			$pidCSV=$value->id;
			$r++;	
			$csvData[$i][$r]=$value->rowid;
			$r++;	
			$uploadTime = date('m.d.Y H:i', $value->Timestamp);
			$csvData[$i][$r]=$uploadTime;			
			$r++;
			$csvData[$i][$r]=$value->NamePic;
			$r++;
			if($value->WpUpload!=NULL && $value->WpUpload>0){		
				$csvData[$i][$r]=$wpdb->get_var("SELECT guid FROM $table_posts WHERE ID = '".$value->WpUpload."'");
			}
			else{
				$csvData[$i][$r]=''.$content_url.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'.'.$value->ImgType.'';
			}
			$r++;
			$csvData[$i][$r]=$value->CountC;
			$r++;
			$csvData[$i][$r]=$value->CountS;
			$r++;
			$csvData[$i][$r]=$value->CountR;
			$r++;
			$csvData[$i][$r]=$value->Rating;
			$r++;		
			@$averageStars = $value->Rating/$value->CountR;
			@$averageStarsRounded = round($averageStars,0);
			$csvData[$i][$r]=@$averageStars;
			$r++;

			$csvData[$i][$r]=$value->Active;
			$r++;
			$csvData[$i][$r]=$value->Informed;	
			$r++;
			
			$selectSQLentries = $wpdb->get_results( "SELECT * FROM $tablenameentries WHERE pid = '$pidCSV' ORDER BY Field_Order ASC");
			
				foreach($selectSQLentries as $value_entries){
					
				$fieldType = $value_entries->Field_Type;
			//	echo $value_entries->Short_Text;
				
				
				if($fieldType=="comment-f"){$csvData[$i][$r]=$value_entries->Long_Text;}
				else{$csvData[$i][$r]=$value_entries->Short_Text;}
				$r++;				
					
				}					
			$i++;
			$r=0;			
		}
		
	//	print_r($csvData);
		
	/*	$list = array (
    array('aaa', 'bbb', 'ccc'),
    array('123', '456', '789')
 
);*/

//print chr(255) . chr(254) . mb_convert_encoding($list, 'UTF-16LE', 'UTF-8');

		$admin_email = get_option('admin_email');
		$adminHashedPass = $wpdb->get_var("SELECT user_pass FROM $wpUsers WHERE user_email = '$admin_email'");

	 $code = $wpdb->base_prefix; // database prefix
	 $code = md5($code.$adminHashedPass);
	 
	$dir = plugin_dir_path( __FILE__ );
	$dir = $dir.$code."_userdata.csv";
	//echo "$dir";
	//chmod($dir,0644);
		$fp = fopen($dir, 'w');
fputs($fp, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
		foreach ($csvData as $fields) {
    fputcsv($fp, $fields, ";");
}

		fclose($fp);
//$bloginfo = bloginfo("language");

     //$code = $wpdb->prefix; // database prefix
	// $code = md5($code);
/*
	if (file_exists($dir)) {
	unlink($dir);
	}*/
	
	$userDataCSVsource = plugins_url( '/'.$code.'_userdata.csv', __FILE__ );
	

	
	//cg_action_create_zip($allPics,''.$pfad.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$code.'_images_download.zip');	
	echo '<p style="text-align:center;width:180px;"><a href="'.$userDataCSVsource.'">Download csv file</a></p>';
	echo '<p style="text-align:center;width:180px;"><a href="?page=contest-gallery/index.php&option_id='.$GalleryID.'&delete_data_csv=true&edit_gallery=true">Delete csv file</a></p>';

		
	}
	
	else if (@$_POST['create_zip']==true or (@$_POST['chooseAction1'] == 4 and (@$_POST['informId']==true or @$_POST['resetInformId']==true))) {
	
	//echo "actions";
	
	$allPics=array();
	//$pfad = $_SERVER['DOCUMENT_ROOT'];
	$uploadFolder = wp_upload_dir();
	
	$pfad = $uploadFolder['basedir'];
	$pfad1 = $uploadFolder['url'];
	$baseurl = $uploadFolder['baseurl'];// Achtung! Unterschied zum pfad1 oben
	
	
		if(@$_POST['create_zip']==true){
		
		$selectSQLall = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID = '$GalleryID'");
		
		//print_r($selectSQLall);
		
			foreach($selectSQLall as $value){
				
				if($value->WpUpload!=NULL && $value->WpUpload>0){		
					$image_url = $wpdb->get_var("SELECT guid FROM $table_posts WHERE ID = '".$value->WpUpload."'");
					$check = explode($baseurl,$image_url);
					$dl_image_original = $pfad.$check[1];
				}
				else{
					$dl_image_original = $pfad.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'.'.$value->ImgType.'';						
				}
			
			
			//$imageGalery = $pfad.'/wp-content/uploads/contest-gallery/'.$value->ImageGalery;	
			//$imageThumb = $pfad.'/wp-content/uploads/contest-gallery/'.$value->ImageThumb;		
			$allPics[] = $dl_image_original;
			//$allPics[] = $imageGalery;
			//$allPics[] = $imageThumb;
			
			}
			//print_r($allPics);
		}
		
	
		
		
		if(@$_POST['chooseAction1'] == 4 and (@$_POST['informId']==true or @$_POST['resetInformId'])){
		
		//echo "2131242131243";
		
		$informId = @$_POST['informId'];
		$resetInformId = @$_POST['resetInformId'];
		
		$selectPICS = "SELECT * FROM $tablename WHERE ";
		
		//$wpdb->get_results( );
		
			foreach(@$informId as $key => $value){
			
			$selectPICS .= "id=$value or ";	
			
			}	
			
			foreach(@$resetInformId as $key => $value){
			
			$selectPICS .= "id=$value or ";	
			
			}	
		
			$selectPICS = substr($selectPICS,0,-4);
			
			//print_r($selectPICS);
			
			$selectPICSzip = $wpdb->get_results("$selectPICS");
		
			foreach($selectPICSzip as $value){
			$dl_image_original = $pfad.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$value->Timestamp.'_'.$value->NamePic.'.'.$value->ImgType.'';
			//$imageGalery = $pfad.'/wp-content/uploads/contest-gallery/'.$value->ImageGalery;	
			//$imageThumb = $pfad.'/wp-content/uploads/contest-gallery/'.$value->ImageThumb;		
			$allPics[] = $dl_image_original;
			//$allPics[] = $imageGalery;
			//$allPics[] = $imageThumb;
			}
					
		}
	
	 
	$admin_email = get_option('admin_email');
	$adminHashedPass = $wpdb->get_var("SELECT user_pass FROM $wpUsers WHERE user_email = '$admin_email'");	

	 $code = $wpdb->base_prefix; // database prefix
	 $code = md5($code.$adminHashedPass);

	if (file_exists(''.$pfad.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$code.'_images_download.zip')) {
	@unlink(''.$pfad.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$code.'_images_download.zip');
	}
	if(cg_action_create_zip($allPics,''.$pfad.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$code.'_images_download.zip')==false){
		die;
	}
	else{
		cg_action_create_zip($allPics,''.$pfad.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$code.'_images_download.zip');
	}
	echo '<p style="text-align:center;width:180px;"><a href="'.$pfad1.'/../../contest-gallery/gallery-id-'.$GalleryID.'/'.$code.'_images_download.zip">Download zip file</a></p>';
	echo '<p style="text-align:center;width:180px;"><a href="?page=contest-gallery/index.php&option_id='.$GalleryID.'&delete_zip=true&edit_gallery=true">Delete zip file</a></p>';
	
	}
	
	else {
	
	if(@$_GET['delete_zip']==true){
	$admin_email = get_option('admin_email');
	$adminHashedPass = $wpdb->get_var("SELECT user_pass FROM $wpUsers WHERE user_email = '$admin_email'");	
		
	$code = $wpdb->base_prefix; // database prefix
	$code = md5($code.$adminHashedPass);
	$uploadFolder = wp_upload_dir();	
	$pfad = $uploadFolder['basedir'];
	@unlink(''.$pfad.'/contest-gallery/gallery-id-'.$GalleryID.'/'.$code.'_images_download.zip');
	?><script>alert('Zip file deleted.');</script><?php
	}
	
	if(@$_GET['delete_data_csv']==true){
	$admin_email = get_option('admin_email');
	$adminHashedPass = $wpdb->get_var("SELECT user_pass FROM $wpUsers WHERE user_email = '$admin_email'");	
	$code = $wpdb->base_prefix; // database prefix
	$code = md5($code.$adminHashedPass);
	@$dir = plugin_dir_path( __FILE__ );
	@$dir = $dir.$code."_userdata.csv";	
	unlink(@$dir);
	?><script>alert('CSV data file deleted.');</script><?php
	}
	
	echo "<form method='POST' action='?page=contest-gallery/index.php&option_id=$GalleryID&step=$step&start=$start&edit_gallery=true'><input type='hidden' name='create_zip' value='true' /><input type='submit' value='Zip all pictures' style='text-align:center;width:180px;' /></form></a>";
	echo "<br/>";
	echo "<form method='POST' action='?page=contest-gallery/index.php&option_id=$GalleryID&step=$step&start=$start&edit_gallery=true'><input type='hidden' name='create_data_csv' value='true' /><input type='submit' value='Create data CSV' style='text-align:center;width:180px;' /></form></a>";
	}
	echo "</div></td>";
	
	echo "<td align='center'><div>";

	echo "<form method='POST' action='?page=contest-gallery/index.php&option_id=$GalleryID&step=$step&start=$start&edit_gallery=true'><input type='submit' value='Reset all informed' style='text-align:center;width:180px;'/>";
	echo "<input type='hidden'  name='reset_all' value='true'>";
	echo "</form></a>";

	echo "</div></td>";
	
	
	echo "</tr>";
	
	echo "</table>";	

	///////////// SHOW Pictures of certain galery


		


?>