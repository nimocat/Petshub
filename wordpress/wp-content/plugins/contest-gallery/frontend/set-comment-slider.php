<?php
/*$start = 0; // Startwert setzen (0 = 1. Zeile)
$step = 4; // Wie viele Eintr�ge gleichzeitig?
// Startwert ver�ndern:
if (isset(@$_GET["start"])) {
  $muster = "/^[0-9]+$/"; // reg. Ausdruck f�r Zahlen
  if (preg_match($muster, @$_GET["start"]) == 0) {
    $start = 0; // Bei Manipulation R�ckfall auf 0
  } else {
    $start = @$_GET["start"]; 
}
$nr = $start + 1;*/

//$start1 = microtime(true);

	// Aurufen von WP-Config hier notwendig
	//require_once("../../../../wp-config.php");

//echo "WORKS!!!";






		//$dataArray = @$_POST['getData']; 
		
?>
<script>
//alert("works");

</script>
<?php
		
		//print_r($dataArray);

		$Name = sanitize_text_field($_REQUEST['action1']); 
		$Comment = sanitize_text_field($_REQUEST['action2']); 
		$Check = sanitize_text_field($_REQUEST['action3']); 
		$Email = sanitize_text_field($_REQUEST['action4']); 
		$Timestamp = intval($_REQUEST['action5']); 
		$pictureID = intval($_REQUEST['action6']); 
		$galeryID = intval($_REQUEST['action7']); 
		$cg_ThankYouComment = $_REQUEST['action8'];		
		
		//$widthCommentArea = $widthCommentArea.'px';

		// User ID �berpr�fung ob es die selbe ist
		$CheckCheck = wp_create_nonce("check");
		//echo "<br>widthCommentArea: $widthCommentArea<br>";
		/*
		echo "<br>Name; $Name<br>";
		echo "<br>Comment; $Comment<br>";
		echo "<br>Check; $Check<br>";
		echo "<br>Email; $Email<br>";
		echo "<br>Timestamp; $Timestamp<br>";
		echo "<br>pictureID; $pictureID<br>";
		echo "<br>galeryID; $galeryID<br>";*/
		//echo "<br>widthCommentArea; $widthCommentArea<br>";
		
		$unix = time();


  // Reaktion auf eingeschaltete magic quotes
  /*if (get_magic_quotes_gpc()) { // eingeschaltet?
    $Name = stripslashes($Name);
    $Comment = stripslashes($Comment);
    $Check = stripslashes($Check);
    $Email = stripslashes($Email);
    $Timestamp = stripslashes($Timestamp);
    //@$_POST["captcha"] = stripslashes(@$_POST["captcha"]);
	//@$_POST["resultat"] = stripslashes(@$_POST["resultat"]);
  }*/
  // Formularwerte escapen und speichern
  
    
  $Name = nl2br(htmlspecialchars($Name));
  $Comment = nl2br(htmlspecialchars($Comment));
  $Check = nl2br(htmlspecialchars($Check));
  $Email = nl2br(htmlspecialchars($Email));
  $Timestamp = nl2br(htmlspecialchars($Timestamp));  
  
  
  $Name = sanitize_text_field($Name);
  $Comment = sanitize_text_field($Comment);
  $Check = sanitize_text_field($Check);
  $Email = sanitize_text_field($Email);
  $Timestamp = sanitize_text_field($Timestamp);
  //$captcha = sanitize_text_field(@$_POST["captcha"]);
  //$resultat = sanitize_text_field(@$_POST["resultat"]);
  
  //echo "Check: $Check;";
   
  $error = false;
  $errortext = "<p>";
  // Eingaben pr�fen und errortext zusammensetzen
  if (empty($Name)) {
    $error = true;
    $errortext .= "<br/<br/><b>Plz fill out the name field. It must contain two characters or more.</b><br>";
  }
  
    if (strlen($Name)<2 and !empty($Name)) {
    $error = true;
    $errortext .= "<br/<br/><b>The name field must contain two characters or more.</b><br>";
  }
  
  if (empty($Comment)) {
    $error = true;
    $errortext .= "<br/<br/><b>Plz fill out the comment field. It must contain three characters or more.</b><br>";
  }
  
   if (strlen($Comment)<3 and !empty($Comment)) {
    $error = true;
    $errortext .= "<br/<br/><b>The comment field must contain three characters or more.</b><br>";
  }
  
       if (!empty($Email)){
    $error = true;
    $errortext .= "<br/<br/><b>Don't fill the email field.</b><br>";
  }
  
    if (!isset($Check)){
    $error = true;
    $errortext .= "<br/<br/><b>You have to check that you are not a robot</b><br>";
  }
  
    else if ($CheckCheck!=$Check){
    $error = true;
    $errortext .= "<br/<br/><b>You are recognized as robot. Plz fill out the form again.</b><br>";
  }  

  
   if ($unix-5 < $Timestamp){
    $error = true;
    $errortext .= "<br/<br/><b>You filled out the form to fast. Plz wait longer then 10 seconds.</b><br>";
  }
  
  
  // errortext ausgeben und Skriptabbruch   
  if ($error) {
    echo "<p>$errortext</p>";  
	
?>

<script>



</script>

<?php
    
  } else {

   
global $wpdb;   
$tablename = $wpdb->prefix . "contest_gal1ery";    
$tablenameComments = $wpdb->prefix . "contest_gal1ery_comments";    


$date = date("Y/m/d, G:i");


		$wpdb->query( $wpdb->prepare(
			"
				INSERT INTO $tablenameComments
				( id, pid, GalleryID, Name, Date, Comment, Timestamp)
				VALUES ( %s,%d,%d,%s,%d,%s,%d )
			", 
				'',$pictureID,$galeryID,$Name,$date,$Comment,$unix
		 ) );




$countC = $wpdb->get_var( $wpdb->prepare( 
	"
		SELECT CountC 
		FROM $tablename
		WHERE id = %d
	", 
	$pictureID
	) );

$newCountC = $countC+1;



				$wpdb->update( 
				"$tablename",
				array('CountC' => $newCountC), 
				array('id' => $pictureID), 
				array('%d'),
				array('%d')
				);



			
?>

<script>
//alert("works");
/*$( "#show-comments" ).empty();
$('#cg-hint-msg').empty();	
$("#show-new-comments").css("display","block");
$("#show-new-comments").css("margin-bottom","50px");*/
//$("#show-new-comments").css("padding-top","50px");

</script>

<?php


    echo "<div id='cg_thank_you_for_your_comment_in_slider' style='color:#fff;font-weight:bold;font-size:18px;'><a href='#example'></a>$cg_ThankYouComment</div>";
	
	
	$setCommentSlider = 1;
	
	require_once("show-comments-slider.php");
	
	
	/*$end1 = microtime(true);

$laufzeit1 = $end1 - $start1;

echo "Laufzeit1: ".$laufzeit1." Sekunden!";*/
	
	  

	
?>

<script>


// hidden value in input field, in show-gallery.php f�r einzelnes Bild generiert, wird gesetzt
var newCountC = <?php echo json_encode($newCountC);?>;	 
var pictureID = <?php echo json_encode($pictureID);?>;
var tagID = 'countCommentsQuantity'+pictureID;

// Klappt nicht. Erreicht nur die im AJAX call response tag kreierten elemente.
var elem = document.getElementById(tagID);
elem.innerHTML = "+newCountC+";

//L�schung der Werte in den Feldern beim erfolgreichen Ausf�llen des Formulars
		/*$("#name").val("");
		$("#comment").val("");
		
					      $('html, body').animate({
        scrollTop: $("#cg-comments-div").offset().top
    }, 400);	*/
		
// Sprung zur Meldung ob Formulardaten erfolgreich �bermittelt wurden oder nicht
		//$('#go-to-comment-success').click();
		
	


	
//location.href = "#";
// Sprung zur Meldung ob Formulardaten erfolgreich �bermittelt wurden oder nicht
//location.href = "#example";

//alert("lala");



		

</script>

<?php	

    
  }
    

?>
