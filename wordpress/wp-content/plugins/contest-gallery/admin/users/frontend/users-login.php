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

/*
session_start();
//echo @$_SESSION["cg_login_count"];
if(@$_SESSION["cg_login_count"]==false){
	//Achtung! Mit 1 anfangen ansonsten wird als false gez�hlt wenn es mit 0 anf�ngt.
	$_SESSION["cg_login_count"]=1;
}
//unset($_SESSION["cg_login_count"]);
if(!@$_SESSION["cg_start_time"]){
	$_SESSION["cg_start_time"]=time();
}

// Nach 10 Minuten wird die Session von der Zeit und von den Counts neu gesetzt
if($_SESSION["cg_start_time"]){
	if(time()-600 > $_SESSION["cg_start_time"]){
		$_SESSION["cg_start_time"]=time();
		$_SESSION["cg_login_count"]=0;
	}
}

if(@$_SESSION["cg_login_count"]>15){
	echo "To many invalid atempts. Please try few minutes later again";return false;
}*/


if(!@$_SESSION["cg_start_time"]){
	$_SESSION["cg_start_time"]=time();
}

// Nach 10 Minuten wird die Session von der Zeit und von den Counts neu gesetzt
if($_SESSION["cg_start_time"]){
	if(time()-600 > $_SESSION["cg_start_time"]){
		$_SESSION["cg_start_time"]=time();
		$_SESSION["cg_login_count"]=0;
	}
}

$check = wp_create_nonce("check");

include(dirname(__FILE__)."/../../../frontend/check-language.php");

global $wpdb;
$tablenameCreateUserForm = $wpdb->prefix . "contest_gal1ery_create_user_form";
$tablename_pro_options = $wpdb->prefix . "contest_gal1ery_pro_options";

if(@$_POST['cg_login_check']){
	
	$ForwardAfterLoginText = nl2br(html_entity_decode(stripslashes($wpdb->get_var("SELECT ForwardAfterLoginText FROM $tablename_pro_options WHERE GalleryID = '$GalleryID'"))));
		
	
							echo "<div id='cg_login' style='padding-top:50px;'>";
							echo "<p>$ForwardAfterLoginText</p>";
							echo "</div>";
							
			
			?>

<script>

location.href = "#cg_login";
		

</script>

<?php								
			

}

else{
	
	if(!is_user_logged_in()){
		
		ob_start();

		$selectUserForm = $wpdb->get_results("SELECT * FROM $tablenameCreateUserForm WHERE GalleryID = '$GalleryID'");

						
		$ForwardAfterLoginUrlValues = $wpdb->get_row("SELECT ForwardAfterLoginUrlCheck, ForwardAfterLoginUrl FROM $tablename_pro_options WHERE GalleryID = '$GalleryID'");
		$ForwardAfterLoginUrlCheck = $ForwardAfterLoginUrlValues->ForwardAfterLoginUrlCheck;
		$ForwardAfterLoginUrl = $ForwardAfterLoginUrlValues->ForwardAfterLoginUrl;

		echo "<input type='hidden' id='cg_gallery_id' value='$GalleryID'/>";

		$i=1;


		echo "<div id='cg_user_login_div' >";
			echo "<div>";
				echo "<input type='hidden' id='cg_check_mail_name_value' value='0'>";
				echo "<input type='hidden' id='cg_site_url' value='".get_site_url()."'/>";
				echo "<input type='hidden' id='cg_gallery_id' value='$GalleryID'/>";
				echo "<input type='hidden' id='cg_ForwardAfterLoginUrlCheck' value='$ForwardAfterLoginUrlCheck'/>";
				echo "<input type='hidden' id='cg_ForwardAfterLoginUrl' value='$ForwardAfterLoginUrl'/>";

				echo "<a id='cg_user_registry_anchor'/>";

				echo '<form action="" method="post" id="cg_user_login_form">';

				echo "<input type='hidden' name='cg_login_check' id='cg_login_check' value='".wp_create_nonce("check")."'>";

								echo "<div id='cg-login-1' class='cg_form_div'>";
								echo "<label for='cg_login_name_mail'>$language_UsernameOrEmail</label>";
								echo "<input type='text'  id='cg_login_name_mail' name='cg_login_name_mail'>";
								echo "<p id='cg_append_login_name_mail_fail' class='cg_input_error' ></p>";// Fehlermeldung erscheint hier				
								echo "</div>";
								
								echo "<div id='cg-login-2' class='cg_form_div'>";
								echo "<label for='cg_login_password'>$language_Password</label>";
								echo "<input type='password'  id='cg_login_password' name='cg_login_password'>";
								echo "<p id='cg_append_login_password_fail' class='cg_input_error' ></p>";// Fehlermeldung erscheint hier				
								echo "</div>";


				echo "<div>";
				echo '<input type="submit" name="submit" id="cg_user_login_check" value="'.$language_sendLogin.'">';
				echo "</div>";
				echo '</form>';
			echo "</div>";
		echo "</div>";

		// Wichtig! Ajax Abarbeitung hier!
		echo "<div id='cg_message' style='text-align:center;margin: 0 auto;font-size16px;color:red;font-weight:bold;'>";

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
	else{
		echo "$language_YouAreAlreadyLoggedIn";		
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

echo "<input type='hidden' id='cg_language_PasswordRequired' value='$language_PasswordRequired'>";
echo "<input type='hidden' id='cg_language_UsernameOrEmailRequired' value='$language_UsernameOrEmailRequired'>";

echo "<input type='hidden' id='cg_language_UsernameOrEmailDoesNotExist' value='$language_UsernameOrEmailDoesNotExist'>";
echo "<input type='hidden' id='cg_language_PasswordIsWrong' value='$language_PasswordIsWrong'>";




?>