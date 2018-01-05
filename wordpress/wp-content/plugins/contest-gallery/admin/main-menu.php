<?php


$siteURL = get_site_url()."/wp-admin/admin.php";




?>
<script type="text/javascript">

var siteURL = <?php echo json_encode($siteURL);?>;

function checkMe(arg) {

var del = arg;

    if (confirm("Are you sure you want to delete this gallery (id "+del+")? All uploaded pictures and entries will be irrevocable deleted.")) {
        //alert("Clicked Ok");
		//confirmForm();
		//fDeleteFieldAndData(del);
		//e.preventDefault();
		//window.location.href = '?page=contest-gallery/index.php&delete=true&option_id='+del+'';
		window.location.replace(siteURL+'?page=contest-gallery/index.php&delete=true&option_id='+del+'');
	    return true;
    } else {
        //alert("Clicked Cancel");
        return false;
    }
}

</script>
<?php

$permalinkURL = get_site_url()."/wp-admin/admin.php";

//echo "$permalinkURL 2323242";

	global $wpdb;

	$tablename_options = $wpdb->prefix . "contest_gal1ery_options";


	$selectSQL = $wpdb->get_results( "SELECT * FROM $tablename_options ORDER BY id ASC" );

	if(@$_POST['cgKey']==TRUE && strpos(floatval($_POST["cgKey"])/44,".") == false && floatval($_POST["cgKey"])!=0 && floatval($_POST["cgKey"])>=3281329700){

			if(get_option( "p_cgal1ery_reg_code" )){
				update_option( "p_cgal1ery_reg_code", $_POST['cgKey'] );
			}
			else{
				add_option( "p_cgal1ery_reg_code", $_POST['cgKey'] );
			}


	}
	else if(@$_POST['cgKey']==TRUE){
		echo "<p>Wrong Key</p>";
	}
	else{

	}

	echo '<div class="main-table">';
	echo "<table style='border: 1px solid #DFDFDF;background-color:#ffffff;' width='635px'>";
	echo "<tr>";
	echo "<td style='padding-left:20px;overflow:hidden;'><h2>Contest Gallery</h2></td>";
	if(get_option("p_cgal1ery_reg_code") == true
	&& strpos(floatval(get_option("p_cgal1ery_reg_code"))/44,".") == false && floatval(get_option("p_cgal1ery_reg_code"))!=0 && floatval(get_option("p_cgal1ery_reg_code"))>=3281329700){
		echo "<td style='padding-left:23px;overflow:hidden;'><h4 style='display:inline;'>You are using PRO version. For any issues on PRO version please contact <br/><a href='mailto:support-pro@contest-gallery.com'>support-pro@contest-gallery.com</a></h4></td>";
	}
	else{
		echo "<td style='padding-left:128px;overflow:hidden;'><h4 style='display:inline;'>PRO Version Key: </h4><form action='?page=contest-gallery/index.php' method='POST' style='display:inline;'>";
		echo "<input type='text' name='cgKey' value='' /><input type='submit' value='Enter' style='text-align:center;width:70px;font-size:14px !important;' /></form></td>";
	}
	echo "</tr>";
	echo "</table>";
	echo "<br/>";

    // Die nexte ID des Option Tables ermitteln
    $last = $wpdb->get_row("SHOW TABLE STATUS LIKE '$tablename_options'");
    $nextID = $last->Auto_increment;



		foreach($selectSQL as $value){

			$option_id = $value -> id;
			$GalleryName = $value -> GalleryName;

			if ($option_id % 2 != 0) {
			$backgroundColor = "#DFDFDF";
			} else {
			$backgroundColor = "#ECECEC";
			}

		echo "<table width='635px' style='border: 1px solid #DFDFDF;background-color:#ffffff;'>";

			echo "<tr style='background-color:#ffffff;'>";

			echo "<td style='padding-left:20px;width:50px;' ><p>ID: $option_id</p></td>";

			if($GalleryName){$GalleryName="<p style='text-align:center;'>$GalleryName</p>";}
			else {$GalleryName="";}

			echo "<td align='center'>$GalleryName";
			echo "<p>Shortcode: <strong>[cg_gallery id=\"".$option_id."\"]</strong></p></td>";
			echo '<td align="center"><p><form action="?page=contest-gallery/index.php&option_id='.$option_id.'
			&edit_gallery=true" method="POST" ><input type="hidden" name="option_id" value="'.$option_id.'">';
			echo '<input type="hidden" name="edit_gallery_hidden_post"';
			echo '<input type="hidden" name="page" value="contest-gallery/index.php"><input name="" value="Edit" type="Submit" 
            style="text-align:center;width:70px;background:linear-gradient(0deg, #bbe0ef 5%, #90d4f0 70%);"></form></p></td>';
			echo '<td align="center"><p><form action="?page=contest-gallery/index.php&option_id='.$nextID.'
			&edit_gallery=true&copy=true" method="POST" ><input type="hidden" name="option_id" value="'.$nextID.'">';
            echo '<input type="hidden" name="cg_copy" value="true">';
            echo '<input type="hidden" name="id_to_copy" value="'.$option_id.'">';
			echo '<input type="hidden" name="edit_gallery_hidden_post"';
			echo '<input type="hidden" name="page" value="contest-gallery/index.php"><input name="" value="Copy" type="Submit" 
            style="text-align:center;width:70px;background:linear-gradient(0deg, #f1f1f1 5%, #eae3e3 70%);"></form></p></td>';
			echo '<td align="center"><p><form action="?page=contest-gallery/index.php" method="GET" >
            <input type="hidden" name="option_id" value="'.$option_id.'">';
			echo '<input type="hidden" name="delete" value="true"><input type="button" value="Delete" onClick="return checkMe('.$option_id.')"
            style="text-align:center;width:70px;background:linear-gradient(0deg, #fef050 5%, #fce129 70%);"></form></p></td>';
			//echo '<td style="padding-left:100px;padding-right:20px;"><p><form action="?page=contest-gallery/index.php&option_id=' . $option_id . '&delete=true" method="GET" ><input value="L&ouml;schen" type="Submit"></form></p></td>';

			echo "</tr>";

		echo "</table>";

		@$option_id++;

			}



echo "<br/>";



echo "<table style='border: 1px solid #DFDFDF;background-color:#ffffff;' width='635px'>";
 	echo '<tr><td style="padding-left:20px;overflow:hidden;" colspan="4"><p><form action="?page=contest-gallery/index.php&option_id='.$nextID.'&edit_gallery=true&create=true" method="POST" >';
	echo '<input type="hidden" name="cg_create" value="true">';
	echo '<input type="hidden" name="option_id" value="'.$nextID.'">';
	echo '<input type="hidden" name="create" value="true"><input type="hidden" name="page" value="contest-gallery/index.php">
    <input name="" value="New gallery" type="Submit" style="background:linear-gradient(0deg, #f1f1f1 5%, #eae3e3 70%);"></form></p></td></tr>';
 	//echo '<tr><td style="padding-left:20px;overflow:hidden;" colspan="4"><p><a href="?page=contest-gallery/index.php&option_id=' . $option_id . '&create=true" class="classname">Neue Galerie</a></p></td></tr>';
 	//echo '<tr><td style="padding-left:20px;overflow:hidden;" colspan="4"><p><a href="?page=contest-gallery/index.php&option_id=' . $option_id . '&create=true" class="classname">Neue Galerie</a></p></td></tr>';

	echo "</table>";

	echo '</div>';

?>