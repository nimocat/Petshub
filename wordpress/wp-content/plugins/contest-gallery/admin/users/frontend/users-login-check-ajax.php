<?php
		
		
		// 1 = Mail or Username
		// 2 = Password
		// 3 = Check	
		// 4 = GalleryID

		session_start();
		
		/*if(@$_SESSION["cg_login_count"]==false){
			echo "Plz don't manipulate the registry Code:117";return false;
		}*/
		
		
		
		if(@$_SESSION["cg_login_count"]==false){
			//Achtung! Mit 1 anfangen ansonsten wird als false gezählt wenn es mit 0 anfängt.
			$_SESSION["cg_login_count"]=1;
		}
		else{
			$_SESSION["cg_login_count"]++;
		}
		
		if(@$_SESSION["cg_login_count"]>15){
			echo "To many invalid atempts. Please try few minutes later again";return false;
		}		

		$cg_check = $_REQUEST['action3'];
		
		// Hier geht die Validierung los
		if($_REQUEST['action3']==wp_create_nonce("check")){
			
		global $wpdb;		
		$tablenameWpUsers = $wpdb->prefix . "users";

		
		$GalleryID = sanitize_text_field($_REQUEST['action4']);
		
		$cg_login_name_mail = sanitize_text_field($_REQUEST['action1']);
		
		$cg_user_email = false;
		$cg_user_login = false;
		
		//Check name or email			
			if(is_email($cg_login_name_mail)){
				$checkWpUserLogin = $wpdb->get_var("SELECT user_login FROM $tablenameWpUsers WHERE user_email = '".$cg_login_name_mail."'");
			}
			else{
				$checkWpUserLogin = $wpdb->get_var("SELECT user_login FROM $tablenameWpUsers WHERE user_login = '".$cg_login_name_mail."'");
			}

		if($checkWpUserLogin==false){
		
				
?>
<script>
var cg_language_UsernameOrEmailDoesNotExist = document.getElementById("cg_language_UsernameOrEmailDoesNotExist").value;

var cg_check_mail_name_value = document.getElementById('cg_check_mail_name_value');
cg_check_mail_name_value.value = 1;

var cg_append_login_name_mail_fail = document.getElementById('cg_append_login_name_mail_fail');
cg_append_login_name_mail_fail.innerHTML = cg_append_login_name_mail_fail.innerHTML + cg_language_UsernameOrEmailDoesNotExist;

// Password Feld leer machen
var cg_login_password = document.getElementById('cg_login_password');
cg_login_password.value = '';

location.href = "#cg_user_registry_anchor";

</script>
<?php	
		return false;

		}
		
		else{
			
			$cg_login_password = sanitize_text_field($_REQUEST['action2']);	
			
			$cgPwHash = $wpdb->get_var("SELECT user_pass FROM $tablenameWpUsers WHERE user_login = '".$checkWpUserLogin."'");
		
			require_once(ABSPATH ."wp-load.php");
			$cgCheckPw = (wp_check_password($cg_login_password, $cgPwHash));		
			
			if($cgCheckPw==false){		
				
?>
<script>
var cg_language_PasswordIsWrong = document.getElementById("cg_language_PasswordIsWrong").value;

var cg_check_mail_name_value = document.getElementById('cg_check_mail_name_value');
cg_check_mail_name_value.value = 1;

var cg_append_login_password_fail = document.getElementById('cg_append_login_password_fail');
cg_append_login_password_fail.innerHTML = cg_append_login_password_fail.innerHTML + cg_language_PasswordIsWrong;

// Password Feld leer machen
var cg_login_password = document.getElementById('cg_login_password');
cg_login_password.value = '';

location.href = "#cg_user_registry_anchor";

</script>
<?php	
			}
			else{
					// Anzahl Login Versuche beginnt von Vorne
					$_SESSION["cg_login_count"]=1;
					$creds = array();
					$creds['user_login'] = $checkWpUserLogin;
					$creds['user_password'] = $cg_login_password;
					$creds['remember'] = true;
					$user = wp_signon( $creds, true );

			}
			


		}
		
		}
		
		else{

            ?>
            <script>
                console.log("Login manipulation prevention code 341. Please contact Administrator if you have questions.");
            </script>
            <?php
            die();
		
		}  

  
?>