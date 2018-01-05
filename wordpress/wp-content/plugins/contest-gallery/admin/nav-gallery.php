<?php
	
	global $wpdb;
	$tablename = $wpdb->prefix."contest_gal1ery";
	$proUploads = $wpdb->get_var( "SELECT COUNT(*) FROM $tablename WHERE id > '0' ");

	if(!get_option("p_cgal1ery_reminder_time")){
		add_option( "p_cgal1ery_reminder_time", time() );
	}
	
	if($proUploads > 200 && (strpos(floatval(get_option("p_cgal1ery_reg_code"))/44,".") == false && floatval(get_option("p_cgal1ery_reg_code"))!=0 && floatval(get_option("p_cgal1ery_reg_code"))>=3281329700)
		 && intval(get_option("p_cgal1ery_reminder_time"))+300 > time()){
		echo "<table style='border: 1px solid #DFDFDF;background-color:#ffffff;' width='937px;'>";
		echo "<tr><td align='center' width='937px;><div style='text-align:center;width:180px;' ><strong>Congratulations!</strong> More then 200 images were added with Contest Gallery.
		<br/>It takes maximum efforts to continue updating new cool features. <br/>A couple of seconds to give a nice review would provide enormous motivation to continue updating.
		<br/><br/><a href='https://wordpress.org/support/plugin/contest-gallery/reviews/' target='_blank'>Ok let's do it!</a> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
		<a href='mailto:support@contest-gallery.com?Subject=Please give me a hint. What&#x27;s wrong?' target='_top'>Forget it!</a>
		<br/><br/>This message will disappear in 5 minutes after you saw it first time</td><tr>";
		echo "</table>";
		echo "<br>";
	}
	
	echo "<table style='border: 1px solid #DFDFDF;background-color:#ffffff;' width='937px;'>";
	
	if(@$GalleryName){@$GalleryName="$GalleryName";}
	else {@$GalleryName="Contest Gallery";}
	
//	if(strpos(floatval(get_option("p_cgal1ery_reg_code"))/44,".") == false && floatval(get_option("p_cgal1ery_reg_code"))!=0 && floatval(get_option("p_cgal1ery_reg_code"))>=3281329700){	
		$versionColor = "#444";		
//	}
//	else{
	//	$versionColor = "#c2c2c2";		
//	}
	
	echo "<tr><td align='center'><div style='text-align:center;width:180px;' ><br/><strong>$GalleryName</strong><br/>Shortcodes<br/><br/></div></td>";
	echo "<td align='center'><div style='text-align:center;width:180px;' ><br/><strong>Gallery shortcode:</strong><br/>[cg_gallery id=\"$GalleryID\"]<br/><br/></div></td>";
	echo "<td align='center'><div style='text-align:center;width:180px;' ><br/><strong>Upload form shortcode:</strong><br/>[cg_users_upload id=\"$GalleryID\"]<br/><br/></div></td>";
	if(file_exists(plugin_dir_path( __FILE__ )."users/admin/registry/create-user-form.php")){
		
	echo "<td align='center'><div style='text-align:center;width:180px;' ><br/>";
	echo "<strong>Users registration form:</strong><br/>[cg_users_reg id=\"$GalleryID\"]<br/><br/></div></td>";
		
	}
	else{
		echo "<td align='center'><div style='width:180px;' >";	
	}

		

/*
echo <<<HEREDOC
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="29YDAVDLKPK6W">
<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
</form>


HEREDOC;*/
	
	echo "</div></td>"; 
	echo "</tr>";
	echo "</table>";
	
	echo "<br/>";
	//fef050 fcd729
	echo "<table style='border: 1px solid #DFDFDF;background-color:#ffffff;' width='937px;'>";
	echo "<tr>";
	echo "<td align='center'><br/><div><form method='POST' action='?page=contest-gallery/index.php' ><input type='submit' value='&nbsp;<<< &nbsp;&nbsp;Back to menu' style='text-align:left;width:180px;background:linear-gradient(0deg, #fef050 5%, #fce129 70%);'></form><br/></div></td>";
	echo "<td align='center'><br/><div><form method='POST' action='?page=contest-gallery/index.php&edit_options=true&option_id=$GalleryID' ><input type='hidden' name='option_id' value='$GalleryID'><input type='submit' value='Edit options' style='text-align:center;width:180px;background:linear-gradient(0deg, #bbe0ef 5%, #90d4f0 70%);' /></form><br/></div></td>";
	echo "<td align='center'><br/><div><form method='POST' action='?page=contest-gallery/index.php&define_upload=true&option_id=$GalleryID'><input type='hidden' name='option_id' value='$GalleryID'><input type='submit' value='Edit upload form' style='text-align:center;width:180px;background:linear-gradient(0deg, #bbe0ef 5%, #90d4f0 70%);' /></form><br/></div></td>"; 
	echo "<td align='center'><br/><div>";

	if(file_exists(plugin_dir_path( __FILE__ )."users/admin/registry/create-user-form.php")){
		
		//echo "<form method='POST' action='?page=contest-gallery/index.php&create_user_form=true&option_id=$GalleryID'><input type='hidden' name='option_id' value='$GalleryID'><input type='submit' value='PRO users management' style='text-align:center;width:180px;background:linear-gradient(0deg, #ffbe4e 5%, #ffac1c 70%);' /></form><br/>";
		echo "<form method='POST' action='?page=contest-gallery/index.php&create_user_form=true&option_id=$GalleryID'><input type='hidden' name='option_id' value='$GalleryID'><input type='submit' value='Edit registration form' style='text-align:center;width:180px;background:linear-gradient(0deg, #bbe0ef 5%, #90d4f0 70%);' /></form><br/>";
		
	}
    
	else{echo "<input type='hidden' name='option_id' value='$GalleryID'><input type='submit' value='Get pro version' style='text-align:center;width:180px;background:linear-gradient(0deg, #90d4f0 5%, #bbe0ef 70%);visibility:hidden;' />";}

	echo "</div></td>"; 
	echo "</tr>";
	
	echo "</table>";

	echo "<br/>";
	if(file_exists(plugin_dir_path( __FILE__ )."users/admin/registry/create-user-form.php")){
		echo "<div style='width:937px;'><div style='margin: 0 auto;width:180px;'>";
		echo "<form method='POST' action='?page=contest-gallery/index.php&users_management=true&option_id=$GalleryID'><input type='hidden' name='option_id' value='$GalleryID'><input type='submit' value='Users management' style='text-align:center;width:180px;background:linear-gradient(0deg, #ffbe4e 5%, #ffac1c 70%);' /></form><br/>";
		echo "</div></div>";		
	}

?>