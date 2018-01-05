<?php

global $wpdb;
$tablename_comments = $wpdb->prefix . "contest_gal1ery_comments";
$tablename = $wpdb->prefix . "contest_gal1ery";

$galeryNR=@$_GET['option_id'];
$pid=@$_GET['id'];


$deleteComments=@$_POST['delete-comment'];


		echo "<table style='border: 1px solid #DFDFDF;background-color:#ffffff;' width='937px;'>";
	echo "<tr><td align='center'><div style='text-align:center;width:180px;' ><br/><strong>Contest Galery</strong><br/>Shortcodes<br/><br/></div></td>";
	echo "<td align='center'><div style='text-align:center;width:180px;' ><br/><strong>Gallery shortcode:</strong><br/>[cg_gallery id=\"$galeryNR\"]<br/><br/></div></td>";
	echo "<td align='center'><div style='text-align:center;width:180px;' ><br/><strong>Upload form shortcode:</strong><br/>[cg_users_upload id=\"$galeryNR\"]<br/><br/></div></td>";
    echo "<td align='center'><div style='text-align:center;width:180px;' ><br/><strong>Users registration form:</strong><br/>[cg_users_reg id=\"$galeryNR\"]<br/><br/></div></td>";
	echo "</tr>";
	echo "</table>";

	echo "<br/>";

	echo "<table style='border: 1px solid #DFDFDF;background-color:#ffffff;' width='937px;'>";
	echo "<tr>";
	echo "<td align='center'><br/><div><form method='POST' action='?page=contest-gallery/index.php&edit_gallery=true&option_id=$galeryNR' ><input type='submit' value='&nbsp;<<< &nbsp;&nbsp;Back to gallery' style='text-align:left;width:180px;background:linear-gradient(0deg, #fef050 5%, #fce129 70%);'></form><br/></div></td>";
	echo "<td align='center'><br/><div><form method='POST' action='?page=contest-gallery/index.php&edit_options=true&option_id=$galeryNR' ><input type='hidden' name='option_id' value=''><input type='submit' value='Edit options' style='text-align:center;width:180px;background:linear-gradient(0deg, #bbe0ef 5%, #90d4f0 70%);' /></form><br/></div></td>";
	echo "<td align='center'><br/><div><form method='POST' action='?page=contest-gallery/index.php&define_upload=true&option_id=$galeryNR'><input type='hidden' name='option_id' value='$galeryNR'><input type='submit' value='Edit upload form' style='text-align:center;width:180px;background:linear-gradient(0deg, #bbe0ef 5%, #90d4f0 70%);' /></form><br/></div></td>";
	echo "<td align='center'><br/><div><form method='POST' action='?page=contest-gallery/index.php&inform_user=true&option_id=$galeryNR'><input type='hidden' name='option_id' value='$galeryNR'><input type='submit' value='Edit registration form' style='text-align:center;width:180px;background:linear-gradient(0deg, #bbe0ef 5%, #90d4f0 70%);' /></form><br/></div></td>";
	echo "</tr>";

	echo "</table>";

echo "<br/>";
if(file_exists(plugin_dir_path( __FILE__ )."../users/admin/registry/create-user-form.php")){
    echo "<div style='width:937px;'><div style='margin: 0 auto;width:180px;'>";
    echo "<form method='POST' action='?page=contest-gallery/index.php&users_management=true&option_id=$galeryNR'><input type='hidden' name='option_id' value='$galeryNR'>
<input type='submit' value='Users management' style='text-align:center;width:180px;background:linear-gradient(0deg, #ffbe4e 5%, #ffac1c 70%);' /></form><br/>";
    echo "</div></div>";
}




// SQL zum Ermitteln von allen Komments mit gesendeter picture id


		/*echo "<br>tablenameComments: $tablenameComments<br>";
		echo "<br>galeryID: $galeryID<br>";
		echo "<br>pictureID: $pictureID<br>";
		echo "<br>start: $start<br>";
		echo "<br>order: $order<br>";
		echo "<br>step: $step<br>"; */
		
