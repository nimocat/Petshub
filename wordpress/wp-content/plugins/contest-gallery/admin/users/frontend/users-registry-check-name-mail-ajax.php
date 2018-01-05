<?php
		
		
		// 1 = Mail
		// 2 = Name
		// 3 = Check	


		$cg_check = $_REQUEST['action3']; 
		
		if($_REQUEST['action3']==wp_create_nonce("check")){
			
		global $wpdb;		
		$tablenameWpUsers = $wpdb->prefix . "users";
		
		$cg_main_mail = sanitize_text_field($_REQUEST['action1']);
		$cg_main_user_name = sanitize_text_field($_REQUEST['action2']);
		
			
		$checkWpIdViaMail = $wpdb->get_var("SELECT ID FROM $tablenameWpUsers WHERE user_email = '".$cg_main_mail."'");
		$checkWpIdViaName = $wpdb->get_var("SELECT ID FROM $tablenameWpUsers WHERE user_login = '".$cg_main_user_name."'");

			
		if($checkWpIdViaMail==true){

		
				
?>
<script>
var cg_language_ThisMailAlreadyExists = document.getElementById("cg_language_ThisMailAlreadyExists").value;

var cg_check_mail_name_value = document.getElementById('cg_check_mail_name_value');
cg_check_mail_name_value.value = 1;

//var div = document.getElementById('divID');
var cg_mail_check_alert = document.getElementById('cg_mail_check_alert');
cg_mail_check_alert.innerHTML = cg_mail_check_alert.innerHTML + cg_language_ThisMailAlreadyExists;

location.href = "#cg_user_registry_anchor";

//alert(cg_language_ThisMailAlreadyExists);
</script>
<?php	
		}
		
		if($checkWpIdViaName==true){

		
				
?>
<script>
var cg_language_ThisUsernameAlreadyExists = document.getElementById("cg_language_ThisUsernameAlreadyExists").value;

var cg_check_mail_name_value = document.getElementById('cg_check_mail_name_value');
cg_check_mail_name_value.value = 1;

var cg_user_name_check_alert = document.getElementById('cg_user_name_check_alert');
cg_user_name_check_alert.innerHTML = cg_user_name_check_alert.innerHTML + cg_language_ThisUsernameAlreadyExists;

location.href = "#cg_user_registry_anchor";


//alert(cg_language_ThisUsernameAlreadyExists);
</script>
<?php	
		}


		}
		
		else{

            ?>
            <script>
                console.log("Registration manipulation prevention code 331. Please contact Administrator if you have questions.");
            </script>
            <?php
            die();

		
		}  

  
?>