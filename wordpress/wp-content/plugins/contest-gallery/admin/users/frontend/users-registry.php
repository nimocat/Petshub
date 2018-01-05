<noscript>
<div style="border: 1px solid purple; padding: 10px">
<span style="color:red">Enable JavaScript to use the form</span>
</div>
</noscript>
<?php

	extract( shortcode_atts( array(
			'id' => ''
		), $atts ) );
	$GalleryID = $atts['id'];

//include("class-sql.php");
//include("class-reg.php");
//$cgSqlClass = new cgSqlClass();
//$cgRegClass = new cgRegClass();

include(dirname(__FILE__)."/../../../frontend/check-language.php");


if(@$_GET["cgkey"]){
	
	//echo "222";
			global $wpdb;
			$tablenameWpUsers = $wpdb->prefix . "users";
			$tablename_pro_options = $wpdb->prefix . "contest_gal1ery_pro_options";
			$tablenameCreateUserEntries = $wpdb->prefix . "contest_gal1ery_create_user_entries";
			
			$pro_options = $wpdb->get_row( "SELECT * FROM $tablename_pro_options WHERE GalleryID='$GalleryID'");
			
			$cgkey = sanitize_text_field($_GET["cgkey"]);			
			
			// Zwei Tests notwendig um zu pr�fen ob User schon registriert ist
			
			// 1.
			$checkUserViaKey = $wpdb->get_row("SELECT * FROM $tablenameWpUsers WHERE user_activation_key='$cgkey'");
			if($checkUserViaKey){
				
				// User Rolle wird gesetzt
				wp_update_user( array( 'ID' => $checkUserViaKey->ID, 'role' => 'contest_gallery_user' ) );
				
				// wp_set_auth_cookie funktioniert auf allen Themes
				//wp_set_auth_cookie( $checkUserViaKey->ID, TRUE );
				
				echo "<a href='#cg_activation'></a>";
				echo "<div style='padding-top:50px;'>";
				echo "<p>";
				echo nl2br(html_entity_decode(stripslashes($pro_options->TextAfterEmailConfirmation)));
				echo "</p>";
				echo "</div>";
				
			}
			
			else{					
					
					$userAccountEntries = $wpdb->get_results( "SELECT Field_Type, Field_Content FROM $tablenameCreateUserEntries WHERE activation_key='$cgkey'");					
					
					//var_dump($userAccountEntries);
					
					if($userAccountEntries){
					$i = 0;
					$fieldRow = '';
					foreach($userAccountEntries as $key => $value){
					
						foreach($value as $key1 => $value1){
							$i++;
							if($value1=="password"){$fieldRow="password";continue;}
							if($fieldRow=="password"){$user_pass=$value1;$fieldRow='';continue;}
							if($value1=="main-mail"){$fieldRow="main-mail";continue;}
							if($fieldRow=="main-mail"){$user_email=$value1;$fieldRow='';continue;}
							if($value1=="main-user-name"){$fieldRow="main-user-name";continue;}
							if($fieldRow=="main-user-name"){
									$user_login=$value1;
									$user_nicename=$value1;
									$display_name=$value1;$fieldRow='';	
							}
						}
						
					}
						$checkUserViaMailName = $wpdb->get_row("SELECT * FROM $tablenameWpUsers WHERE user_email='$user_email' or user_login='$user_login'");
						if($checkUserViaMailName){
							
							echo "<a href='#cg_activation'></a>";
							echo "<div style='padding-top:50px;'>";
							echo "<p>";
							echo "This user is already registered.";
							echo "</p>";
							echo "</div>";
							
						}
						else{
							
								$user_registered = date("Y-m-d H:i:s");	
							

								$wpdb->query( $wpdb->prepare(
								"
									INSERT INTO $tablenameWpUsers
									( id, user_login, user_pass, user_nicename, user_email, user_url,
									user_registered, user_activation_key, user_status, display_name)
									VALUES (%s,%s,%s,%s,%s,%s,
									%s,%s,%d,%s)
								",
									'',$user_login,$user_pass,$user_nicename,$user_email,'',
									$user_registered,$cgkey,'',$display_name
								) );						
					
								
							//	die;
								
								$newWpId = $wpdb->get_var( "SELECT ID FROM $tablenameWpUsers WHERE user_activation_key='$cgkey'");
								
								// Den Eintr�gen wird die neue WP ID hinzugef�gt
								$wpdb->update(
									"$tablenameCreateUserEntries",
									array('wp_user_id' => $newWpId,'activation_key' => ''),
									array('activation_key' => $cgkey),
									array('%d','%s'),
									array('%s')
								);

								$wpdb->query( $wpdb->prepare(
									"
										DELETE FROM $tablenameCreateUserEntries WHERE GalleryID = %d AND (Field_Type = %s OR Field_Type = %s)
									",
										$GalleryID, "password", "password-confirm"
								 ));
								 
				 
							// User Rolle wird gesetzt
							wp_update_user( array( 'ID' => $newWpId, 'role' => 'contest_gallery_user' ) );
							
							// wp_set_auth_cookie funktioniert auf allen Themes
							//wp_set_auth_cookie( $newWpId, TRUE );
							
							if(get_option( "p_cgal1ery_reg_code" )==false || intval(get_option( "p_cgal1ery_reg_code" )) % 44 != 0 && intval(get_option( "p_cgal1ery_reg_code" )) != 0){

								if(get_option("p_cgal1ery_count_users")==true){
									$p_cgal1ery_count_users = get_option( "p_cgal1ery_count_users" );
										
									$p_cgal1ery_count_users--;
						
									update_option( "p_cgal1ery_count_users", $p_cgal1ery_count_users );
									
								}
								else{
									add_option( "p_cgal1ery_count_users", 10 );
								}
							
							}

							echo "<div id='cg_activation' style='padding-bottom:100px;'>";
							echo "<p>";
							echo html_entity_decode(stripslashes($pro_options->TextAfterEmailConfirmation));
							echo "</p>";
							echo "</div>";
							
			
			?>

<script>

location.href = "#cg_activation";
		

</script>

<?php								
							
						}
					

					
					}
					else{
						echo "222";
						echo "Plz don't manipulate the registry";die();				
					}
			
			}
			
			?>

<script>


location.href = "#cg_success";

		

</script>

<?php	
	
	
}


