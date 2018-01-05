<?php

		$cg_wp_upload_ids = $_REQUEST['action1'];
		$galeryID = $_REQUEST['action2'];	


$table_posts = $wpdb->prefix."posts";
$tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";
$selectSQL1 = $wpdb->get_row( "SELECT * FROM $tablenameOptions WHERE id = '$galeryID'" );
$tablename = $wpdb->prefix . "contest_gal1ery";

//var_dump("asdfsad");die;

foreach($cg_wp_upload_ids as $key => $value){
	
	
$wp_image_info = $wpdb->get_row("SELECT * FROM $table_posts WHERE ID = '$value'");
$image_url = $wp_image_info->guid;
$post_title = $wp_image_info->post_title;
$post_type = $wp_image_info->post_mime_type;
$wp_image_id = $wp_image_info->ID;

// Notwendig: wird in convert-several-pics so verabeitet. Darf keine Sonderzeichen enthalten!
$search = array(" ", "!", '"', "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "/", ":", ";", "=", "?", "@", "[","]","‘");
$post_title = str_replace($search,"_",$post_title);
    //var_dump($post_title); die;
$dateiname = $post_title;

$doNotProcess=0;

if($post_type=="image/jpeg"){$post_type="jpg";}
else if($post_type=="image/png"){$post_type="png";}
else if($post_type=="image/gif"){$post_type="gif";}
else{	
	$doNotProcess=1;
}
//echo "post_type $post_type<br>";
$uploads = wp_upload_dir();

$check = explode($uploads['baseurl'],$image_url);

//echo $uploads['basedir'].$check[1].$post_title.".".$post_type;

$filename = $uploads['basedir'].$check[1];


//echo "post_type $filename<br>";

   // var_dump($filename); die;

  //  var_dump($dateiname); die;


if($doNotProcess!=1){
	
	$unix = time();
	$unixadd = $unix+2;
	
	require(dirname(__FILE__) . "/../../convert-several-pics.php");

	$WpUserId = get_current_user_id();

	$wpdb->query( $wpdb->prepare(
		"
			INSERT INTO $tablename
			( id, rowid, Timestamp, NamePic,
			ImgType, CountC, CountR, Rating,
			GalleryID, Active, Informed, WpUpload,Width,Height,WpUserId)
			VALUES ( %s,%s,%d,%s,
			%s,%d,%s,%s,
			%d,%s,%s,%s,%s,%s,%s )
		", 
			'','',$unixadd,$dateiname,
			$post_type,0,'','',
			$galeryID,'','',$wp_image_id,$current_width,$current_height,$WpUserId
	 ) );
	 
	$nextId = $wpdb->get_var("SELECT id FROM $tablename WHERE Timestamp='$unixadd' AND NamePic='$post_title'");
	
	//echo "nextId $nextId<br>";

	$wpdb->update(
		"$tablename",
		array('rowid' => $nextId), 
		array('id' => $nextId), 
		array('%d'),
		array('%d')
	);
	
	
	if(!get_option("p_cgal1ery_uploadscounter_reminder")){
		add_option( "p_cgal1ery_uploadscounter_reminder", 0 );
	}
	
	if(get_option("p_cgal1ery_uploadscounter_reminder")){
		$p_cgal1ery_uploadscounter_reminder = get_option("p_cgal1ery_uploadscounter_reminder");
		$p_cgal1ery_uploadscounter_reminder++;
		update_option( "p_cgal1ery_uploadscounter_reminder", $p_cgal1ery_uploadscounter_reminder);
	}
	
	

		if(get_option( "p_cgal1ery_reg_code" )==false || intval(get_option( "p_cgal1ery_reg_code" )) % 44 != 0 && intval(get_option( "p_cgal1ery_reg_code" )) != 0){

			if(get_option("p_cgal1ery_count_uploads")==true){
				$p_cgal1ery_count_uploads = get_option( "p_cgal1ery_count_uploads" );
					
				$p_cgal1ery_count_uploads--;
	
				update_option( "p_cgal1ery_count_uploads", $p_cgal1ery_count_uploads );
			}
			else{
				add_option( "p_cgal1ery_count_uploads", 50 );
			}
		
		}
}
else{
	
	echo "One of your selected file types is unsupported. Only JPG, PNG and GIF are allowed.";
}
	
}


