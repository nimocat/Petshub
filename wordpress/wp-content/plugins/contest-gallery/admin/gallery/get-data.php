<?php
/*error_reporting(E_ALL); 
ini_set('display_errors', 'On');


AUFBAU

*/





	$start = 0; // Startwert setzen (0 = 1. Zeile)
	$step =10;

	if (isset($_GET["start"])) {
	  $muster = "/^[0-9]+$/"; // reg. Ausdruck f�r Zahlen
	  if (preg_match($muster, @$_GET["start"]) == 0) {
		$start = 0; // Bei Manipulation R�ckfall auf 0
	  } else {
		$start = @$_GET["start"];
	  }
	}
	
	if (isset($_GET["step"])) {
	  $muster = "/^[0-9]+$/"; // reg. Ausdruck f�r Zahlen
	  if (preg_match($muster, @$_GET["start"]) == 0) {
		$step = 10; // Bei Manipulation R�ckfall auf 0
	  } else {
		$step = @$_GET["step"];
	  }
	}


    $GalleryID = @$_GET['option_id'];




	// Tabellennamen ermitteln, GalleryID wurde als Shortcode bereits �bermittelt.
	global $wpdb;

	$tablename = $wpdb->prefix . "contest_gal1ery";	
	$tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";
	//$tablenameVisualOptions = $wpdb->prefix . "contest_gal1ery_options_visual";
	$tablenameComments = $wpdb->prefix . "contest_gal1ery_comments";
	$tablenameentries = $wpdb->prefix . "contest_gal1ery_entries";
	$tablename_f_input = $wpdb->prefix . "contest_gal1ery_f_input";
	$table_posts = $wpdb->prefix."posts";
	$wpUsers = $wpdb->base_prefix . "users";
	
	// Reset Informed
	
	// Reset von allen Informed
	if(@$_GET['resetInformed']=='true'){
	
		//echo "<br>resetInformed: $resetInformed<br>";
		
		//$querySEToptions = "UPDATE $tablename SET Informed='0' WHERE Informed = '1' AND GalleryID = '$GalleryID' ";
		//$updateSQL = $wpdb->query($querySEToptions);
		
						$wpdb->update( 
						"$tablename",
						array('Informed' => '0'), 
						array(
						'id' => '1',
						'GalleryID' => "$GalleryID"
						), 
						array('%d'),
						array('%d','%d')
						);

	}

	// Reset Informed --- ENDE
	
	
	
	
	
	//echo "$GalleryID";
	
	/*$selectSQL = $wpdb->get_results("SELECT $tablename.id,  $tablename.rowid,  $tablename.Timestamp,  $tablename.NamePic, $tablename.CountC,  $tablename.Rating,  $tablename.CountR,  $tablename.CountS,  $tablename.WpUpload,  $tablename.WpUserId
		FROM $tablename, $wpUsers WHERE $tablename.WpUserId = $wpUsers.ID 
		AND ($wpUsers.user_login LIKE '%".@$_POST['cg-user-name']."%' or $wpUsers.user_email LIKE '%".@$_POST['cg-user-name']."%')";*/

	$selectSQL = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID = '$GalleryID' ORDER BY rowid DESC LIMIT $start, $step " );
	
	$countSelectSQL = count($selectSQL);
	
	$optionsSQL = $wpdb->get_row( "SELECT * FROM $tablenameOptions WHERE id = '$GalleryID'" );
	
	//$visualOptionsSQL = $wpdb->get_row( "SELECT * FROM $tablenameVisualOptions WHERE GalleryID = '$GalleryID'" );
	

	//$rows = $wpdb->get_var( "SELECT COUNT(*) AS NumberOfRows FROM $tablename WHERE GalleryID='$GalleryID'");
	
	$rows = $wpdb->get_var( $wpdb->prepare( 
	"
		SELECT COUNT(*) AS NumberOfRows
		FROM $tablename 
		WHERE GalleryID = %d
	",
	$GalleryID
	) );
	
	// Pr�fen ob ein bestimmtes input Feld als Forward URL dienen soll
	
	
	
	@$Use_as_URL_id = $wpdb->get_var( "SELECT id FROM $tablename_f_input WHERE GalleryID = '$GalleryID' AND Use_as_URL = '1' ");
	
	
	
	// Pr�fen ob ein bestimmtes input Feld als Forward URL dienen soll --- ENDE
	
	
	
	