else if(@$_POST['cg_check']){
include('users-registry-check.php');
}



else{


//include("class-reg.php");

ob_start();	


include(plugin_dir_path( __FILE__ )."../../../frontend/check-language.php");

global $wpdb;
$tablenameCreateUserForm = $wpdb->prefix . "contest_gal1ery_create_user_form";

$selectUserForm = $wpdb->get_results("SELECT * FROM $tablenameCreateUserForm WHERE GalleryID = '$GalleryID' ORDER BY Field_Order ASC");

if(empty($selectUserForm)){
    echo "Please check your shortcode. The id does not exists.<br>";
    return false;
}


//print_r($selectUserForm);

$i=1;

//echo "test";

echo "<div id='cg_user_registry_div'>";
echo "<div>";
echo "<input type='hidden' id='cg_check_mail_name_value' value='0'>";
echo "<input type='hidden' id='cg_site_url' value='".get_site_url()."'/>";

echo "<a id='cg_user_registry_anchor'/></a>";

echo '<form action="" method="post" id="cg_user_registry_form">';

echo "<input type='hidden' name='cg_check' id='cg_check' value='".wp_create_nonce("check")."'>";

foreach($selectUserForm as $key => $value) {

				$required = ($value->Required==1) ? "*": "";
				
				$cgCheckUsernameMail = '';


				if($value->Field_Type=='main-user-name' OR $value->Field_Type=='main-mail'){
					$placeholder = html_entity_decode(stripslashes($value->Field_Content));
					$cgContentField = "<input type='text' placeholder='$placeholder' class='cg-".$value->Field_Type."' id='cg_registry_form_field".$value->id."' name='cg_Fields[$i][Field_Content]'>";
					if($value->Field_Type=='main-user-name'){
						$cgCheckUsernameMail = "id='cg_user_name_check_alert'";
					}
					if($value->Field_Type=='main-mail'){
						$cgCheckUsernameMail = "id='cg_mail_check_alert'";
					}
				}
				if($value->Field_Type=='password' OR $value->Field_Type=='password-confirm'){
					$placeholder = html_entity_decode(stripslashes($value->Field_Content));
					$cgContentField = "<input type='password'  placeholder='$placeholder' class='cg-".$value->Field_Type."' id='cg_registry_form_field".$value->id."' name='cg_Fields[$i][Field_Content]'>";
				}
				if($value->Field_Type=='user-comment-field'){
					$placeholder = html_entity_decode(stripslashes($value->Field_Content)); 
					$cgContentField = "<textarea maxlength='".$value->Max_Char."' placeholder='$placeholder' class='cg-".$value->Field_Type."' id='cg_registry_form_field".$value->id."' name='form_input[]'  rows='10' ></textarea>";
				}
				
				if($value->Field_Type=='user-text-field'){
					$placeholder = html_entity_decode(stripslashes($value->Field_Content));
					$cgContentField = "<input type='text' placeholder='$placeholder' class='cg-".$value->Field_Type."' id='cg_registry_form_field".$value->id."' name='cg_Fields[$i][Field_Content]'>";
				}
								
				if($value->Field_Type=='user-html-field'){
					$content = html_entity_decode(stripslashes($value->Field_Content));
					$cgContentField = "<div class='cg-".$value->Field_Type."'>$content</div>";
				}
				
				

				echo "<div id='cg-registry-".$value->Field_Order."' class='cg_form_div'>";
				
				if (@$value->Field_Type!='user-html-field'){
					echo "<label for='cg_registry_form_field".$value->id."' >".html_entity_decode(stripslashes($value->Field_Name))." $required</label>";
					echo "<input type='hidden' name='cg_Fields[$i][Form_Input_ID]' value='".$value->id."'>";
					echo "<input type='hidden' name='cg_Fields[$i][Field_Type]' value='".$value->Field_Type."'>";
					echo "<input type='hidden' name='cg_Fields[$i][Field_Order]' value='".$value->Field_Order."'>";
				}
					
					
					
					// Pr�fen ob check-agreement-feld ist ansonsten Text oder, Comment Felder anzeigen
					if (@$value->Field_Type=='user-check-agreement-field'){
					
						$cgCheckContent = html_entity_decode(stripslashes($value->Field_Content));
						echo "<input type='checkbox' id='cg_registry_form_field".$value->id."' class='cg_check_f_checkbox'>&nbsp;$cgCheckContent";
						
					}
					else{			
						echo @$cgContentField;
						
						if (@$value->Field_Type!='user-html-field'){
							echo "<input type='hidden' class='cg_Min_Char' value='".$value->Min_Char."'>"; // Pr�fen minimale Anzahl zeichen
							echo "<input type='hidden' class='cg_Max_Char' value='".$value->Max_Char."'>"; // Pr�fen maximale Anzahl zeichen
							echo "<input type='hidden' class='cg_form_required' value='".$value->Required."'>";// Pr�fen ob Pflichteingabe
						}
						
					}

					echo "<p class='cg_input_error' $cgCheckUsernameMail></p>";// Fehlermeldung erscheint hier
				
				echo "</div>";

	

$i++;	
	
}

echo "<div>";
echo '<input type="submit" name="cg_registry_submit" id="cg_users_registry_check" value="'.$language_sendRegistry.'">';
echo "</div>";
echo '</form>';
echo "</div>";
echo "</div>";

// Wichtig! Ajax Abarbeitung hier!
echo "<div id='cg_registry_message' style='text-align:center;margin: 0 auto;font-size16px;color:red;font-weight:bold;'>";

echo "</div>";

$formOutput = ob_get_clean();

		if(!get_option("p_cgal1ery_count_users")){
			add_option( "p_cgal1ery_count_users", 10 );
		}
					
		if(get_option("p_cgal1ery_reg_code") == true
			&& strpos(floatval(get_option("p_cgal1ery_reg_code"))/44,".") == false && floatval(get_option("p_cgal1ery_reg_code"))!=0 && floatval(get_option("p_cgal1ery_reg_code"))>=3281329700){		
			echo @$formOutput;	
		}

		else if(get_option("p_cgal1ery_count_users")>=1){

			echo @$formOutput;
		 
		}
		else if(get_option("p_cgal1ery_count_users")<1){
			echo "<a href='https://www.contest-gallery.com/pro-version/' title='Get PRO version for unlimited registrations' target='_blank'>Get PRO version for unlimited registrations</a>";
		}
		else{
			echo "";				
		}

}

//echo "$language_MaximumAllowedWidthForJPGsIs";
echo "<input type='hidden' id='cg_show_upload' value='1'>";

//echo "language_ThisFileTypeIsNotAllowed: $language_ThisFileTypeIsNotAllowed";
echo "<input type='hidden' id='cg_file_not_allowed_1' value='$language_ThisFileTypeIsNotAllowed'>";
echo "<input type='hidden' id='cg_file_size_to_big' value='$language_TheFileYouChoosedIsToBigMaxAllowedSize'>";
//echo "<input type='hidden' id='cg_post_size' value='$post_max_sizeMB'>";

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

echo "<input type='hidden' id='cg_language_BulkUploadLowQuantityIs' value='$language_BulkUploadLowQuantityIs'>";

echo "<input type='hidden' id='cg_language_ThisMailAlreadyExists' value='$language_ThisMailAlreadyExists'>";
echo "<input type='hidden' id='cg_language_ThisUsernameAlreadyExists' value='$language_ThisUsernameAlreadyExists'>";

echo "<input type='hidden' id='cg_language_PleaseFillOut' value='$language_PleaseFillOut'>";

echo "<input type='hidden' id='cg_language_PasswordsDoNotMatch' value='$language_PasswordsDoNotMatch'>";




?>