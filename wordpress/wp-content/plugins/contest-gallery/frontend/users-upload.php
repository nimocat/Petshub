<noscript>
<div style="border: 1px solid purple; padding: 10px">
<span style="color:red">Enable JavaScript to use the form</span>
</div>
</noscript>
<?php


if(@$_GET["cg_upload"]){
	
	//echo "222";
			global $wpdb;
			
			
			$galeryID = @$_GET["cg_id"];
			$contest_gal1ery_options_input = $wpdb->prefix . "contest_gal1ery_options_input";
			$inputOptionsSQL = $wpdb->get_row( "SELECT * FROM $contest_gal1ery_options_input WHERE GalleryID='$galeryID'"); 

			$Confirmation_Text = $inputOptionsSQL->Confirmation_Text;
			$Confirmation_Text = nl2br(html_entity_decode(stripslashes($Confirmation_Text)));
			echo "<div id='cg_success' style='padding-top:50px;'>";	
			echo $Confirmation_Text;	
			echo "</div>";
			
			
			
			
			?>

<script>

location.href = "#cg_success";
		

</script>

<?php	
	
	
}


else if(@$_POST['cg_form_submit']){
include('users-upload-check.php');
}
 else{
 include("check-language.php");
 

// Ermitteln der maximalen UploadSize
//$get_max_post = (int)(ini_get('post_max_size'));
$get_max_post = (int)(ini_get('upload_max_filesize'));

$post_max_sizeMB = $get_max_post.'MB';

$max_post = $get_max_post.'m';

$upload_mb = min(@$max_upload, @$max_post, @$memory_limit);





$memory_limit = (int)(ini_get('memory_limit'));



	extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );
$galeryID = $atts['id'];


//echo "$galeryID";