//	@$selectFormInput = $wpdb->get_results( "SELECT id, Show_Slider FROM $tablename_f_input WHERE GalleryID = '$GalleryID' AND Show_Slider = '1' ");
	
	
	// Selected Slider Fields bestimmen
	
	
	@$selectFormInput = $wpdb->get_results( "SELECT id, Show_Slider FROM $tablename_f_input WHERE GalleryID = '$GalleryID' AND Show_Slider = '1' ");
	
			
		@$ShowSliderInputID = array();
		
		$ShowSliderInputID[] = 0;
	
		foreach (@$selectFormInput as $value) {
	
	//$ShowSlider = 	$value->Show_Slider;
	@$ShowSliderInputID[] = $value->id;
		
		}
		
		//print_r($ShowSliderInputID);
		
		//echo "lol";
	
	// Selected Fields bestimmen -- ENDE
	
	//$optionsSQL = $wpdb->get_row( "SELECT * FROM $tablename_f_input WHERE id = '$GalleryID'" );
	
	$selectFormInput = $wpdb->get_results( "SELECT id, Field_Type, Field_Order, Field_Content FROM $tablename_f_input WHERE GalleryID = '$GalleryID' AND (Field_Type = 'text-f' OR Field_Type = 'comment-f' OR Field_Type ='email-f' OR Field_Type ='select-f') ORDER BY Field_Order ASC" );
	
	$checkInformed = $wpdb->get_results("SELECT 1 FROM $tablename WHERE GalleryID = '$GalleryID' AND Informed = '1' LIMIT 1");
	
	// Die Field_Content Felder werden jetzt schon usnerialized und in einem Array gespeichert um weniger Load zu erzeugen
	

	$selectContentFieldArray = array();
	
	foreach ($selectFormInput as $value) {
	
	// 1. Feld Typ
	// 2. ID des Feldes in F_INPUT
	// 3. Feld Reihenfolge
	// 4. Feld Content

	$selectFieldType = 	$value->Field_Type;
	$id = $value->id;// prim�re unique id der form wird auch gespeichert und sp�ter in entries inserted zur erkennung des verwendeten formular feldes
	$fieldOrder = $value->Field_Order;// Die originale Field order in f_input Tabelle. Zwecks Vereinheitlichung.
	$selectContentFieldArray[] = $selectFieldType;
	$selectContentFieldArray[] = $id;
	$selectContentFieldArray[] = $fieldOrder;
	$selectContentField = unserialize($value->Field_Content);
	$selectContentFieldArray[] = $selectContentField["titel"];
	
	}
	
	//print_r($optionsSQL);
	
	
		// ------------ Ermitteln der Options-Daten
		
		$RowLook = $optionsSQL->RowLook; // Bilder in einer Reiehe nacheinander anzeigen oder nicht 
		$LastRow = $optionsSQL->LastRow; // Wenn $RowLook an dann wie viele Bilder sollen in letzter Spalte gezeigt werden
		//$PicsPerSite = $optionsSQL->PicsPerSite; // Wie viele Bilder sollen insgesamt  gezeigt werden --- UPDATE: Wird bereits weiter oben oder bei get-data-1.php ermittelt
		$PicsInRow = $optionsSQL->PicsInRow; // Wie viele Bilder in einer Reiehe sollen gezeigt werden
		$GalleryName = $optionsSQL->GalleryName;
		$WidthGalery = $optionsSQL->WidthGallery;
		$HeightGalery = $optionsSQL->HeightGallery;
		$DistancePics = $optionsSQL->DistancePics;
		$DistancePicsV = $optionsSQL->DistancePicsV;
		$AllowComments = $optionsSQL->AllowComments;
		$AllowRating = $optionsSQL->AllowRating;
		@$ScalePics = $optionsSQL->ScalePics;
		$ScaleAndCut = $optionsSQL->ScaleAndCut;
		$AllowGalleryScript = $optionsSQL->AllowGalleryScript;  
		$ThumbsInRow = $optionsSQL->ThumbsInRow; // Anzahl der Bilder in einer Reihe bei Auswahl von ReihenAnsicht (Semi-Flickr-Ansicht)
		$ThumbLook = $optionsSQL->ThumbLook;
		$WidthThumb = $optionsSQL->WidthThumb; // Breite der ThumbBilder (Normale Ansicht mit Bewertung etc.)
		$HeightThumb = $optionsSQL->HeightThumb;  // H�he der ThumbBilder (Normale Ansicht mit Bewertung etc.)
		$LastRow = $optionsSQL->LastRow;
		$FullSize = $optionsSQL->FullSize;
		$IpBlock = $optionsSQL->IpBlock;
		$FbLike = $optionsSQL->FbLike;
		$ScaleOnly = $optionsSQL->ScaleOnly;
		@$JqgGalery = $optionsSQL->JqgGalery;
		$Inform = $optionsSQL->Inform;
		$ForwardToURL = $optionsSQL->ForwardToURL;
		$ForwardType = $optionsSQL->ForwardType;
	
		// Notwendig um sp�ter die star Icons anzuzeigen
		$iconsURL = content_url().'/plugins/contest-gallery/css';
		$titleIcon = $iconsURL.'/backend/img-title-icon.png';
		$descriptionIcon = $iconsURL.'/backend/img-description-icon.png';
		$excerptIcon = $iconsURL.'/backend/img-excerpt-icon.png';

		$WidthThumb = $WidthThumb.'px';// Breite Thumb mit px f�r Heredoc
		$HeightThumb = $HeightThumb.'px';// H�he Thumb mit px f�r Heredoc
		$DistancePics = $DistancePics.'px'; // Abstand der Thumbs Breite mit px f�r Heredoc
		$DistancePicsV = $DistancePicsV.'px'; // Abstand der Thumbs H�he mit px f�r Heredoc
		
				// Ermitteln ob checked oder nicht
				
				$selectedCheckComments = ($AllowComments==1) ? 'checked' : '';
				$selectedCheckRating = ($AllowRating==1) ? 'checked' : '';
				$selectedCheckIp = ($IpBlock==1) ? 'checked' : '';
				$selectedCheckFb = ($FbLike==1) ? 'checked' : '';
				$selectedCheckScalePics = ($ScalePics==1) ? 'checked' : '';
				//$selectedCheckPicUpload = ($value->PicUpload==1) ? 'checked' : '';
				//$selectedCheckSendEmail = ($value->SendEmail==1) ? 'checked' : '';
				//$selectedSendName = ($value->SendName==1) ? 'checked' : '';
				//$selectedCheckSendComment = ($value->SendComment==1) ? 'checked' : '';
				//$AllowGalleryScript = ($AllowGalleryScript==1) ? 'checked' : '';

 				
		$urlSource = site_url();		
				
				
				
				
				
				
				
	

