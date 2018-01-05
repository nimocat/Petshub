<?php 

if($_POST['cg_check']==wp_create_nonce("check") && @$_POST['cg_Fields']==true){
	
		global $wpdb;
		
		$tablenameCreateUserEntries = $wpdb->prefix . "contest_gal1ery_create_user_entries";
		$tablenameProOptions = $wpdb->prefix . "contest_gal1ery_pro_options";
		$tablenameWpUsers = $wpdb->prefix . "users";
		
		$proOptions = $wpdb->get_row("SELECT * FROM $tablenameProOptions WHERE GalleryID = '".$GalleryID."'");
		
		$cg_check = $_POST['cg_Fields'] ;
		
		// Validierung und Erstellung von Activation Key
		foreach($cg_check as $key => $value){
			if($value["Field_Type"]=="password"){
				$password = sanitize_text_field($value["Field_Content"]);
				$activation_key = md5(time().$password);
			}
			
			if($value["Field_Type"]=="password-confirm"){
				$passwordConfirm = sanitize_text_field($value["Field_Content"]);
			}
			
			if($value["Field_Type"]=="main-mail"){
				$cg_main_mail = sanitize_text_field($value["Field_Content"]);
				$checkWpIdViaMail = $wpdb->get_var("SELECT ID FROM $tablenameWpUsers WHERE user_email = '".$cg_main_mail."'");
			}
			
			if($value["Field_Type"]=="main-user-name"){
				$cg_main_user_name = sanitize_text_field($value["Field_Content"]);
				$checkWpIdViaName = $wpdb->get_var("SELECT ID FROM $tablenameWpUsers WHERE user_login = '".$cg_main_user_name."'");
			}
			
		}
		
		if($password!=$passwordConfirm){
			echo "Plz don't manipulate the registry Code:221";return false;
		}		
		
		if($checkWpIdViaMail){
			echo "Plz don't manipulate the registry Code:222";return false;
		}
		if($checkWpIdViaName){
			echo "Plz don't manipulate the registry Code:223";return false;
		}
		if($cg_main_mail==false){
			echo "Plz don't manipulate the registry Code:224";return false;
		}
		if(is_email($cg_main_mail)==false){
			echo "Plz don't manipulate the registry Code:225";return false;
		}
		
		$password = wp_hash_password($password);
			
		// Validierung und Erstellung von Activation Key --- ENDE
		
		// Einfügen von Werten mit Kennzeichnung durch Activation Key zur späteren Wiederfindung
		
		foreach($cg_check as $key => $value){
			
		//	var_dump($value);echo "<br>";			

			$Form_Input_ID = sanitize_text_field($value["Form_Input_ID"]);
			$Field_Type = sanitize_text_field($value["Field_Type"]);
			$Field_Order = sanitize_text_field($value["Field_Order"]);
			@$Field_Content = sanitize_text_field($value["Field_Content"]);
			@$Field_Name = sanitize_text_field($value["Field_Name"]);
			
			if($value["Field_Type"]=="password"){$Field_Content = $password;}
			if($value["Field_Type"]=="password-confirm"){$Field_Content = $password;}
			
			//var_dump($password);die;

					$wpdb->query( $wpdb->prepare(
					"
						INSERT INTO $tablenameCreateUserEntries
						(id, GalleryID, wp_user_id, f_input_id, Field_Type,
						Field_Content, activation_key)
						VALUES (%s,%d,%d,%d,%s,
						%s,%s)
					",
						'',$GalleryID,'',$Form_Input_ID,$Field_Type,
						@$Field_Content,$activation_key
					) );
			
		}
		
		// Einfügen von Werten mit Kennzeichnung durch Activation Key zur späteren Wiederfindung --- ENDE
		
			// Versand E-Mail mit confirmation Link
		
			/*
			require_once(dirname(__FILE__)."/class-inform-user.php");
			$headers[] .= "Reply-To: ". strip_tags(@$Reply) . "\r\n";
			$headers .= "Reply-To: ". strip_tags(@$Reply) . "\r\n";
			$headers .= "CC: $cc\r\n";
			$headers .= "BCC: $bcc\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";*/
			
			// Check if valid mail. Wenn nicht dann admin Mail nehmen.
			if(is_email(@$proOptions->RegMailReply)){$cgReply = @$proOptions->RegMailReply;}
			else{$cgReply = get_option('admin_email');}
			

			
			$headers = array();			
			$headers[] = "From: ". strip_tags(@$proOptions->RegMailAddressor) . " <". strip_tags($cgReply) . ">";
			$headers[] = "Reply-To: ". strip_tags($cgReply) . "";
			$headers[] = "MIME-Version: 1.0";
			$headers[] = "Content-Type: text/html; charset=utf-8";
			
			@$TextEmailConfirmation = nl2br(html_entity_decode(stripslashes(@$proOptions->TextEmailConfirmation)));
			@$ForwardAfterRegText = nl2br(html_entity_decode(stripslashes(@$proOptions->ForwardAfterRegText)));

			@$posUrl = '$regurl$';
			
			
			
			@$urlCheck = (stripos(@$TextEmailConfirmation,@$posUrl)) ? 1 : 0;
			
			if($urlCheck!=1){echo "Confirmation URL can't be provided. Plz contact Administrator"; die();}
			else{
				@$currentPageUrl = get_permalink();
				@$currentPageUrl = (strpos(@$currentPageUrl,'?')) ? @$currentPageUrl.'&' : @$currentPageUrl.'?';
				@$TextEmailConfirmation = str_ireplace(@$posUrl, $currentPageUrl."cgkey=$activation_key#cg_activation", @$TextEmailConfirmation);
			}			
			
			@wp_mail(@$cg_main_mail, @$proOptions->RegMailSubject, @$TextEmailConfirmation, @$headers);
			
			
							echo "<div id='cg_reg_confirm' style='padding-bottom:100px;'>";
							echo "<p>";
							echo "$ForwardAfterRegText";
							echo "</p>";
							echo "</div>";
							
			
			?>

<script>

location.href = "#cg_reg_confirm";
		

</script>

<?php	
			
			
			
			// Versand E-Mail mit confirmation Link --- ENDE	

}
?>