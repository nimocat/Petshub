<?php 

//header("Cache-Control: no-cache, must-revalidate");
//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
//header("Content-Type: application/xml; charset=utf-8");
//echo "<b>TEST</b>";
//------------------------------------------------------------
// ----------------------------------------------------------- Userbilder Galerie anzeigen !----------------------------------------------------------
//------------------------------------------------------------


function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val)-1});
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

$unix = time();
$unixadd = $unix+2;


$maxigroesse = return_bytes(ini_get('upload_max_filesize'));

$galeryID = @$_GET['option_id'];

if(@$_FILES['files']['tmp_name']){
foreach(@$_FILES['files']['tmp_name'] as $key => $tmp_name ){

$uploads = wp_upload_dir();
$WPdestination = $uploads['basedir'].'/contest-gallery/gallery-id-'.$galeryID.'/'; // Pfad zum Bilderordner angeben
if (isset($_FILES['files']) && $_FILES['files']['size'] > 0) {
  $tempname = $_FILES['files']['tmp_name'][$key];
  $dateiname = $_FILES['files']['name'][$key];
  //print_r($dateiname);
  $dateiname = strtolower($dateiname);
  //echo "<br>";
  //print_r($dateiname);
  $dateigroesse = $_FILES['files']['size'][$key];
  $dateityp = GetImageSize($tempname);
  
  /*$search = array();
  $search[] =' ';
  $search[] ='!';
  $search[] ='"';
  $search[] ='#';
  $search[] ='$';
  $search[] ='%';
  $search[] ='&';
  $search[] ="'";
  $search[] ='(';
  $search[] =')';
  $search[] ='*';
  $search[] ='+';
  $search[] =',';
  $search[] ='/';
  $search[] =':';
  $search[] =';';
  $search[] ='=';
  $search[] ='?';
  $search[] ='@';
  $search[] ='[';
  $search[] =']';*/
		  $search = array(" ", "!", '"', "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "/", ":", ";", "=", "?", "@", "[", "]", ".");
		  $dateiname = str_replace($search,"_",$dateiname);
		 
		$dateiname = htmlspecialchars($dateiname);
		 
		//print_r($dateiname);
		 
		//$dateiname = str_replace($search,"_",$dateiname);
		
		// ö –> Ã¶, ü –> Ã¼, ä –> Ã¤, Ö –> Ã?, Ü –> Ã?, Ä –> Ã?
		
		$dateiname = str_replace("Ã¶","oe",$dateiname);
		$dateiname = str_replace("Ã¶","oe",$dateiname);
		$dateiname = str_replace("Ã¼","ue",$dateiname);
		$dateiname = str_replace("Ã¤","ae",$dateiname);
		$dateiname = str_replace("Ã?","Oe",$dateiname);
		$dateiname = str_replace("Ã?","Ue",$dateiname);
		$dateiname = str_replace("Ã","Ae",$dateiname);
		
		//print_r($dateiname);
  
  // echo "<br>";
  //print_r($dateiname);
  
  //echo "<br/>$tempname";
  //echo "<br/>$dateiname";
  //echo "<br/>$dateigroesse";
  //echo "<br/>$dateityp";
  //echo "<br/>$dateityp[0]";
  //echo "<br/>$dateityp[1]";
  //echo "<br/>$dateityp[2]";
  
  
  if ($dateityp[2] == 1 || $dateityp[2] == 2 || $dateityp[2] == 3) { // GIF o. JPG?
    if ($dateigroesse <= $maxigroesse) { // Datei zu groß?
	
		
		$checkDataNameEnding = substr($dateiname, -4);	
		
		
		if($checkDataNameEnding=='jpeg'){
		
			$dateiname = substr($dateiname,0,-5);		
			
		}
		
		else{
			
			$dateiname = substr($dateiname,0,-4);	
			
		}
		
		if($dateityp[2] == 1){

			$imageType = 'gif';
			
		}
		
		if($dateityp[2] == 2){

			$imageType = 'jpg';
			
		}
		
		if($dateityp[2] == 3){

			$imageType = 'png';
			
		}
	
//----------------------------Upload file and save in database ---------------->	

      if (move_uploaded_file($tempname, $WPdestination . $unixadd . '_' . $dateiname.".".$imageType)) {
       // echo "<p>Datei wurde <b>erfolgreich</b> hochgeladen!
//Dateigröße: <b>$dateigroesse</b> Byte, 
//Bildname: <b>$dateiname</b><br></p>";

/*	extract( shortcode_atts( array(
		'id' => '1'
	), $atts ) );
$galeryID = $atts['id'];*/

//----------------------------Create Thumbs and Galery pics ---------------->

//global $wpdb;

$tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";

//$picsSQL1 = $wpdb->get_results( "SELECT * FROM $tablenameOptions WHERE id='$galeryID'");

// destination of the uploaded original image
$filename = $WPdestination . $unixadd . '_' . $dateiname.".".$imageType;

require(dirname(__FILE__) . "/../../convert-several-pics.php");


//----------------------------Create Thumbs and Galery pics END ----------------//


$tablename = $wpdb->prefix . "contest_gal1ery";

 
$wpdb->query( $wpdb->prepare(
	"
		INSERT INTO $tablename
		( id, rowid, Timestamp, NamePic,
		ImgType, CountC, CountR, Rating,
		GalleryID, Active, Informed, WpUpload )
		VALUES ( %s,%s,%d,%s,
		%s,%d,%s,%s,
		%d,%s,%s,%s )
	", 
		'','',$unixadd,$dateiname,
		$imageType,0,'','',
		$galeryID,'','',0
 ) );

// Insert Upload Fields for pic if exists

$nextId = $wpdb->get_var("SELECT id FROM $tablename WHERE Timestamp='$unixadd' AND NamePic='$dateiname'");


						$wpdb->update(
						"$tablename",
						array('rowid' => $nextId),
						array('id' => $nextId), 
						array('%d'),
						array('%d')
						);




      } else {
        echo "<p>Upload was not successfull</p>";
      } 
    } else {
      echo "<p>One of the files you have selected is bigger then max alowed <b>$maxigroesse Byte</b></p>";
	  break;
    } 
  } else {
    echo "<p>One the files you have selected is not a JPG, PNG or GIF file</p>";
	break;
  } 
  //echo "<form action='?page_id=30/hochladen.php' method='post'>
//<input type='submit' value='OK'></form>";

} 

}
}


?>



</body>
</html>