// ----------  Auswahl aufsteigend oder absteigend
	
	/*if (@$_POST['dauswahl']==false) {

	$galeryrow = $wpdb->get_row( "SELECT * FROM $tablenameOptions WHERE id = '$GalleryID'" );

	$orderpicsdesc = ($galeryrow->OrderPics == 0) ? 'selected' : '';
	$orderpicsasc = ($galeryrow->OrderPics == 1) ? 'selected' : '';

	}


		// Show choice desc or asc
		
		if (@@$_POST['dauswahl'] == "dab" OR @@$_GET['dauswahl'] == "dab") {
		$turn = 'DESC';
		$turn1 = 'dab';
		$orderpicsdesc = 'selected';
		}

		echo @$_POST['dauswahl'];
		
		if (@@$_POST['dauswahl'] == "dauf"  OR @@$_GET['dauswahl'] == "dauf") {
		$turn = 'ASC';
		$turn1 = 'dauf';
		$orderpicsasc = 'selected';
		}

		else {
		$turn = 'DESC';
		} */
		
// Auswahl aufsteigend oder absteigend ----------- ENDE

	
		//// Config how many pics should be shown at one time 
	
		$i=0;	
		$nr1 = $start + 1;			
				
				
				
	// Configuration link urls ----->
	
	//$content_url  = content_url();
	
	$content_url = wp_upload_dir();
	$content_url = $content_url['baseurl']; // Pfad zum Bilderordner angeben
	
	$pathPlugin = plugins_url();
	$pageURL = 'http';
	if (@$_SERVER["HTTPS"] == "on") {@$pageURL .= "s";}
	@$pageURL .= "://";
	
	$path = $_SERVER['REQUEST_URI'];
	
	$host = $_SERVER['HTTP_HOST'];
	
	/*echo "<br/>";
	echo "$path";
	echo "<br/>";
	echo "$host";
	echo "<br/>";*/
	
	$siteUrlOff = (strpos($path,'?')) ? $host.$path.'&' : $host.$path.'?';
	
	// echo "<b>$siteUrlOff</b>";
	
	$siteURL = $pageURL.$siteUrlOff;
	
	//echo $siteURL;
	
	// Wenn der zweite Teil des Explodes existiert, dann die URL wieder zur�ck machen
	
	$siteURL = (strpos($siteURL,'&&')) ? str_replace("&&", "&","$siteURL") : $siteURL;
	
	$explode = explode('&',$siteURL);
	
	$siteURLdauswahl = ($explode[2]) ? $explode[0].'&'. $explode[1].'&'.'dauswahl' : $siteURL.'dauswahl';
	
	//echo "$siteURLdauswahl";
	
	// Configuration link urls -----> END
	

	
    	// Ermitteln der Options-Daten ---------------- ENDE
		
		
		
		

		
			// Determine values of Options Table>>>>END
			
		// Determine name fields names of upload Form
		
		//$i=0;
		
		/*echo "<br/>";
		print_r($defineUpload);	
		echo "<br/>";		
		
		foreach ($defineUpload as $variant => $value) {

		if ($value=='nf' AND $i==0) {$i++;continue;}
		if ($i==1) {$name1uploadForm = $value;$i++;continue;}
		
		if ($value=='nf' AND $i==2) {$i++;continue;}
		if ($i==3) {$name2uploadForm = $value;$i++;continue;}
		
		if ($value=='nf' AND $i==4) {$i++;continue;}
		if ($i==5) {$name3uploadForm = $value;$i++;continue;}		
			
		}
		
		
		// Checken ob Kommentar oder E-Mail Feld vorhanden sind
		
		$keysDuKf = @array_keys($defineUpload,'kf',true);
		
		if ($keysDuKf) {
		
		$keysDuKf[0]++;
		$keysDuKf1 = $keysDuKf[0];
		
		echo "<br/>";
		echo print_r($keysDuKf);
		echo "<br/>";
		
		}

		
		$keysDuEf = @array_keys($defineUpload,'ef',true);
		
		if ($keysDuEf) {
		
		$keysDuEf[0]++;
		$keysDuEf1 = $keysDuEf[0];
		
		}
		

		$kFtitle = ($keysDuKf) ? "$defineUpload[$keysDuKf1]": "";
		$eFtitle = ($keysDuEf) ? "$defineUpload[$keysDuEf1]": "";
		
		// Checken ob Kommentar oder E-Mail Feld vorhanden sind --- ENDE
	
		
		

		echo "<br/>Name1: ";
		echo $name1uploadForm;
		echo "<br/>Name2: ";
		echo $name2uploadForm;
		echo "<br/>Name3: ";
		echo $name3uploadForm;
		echo "<br/>";*/
		

		
		
		
		// Determine name fields names of upload Form --- END
			
			// Determine if User should be informed or not
	
		//	$disabledInform = ($Inform==0) ? 'disabled' : '';
			
			// Determine if User should be informed or not END
	

			/* creates a compressed zip file */
			if(!function_exists('cg_action_create_zip')){	
function cg_action_create_zip($files = array(),$destination = '',$overwrite = false) {
	
	if(!class_exists('ZipArchive')){
		echo "The Zip extension for php is not installed on your server. Please contact your server provider in order to install you the Zip extension for php.";
		return false;
	}
	
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {

            if (file_exists($file) && is_file($file)){
                $zip->addFile($file, $file);
            }
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

			}

//echo $rows;

			// rausfinden wie viel Mega-/Kilobyte hochgeladen werden k�nnen und es anzeigen lassen

				
			/*	function return_bytes1($val) {
				$val = trim($val);
				$last = strtolower($val[strlen($val)-1]);
				switch($last) {
					// The 'G' modifier is available since PHP 5.1.0
					case 'g':
						$val *= 1024;
					case 'm':
						$val *= 1024;
					case 'k':
						$val *= 1024;
				}

				return $val;
			}

			$maxsize = return_bytes1(ini_get('post_max_size'));

			function formatBytes1($bytes, $precision = 2) { 
				$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

				$bytes = max($bytes, 0); 
				$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
				$pow = min($pow, count($units) - 1); 

				$bytes /= pow(1024, $pow); 

				return round($bytes, $precision) . ' ' . $units[$pow]; 
			}

			$maxsizeConverted = formatBytes1($maxsize,2);
			
			
			
			echo formatBytes1(memory_get_peak_usage(),2);
			
			echo "<br/>";
			
			echo formatBytes1(memory_get_usage(),2);
			echo "<br/>";

			

			
			echo "<br/>";*/

			// rausfinden wie viel Mega-/Kilobyte hochgeladen werden k�nnen und es anzeigen lassen ---- ENDE
	
	// Maximal m�glich eingestellter Upload wird ermittelt
	$max_post = (int)(ini_get('upload_max_filesize'));
	$memory_limit = (int)(ini_get('memory_limit'));
	
	

	
	
?>