//echo "GaleryID: $galeryID<br/>";


	global $wpdb;

	$tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";
	$tablenameFormInput = $wpdb->prefix . "contest_gal1ery_f_input";
	$tablename_pro_options = $wpdb->prefix . "contest_gal1ery_pro_options";
	
	//echo "tablenameOptions: $tablenameOptions<br/>";
	//echo "tablenameFormInput: $tablenameFormInput<br/>";
	//echo "GaleryID: $galeryID<br/>";

	$picsSQL1 = $wpdb->get_row( "SELECT * FROM $tablenameOptions WHERE id='$galeryID'");
	
	if($picsSQL1==NULL){
        echo "Please check your shortcode. The id does not exists.<br>";
	}
	
	else{
		
				$getUploadForm = $wpdb->get_results("SELECT * FROM $tablenameFormInput WHERE GalleryID='$galeryID' ORDER BY Field_Order ASC");
				
				$proOptions = $wpdb->get_row("SELECT * FROM $tablename_pro_options WHERE GalleryID='$galeryID'");
				
				//print_r($getUploadForm);
				

				


				@$MemoryLimit = $picsSQL1->MemoryLimit;
				
							if(!function_exists('formatBytes2')){function formatBytes2($bytes, $precision = 2) { 
							$units = array('B', 'KB', 'MB', 'GB', 'TB'); 

							$bytes = max($bytes, 0); 
							$pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
							$pow = min($pow, count($units) - 1); 

							$bytes /= pow(1024, $pow); 

							return round($bytes, $precision) . ' ' . $units[$pow]; 
						}
						
						}
						
						if(!function_exists('return_bytes1')){
							function return_bytes1($val) {
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
						}
						
						//echo "Return Bytes: <br>";
						//echo return_bytes1($max_post);
						//echo "<br>";
						
				
						
						$max_post = return_bytes1($max_post);
						
						//echo "<br>max_post: $max_post<br>";
						
						//echo "<br/>Post Max Size: $post_max_size<br/>";
						
						$memoryPeak = formatBytes2(memory_get_peak_usage(),2);
						
						//echo "<br/>";
						//echo formatBytes2(memory_get_peak_usage(),2);
						//echo "<br/>";
						
						//$maxMemory = $MemoryLimit-$MemoryLimit/100*16-$memoryPeak;
						
						$maxMemory = $MemoryLimit-$MemoryLimit/100*16-$memoryPeak;
						
						// Ermitteln der maximalen Aufl�sung der einzelnen Formate
						
						
						//print_r($MaxRes);
						
						/* aktuelle MaxRes Options

						$maxRes = array();
						$maxRes[] = '';//JPG
						$maxRes[] = 'JPG';
						$maxRes[] = '';
						$maxRes[] = 1;//PNG
						$maxRes[] = 'PNG';
						$maxRes[] = '';
						$maxRes[] = 1;//GIF
						$maxRes[] = 'GIF';
						$maxRes[] = '';
						
						*/
						
						$i = 3;
						
						if (@$MaxRes) {		
						
					
							foreach($MaxRes as $key => $value){
							
							if ($i == 0) {$i=3;}
							
							if ($i == 3) {$resRestrict = ($value=='on') ? 'on' : 'off';}
							
							if ($i == 2) {
							if($value == 'JPG'){$imgType = 'JPG';}
							elseif($value == 'PNG'){$imgType = 'PNG';}
							elseif($value == 'GIF'){$imgType = 'GIF';}
							}
							
							if ($i == 1) {
							
							if ($imgType=='JPG'){$maxResJpg = (!$value) ? 0 : $value; $restrictJpg = ($resRestrict=='on') ? 1 : 0 ;}
							elseif($imgType=='GIF'){$maxResPng = (!$value) ? 0 : $value; $restrictPng = ($resRestrict=='on') ? 1 : 0 ;}
							elseif($imgType=='PNG'){$maxResGif = (!$value) ? 0 : $value; $restrictGif = ($resRestrict=='on') ? 1 : 0 ;}
							
							}
							
							$i--;
							
							}
						
						}
						
						else {
						
						echo "";
						
						}
						
						@$MaxResJPGon = intval($picsSQL1->MaxResJPGon);
						@$MaxResPNGon = intval($picsSQL1->MaxResPNGon);
						@$MaxResGIFon = intval($picsSQL1->MaxResGIFon);
						
						@$MaxResJPGwidth = intval($picsSQL1->MaxResJPGwidth);
						@$MaxResJPGheight = intval($picsSQL1->MaxResJPGheight);
						@$MaxResPNGwidth = intval($picsSQL1->MaxResPNGwidth);
						@$MaxResPNGheight = intval($picsSQL1->MaxResPNGheight);
						@$MaxResGIFwidth = intval($picsSQL1->MaxResGIFwidth);
						@$MaxResGIFheight = intval($picsSQL1->MaxResGIFheight);
						
						@$ContestEnd = $picsSQL1->ContestEnd;
						@$ContestEndTime = $picsSQL1->ContestEndTime;
						

						//echo "<br>ContestEnd: $ContestEnd<br>";
						//echo "<br>ContestEndTime: $ContestEndTime<br>";
						
						
						$ActivatePostMaxMB = $wpdb->get_var("SELECT ActivatePostMaxMB FROM $tablenameOptions WHERE id = '$galeryID'");
						//echo "ActivatePostMaxMB: $ActivatePostMaxMB <br>";
						if($ActivatePostMaxMB==1){
							$PostMaxMB = $wpdb->get_var("SELECT PostMaxMB FROM $tablenameOptions WHERE id = '$galeryID'");
						}
						else{
							$PostMaxMB=$get_max_post;
						}
			//echo "PostMaxMB: $PostMaxMB <br>";
						
						
						$ActivateBulkUpload = $wpdb->get_var("SELECT ActivateBulkUpload FROM $tablenameOptions WHERE id = '$galeryID'");
						
						if($ActivateBulkUpload==1){
							$SingleBulkUploadConfiguration = "name='data[]' multiple";
						}
						else{
							$SingleBulkUploadConfiguration = "name='data[]' ";
						}
						
						
						$BulkUploadQuantity = $wpdb->get_var("SELECT BulkUploadQuantity FROM $tablenameOptions WHERE id = '$galeryID'");
						$BulkUploadMinQuantity = $wpdb->get_var("SELECT BulkUploadMinQuantity FROM $tablenameOptions WHERE id = '$galeryID'");
						
			echo "<input type='hidden' value='$MaxResJPGon' id='restrictJpg' />";
			echo "<input type='hidden' value='$MaxResJPGwidth' id='MaxResJPGwidth' />";
			echo "<input type='hidden' value='$MaxResJPGheight' id='MaxResJPGheight' />";
			echo "<input type='hidden' value='$MaxResPNGon' id='restrictPng' />";
			echo "<input type='hidden' value='$MaxResPNGwidth' id='MaxResPNGwidth' />";
			echo "<input type='hidden' value='$MaxResPNGheight' id='MaxResPNGheight' />";
			echo "<input type='hidden' value='$MaxResGIFon' id='restrictGif' />";
			echo "<input type='hidden' value='$MaxResGIFwidth' id='MaxResGIFwidth' />";
			echo "<input type='hidden' value='$MaxResGIFheight' id='MaxResGIFheight' />";
			echo "<input type='hidden' value='$get_max_post' id='post_max_size_php_ini' />";
			echo "<input type='hidden' value='$PostMaxMB' id='post_max_size_user_config' />";


			echo "<input type='hidden' value='$ActivateBulkUpload' id='ActivateBulkUpload' />";


			echo "<input type='hidden' id='cg_ContestEndTime' value='".@$ContestEndTime."'>";
			echo "<input type='hidden' id='cg_ContestEnd' value='".@$ContestEnd."'>";
			echo "<input type='hidden' id='cg_photo_contest_over' value='$language_ThePhotoContestIsOver.'>";



			//echo "$ContestEndTime";


			echo "<input type='hidden' value='$BulkUploadQuantity' id='BulkUploadQuantity' />";
			echo "<input type='hidden' value='$BulkUploadMinQuantity' id='BulkUploadMinQuantity' />";
						
					
						//echo "<br/>MaxResolutionJPG: $maxResJpg<br/>";
						//echo "<br/>MaxResolutionPNG: $maxResPng<br/>";
						//echo "<br/>MaxResolutionGIF: $maxResGif<br/>";
						
						
						/*
						
						// Maximaler m�gliche Aufl�sung f�r JPG Bilder  
						
						if ($maxMemory < 34) {$maxResJPG = 1000000; }
						elseif ($maxMemory < 38) {$maxResJPG = 2000000; }
						elseif ($maxMemory < 43) {$maxResJPG = 3000000; }
						elseif ($maxMemory < 48) {$maxResJPG = 4000000; } 
						elseif ($maxMemory < 53) {$maxResJPG = 5000000; }
						elseif ($maxMemory < 57) {$maxResJPG = 6000000; }
						elseif ($maxMemory < 59) {$maxResJPG = 6250000; }
						elseif ($maxMemory < 62) {$maxResJPG = 7000000; }
						
						// Maximaler m�gliche Aufl�sung f�r PNG Bilder
						
						if ($maxMemory < 37) {$maxResPNG = 1000000; }
						elseif ($maxMemory < 44) {$maxResPNG = 2000000; }
						elseif ($maxMemory < 52) {$maxResPNG = 3000000; }
						elseif ($maxMemory < 59) {$maxResPNG = 4000000; }
						elseif ($maxMemory < 72) {$maxResPNG = 5000000; }
						elseif ($maxMemory < 75) {$maxResPNG = 6000000; }
						elseif ($maxMemory < 77) {$maxResPNG = 6250000; } */
				







			 
			 
			/*
			<noscript>
			<div style="border: 1px solid purple; padding: 10px">
			<span style="color:red">You have to activate JavaScript to use the form</span>
			</div>
			</noscript>	*/




			if(!isset($_POST['cg_form_submit'])){
				
				
				ob_start();

					
				
			echo "<div id='cg_upload_form_container' style='visibility:hidden; text-align:left;color:#000;'>";


					// User ID �berpr�fung ob es die selbe ist
					$check = wp_create_nonce("check");
					
					
			echo "<input type='hidden' id='cg_upload_form_e_prevent_default' value=''>";

			//$path = $_SERVER['REQUEST_URI'];
			
		//	echo get_page_link(NULL,false,NULL);
			echo "<div>";
			echo '<form action="" method="post" id="cg_upload_form" enctype="multipart/form-data" novalidate >';

			echo "<input type='hidden' id='cg_check' value='$check'>";
			echo "<input type='hidden' name='check' value='$check'>";

			echo "<input type='hidden' name='GalleryID' value='$galeryID'>";	
			
			echo "<input type='hidden' name='cg_upload_action' value='true'>";	


			$i=0;

			// Beim Eintrag wird gesendet:
			/*
			- Feldart
			- FeldID
			- FeldReihenfolge
			- Content
			*/

			//echo "<br>getUploadform:<br>";
			//print_r($getUploadForm );

					foreach($getUploadForm as $value){
				
					if ($value->Field_Type=='image-f'){
					
						//@$id = $value->f_input_id;
						$Field_Order = $value->Field_Order;
						$Field_Content = $value->Field_Content;
						$Field_Content = unserialize($Field_Content);
						foreach($Field_Content as $key => $value){if($key=='titel'){ $titel = html_entity_decode(stripslashes($value)); break;} }				

								echo "<div class='cg_form_div'>";
								echo "<label for='cg_input_image_upload_id'>$titel *</label>";
								echo "<input type='file' id='cg_input_image_upload_id' $SingleBulkUploadConfiguration />";// Content Feld
								//echo "<input type='submit' value='Upload' />";
								echo "<p class='cg_input_error cg_input_error_image_upload'></p>";// Fehlermeldung erscheint hier
								echo "</div>";

				
					}	

					if (@$value->Field_Type=='text-f'){
					
						$id = $value->id;
						$Field_Order = $value->Field_Order;
						$Field_Content = $value->Field_Content;
						$Field_Content = unserialize($Field_Content);
						foreach($Field_Content as $key => $value){
							if($key=='titel'){ $titel = html_entity_decode(stripslashes($value)); }
							if($key=='content'){ $content = html_entity_decode(stripslashes($value)); }
							
							if($key=='min-char'){ $minsize = ($value) ? "$value" : "" ;}
							if($key=='max-char'){ $maxsize = ($value) ? "$value" : "" ;}
							
							if($key=='mandatory'){ 
							$necessary = ($value=='on') ? '*' : '' ;
							$checkIfNeed = ($value=='on') ? 'on' : '' ;
							}
						}
						
						echo "<div class='cg_form_div'>";
						echo "<label for='cg_upload_form_field$id'>$titel $necessary</label>";
						echo "<input type='hidden' name='form_input[]' value='nf'><input type='hidden' name='form_input[]' value='$id'>";// Formart und FormfeldID hidden
						echo "<input type='hidden' name='form_input[]'  value='$Field_Order'>";// Feldreihenfolge wird mitgegeben
						echo "<input type='text' placeholder='$content' class='cg_input_text_class' id='cg_upload_form_field$id' value='' name='form_input[]'>";// Content Feld, l�nge wird �berpr�ft
						echo "<input type='hidden' class='minsize' value='$minsize'>"; // Pr�fen minimale Anzahl zeichen
						echo "<input type='hidden' class='maxsize' value='$maxsize'>"; // Pr�fen maximale Anzahl zeichen
						echo "<input type='hidden' class='cg_form_required' value='$checkIfNeed'>";// Pr�fen ob Pflichteingabe
						echo "<p class='cg_input_error'></p>";// Fehlermeldung erscheint hier
						echo "</div>";
									
					}		
					
					if (@$value->Field_Type=='email-f'){
					
						if(is_user_logged_in()==false){
					
							$id = $value->id;		
							$Field_Order = $value->Field_Order;
							$Field_Content = $value->Field_Content;
							$Field_Content = unserialize($Field_Content);
							foreach($Field_Content as $key => $value){
								if($key=='titel'){ $titel = html_entity_decode(stripslashes($value)); }
								if($key=='content'){ $content = html_entity_decode(stripslashes($value)); }
								if($key=='mandatory'){ 
								$necessary = ($value=='on') ? '*' : '' ;
								$checkIfNeed = ($value=='on') ? 'on' : '' ;
								}
							}
							echo "<div class='cg_form_div'>";
							echo "<label for='cg_upload_form_field$id'>$titel $necessary</label>";
							echo "<input type='hidden' name='form_input[]'  value='ef'><input type='hidden' name='form_input[]' value='$id'>";//Formart und FormfeldID hidden
							echo "<input type='hidden' name='form_input[]'  value='$Field_Order'>";// Feldreihenfolge wird mitgegeben
							echo "<input type='text' placeholder='$content' value='' class='cg_input_email_class' id='cg_upload_form_field$id' name='form_input[]'>";// Content Feld, l�nge wird �berpr�ft
							echo "<input type='hidden' class='cg_form_required' value='$checkIfNeed'>";// Pr�fen ob Pflichteingabe
							echo "<p class='cg_input_error'></p>";// Fehlermeldung erscheint hier
							echo "</div>";

						}
						
					}

					if (@$value->Field_Type=='comment-f'){
					
						$id = $value->id;				
						$Field_Order = $value->Field_Order;
						$Field_Content = $value->Field_Content;
						$Field_Content = unserialize($Field_Content);
						foreach($Field_Content as $key => $value){
							if($key=='titel'){ $titel = html_entity_decode(stripslashes($value)); }
							if($key=='content'){ $content = html_entity_decode(stripslashes($value)); }
							
							if($key=='min-char'){ $minsize = ($value) ? "$value" : "";}
							if($key=='max-char'){ $maxsize = ($value) ? "$value" : "";}

							if($key=='mandatory'){
							$necessary = ($value=='on') ? '*' : '' ;
							$checkIfNeed = ($value=='on') ? 'on' : '' ;
							}
						}

						echo "<div class='cg_form_div'>";
						echo "<label for='cg_upload_form_field$id'>$titel $necessary</label>";
						echo "<input type='hidden' name='form_input[]'  value='kf'><input type='hidden' name='form_input[]' value='$id'>";// Formart und FormfeldID hidden
						echo "<input type='hidden' name='form_input[]'  value='$Field_Order'>";// Feldreihenfolge wird mitgegeben
						echo "<textarea maxlength='$maxsize' class='cg_textarea_class' id='cg_upload_form_field$id' placeholder='$content' name='form_input[]'  rows='10' ></textarea>";// Content Feld, l�nge wird �berpr�ft
						echo "<input type='hidden' class='minsize' value='$minsize'>"; // Pr�fen minimale Anzahl zeichen
						echo "<input type='hidden' class='maxsize' value='$maxsize'>"; // Pr�fen maximale Anzahl zeichen
						echo "<input type='hidden' class='cg_form_required' value='$checkIfNeed'>";// Pr�fen ob Pflichteingabe
						echo "<p class='cg_input_error'></p>";// Fehlermeldung erscheint hier
						echo "</div>";
						
					}
					
					
					if (@$value->Field_Type=='check-f'){
					
						$id = $value->id;				
						$Field_Order = $value->Field_Order;
						$Field_Content = $value->Field_Content;
						$Field_Content = unserialize($Field_Content);
						foreach($Field_Content as $key => $value){
							if($key=='titel'){ $titel = html_entity_decode(stripslashes($value)); }
							if($key=='content'){ $content = html_entity_decode(stripslashes($value)); }
							}

						echo "<div class='cg_form_div'>";
						echo "<label for='cg_upload_form_field$id'>$titel $necessary</label>";
						echo "<input type='checkbox' class='cg_check_agreement_class' id='cg_upload_form_field$id'>&nbsp;$content";
						echo "<p class='cg_input_error'></p>";// Fehlermeldung erscheint hier 
						echo "</div>";
						
					}

                    if (@$value->Field_Type=='select-f'){

                        $id = $value->id;
                        $Field_Order = $value->Field_Order;
                        $Field_Content = $value->Field_Content;
                        $Field_Content = unserialize($Field_Content);
                        foreach($Field_Content as $key => $value){
                            if($key=='titel'){ $titel = html_entity_decode(stripslashes($value)); }
                            if($key=='content'){ $content = html_entity_decode(stripslashes($value)); }

                            if($key=='mandatory'){
                                $necessary = ($value=='on') ? '*' : '' ;
                                $checkIfNeed = ($value=='on') ? 'on' : '' ;
                            }
                        }

                        echo "<div class='cg_form_div'>";
                        echo "<label for='cg_upload_form_field$id'>$titel $necessary</label>";
                        echo "<input type='hidden' name='form_input[]'  value='se'><input type='hidden' name='form_input[]' value='$id'>";// Formart und FormfeldID hidden
                        echo "<input type='hidden' name='form_input[]'  value='$Field_Order'>";// Feldreihenfolge wird mitgegeben

                        $textAr = explode("\n", $content);

                        echo "<select name='form_input[]' class='cg_input_select_class'>";

                            echo "<option value=''>$language_pleaseSelect</option>";

                            foreach($textAr as $key => $value){

                                echo "<option value='$value'>$value</option>";

                            }

                        echo "</select>";

                        echo "<input type='hidden' class='cg_form_required' value='$checkIfNeed'>";// Pr�fen ob Pflichteingabe
                        echo "<p class='cg_input_error'></p>";// Fehlermeldung erscheint hier
                        echo "</div>";

                    }


					if (@$value->Field_Type=='html-f'){
					
						$id = $value->id;				
						$Field_Order = $value->Field_Order;
						$Field_Content = $value->Field_Content;
						$Field_Content = unserialize($Field_Content);
						foreach($Field_Content as $key => $value){
							if($key=='titel'){ $titel = html_entity_decode(stripslashes($value)); }
							if($key=='content'){ $content = nl2br(html_entity_decode(stripslashes($value))); }
						}

						echo "<div class='cg_form_div cg_html_field_class'>";
						echo $content;
						echo "</div>";
						
					}

					if (@$value->Field_Type=='caRo-f'){

						$id = $value->id;
						$Field_Order = $value->Field_Order;
						$Field_Content = $value->Field_Content;
						$Field_Content = unserialize($Field_Content);
						foreach($Field_Content as $key => $value){
							if($key=='titel'){ $titel = html_entity_decode(stripslashes($value)); }
						}

						echo "<div class='cg_form_div' id='cg_captcha_not_a_robot_field'>";
						echo "<label for='cg_$check' >$titel</label>";
                        echo "<p class='cg_input_error'></p>";
						echo "</div>";

					}


					
				}
				
			//$unix = time()+2;

			//echo '<input type="hidden" name="timestamp" value="'.$unix.'">';


			//echo "<div style='display:inline;width:100%;float:left;text-align:left;'>";
			echo "<div class='cg_form_div' id='cg_form_upload_submit_div'>";
			echo '<input type="submit" name="cg_form_submit" id="cg_users_upload_submit" value="'.$language_sendUpload.'">';
            echo "<p class='cg_input_error'></p>";
			echo "</div>";
			//echo "</div>";
			echo '</form>';
			echo "</div>";
			echo "</div>";// Zum schlie�en des obersten Divs #ausgabe1, ist auf hidden wegen javascript

			echo "<br/>";

			//echo "<input type='hidden' id='resPic'>";

			//update_option( "p_cgal1ery_count_uploads", 100 );

			$formOutput = ob_get_clean();

				if(@$proOptions->RegUserUploadOnly==1 && is_user_logged_in()==false){
					echo nl2br(@$proOptions->RegUserUploadOnlyText);		
				}
				else{
					
						if(!get_option("p_cgal1ery_count_uploads")){
							add_option( "p_cgal1ery_count_uploads", 50 );
						}
									
						if(get_option("p_cgal1ery_reg_code") == true
				&& strpos(floatval(get_option("p_cgal1ery_reg_code"))/44,".") == false && floatval(get_option("p_cgal1ery_reg_code"))!=0 && floatval(get_option("p_cgal1ery_reg_code"))>=3281329700){		
							echo @$formOutput;	
						}


						else if(get_option("p_cgal1ery_count_uploads")>=1){

							echo @$formOutput;
						 
						}
						else if(get_option("p_cgal1ery_count_uploads")<1){
							echo "<a href='https://www.contest-gallery.com/pro-version/' title='Get PRO and enjoy community features' target='_blank'>Get PRO version to upload unlimited amount of images</a>";
						}
						else{
							
							echo "";
							
						}
					
				}
			}
			//echo "$language_MaximumAllowedWidthForJPGsIs";
			echo "<input type='hidden' id='cg_show_upload' value='1'>";

			//echo "language_ThisFileTypeIsNotAllowed: $language_ThisFileTypeIsNotAllowed";
			echo "<input type='hidden' id='cg_file_not_allowed' value='$language_ThisFileTypeIsNotAllowed'>";
			echo "<input type='hidden' id='cg_file_size_to_big' value='$language_TheFileYouChoosedIsToBigMaxAllowedSize'>";
			echo "<input type='hidden' id='cg_post_size' value='$post_max_sizeMB'>";

			echo "<input type='hidden' id='cg_to_high_resolution' value='$language_TheResolutionOfThisPicIs'>";

			echo "<input type='hidden' id='cg_max_allowed_resolution_jpg' value='$language_MaximumAllowedResolutionForJPGsIs'>";
			echo "<input type='hidden' id='cg_max_allowed_width_jpg' value='$language_MaximumAllowedWidthForJPGsIs'>";
			echo "<input type='hidden' id='cg_max_allowed_height_jpg' value='$language_MaximumAllowedHeightForJPGsIs'>";

			echo "<input type='hidden' id='cg_max_allowed_resolution_png' value='$language_MaximumAllowedResolutionForPNGsIs'>";
			echo "<input type='hidden' id='cg_max_allowed_width_png' value='$language_MaximumAllowedWidthForPNGsIs'>";
			echo "<input type='hidden' id='cg_max_allowed_height_png' value='$language_MaximumAllowedHeightForPNGsIs'>";

			echo "<input type='hidden' id='cg_max_allowed_resolution_gif' value='$language_MaximumAllowedResolutionForGIFsIs'>";
			echo "<input type='hidden' id='cg_max_allowed_width_gif' value='$language_MaximumAllowedWidthForGIFsIs'>";
			echo "<input type='hidden' id='cg_max_allowed_height_gif' value='$language_MaximumAllowedHeightForGIFsIs'>";


			echo "<input type='hidden' id='cg_check_agreement' value='$language_YouHaveToCheckThisAgreement '>";
			echo "<input type='hidden' id='cg_check_email_upload' value='$language_EmailAdressHasToBeValid'>";
			echo "<input type='hidden' id='cg_min_characters_text' value='$language_MinAmountOfCharacters'>";
			echo "<input type='hidden' id='cg_max_characters_text' value='$language_MaxAmountOfCharacters'>";
			echo "<input type='hidden' id='cg_no_picture_is_choosed' value='$language_ChooseYourImage'>";

			echo "<input type='hidden' id='cg_language_BulkUploadQuantityIs' value='$language_BulkUploadQuantityIs'>";
			echo "<input type='hidden' id='cg_language_BulkUploadLowQuantityIs' value='$language_BulkUploadLowQuantityIs'>";
			
			echo "<input type='hidden' id='cg_language_PleaseFillOut' value='$language_PleaseFillOut'>";
			echo "<input type='hidden' id='cg_language_youHaveNotSelected' value='$language_youHaveNotSelected'>";

			echo "<input type='hidden' id='cg_language_pleaseConfirm' value='$language_pleaseConfirm'>";

			}


		
	}
	


?>