// DATEN Löschen und exit	


				
						//		$updateCountC = "UPDATE $tablename SET CountC='0' WHERE id = '$pid'";
				//$wpdb->query($updateCountC);

	if ($deleteComments) {
	
			//$deleteQuery = 'DELETE FROM ' . $tablename_comments . ' WHERE';
		

			foreach($deleteComments as $key => $value){
		
			
						//$deleteQuery .= ' id = "' . $value . '"';
						//$deleteQuery .= ' or';
						
						$deleteQuery = 'DELETE FROM ' . $tablename_comments . ' WHERE';			
						$deleteQuery .= ' id = %d';
						
						$deleteParameters = '';
						$deleteParameters .= $value;
						
						$wpdb->query( $wpdb->prepare(
						"
							$deleteQuery
						", 
							$deleteParameters
					 ));
		
			}
			
						//$deleteQuery = substr($deleteQuery,0,-3);	
						//$wpdb->query($deleteQuery);
				
						//$countPicsSQL = $wpdb->get_var( "SELECT COUNT(1) FROM $tablename_comments WHERE pid='$pid'");
						
						$countCommentsSQL = $wpdb->get_var( $wpdb->prepare( 
						"
							SELECT COUNT(1)
							FROM $tablename_comments 
							WHERE pid = %d
						",
						$pid
						) );						
						
				//echo "<br>pid: $pid<br>";		
				//echo "<br>countPicsSQL: $countPicsSQL<br>";		
				
			//	$updateCountC = "UPDATE $tablename SET CountC='$countPicsSQL' WHERE id = '$pid'";
				//$wpdb->query($updateCountC);
				
						$wpdb->update( 
						"$tablename",
						array('CountC' => $countCommentsSQL), 
						array('id' => $pid), 
						array('%d'),
						array('%d')
						);
						

				

		}
		
		
	
// DATEN Löschen und exit ENDE	

		$select_comments = $wpdb->get_results( "SELECT * FROM $tablename_comments WHERE pid='$pid' ORDER BY Timestamp DESC" );


		
		if($select_comments){  
		
		echo "<div style='width:895px;padding:20px;background-color:#fff;margin-bottom:0px !important;margin-bottom:0px;border: 1px solid #DFDFDF;'>";
		
		
		echo "<form action='?page=contest-gallery/index.php&option_id=$galeryNR&show_comments=true&id=$pid' method='POST'>";	
		
		//print_r($select_comments);
		
		
		foreach($select_comments as $value){
		
		$id = $value->id;
		$pid = $value->pid;
		$name = htmlspecialchars($value->Name);
		$date = htmlspecialchars($value->Date);
		$timestamp = $value->Timestamp;
		$comment = nl2br(htmlspecialchars($value->Comment));
		$comment1 = $value->Comment;
		
		

		echo "<div style='margin-bottom:20px;margin-top:20px;'>";
		echo "<hr style='width:$WidthGalery;margin-left:0px;'>";
		echo "<div style='display:inline;'><b>$name</b> ";
		echo "(<div id='cg-comment-$id' style='display:inline;'></div>):</div><div style='display:inline;float:right;'>Delete: &nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='delete-comment[]' value='$id'></div>";
		echo "<br/>";
		
		
?>
<script>



var getTimestamp = <?php echo json_encode($timestamp);?>;
var id = <?php echo json_encode($id);?>;

var commentDate = new Date(getTimestamp*1000);

var month = parseInt(commentDate.getMonth());
	month = month+1;

var monthUS = month;

//alert(commentDate.getMinutes());

	var hours = commentDate.getHours();
	var minutes = commentDate.getMinutes();

	if(commentDate.getMinutes()<10){
		
	var minutes = "0"+commentDate.getMinutes();
		
	}
	
	if(commentDate.getHours()<10){
		
	var hours = "0"+commentDate.getHours();
		
	}
	
	commentDate = commentDate.getFullYear()+"/"+monthUS+"/"+commentDate.getDate()+" "+hours+":"+minutes;
	
	
//$("#cg-comment-"+id ).append( commentDate );	

var tagID = 'cg-comment-'+id;
//alert(tagID);
var elem = document.getElementById(tagID);
elem.innerHTML  = commentDate;


//alert(id);
//alert(commentDate);

</script>



<?php
		
		
		
		
		
		
		
		
		echo "<p>$comment1</p>";
				echo "<br/>";

		echo "</div>";
		
			}
		
		
		

	
echo "</div>";

								echo "<div style='padding:20px;background-color:white;width:895px;text-align:right;margin-top:0px;border-bottom: 1px solid #DFDFDF;border-left: 1px solid #DFDFDF;border-right: 1px solid #DFDFDF;'>";
		echo '&nbsp;&nbsp; <input type="submit" value="Delete" id="submit" style="text-align:center;width:80px;">';
		//echo '<input type="hidden" value="delete-comment" name="delete-comment">';

echo '</form>';						
		echo "</div>";
}

		else{
		echo "<div style='width:895px;padding:20px;background-color:#fff;margin-bottom:0px !important;margin-bottom:0px;border: 1px solid #DFDFDF;text-align:center;'>";	
		echo "<p><b>All picture comments are deleted</b></p>";
		echo "</div>";
			
		}

?>