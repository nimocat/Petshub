<?php

// SQL zum Ermitteln von allen Komments mit gesendeter picture id


		/*echo "<br>tablenameComments: $tablenameComments<br>";
		echo "<br>galeryID: $galeryID<br>";
		echo "<br>pictureID: $pictureID<br>";
		echo "<br>start: $start<br>";
		echo "<br>order: $order<br>";
		echo "<br>step: $step<br>"; */
		
		global $wpdb; 
		$tablenameComments = $wpdb->prefix . "contest_gal1ery_comments"; 
		
	
		// Wird abgefragt wenn kein Comments Set vorher gesendet wurde
		if(!@$_REQUEST['action3']){
		$pictureID = intval(@$_REQUEST['action1']); 
		}
	

			
		$select_comments = $wpdb->get_results( "SELECT * FROM $tablenameComments WHERE pid='$pictureID' ORDER BY Timestamp DESC" );
		
		//print_r($select_comments);
		

		//echo "<br>";
		
		//print_r($select_comments);
		
		if($select_comments){  
		foreach($select_comments as $value){
		
		$id = $value->id;
		$name = html_entity_decode(stripslashes(($value->Name)));
		$date = html_entity_decode(stripslashes(($value->Date)));
		$timestamp = $value->Timestamp;
		//$comment = html_entity_decode(stripslashes(($value->Comment)));
		$comment1 = html_entity_decode(stripslashes(($value->Comment)));
		
		

		echo "<div style='margin-top:20px;font-size:18px;font-weight:bold;color:#fff;word-wrap: break-word;'>";
		echo "<hr style='margin-left:0px;' class='cg_picture_comments_single_view_hr'>";
		echo "<div style='line-height:18px;height:18px;font-size:18px;font-weight:normal;color:#fff;word-wrap: break-word; class='cg_your_comment_name_single_view'><b>$name</b> ";
		echo "(<div id='cg-comment-slider-$id' style='display:inline;'></div>):</div>";
		//echo "<br/>";
		
		
?>
<script type="text/javascript">


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
	
//	alert(id);
	
	var div = document.getElementById("cg-comment-slider-"+id);
	
	div.innerHTML = div.innerHTML + commentDate;
	
//$( "#cg-comment-"+id ).append( commentDate );	


//alert(commentDate);

</script>



<?php
		
		
		echo "$comment1";
		echo "</div>";
		
			}
			
				//echo "<br>";
				echo "<hr style='margin-left:0px;' class='cg_comments_hr' style='margin-left:0px;' class='cg_picture_comments_single_view_hr'>";
	//echo "<hr style='padding:0px;margin:0px;width:$widthCommentArea;'>";
		
		}


?>