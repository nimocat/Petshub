 <?php

require_once('get-data-define-output.php');

// Path to jquery Lightbox Script

//$pathJquery = plugins_url().'/contest-gallery/js/jquery.js';
//$pathPlugin1 = plugins_url().'/contest-gallery/js/lightbox-2.6.min.js';
//$pathPlugin2 = plugins_url().'/contest-gallery/css/lightbox.css';
//$pathPlugin3 = plugins_url().'/contest-gallery/css/star_off_48.png';
//$pathPlugin4 = plugins_url().'/contest-gallery/css/star_48.png';
//$pathCss = plugins_url().'/contest-gallery/css/style.css';
//$pathJqueryUI = plugins_url().'/contest-gallery/js/jquery-ui.js';
//$pathJqueryUIcss = plugins_url().'/contest-gallery/js/jquery-ui.css';
//$cssPng = content_url().'/plugins/contest-gallery/css/lupe.png';// URL for zoom pic


//add_action('wp_enqueue_scripts','my_scripts');

 
/*

echo <<<HEREDOC

<link href="$pathPlugin2" rel="stylesheet" />
<link href="$pathCss" rel="stylesheet" />
<link href="$pathPlugin6" rel="stylesheet" />


HEREDOC;

//echo $pathCss;


echo <<<HEREDOC

	<script src='$pathJquery'></script>
	<script src='$pathJqueryUI'></script>
	<script src='$pathJqueryUIcss'></script>

HEREDOC;*/

require_once(dirname(__FILE__) . "/../nav-menu.php");


echo "<form name='defineUpload' enctype='multipart/form-data' action='?page=contest-gallery/index.php&option_id=$GalleryID&define_output=true' method='post'>";

//print_r($getOutput);

//print_r($upload);
//print_r(array_keys($upload,'nf')); 



	
// FELDBENENNUNGEN

// 1 = Feldtyp
// 2 = Feldnummer // 0 ist für Bild hochladen reserviert
// 3 = Feldtitel
// 4 = Feldinhalt
// 5 = Feldkrieterium1
// 6 = Feldkrieterium2
// 7 = Felderfordernis

$i3 = 7;

//echo "<option  value='nf3'>$upload[$nfKey1]</option>";

	// SELECT Formular erstellen 
 	
	echo "<br/>";
	
	// SELECT Formular erstellen ----------- ENDE
	
	$visualPath = plugins_url()."/contest-gallery/admin/upload/visual.jpg";	
	

 
  ?>
 
 




<div style="width:917px;float:left;padding-right:20px;background-color:#fff;border: 1px solid #DFDFDF;">

<?php
/*
echo "<br>";
print_r($selectUserFormIDsArray);
echo "<br>";

echo "<br>";
print_r($selectContentFieldArray);
echo "<br>";*/

	echo "<div id='options' style='display:block;width:125px;padding-top:20px;padding-bottom:20px;padding-left:26px;padding-right:5px;'>";
	

	
	if(@$freeOutputFields){	
	
 	echo "<select name='cg_define_output' id='cg_define_output' style='font-size:16px;width:100px;'>";
	
	$i=0;
	
	
	
		foreach ($selectContentFieldArray as $key => $formvalue) {
		
		
			if($formvalue=='image-f'){$fieldtype="bh"; $i++; continue;}	
			if($fieldtype=='bh'){$fieldtype="bh-again";continue;}
			if($fieldtype=='bh-again'){$fieldtype="";continue;}
			

			if($formvalue=='text-f'){$fieldtype="nf"; $i++; continue;}
			if ($fieldtype=="nf") {$fieldtype="nf-again";$id=$formvalue;continue;}
			if ($fieldtype=="nf-again") {
			
			//$key=false;
			$key = array_search($id, $selectUserFormIDsArray);
			
			/*?><script>
			var key = <?php echo json_encode($key);?>;
			alert(key);
			</script><?php*/
			
			if(!is_int($key)){  
			echo "<option id='$id' name='$i' value='$formvalue' class='nf'>$formvalue</option>";
			
			}

			$fieldtype='';
			
			}
			
			if($formvalue=='email-f'){$fieldtype="ef"; $i++; continue;}
			if ($fieldtype=="ef") {$fieldtype="ef-again";$id=$formvalue;continue;}			
			if ($fieldtype=='ef-again') {
			
			//$key=false;
			$key = array_search($id, $selectUserFormIDsArray);
			
			/*?><script>
			var key = <?php echo json_encode($key);?>;
			alert(key);
			</script><?php*/
			
			if(!is_int($key)){ 
			echo "<option id='$id' name='$i' value='$formvalue' class='ef'>$formvalue</option>";
			
			}
			
			$fieldtype='';
			
			}
			
			if($formvalue=='comment-f'){$fieldtype="kf"; $i++; continue;}
			if ($fieldtype=="kf") {$fieldtype="kf-again";$id=$formvalue;continue;}		
			if ($fieldtype=='kf-again') {
			
			//$key=false;
			$key = array_search($id, $selectUserFormIDsArray);
			
			/*?><script>
			var key = <?php echo json_encode($key);?>;
			alert(key);
			</script><?php*/
			
			if(!is_int($key)){ 
			echo "<option id='$id' name='$i' value='$formvalue' class='kf'>$formvalue</option>";
			
			}			
			
			$fieldtype='';
				
			}

		}
	
	echo "</select>";
	
	}
	
		echo "<div id='plus' style='display:inline;float:right;'>";
	if(@$freeOutputFields){	
	echo "<input id='submit_define_output' type='button' name='plus' value='+' style='width:20px;'>";
	}
	echo "</div>";
	
	echo "</div>";

