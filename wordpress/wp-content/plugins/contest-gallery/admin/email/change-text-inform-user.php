<?php


?>
<style>
.neweditor{
width:600px;
border: 8px solid;
margin-left: 100px;
}
</style>
<?php

add_action('admin_head', 'cg_css_new_editor_change_text_inform_user');

function cg_css_new_editor_change_text_inform_user() {
  echo '<style>
.neweditor{
width:700px; // What ever size you want
border: 8px solid;
margin-left: 100px;
}


 </style>';}


function cg_css_change_text_inform_user() {
//wp_enqueue_script( 'jquery' );
wp_register_style( 'contest-style', plugins_url('css/style.css', __FILE__) );
wp_enqueue_style( 'contest-style' );
}
add_action('wp_enqueue_scripts','cg_css_change_text_inform_user');

	//Check all GET Variables
	
		if (isset($_GET["inform_user"])) {
		  	$muster = "/^[a-zA-Z0-9-_.]+$/"; // reg. Ausdruck f�r Zahlen und W�rter
		  if (preg_match($muster, @$_GET["inform_user"]) == 0 AND @$_GET["inform_user"] != 'true') {
			die('Manipulieren Sie die URL nicht!');
		  } else {
			$informuser = @$_GET["inform_user"];
		  }
		}
		
		if (isset($_GET["option_id"])) {
		  $muster = "/^[0-9]+$/"; // reg. Ausdruck f�r Zahlen
		  if (preg_match($muster, @$_GET["option_id"]) == 0) {
			die('Manipulieren Sie die URL nicht!');
		  } else {
			$GalleryID = intval(@$_GET["option_id"]);
		  }
		}
		
	global $wpdb;
	  
	$option_id = @$_GET["option_id"];
	
	$tablenameemail = $wpdb->prefix . "contest_gal1ery_mail";
	$tablenameoptions = $wpdb->prefix . "contest_gal1ery_options";
	
	//$selectSQLoptions = $wpdb->get_row( "SELECT * FROM $tablenameoptions WHERE id = '$GalleryID'" );

		if(@$_POST['inform_user']){
			

			
				// Set option if inform or not
					
				//	echo "Inform<br>";
				//	echo @$_POST['inform'];
					
					if (@$_POST['inform']) {
					
					//$querySEToptions1 = "UPDATE $tablenameoptions SET Inform='1' WHERE id = '$GalleryID' ";
					//$wpdb->query($querySEToptions1);	
					
						$wpdb->update( 
						"$tablenameoptions",
						array('Inform' => '1'),
						array('id' => $GalleryID), 
						array('%d'),
						array('%d')
						);
					
					}
				//	echo "informUserText<br>";
					//echo @$_POST['informUserText'];
					
					if (@$_POST['inform']==false) {
					

					//$querySEToptions = "UPDATE $tablenameoptions SET Inform='0' WHERE id = '$GalleryID' ";
					//$updateSQLoptions = $wpdb->query($querySEToptions);	
					
						$wpdb->update( 
						"$tablenameoptions",
						array('Inform' => '0'), 
						array('id' => $GalleryID), 
						array('%d'),
						array('%d')
						);
					
					}
					
					// Set option if inform or not ENDE
			
			
			
			
			
			
			
		
		// Prove variables
		
		$content = @$_POST["editpost"];
		
		//Ganz wichtig, ansonsten werden bei vielen Servern immer / (Backslashes bei Anf�hrungszeichen und aneren speziellen Sonderzeichen) hinzugef�gt
		$content = preg_replace('/\\\\/', '', $content);
		
		//$content = htmlentities($content, ENT_QUOTES, 'UTF-8');
		
				// Magic Quotes on?
			if (get_magic_quotes_gpc()) { // eingeschaltet?
			@$_POST["from"] = stripslashes(@$_POST["from"]);
			@$_POST["reply"] = stripslashes(@$_POST["reply"]);
			@$_POST["cc"] = stripslashes(@$_POST["cc"]);
			@$_POST["bcc"] = stripslashes(@$_POST["bcc"]);
			@$_POST["header"] = stripslashes(@$_POST["header"]);
			@$_POST["url"] = stripslashes(@$_POST["url"]);
		//	@$_POST["content"] = stripslashes($content);
		//	echo "<br>ja<br>";
		    }
	//	stripslashes($content);	
	//	echo "<br>content2: $content<br>";
	
		
		// Escape values wordpress sql
		
		$from = sanitize_text_field(@$_POST["from"]);
		$reply = sanitize_text_field(@$_POST["reply"]);
		$cc = sanitize_text_field(@$_POST["cc"]);
		$bcc = sanitize_text_field(@$_POST["bcc"]);
		$header = sanitize_text_field(@$_POST["header"]);
		$url = sanitize_text_field(@$_POST["url"]);
		//$content = sanitize_text_field($content); <<< ansonten verschieden html eingaben wie <br> und andere
		
		// Make htmlspecialchars
		
		htmlentities($from);
		htmlentities($reply);
		htmlentities($cc);
		htmlentities($bcc);
		htmlentities($header);
		htmlentities($url);
		//htmlentities($content); <<< ansonten verschieden html eingaben wie <br> und andere
		

			
		//$querySETemail = "UPDATE $tablenameemail SET Admin='$from', Header = '$header', Reply='$reply', BCC='$bcc',
		//CC='$cc', URL='$url', Content='$content' WHERE GalleryID = '$GalleryID' ";
		//$updateSQLemail = $wpdb->query($querySETemail);
		
			$wpdb->update( 
			"$tablenameemail",
			array(
			'Admin' => "$from",'Header' => "$header",'Reply' => "$reply",'BCC' => "$bcc",
			'CC' => "$cc",'URL' => "$url",'Content' => "$content"
			), 
			array('GalleryID' => $GalleryID), 
			array('%s','%s','%s','%s',
			'%s','%s','%s'),
			array('%d')
			);
			
			echo "<p id='cg_changes_saved' style='font-size:18px;'><strong>Changes saved</strong></p>";
		
		}
	
	

	
	$selectSQLemail = $wpdb->get_row( "SELECT * FROM $tablenameemail WHERE GalleryID = '$GalleryID'" );
	
	//$content = (@$_POST['editpost']) ? @$_POST['editpost'] : $selectSQLemail->Content;
	$content = $selectSQLemail->Content;
	//$content = html_entity_decode(stripslashes($content));
		
	//nl2br($contentBr);	
	
	$selectSQL1 = $wpdb->get_results( "SELECT * FROM $tablenameoptions WHERE id = '$GalleryID'" );	
	
	foreach($selectSQL1 as $value){
		
	$Inform = $value->Inform;
	$AllowGalleryScript = $value->AllowGalleryScript;

		
	}
	
	$Inform = $value->Inform;
	//$AllowGalleryScript = ($AllowGalleryScript==1) ? 'disabled' : '';
	//$inputUrlLink = ($AllowGalleryScript==0) ? 'Put this variabel in the script: <input type="text" value="$url$" disabled style="background-color:white;" size="3" >&nbsp; <a href="javascript: void(0)" id="questionLink"><b>?</b></a>' : '';
	
	//echo $Inform;
	
	$checkedInform = ($Inform==1) ? 'checked' : '';

	
require_once(dirname(__FILE__) . "/../nav-menu.php");
	
//--------------------------------- ANFANG FORMULAR --------------------------------------------	
	
		// Formular Anfang
		echo "<form action='?page=contest-gallery/index.php&option_id=$GalleryID&inform_user=$GalleryID' method='post'>";
		
		// Main Div
		echo '<div style="display:table;width:914px !important;border: 1px solid #DFDFDF;padding:10px;background-color:#fff;font-size:16px;">';
		
		// Wenn aktiviert werden die User beim Activaten benachrichtigt	
		echo "<div style='padding-left:20px;padding-right:20px;'>";
		echo "<br/>";
		echo '<input type="text" hidden name="id" value="' . @$id . '" method="post" >';		
		echo 'Inform users when activate pictures:';
		echo '&nbsp;&nbsp;<input type="checkbox" name="inform"  value="1" '.$checkedInform.'><br/>';
		echo "</div>";			
		echo  "<br/>";
		echo  "<hr/>";
		// Absender Feld		
		echo "<div style='padding-left:20px;'>";
		echo "<br/>";		
		echo 'Addressor:<br/>';
		echo '<input type="text" name="from" value="'.$selectSQLemail->Admin.'" size="119" maxlength="110" ><br/>';
		echo "</div>";		
	
		// Reply Feld		
		echo "<div style='padding-left:20px;'>";
		echo "<br/>";		
		echo 'Reply mail:<br/>';
		echo '<input type="text" name="reply" value="'.$selectSQLemail->Reply.'" size="119" maxlength="110"><br/>';
		echo "</div>";		
		
		// CC Feld		
		echo "<div style='padding-left:20px;'>";
		echo "<br/>";
		echo 'Cc mail:<br/>';
		echo '<input type="text" name="cc" value="'.$selectSQLemail->CC.'" size="119" maxlength="110"><br/>';
		echo "</div>";		

		
		// BCC Feld		
		echo "<div style='padding-left:20px;'>";
		echo "<br/>";
		echo 'Bcc mail:<br/>';
		echo '<input type="text" name="bcc" value="'.$selectSQLemail->BCC.'" size="119" maxlength="110"><br/>';
		echo "</div>";	
		
	    // Header Feld		
		echo "<div style='padding-left:20px;'>";
		echo "<br/>";
		echo "<div id='answerUrl' style='position:absolute;margin-left:55px;width:200px;background-color:white;border:1px solid;padding:5px;display:none;'>Fill in this field the url of the ";
		echo "site where you inserted the short code of this gallery.</div>";
		echo 'Subject:<br/>';
		echo '<input type="text" name="header" value="'.$selectSQLemail->Header.'" size="119" maxlength="110"><br/>';
		echo "</div>";		


		// URL Feld	
		echo "<div style='padding-left:20px;position:fix;'>";
		echo "<br/>";
		echo "<div id='answerLink' style='position:absolute;margin-left:315px;width:440px;background-color:white;border:1px solid;padding:5px;display:none;'>";
		echo "You have to fill the url in the field abovve where you inserted the shortcode of this gallery. Then you have to put this variable in the editor. If user has an e-mail he will and inform user option is activated";
		echo "then user will receive the url of their image which have been activated. Test it.</div>";
		echo '<div id="questionUrl" style="display:inline;">Url: <a><b>?</b></a></div><br/>';
		echo '<input type="text" name="url" value="'.$selectSQLemail->URL.'" size="119" maxlength="110" ><br/>';		
		//echo $inputUrlLink;
		echo 'Put this variable in the editor: <b>$url$</b> &nbsp; <div  id="questionLink" style="display:inline;width:15px;height:18px;" ><a><b>?</b></a></div>';
		echo "</div>";		
		echo "<div>";
		echo "<br>";
		echo "</div>";		

	echo "<br/>";		

		
		// TinyMCE Editor
		echo "<div style='padding-left:20px;padding-right:20px;'>";
		$post_id = 51;

		$editor_id = 'editpost';


		wp_editor( $content, $editor_id, $settings = array("media_buttons"=>false,"teeny" => true));
		echo "</div>";
		
		// Speichern Feld		
		echo "<div>";
		echo "<br/>";
		echo '<p><input type="submit" name="inform_user" value="Save" style="text-align:center;width:180px;float:right;margin-right:2px;margin-right:20px;"></p>';
		echo "<br/>";
		echo "<br/>";
		echo '</div>';
		
		echo "</div>";	
		
		// Form END
		echo "</form>";
	
//--------------------------------- ENDE FORMULAR --------------------------------------------	


?>