echo '<div id="ausgabe1" style="display:table;width:882px;border-top: 1px solid #DFDFDF;border-bottom: 1px solid #DFDFDF;padding:10px;background-color:#fff;padding-left:27px;padding-right:26px;">';


if ($selectFormOutput) {


	$i = 0;
	

	foreach($selectFormOutput as $value){
	
		if ($value->Field_Type=='image-f'){
		
			$i++; $id = $value->f_input_id;


			echo "<div style='border: 1px solid #DFDFDF;margin-top:20px;margin-bottom:20px;text-align:center;' class='output' id='$id'>";			
			echo "<img src='$visualPath' />";
			echo "<input type='hidden' name='output[]' value='bh'>";
			echo "<input type='hidden' name='output[]' value='$id'>";
			echo "<input type='hidden' name='output[]' value='$value->Field_Content'>";
			echo "</div>";

	
	
		}	

		if ($value->Field_Type=='text-f'){
		
			$i++; $id = $value->f_input_id;
		
			$Field_Content = $value->Field_Content;
			
			echo "<div style='border: 1px solid #DFDFDF;padding:10px;margin-top:20px;margin-bottom:20px;' class='cg-output'><div style='display:inline;float:left;margin-top:15px;font-size:16px;'>$Field_Content:</div>";
			echo "<input type='hidden' name='output[]' class='nf' value='nf'><input type='hidden' name='output[]' class='form_input_id' value='$id'><input class='cg-delete' type='button' alt='nf' titel='$Field_Content' value='-' style='width:20px;float:left;margin-top:10px;margin-bottom:10px;margin-left:10px;'><br/>";
			echo "<input type='hidden' name='output[]' value=\"$Field_Content\"><input type='text' value='' size='102' style='width:855px;' disabled style='background-color: white;'><br/><br/></div>";

						
		}
	
		
		
		if ($value->Field_Type=='email-f'){
		
			$i++; $id = $value->f_input_id;		
			
			$Field_Content = $value->Field_Content;

			echo "<div style='border: 1px solid #DFDFDF;padding:10px;margin-top:20px;margin-bottom:20px;' id='$id'><div style='display:inline;float:left;margin-top:15px;font-size:16px;'>$Field_Content</div>";
			echo "<input type='hidden' name='output[]' class='ef' value='ef'><input type='hidden' name='output[]' class='form_input_id' value='$id'><input class='cg-delete' type='button' alt='ef' titel='$Field_Content' value='-' style='width:20px;float:left;margin-top:10px;margin-bottom:10px;margin-left:10px;'><br/>";
			echo "<input type='hidden' name='output[]' value=\"$Field_Content\"><input type='text' value='' size='102' style='width:855px;' disabled style='background-color: white;'><br/><br/></div>";

		}

		if ($value->Field_Type=='comment-f'){
		
			$i++; $id = $value->f_input_id;				
			
			$Field_Content = $value->Field_Content;

			echo "<div style='border: 1px solid #DFDFDF;padding:10px;margin-top:20px;margin-bottom:20px;' id='$id'><div style='display:inline;float:left;margin-top:15px;font-size:16px;'>$Field_Content:</div>";
			echo "<input type='hidden' name='output[]' class='kf' value='kf'><input type='hidden' name='output[]' class='form_input_id' value='$id'><input class='cg-delete' type='button' alt='kf' titel='$Field_Content' value='-' style='width:20px;float:left;margin-top:10px;margin-bottom:10px;margin-left:10px;'><br/>";
			echo "<input type='hidden' name='output[]' value=\"$Field_Content\"><textarea maxlength='1000' style='width:855px;' rows='10' disabled style='background-color: white;'></textarea><br/><br/></div>";

			
			
		}
		
	}
	
}



?>

</div>

<div style="width:917px;float:left;text-left:right;padding-right:20px;background-color:#fff;">
<input id="submit-form-output" type="submit" name="submit-form-output" value="Save" style="text-align:center;width:180px;float:right;margin-top:20px;margin-bottom:20px;margin-right:8px;">
<br/>
<br/>
</div>



</div>
<?php
echo "<br/>";



?>
</form>
<!--
<br/>
<div style="display:block;">
<p><form method='POST' action='?page=contest-gallery/index.php&optionID=<?php echo "$GalleryID";?>&defineUpload=true'><input type='submit' value='Definiere Uploadformular' /></form></p>
</div>
</form>-->