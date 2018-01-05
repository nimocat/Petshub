<?php
/*
Plugin Name: Contest Gallery
Description: Create a professional photo contest.
Version: 6.1.0
Author: Contest Gallery
Author URI: http://www.contest-gallery.com/
Text Domain: contest-gallery
Domain Path: /languages
*/


/*error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);*/


//Create MySQL WP Table

// Register a new shortcode: [book]
add_shortcode( 'cg_gallery', 'contest_gal1ery_frontend_gallery' );
add_shortcode( 'cg_users_upload', 'contest_gal1ery_users_upload' );

//[cg_gallery id="1"]

//apply_filters( 'cg_app_test_lala', '[cg_gallery id="1"]');


function contest_gal1ery_frontend_gallery($atts){

	// PLUGIN VERSION CHECK HERE

	contest_gal1ery_db_check();

	// PLUGIN VERSION CHECK HERE --- END

	wp_enqueue_script( 'cg_check_back_button_click', plugins_url( '/js/cg_check_back_button_click.js', __FILE__ ), array('jquery'), '6.1.0');

		wp_enqueue_script( 'cg_rate', plugins_url( '/js/cg_rate.js', __FILE__ ), array('jquery'), '6.1.0' );

	wp_localize_script( 'cg_rate', 'post_cg_rate_wordpress_ajax_script_function_name', array(
		'cg_rate_ajax_url' => admin_url( 'admin-ajax.php' )
	));

		wp_enqueue_script( 'cg_comment', plugins_url( '/js/cg_comment.js', __FILE__ ), array('jquery'), '6.1.0' );

	wp_localize_script( 'cg_comment', 'post_cg_comment_wordpress_ajax_script_function_name', array(
		'cg_comment_ajax_url' => admin_url( 'admin-ajax.php' )
	));

		wp_enqueue_script( 'cg_set_comment_slider', plugins_url( '/js/cg_set_comment_slider.js', __FILE__ ), array('jquery'), '6.1.0' );

	wp_localize_script( 'cg_set_comment_slider', 'post_cg_set_comment_slider_wordpress_ajax_script_function_name', array(
		'cg_set_comment_slider_ajax_url' => admin_url( 'admin-ajax.php' )
	));


	wp_enqueue_script( 'cg_show_comments_slider', plugins_url( '/js/cg_show_comments_slider.js', __FILE__ ), array('jquery'), '6.1.0' );

	wp_localize_script( 'cg_show_comments_slider', 'post_cg_show_comments_slider_wordpress_ajax_script_function_name', array(
		'cg_show_comments_slider_ajax_url' => admin_url( 'admin-ajax.php' )
	));


	wp_enqueue_style( 'cg_frontend_single_image_style', plugins_url('/css/cg_frontend_single_image_style.css', __FILE__), false, '6.1.0' );
	wp_enqueue_style( 'cg_contest_style',  plugins_url('css/style.css', __FILE__), false, '6.1.0' );

	wp_enqueue_style( 'cg_frontend_single_image_style', plugins_url('/css/cg_frontend_single_image_style.css', __FILE__), false, '6.1.0' );


	wp_enqueue_style( 'cg_contest_style',  plugins_url('css/style.css', __FILE__), false, '6.1.0' );

    wp_enqueue_script( 'show_gallery_jquery', plugins_url( '/js/show_gallery_jquery.js', __FILE__ ), array('jquery'), '6.1.0');


	// Slider development here
    $AllowGalleryScript = 2;
    if($AllowGalleryScript == 2){
        wp_enqueue_style( 'cg_contest_style_slider',  plugins_url('css/style_slider.css', __FILE__), false, '6.1.0' );


        wp_enqueue_script( 'show_gallery_jquery_image_slider_init', plugins_url( '/js/slider/init.js', __FILE__ ), array('jquery'), '6.1.0' );
        wp_enqueue_script( 'show_gallery_jquery_image_slider_vars', plugins_url( '/js/slider/vars.js', __FILE__ ), array('jquery'), '6.1.0' );
        wp_enqueue_script( 'show_gallery_jquery_image_slider_open', plugins_url( '/js/slider/open.js', __FILE__ ), array('jquery'), '6.1.0' );
        wp_enqueue_script( 'show_gallery_jquery_image_slider_close', plugins_url( '/js/slider/close.js', __FILE__ ), array('jquery'), '6.1.0' );
        wp_enqueue_script( 'show_gallery_jquery_image_slider_slide', plugins_url( '/js/slider/slide.js', __FILE__ ), array('jquery'), '6.1.0' );

        wp_enqueue_script( 'show_gallery_jquery_image_slider_resize', plugins_url( '/js/slider/resize.js', __FILE__ ), array('jquery'), '6.1.0' );

        wp_enqueue_script( 'show_gallery_jquery_image_slider', plugins_url( '/js/show_gallery_jquery_image_slider_new_slider.js', __FILE__ ), array('jquery'), '6.1.0' );
        wp_enqueue_script( 'show_gallery_jquery_image_slider_control', plugins_url( '/js/show_gallery_jquery_image_slider_control.js', __FILE__ ), array('jquery'), '6.1.0' );


    }
    else{
        wp_enqueue_script( 'show_gallery_jquery_image_slider', plugins_url( '/js/show_gallery_jquery_image_slider.js', __FILE__ ), array('jquery'), '6.1.0' );
    }

    // Slider development here

    wp_enqueue_script( 'show_image_jquery', plugins_url( '/js/show_image_jquery.js', __FILE__ ), array('jquery'), '6.1.0' );



	//wp_enqueue_script( 'jquery-ui-draggable' );

	include("frontend/get-data.php");
	include("frontend/check-language.php");

	@ob_start();
	include 'frontend/frontend-gallery.php';
	$frontend_gallery = @ob_get_clean();

	apply_filters( 'cg_filter_frontend_gallery', $frontend_gallery );

	return $frontend_gallery;

}


	function contest_gal1ery_users_upload($atts){

	// PLUGIN VERSION CHECK HERE

	contest_gal1ery_db_check();

	// PLUGIN VERSION CHECK HERE --- END


	wp_enqueue_style( 'cg_form_style', plugins_url('/css/cg_form_style.css', __FILE__), false , '6.1.0' );
	wp_enqueue_script( 'users_upload', plugins_url( '/js/users_upload.js', __FILE__ ), array('jquery'), '6.1.0' );
	//wp_enqueue_script( 'show_jquery_language', plugins_url( '/js/show_jquery_language.js', __FILE__ ), array('jquery'), false, true );
	ob_start();
	include 'frontend/users-upload.php';
	$users_upload = ob_get_clean();

	apply_filters('cg_filter_users_upload', $users_upload);

	return $users_upload;
	}


if(!function_exists('contest_gal1ery_create_table')){
	function contest_gal1ery_create_table($i){


	global $wpdb;


	$tablename = $wpdb->prefix . "$i"."contest_gal1ery";
	$tablename_ip = $wpdb->prefix . "$i"."contest_gal1ery_ip";
	$tablename_comments = $wpdb->prefix . "$i"."contest_gal1ery_comments";
	$tablename_options = $wpdb->prefix . "$i"."contest_gal1ery_options";
	$tablename_options_input = $wpdb->prefix . "$i"."contest_gal1ery_options_input";
	$tablename_options_visual = $wpdb->prefix . "$i"."contest_gal1ery_options_visual";
	$tablename_email = $wpdb->prefix . "$i"."contest_gal1ery_mail";
	$tablename_email_admin = $wpdb->prefix . "$i"."contest_gal1ery_mail_admin";
	$tablename_entries = $wpdb->prefix . "$i"."contest_gal1ery_entries";
	$tablename_create_user_entries = $wpdb->prefix . "$i"."contest_gal1ery_create_user_entries";
	$tablename_pro_options = $wpdb->prefix . "$i"."contest_gal1ery_pro_options";
	$tablename_create_user_form = $wpdb->prefix . "$i"."contest_gal1ery_create_user_form";
	$tablename_form_input = $wpdb->prefix . "$i"."contest_gal1ery_f_input";
	$tablename_form_output = $wpdb->prefix . "$i"."contest_gal1ery_f_output";


		if($wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename){
		$sql = "CREATE TABLE $tablename (
		id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		rowid INT(99),
		Timestamp INT(20),
		NamePic VARCHAR(1000),
		ImgType VARCHAR(5),
		CountC VARCHAR(7),
		CountR VARCHAR(7),
		CountS VARCHAR(7),
		Rating VARCHAR(13),
		GalleryID INT(99),
		Active INT(1),
		Informed INT(1),
		WpUpload INT(11),
		Width INT (11),
		Height INT (11),
		WpUserId INT (11)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
			}

		else{

		$sql = "ALTER TABLE $tablename MODIFY COLUMN NamePic VARCHAR(1000) NOT NULL";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		}

		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_ip'") != $tablename_ip){
		$sql = "CREATE TABLE $tablename_ip (
		id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		pid INT (99),
		IP VARCHAR (40),
		GalleryID INT (99),
		Rating INT (1),
		RatingS INT (1),
		WpUserId INT (11)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
				}

		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_comments'") != $tablename_comments){
		$sql = "CREATE TABLE $tablename_comments (
		id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		pid INT (99),
		GalleryID INT (6),
		Name VARCHAR(35),
		Date VARCHAR(50),
		Comment TEXT,
		Timestamp VARCHAR(20)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

		//URL VARCHAR(2000) erst ab Version 3.06 vorhanden
		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_email'") != $tablename_email){
		$sql = "CREATE TABLE $tablename_email (
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		GalleryID INT (99),
		Admin VARCHAR(200),
		Header VARCHAR(200),
		Reply VARCHAR(200),
		CC VARCHAR(200),
		BCC VARCHAR(200),
		URL VARCHAR(2000),
		Content VARCHAR (65535)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

				//URL VARCHAR(2000) erst ab Version 3.06 vorhanden
		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_email_admin'") != $tablename_email_admin){
		$sql = "CREATE TABLE $tablename_email_admin (
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		GalleryID INT (99),
		Admin VARCHAR(200),
		AdminMail VARCHAR(200),
		Header VARCHAR(200),
		Reply VARCHAR(200),
		CC VARCHAR(200),
		BCC VARCHAR(200),
		URL VARCHAR(2000),
		Content VARCHAR (65535)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);


		// Options Admin Email erschaffen und Werte einfügen falls nicht vorhanden ist
				$selectIDs = $wpdb->get_results( "SELECT id FROM $tablename_options" );

				$collectIDs = array();

				foreach ($selectIDs as $key => $value) {

						foreach ($value as $key => $value1) {
							$collectIDs[]= $value1;
						}
				}


		if(!function_exists('cg_create_table_email_admin')){
		// Determine email of blog admin and variables for email table
		$from = get_option('blogname');
		$reply = get_option('admin_email');
		$AdminMail = get_option('admin_email');
		$Header = 'A new picture was published';
		$ContentAdminMail = 'Dear Admin<br/><br/>A new picture was published<br/><br/><br/>$info$';
		}


		/*$wpdb->insert( $tablenameMail, array( 'id' => '', 'GalleryID' => $nextIDgallery, 'Admin' => "$from",
			'Header' => "$Header", 'Reply' => "$reply", 'cc' => "$reply",
			'bcc' => "$reply", 'Url' => '', 'Content' => "$Content"));*/

					foreach ($collectIDs as $key => $value) {



					$wpdb->query($wpdb->prepare(
						"
							INSERT INTO $tablename_email_admin
							( id, GalleryID, Admin, AdminMail,
							Header,Reply,cc,
							bcc,Url,Content)
							VALUES ( %s,%d,%s,%s,
							%s,%s,%s,
							%s,%s,%s)
						",
							'',$value,$from,$AdminMail,
							$Header,$reply,$reply,
							$reply,'',$ContentAdminMail
					 ));

				}

		}



		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_options'") != $tablename_options){
		$sql = "CREATE TABLE $tablename_options(
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		GalleryName VARCHAR(200),
		PicsPerSite INT (3),
		WidthThumb INT (5),
		HeightThumb INT (5),
		WidthGallery INT (5),
		HeightGallery INT (5),
		DistancePics INT (5),
		DistancePicsV INT (5),
		MaxResJPGon INT(1),
		MaxResPNGon INT(1),
		MaxResGIFon INT(1),
		MaxResJPG INT(20),
		MaxResJPGwidth INT(20),
		MaxResJPGheight INT(20),
		MaxResPNG INT(20),
		MaxResPNGwidth INT(20),
		MaxResPNGheight INT(20),
		MaxResGIF INT(20),
		MaxResGIFwidth INT(20),
		MaxResGIFheight INT(20),
		OnlyGalleryView TINYINT,
		SinglePicView TINYINT,
		ScaleOnly TINYINT,
		ScaleAndCut TINYINT,
		FullSize TINYINT,
		AllowSort TINYINT,
		RandomSort TINYINT,
		AllowComments TINYINT,
		CommentsOutGallery TINYINT,
		AllowRating TINYINT,
		VotesPerUser INT(5),
		RatingOutGallery TINYINT,
		ShowAlways TINYINT,
		ShowAlwaysInfoSlider TINYINT,
		IpBlock TINYINT,
		CheckLogin TINYINT,
		FbLike TINYINT,
		FbLikeGallery TINYINT,
		FbLikeGalleryVote TINYINT,
		AllowGalleryScript TINYINT,
		InfiniteScroll TINYINT,
		FullSizeImageOutGallery TINYINT,
		FullSizeImageOutGalleryNewTab TINYINT,
		Inform TINYINT,
		InformAdmin TINYINT,
		TimestampPicDownload VARCHAR(20),
		ThumbLook TINYINT,
		AdjustThumbLook TINYINT,
		HeightLook TINYINT,
		RowLook TINYINT,
		ThumbLookOrder TINYINT,
		HeightLookOrder TINYINT,
		RowLookOrder TINYINT,
		HeightLookHeight INT(3),
		ThumbsInRow TINYINT,
		PicsInRow TINYINT,
		LastRow TINYINT,
		HideUntilVote TINYINT,
		HideInfo TINYINT,
		ActivateUpload TINYINT,
		ContestEnd TINYINT,
		ContestEndTime VARCHAR(100),
		ForwardToURL TINYINT,
		ForwardFrom TINYINT,
		ForwardType TINYINT,
		ActivatePostMaxMB TINYINT,
		PostMaxMB INT(20),
		ActivateBulkUpload TINYINT,
		BulkUploadQuantity INT(20),
		BulkUploadMinQuantity INT(20),
		ShowOnlyUsersVotes TINYINT,
		FbLikeGoToGalleryLink VARCHAR(1000)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

		}


		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_options_visual'") != $tablename_options_visual){
		//IF(SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = "$tablename_options_visual" LIMIT 1){
		$sql = "CREATE TABLE $tablename_options_visual(
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		GalleryID INT(99),
		CommentsAlignGallery VARCHAR(20),
		RatingAlignGallery VARCHAR(20),
		Field1IdGalleryView INT(20),
		Field1AlignGalleryView VARCHAR(20),
		Field2IdGalleryView INT(20),
		Field2AlignGalleryView VARCHAR(20),
		Field3IdGalleryView INT(20),
		Field3AlignGalleryView VARCHAR(20),
		ThumbViewBorderWidth INT(20),
		ThumbViewBorderRadius INT(20),		
		ThumbViewBorderColor VARCHAR(20),
		ThumbViewBorderOpacity VARCHAR(20),
		HeightViewBorderWidth INT(20),
		HeightViewBorderRadius INT(20),
		HeightViewBorderColor VARCHAR(20),
		HeightViewBorderOpacity VARCHAR(20),
		HeightViewSpaceWidth INT(20),
		HeightViewSpaceHeight INT(20),
		RowViewBorderWidth INT(20),
		RowViewBorderRadius INT(20),
		RowViewBorderColor VARCHAR(20),
		RowViewBorderOpacity VARCHAR(20),
		RowViewSpaceWidth INT(20),
		RowViewSpaceHeight INT(20),
		TitlePositionGallery TINYINT,
		RatingPositionGallery TINYINT,
		CommentPositionGallery TINYINT,
		ActivateGalleryBackgroundColor TINYINT,
		GalleryBackgroundColor VARCHAR(20),
		GalleryBackgroundOpacity VARCHAR(20),
		FormRoundBorder INT(11),
		FormBorderColor VARCHAR(256),
		FormButtonColor VARCHAR(256),
		FormButtonWidth INT(11),
		FormInputWidth INT(11),
        OriginalSourceLinkInSlider TINYINT,
        PreviewInSlider TINYINT
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);


				// Options Visual erschaffen und Werte einfügen falls nicht vorhanden ist
				// Muss bei allen Updates angewendet werden
				$selectIDs = $wpdb->get_results( "SELECT id FROM $tablename_options" );

				$collectIDs = array();

				foreach ($selectIDs as $key => $value) {

						foreach ($value as $key => $value1) {
							$collectIDs[]= $value1;
						}
				}

				foreach ($collectIDs as $key => $value) {

						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_options_visual
								( id, GalleryID, CommentsAlignGallery, RatingAlignGallery,
								Field1IdGalleryView,Field1AlignGalleryView,Field2IdGalleryView,Field2AlignGalleryView,Field3IdGalleryView,Field3AlignGalleryView,
								ThumbViewBorderWidth,ThumbViewBorderRadius,ThumbViewBorderColor,ThumbViewBorderOpacity,HeightViewBorderWidth,HeightViewBorderRadius,HeightViewBorderColor,HeightViewBorderOpacity,HeightViewSpaceWidth,HeightViewSpaceHeight,
								RowViewBorderWidth,RowViewBorderRadius,RowViewBorderColor,RowViewBorderOpacity,RowViewSpaceWidth,RowViewSpaceHeight,TitlePositionGallery,RatingPositionGallery,CommentPositionGallery,
								ActivateGalleryBackgroundColor,GalleryBackgroundColor,GalleryBackgroundOpacity)
								VALUES ( %s,%d,%s,%s,
								%s,%s,%s,%s,%s,%s,
								%d,%d,%s,%d,%d,%d,%s,%d,%d,%d,
								%d,%d,%s,%d,%d,%d,%d,%d,%d,%d,%s,%d)
							",
								'',$nextIDgallery,'left','left',
								'','left','','left','','left',
								0,0,'#000000',1,0,0,'#000000',1,0,0,
								0,0,'#000000',1,0,0,1,1,1,0,'#000000',1
					 ) );

				}



		}

		//if($wpdb->get_var('SHOW TABLES LIKE ' . $tablename_options_visual) == $tablename_options_visual){}


		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_options_input'") != $tablename_options_input){
		$sql = "CREATE TABLE $tablename_options_input(
		id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		GalleryID INT(99),
		Forward TINYINT,
		Forward_URL VARCHAR(999),
		Confirmation_Text VARCHAR(65535)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

		}


		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_entries'") != $tablename_entries){
		$sql = "CREATE TABLE $tablename_entries (
		id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		pid INT(99),
		f_input_id INT (99),
		GalleryID INT(99),
		Field_Type VARCHAR(10),
		Field_Order INT(3),
		Short_Text VARCHAR(999),
		Long_Text VARCHAR(65535)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}


		if(file_exists(plugin_dir_path( __FILE__ )."admin/users/admin/registry/create-user-form.php")){

			add_role(
				'contest_gallery_user',
				__( 'Contest Gallery User' ),
				array(
					'read' => false
				)
			);

			if($wpdb->get_var("SHOW TABLES LIKE '$tablename_pro_options'") != $tablename_pro_options){
			$sql = "CREATE TABLE $tablename_pro_options (
			id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			GalleryID INT(99),
			ForwardAfterRegUrl VARCHAR(999),
			ForwardAfterRegText VARCHAR(65535),
			ForwardAfterLoginUrlCheck TINYINT,
			ForwardAfterLoginUrl VARCHAR(999),
			ForwardAfterLoginTextCheck TINYINT,
			ForwardAfterLoginText VARCHAR(65535),
			TextEmailConfirmation VARCHAR(65535),
			TextAfterEmailConfirmation VARCHAR(65535),
			RegMailAddressor VARCHAR(200),
			RegMailReply VARCHAR(200),
			RegMailSubject VARCHAR(200),
			RegUserUploadOnly TINYINT,
			RegUserUploadOnlyText VARCHAR(65535)
			) DEFAULT CHARACTER SET utf8";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);

				// Anlegen der absolut notwendigen User Form Feldern (Username, E-Mail, Password und Confirm Password)

				$selectIDs = $wpdb->get_results( "SELECT id FROM $tablename_options" );

				$collectIDs = array();

				foreach ($selectIDs as $key => $value) {

						foreach ($value as $key => $value1) {
							$collectIDs[]= $value1;
						}
				}


				$ForwardAfterRegText = 'Thank you for your registration<br/>Check your email account to confirm your email and complete the registration. If you don\'t see any message then plz check also the spam folder.';
				$ForwardAfterLoginText = 'You are now logged in. Have fun with photo contest.';
				$TextEmailConfirmation = 'Thank you for your registration by clicking on the link below: <br/><br/> $regurl$';
				$TextAfterEmailConfirmation = 'Thank you for your registration. You are now able to login and to take part on the photo contest.';
				$RegUserUploadOnlyText = 'You have to be registered to upload your images.';

				// Determine email of blog admin and variables for email table
				$RegMailAddressor = get_option('blogname');
				$RegMailReply = get_option('admin_email');
				$RegMailSubject = 'Please confirm your registration';

				foreach ($collectIDs as $key => $value) {
						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_pro_options
							( id, GalleryID, ForwardAfterRegUrl, ForwardAfterRegText,
							ForwardAfterLoginUrlCheck,ForwardAfterLoginUrl,
							ForwardAfterLoginTextCheck,ForwardAfterLoginText,
							TextEmailConfirmation,TextAfterEmailConfirmation,
							RegMailAddressor,RegMailReply,RegMailSubject,RegUserUploadOnly,RegUserUploadOnlyText)
							VALUES (%s,%d,%s,%s,
							%d,%s,
							%d,%s,
							%s,%s,
							%s,%s,%s,%d,%s)
						",
							'',$value,'',$ForwardAfterRegText,
							0,'',
							0,$ForwardAfterLoginText,
							$TextEmailConfirmation,$TextAfterEmailConfirmation,
							$RegMailAddressor,$RegMailReply,$RegMailSubject,0,$RegUserUploadOnlyText
						) );
				}
			}




			if($wpdb->get_var("SHOW TABLES LIKE '$tablename_create_user_entries'") != $tablename_create_user_entries){
			$sql = "CREATE TABLE $tablename_create_user_entries (
			id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			GalleryID INT(99),
			wp_user_id INT(99),
			f_input_id INT (99),
			Field_Type VARCHAR(100),
			Field_Content VARCHAR(65535),
			activation_key VARCHAR(200)
			) DEFAULT CHARACTER SET utf8";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}


			if($wpdb->get_var("SHOW TABLES LIKE '$tablename_create_user_form'") != $tablename_create_user_form){
			$sql = "CREATE TABLE $tablename_create_user_form (
			id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			GalleryID INT(99),
			Field_Type VARCHAR(100),
			Field_Order INT(3),
			Field_Name VARCHAR(200),
			Field_Content VARCHAR(65535),
			Min_Char VARCHAR(200),
			Max_Char VARCHAR(200),
			Required TINYINT
			) DEFAULT CHARACTER SET utf8";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

				// Anlegen der absolut notwendigen User Form Feldern (Username, E-Mail, Password und Confirm Password)

				$selectIDs = $wpdb->get_results( "SELECT id FROM $tablename_options" );

				$collectIDs = array();

				foreach ($selectIDs as $key => $value) {

						foreach ($value as $key => $value1) {
							$collectIDs[]= $value1;
						}
				}

				foreach ($collectIDs as $key => $value) {

						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_create_user_form
							( id, GalleryID, Field_Type, Field_Order,
							Field_Name,Field_Content,Min_Char,Max_Char,
							Required)
							VALUES ( %s,%d,%s,%s,
							%s,%s,%d,%d,
							%d)
						",
							'',$value,'main-user-name','1',
							'Username','',3,100,
							1
						) );

						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_create_user_form
							( id, GalleryID, Field_Type, Field_Order,
							Field_Name,Field_Content,Min_Char,Max_Char,
							Required)
							VALUES ( %s,%d,%s,%s,
							%s,%s,%d,%d,
							%d)
						",
							'',$value,'main-mail','2',
							'E-mail','','','',
							1
						) );

						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_create_user_form
							( id, GalleryID, Field_Type, Field_Order,
							Field_Name,Field_Content,Min_Char,Max_Char,
							Required)
							VALUES ( %s,%d,%s,%s,
							%s,%s,%d,%d,
							%d)
						",
							'',$value,'password','3',
							'Password','',6,100,
							1
						) );

						$wpdb->query( $wpdb->prepare(
						"
							INSERT INTO $tablename_create_user_form
							( id, GalleryID, Field_Type, Field_Order,
							Field_Name,Field_Content,Min_Char,Max_Char,
							Required)
							VALUES ( %s,%d,%s,%s,
							%s,%s,%d,%d,
							%d)
						",
							'',$value,'password-confirm','4',
							'Confirm Password','',6,100,
							1
						) );


				}

			// Anlegen der absolut notwendigen User Form Feldern (Username, E-Mail, Password und Confirm Password) --- ENDE


			}

		}


		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_form_input'") != $tablename_form_input){
		$sql = "CREATE TABLE $tablename_form_input (
		id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		GalleryID INT(99),
		Field_Type VARCHAR(10),
		Field_Order INT(3),
		Field_Content VARCHAR(65535),
		Show_Slider TINYINT,
		Use_as_URL TINYINT
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}


		if($wpdb->get_var("SHOW TABLES LIKE '$tablename_form_output'") != $tablename_form_output){
		$sql = "CREATE TABLE $tablename_form_output (
		id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		f_input_id INT (99),
		GalleryID INT(99),
		Field_Type VARCHAR(10),
		Field_Order INT(3),
		Field_Content VARCHAR(65535)
		) DEFAULT CHARACTER SET utf8";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}


		//ADD first first Galery

$uploads = wp_upload_dir();
$checkUploads = $uploads['basedir'].'/contest-gallery';

	if(!file_exists($checkUploads)){
	mkdir($checkUploads,0777,true);
	}


// Update Tables if already created


$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'HideUntilVote'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD HideUntilVote TINYINT NULL");
}

	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ActivateUpload'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ActivateUpload TINYINT NULL");
}

	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ContestEnd'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ContestEnd TINYINT NULL");
}

	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ContestEndTime'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ContestEndTime VARCHAR(100) NULL");
}
// (Show always vote, comments and info in gallery view)
	$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ShowAlways'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ShowAlways TINYINT NULL");
}


$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'CheckLogin'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD CheckLogin TINYINT NULL");
}


$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'ThumbViewBorderOpacity'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD ThumbViewBorderOpacity VARCHAR(20) NULL DEFAULT 1");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'HeightViewBorderOpacity'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD HeightViewBorderOpacity VARCHAR(20) NULL DEFAULT 1");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'RowViewBorderOpacity'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD RowViewBorderOpacity VARCHAR(20) NULL DEFAULT 1");
}

// Update ab 01.01.2016

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'CommentsOutGallery'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD CommentsOutGallery TINYINT NULL");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'RatingOutGallery'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD RatingOutGallery TINYINT NULL");
}

// Textinformationen vom User diese zu entsprechender Feld ID gehören, sollen im Slider gezeigt werden
$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_form_input' AND column_name = 'Show_Slider'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_form_input ADD Show_Slider TINYINT NULL");
}


// Update ab 05.02.2016 (Image URL Forwarding wurde eingebaut)

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_form_input' AND column_name = 'Use_as_URL'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_form_input ADD Use_as_URL TINYINT NULL");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ForwardToURL'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ForwardToURL TINYINT NULL DEFAULT 1 ");
}


// 1 ist vom Slider 2 ist out of gallery
$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ForwardFrom'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ForwardFrom TINYINT NULL DEFAULT 1");
}

// 1 ist normal 2 ist _blank
$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ForwardType'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ForwardType TINYINT NULL");
}


// Update ab 27.02.2016 (Restrict width und height resolution wurde eingebaut)


// JPG
$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'MaxResJPGwidth'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD  MaxResJPGwidth INT(20) DEFAULT '800'");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'MaxResJPGheight'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD  MaxResJPGheight INT(20) DEFAULT '600'");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'MaxResJPGwidth'"  );

// PNG

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'MaxResPNGwidth'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD  MaxResPNGwidth INT(20) DEFAULT '800'");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'MaxResPNGheight'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD  MaxResPNGheight INT(20) DEFAULT '600'");
}


// GIF

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'MaxResGIFwidth'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD  MaxResGIFwidth INT(20) DEFAULT '800'");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'MaxResGIFheight'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD  MaxResGIFheight INT(20) DEFAULT '600'");
}

// Update ab 05.03.2016 ActivatePostMaxMB und PostMaxMB


$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ActivatePostMaxMB'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ActivatePostMaxMB TINYINT NULL");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'PostMaxMB'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD PostMaxMB INT(20) DEFAULT '2'");
}

// Update ab 05.03.2016 ActivatePostMaxMB und PostMaxMB

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ActivateBulkUpload'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ActivateBulkUpload TINYINT NULL");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'BulkUploadQuantity'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD BulkUploadQuantity INT(20) DEFAULT '3'");
}

// Update ab 13.03.2016 IformAdmin

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'InformAdmin'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD InformAdmin TINYINT NULL");
}

// Update ab 23.04.2016 BulkUploadMinQuantity und ShowAlwaysInfoSlider

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'BulkUploadMinQuantity'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD BulkUploadMinQuantity TINYINT NULL");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ShowAlwaysInfoSlider'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD ShowAlwaysInfoSlider TINYINT NULL");
}

// Update ab 26.0.54.2016 FullSizeImageOutGallery und FullSizeImageOutGalleryNewTab

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'FullSizeImageOutGallery'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD FullSizeImageOutGallery TINYINT NULL");
}


$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'FullSizeImageOutGalleryNewTab'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD FullSizeImageOutGalleryNewTab TINYINT NULL");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'SinglePicView'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD SinglePicView TINYINT NULL");
}

$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'OnlyGalleryView'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD OnlyGalleryView TINYINT NULL");
}


// Update ab 26.0.55.2016 InfiniteScroll

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'InfiniteScroll'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD InfiniteScroll TINYINT NULL");
}

// Update ab 11.06.2016 One vote rating

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename' AND column_name = 'CountS'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename ADD CountS VARCHAR(7) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_ip' AND column_name = 'RatingS'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_ip ADD RatingS INT(1) NULL");
}


// Update ab 26.0.53.2016 Facebook Like in Gallery

// Spalte configuriert ob Facebook Like auch in der Galerie gezeigt werden soll
$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'FbLikeGallery'"  );

if(empty($row)){

	$wpdb->query("ALTER TABLE $tablename_options ADD FbLikeGallery TINYINT NULL");




				// Alle Gallery IDs auswählen um entsprechende Ordner auswählen zu können

				$getAllOptionIDs = $wpdb->get_results( "SELECT id FROM $tablename_options");

				foreach($getAllOptionIDs as $optionID){

						$GalleryID = $optionID->id;

						// Alle Bilder die Aktiv sind sollen für den Facebook button eine eigene HTML erhalten
						$picsSQL = $wpdb->get_results( "SELECT Timestamp, NamePic FROM $tablename WHERE GalleryID = '$GalleryID' ORDER BY rowid DESC");

										// Größe der Bilder bei ThumbAnsicht (gewöhnliche Ansicht mit Bewertung)
						$uploadFolder = wp_upload_dir();
						$urlSource = site_url();
						$blog_title = get_bloginfo();


						foreach($picsSQL as $value12){


								//$id = $value12->id;
								//$rowid = $value12->rowid;
								$Timestamp = $value12->Timestamp;
								$NamePic = $value12->NamePic;
								$ImgType = $value12->ImgType;
								//$rating = $value12->Rating;
								//$countR = $value12->CountR;
								//$countC = $value12->CountC;

									$dirHTML = $uploadFolder['basedir'].'/contest-gallery/gallery-id-'.$GalleryID.'/'.$Timestamp."_".$NamePic."413.html";

									if(!file_exists($dirHTML)){

											$scrImgMeta624 = $uploadFolder['baseurl'].'/contest-gallery/gallery-id-'.$GalleryID.'/'.$Timestamp."_".$NamePic."-624width.".$ImgType."";
											$scrImgMeta1024 = $uploadFolder['baseurl'].'/contest-gallery/gallery-id-'.$GalleryID.'/'.$Timestamp."_".$NamePic."-1024width.".$ImgType."";
											$scrImgMeta = $uploadFolder['baseurl'].'/contest-gallery/gallery-id-'.$GalleryID.'/'.$Timestamp."_".$NamePic.".".$ImgType."";

											$urlForFacebook= $uploadFolder['baseurl'].'/contest-gallery/gallery-id-'.$GalleryID."/".$Timestamp."_".$NamePic.".html";


											//$urlForFacebook= $urlSource.'/wp-content/uploads/contest-gallery/gallery-id-'.$GalleryID."/".$Timestamp."_".$NamePic.".html";


											$fields = '<!DOCTYPE html>
											<html lang="en">
											<head>
											<title>'.$blog_title.'</title>
											<meta property="og:url"           content="'.$urlForFacebook.'" />
											<meta property="og:type"          content="website" />
											<meta property="og:title"         content="'.$blog_title.'" />
											<meta property="og:image"         content="'.$scrImgMeta624.'" />
											<meta charset="utf-8">
											<meta name="viewport" content="width=device-width, initial-scale=1.0">
											 </head>
											<body  onload="checkIfIframe(),loadButton();">
											 
											 <div id="fb-root"></div>
											<script>
											
											window.onmessage = function(event) {
											  if (event.data === "reload") {
												location.reload();
											  }
											};
											
											function checkIfIframe(){
												if (window.frameElement) {
												
												}
												else{
													document.getElementById("cgimg").innerHTML = "<img src=\''.$scrImgMeta1024.'\' width=\'800px\' />";
												}
											}
									
											
											var userBrowserLang = navigator.language || navigator.userLanguage;

											if(userBrowserLang.indexOf("en")==0){var userLang = "en_US";}
											else if(userBrowserLang.indexOf("de")==0){var userLang = "de_DE";}
											else if(userBrowserLang.indexOf("fr")==0){var userLang = "fr_FR";}
											else if(userBrowserLang.indexOf("es")==0){var userLang = "es_ES";}
											else if(userBrowserLang.indexOf("pt")==0){var userLang = "pt_PT";}
											else if(userBrowserLang.indexOf("nl")==0){var userLang = "nl_NL";}
											else if(userBrowserLang.indexOf("ru")==0){var userLang = "ru_RU";}
											else if(userBrowserLang.indexOf("zh")==0){var userLang = "zh_CN";}
											else if(userBrowserLang.indexOf("ja")==0){var userLang = "ja_JP";}
											else{var userLang = "en_US";}
											
											(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0];
											  if (d.getElementById(id)) return;
											  js = d.createElement(s); js.id = id;
											  js.src = "//connect.facebook.net/"+userLang+"/sdk.js#xfbml=1&version=v2.8";
											  fjs.parentNode.insertBefore(js, fjs);
											}(document, "script", "facebook-jssdk"));
											</script>
											
											<script src="backtogalleryurl.js"></script>
											<div class="fb-like" data-href="'.$urlForFacebook.'" data-layout="button_count" data-action="like" data-share="true" style="float:left;display:inline;width:76px;vertical-align: middle;position:absolute;top:0px;height: 20px;width:400px;overflow:hidden;"></div>
											<div style="margin-top:40px;" id="cgimg"></div>
											<div id="cgBackToGallery"></div>
											  
											  
											</body>
											</html>';
											$fp = fopen($dirHTML, 'w');
											fwrite($fp, $fields);
											fclose($fp);

										}

						}

				}

}

// Spalte configuriert ob Facebook Like in der Galerie gezeigt werden soll und man in der Gallerie auch voten kann
$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'FbLikeGalleryVote'"  );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD FbLikeGalleryVote TINYINT NULL");
}


// Update ab 13.08.2016 RandomSort + Viele viuselle Verbesserungen


$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'RandomSort'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD RandomSort TINYINT NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'AdjustThumbLook'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD AdjustThumbLook TINYINT NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'ThumbViewBorderRadius'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD ThumbViewBorderRadius INT(20) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'HeightViewBorderRadius'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD HeightViewBorderRadius INT(20) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'HeightViewSpaceHeight'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD HeightViewSpaceHeight INT(20) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'RowViewBorderRadius" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD RowViewBorderRadius INT(20) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'RowViewSpaceHeight" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD RowViewSpaceHeight INT(20) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'TitlePositionGallery" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD TitlePositionGallery TINYINT DEFAULT '1'");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'RatingPositionGallery" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD RatingPositionGallery TINYINT DEFAULT '1'");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'CommentPositionGallery" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD CommentPositionGallery TINYINT DEFAULT '1'");
}

// Update ab 13.08.2016 Background Color + Votes per User

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'VotesPerUser'" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options ADD VotesPerUser INT(5) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'ActivateGalleryBackgroundColor" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD ActivateGalleryBackgroundColor TINYINT NULL");
}


$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'GalleryBackgroundColor" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD GalleryBackgroundColor VARCHAR(20) DEFAULT '#ffffff'");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'GalleryBackgroundOpacity" );

if(empty($row)){

   $wpdb->query("ALTER TABLE $tablename_options_visual ADD GalleryBackgroundOpacity VARCHAR(20) DEFAULT 1");
}


$p_cgal1ery_db_installed_ver = get_option( "p_cgal1ery_db_version" );
$p_cgal1ery_db_installed_ver = floatval($p_cgal1ery_db_installed_ver);

if($p_cgal1ery_db_installed_ver<4.10){

	$wpdb->query("ALTER TABLE $tablename MODIFY COLUMN CountC INT(7)");
	$wpdb->query("ALTER TABLE $tablename MODIFY COLUMN CountR INT(7)");
	$wpdb->query("ALTER TABLE $tablename MODIFY COLUMN CountS INT(7)");
	$wpdb->query("ALTER TABLE $tablename MODIFY COLUMN Rating INT(13)");
}


// Update ab 08.12.2016 Prüfen ob das Bild über WP-Upload hochgeladen wurde

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename' AND column_name = 'WpUpload'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename ADD WpUpload INT(11) NULL");
}

// Update ab 08.12.2016 Prüfen ob das Bild über WP-Upload hochgeladen wurde

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename' AND column_name = 'Width'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename ADD Width INT(11) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename' AND column_name = 'Height'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename ADD Height INT(11) NULL");
}

// Nachträglich eingefügt 27.02.2017

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'HideInfo'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_options ADD HideInfo TINYINT NULL");
}

// Hinzugefügt am 02.03.2017

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename' AND column_name = 'WpUserId'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename ADD WpUserId INT(11) NULL");
}

	// Korrektur ab 11.05.2017, vorher TINYINT gewesen
	$wpdb->query("ALTER TABLE $tablename CHANGE WpUserId WpUserId INT(11) NULL");

// Hinzugefügt am 28.03.2017

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'ShowOnlyUsersVotes'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_options ADD ShowOnlyUsersVotes TINYINT NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options' AND column_name = 'FbLikeGoToGalleryLink'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_options ADD FbLikeGoToGalleryLink VARCHAR(1000) NULL");
}


$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_ip' AND column_name = 'WpUserId'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_ip ADD WpUserId INT(11) NULL");
}

	// Korrektur ab 11.05.2017, vorher TINYINT gewesen
	$wpdb->query("ALTER TABLE $tablename_ip CHANGE WpUserId WpUserId INT(11) NULL");


// Hinzugefügt am 29.04.2017

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'FormRoundBorder'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_options_visual ADD FormRoundBorder INT(11) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'FormBorderColor'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_options_visual ADD FormBorderColor VARCHAR(256) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'FormButtonColor'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_options_visual ADD FormButtonColor VARCHAR(256) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'FormButtonWidth'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_options_visual ADD FormButtonWidth INT(11) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'FormInputWidth'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_options_visual ADD FormInputWidth INT(11) NULL");
}

/*
// Vorbereitet am 07.06.2017

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_pro_options' AND column_name = 'ReminderTime'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_pro_options ADD ReminderTime INT(11) NULL");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_pro_options' AND column_name = 'ReminderOption'" );

if(empty($row)){
   $wpdb->query("ALTER TABLE $tablename_pro_options ADD ReminderOption TINYINT NULL");
}*/

// Update 24.12.2017

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'OriginalSourceLinkInSlider'" );

if(empty($row)){
    $wpdb->query("ALTER TABLE $tablename_options_visual ADD OriginalSourceLinkInSlider TINYINT DEFAULT 1");
}

$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tablename_options_visual' AND column_name = 'PreviewInSlider'" );

if(empty($row)){
    $wpdb->query("ALTER TABLE $tablename_options_visual ADD PreviewInSlider TINYINT DEFAULT 1");
}


// Update Tables if already created --- END


// Update jquery files

		//$arrowPngLeft = plugins_url( 'css/arrow_left.png', __FILE__ );

		/*
		$js_cg_version = '512';

		$dir = plugin_dir_path( __FILE__ );

		$getAllJsFiles = $dir."js/*.js";
		// echo "<br>deleteCachedSiteFiles: $deleteCachedSiteFiles<br>";

		$getAllJsFiles = glob($getAllJsFiles); // get all file names
		foreach($getAllJsFiles as $oldFile){ // iterate files

		$newFileName = substr($oldFile, 0, -6).$js_cg_version.".js";

		copy($oldFile, $newFileName);

		}

		$dir = plugin_dir_path( __FILE__ );

		$getAllJsFiles = $dir."admin/users/js/*.js";
		// echo "<br>deleteCachedSiteFiles: $deleteCachedSiteFiles<br>";

		$getAllJsFiles = glob($getAllJsFiles); // get all file names
		foreach($getAllJsFiles as $oldFile){ // iterate files

		$newFileName = substr($oldFile, 0, -6).$js_cg_version.".js";

		copy($oldFile, $newFileName);


		//if(is_file($file1))
		//unlink($file1); // delete file

		}*/


// Update jquery files --- END


}
}

register_activation_hook( __FILE__, 'contest_gal1ery_db_check' );


// Update DB



function contest_gal1ery_db_check() {

	global $p_cgal1ery_db_new_version;
$p_cgal1ery_db_new_version = '6.08';


@$p_cgal1ery_db_installed_ver = get_option( "p_cgal1ery_db_version" );

if ( @$p_cgal1ery_db_installed_ver != @$p_cgal1ery_db_new_version ) {

		if(function_exists('contest_gal1ery_create_table')){
			// Achtung! Bei einer Multisite kann Wordpress sowohl von einem Hauptbereich (network), wie auch von einem einzelnen Blog aus aktiviert werden
			if (is_multisite()) {

					global $wpdb;

					$wpBlogs = $wpdb->prefix . "blogs";

					$getBlogIDs = $wpdb->get_results( "SELECT blog_id FROM $wpBlogs ORDER BY blog_id ASC" );

					if(strpos(@$_SERVER['REQUEST_URI'],"wp-admin/network")){

						foreach($getBlogIDs as $key => $value){
							foreach($value as $key1 => $value1){
								if($value1==1){
									$i='';
								}
								else{
									$i=$value1."_";
								}
								contest_gal1ery_create_table($i);
							}
						}
					}
					else{
						// Richtiger Prefix wird von Wordpress auotmatisch weitergegeben
						$i="";
						contest_gal1ery_create_table($i);
					}

				}
				// Wenn single Site dann ganz normalen Drop contest gallery Tables
				else{
					// Richtiger Prefix wird von Wordpress auotmatisch weitergegeben
					$i='';
					contest_gal1ery_create_table($i);
				}


	// Löschen von Tabellen --- ENDE
		}

		if($p_cgal1ery_db_installed_ver){update_option( "p_cgal1ery_db_version", $p_cgal1ery_db_new_version );}

		else{add_option( "p_cgal1ery_db_version", $p_cgal1ery_db_new_version );}

	}

}

// Update DB - END


	add_shortcode( 'cg_users_reg', 'contest_gal1ery_users_registry' );


	function contest_gal1ery_users_registry($atts){

		// PLUGIN VERSION CHECK HERE

		contest_gal1ery_db_check();

		// PLUGIN VERSION CHECK HERE --- END


		wp_enqueue_style( 'cg_form_style', plugins_url('/css/cg_form_style.css', __FILE__), false , '6.1.0' );
		wp_enqueue_script( 'cg_registry', plugins_url( '/admin/users/js/users_registry.js', __FILE__ ), array('jquery'), '6.1.0' );

			wp_localize_script( 'cg_registry', 'post_cg_registry_wordpress_ajax_script_function_name', array(
			'cg_registry_ajax_url' => admin_url( 'admin-ajax.php' )
		));


		ob_start();
		include('admin/users/frontend/users-registry.php');
		$contest_gal1ery_users_registry = ob_get_clean();

		apply_filters( 'cg_filter_users_registry', $contest_gal1ery_users_registry );

		return $contest_gal1ery_users_registry;

	}



	add_action('wp_ajax_nopriv_post_cg_registry','post_cg_registry');
	add_action('wp_ajax_post_cg_registry','post_cg_registry');

			function post_cg_registry(){

				global $wpdb;

				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

					require_once(dirname(__FILE__).'/admin/users/frontend/users-registry-check-name-mail-ajax.php');
					die();

				}
				else {

					exit();
				}

			}











// Add a top level menu to wordpress

// page_title â€” The title of the page as shown in the <title> tags
// menu_title â€” The name of your menu displayed on the dashboard
// capability â€” Minimum capability required to view the menu
// menu_slug â€” Slug name to refer to the menu; should be a unique name
// function : Function to be called to display the page content for the item
// icon_url â€” URL to a custom image to use as the Menu icon
// position â€” Location in the menu order where it should appear

//create submenu items

// parent_slug : Slug name for the parent menu ( menu_slug previously defi ned)
// page_title : The title of the page as shown in the <title> tags
// menu_title : The name of your submenu displayed on the dashboard
// capability : Minimum capability required to view the submenu
// menu_slug : Slug name to refer to the submenu; should be a unique name
// function : Function to be called to display the page content for the item


/*

add_action( 'wp_enqueue_scripts', 'ajax_test_enqueue_scripts1' );
if(!function_exists('ajax_test_enqueue_scripts1')){
function ajax_test_enqueue_scripts1() {
	if( is_single() ) {
		wp_enqueue_style( 'love1', plugins_url( '/love1.css', __FILE__ ) );
	}

	wp_enqueue_script( 'cg_rate', plugins_url( '/cg_rate2.js', __FILE__ ), array('jquery'), '1.0', true );

	wp_localize_script( 'cg_rate', 'postlove1', array(
		'ajax_url1' => admin_url( 'admin-ajax.php' )
	));

}
}*/

// Register CSS


function cg_options_tabcontent() {
       /* Register our stylesheet. */

	   if (@$_GET['page']!='contest-gallery/index.php') {
        return;
       }

       wp_enqueue_style( 'cg_options_tabcontent', plugins_url('/admin/options/cg_options_tabcontent.css', __FILE__), false , '6.1.0' );
       wp_enqueue_style( 'cg_options_style', plugins_url('/css/cg_options_style.css', __FILE__), false , '6.1.0' );

}

add_action('admin_enqueue_scripts', 'cg_options_tabcontent' );


add_action( 'wp_ajax_nopriv_post_cg_rate', 'post_cg_rate' );
add_action( 'wp_ajax_post_cg_rate', 'post_cg_rate' );

function post_cg_rate() {


	global $wpdb;

$ip = $_SERVER['REMOTE_ADDR'];


	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		require_once('frontend/rate-picture.php');

		die();
	}
	else {

		exit();
	}
}


// AJAX Script für rate picture ---- ENDE


add_action( 'wp_ajax_nopriv_post_cg_comment', 'post_cg_comment' );
add_action( 'wp_ajax_post_cg_comment', 'post_cg_comment' );

function post_cg_comment() {


	global $wpdb;


	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		require_once('frontend/set-comment.php');
		die();
	}
	else {

		exit();
	}
}


// AJAX Script für set comment ---- ENDE

add_action( 'wp_ajax_nopriv_post_cg_set_comment_slider', 'post_cg_set_comment_slider' );
add_action( 'wp_ajax_post_cg_set_comment_slider', 'post_cg_set_comment_slider' );

function post_cg_set_comment_slider() {


	global $wpdb;


	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		require_once('frontend/set-comment-slider.php');
		die();
	}
	else {

		exit();
	}
}


// AJAX Script für set comment Slider ---- ENDE


// AJAX Script show comment Slider or out of Gallery




add_action( 'wp_ajax_nopriv_post_cg_show_comments_slider', 'post_cg_show_comments_slider' );
add_action( 'wp_ajax_post_cg_show_comments_slider', 'post_cg_show_comments_slider' );

function post_cg_show_comments_slider() {


	global $wpdb;


	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		require_once('frontend/show-comments-slider.php');
		die();
	}
	else {

		exit();
	}
}


// AJAX Script show comment Slider or out of Gallery ---- ENDE



// AJAX Script für Check Admin Image Upload im Backend
// Achtung! Für Backend AJAX Calls ist der FrontEnd Aufbau nicht erforderlich, nur die Action muss registriert werden

add_action( 'wp_ajax_nopriv_cg_check_wp_admin_upload', 'cg_check_wp_admin_upload' );
add_action( 'wp_ajax_cg_check_wp_admin_upload', 'cg_check_wp_admin_upload' );

function cg_check_wp_admin_upload() {

	global $wpdb;

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

		require_once('admin/gallery/wp-uploader.php');
		die();
	}
	else {
		exit();
	}
}

// AJAX Script für Check Admin Image Upload im Backend ---- ENDE


// init languages

if(!function_exists('contest_gallery_init_languages')){
	function contest_gallery_init_languages() {

	  $folderName = (basename(dirname(__FILE__))=='trunk') ? 'contest-gallery' :  basename(dirname(__FILE__)); // check if offline development
	  load_plugin_textdomain( 'contest-gallery', false, $folderName . '/languages/' );

	}
}
add_action('plugins_loaded', 'contest_gallery_init_languages');


// init languages --- ENDE



// localize Scripts --- ENDE


add_action('admin_menu', 'contest_gallery_add_page');
if(!function_exists('contest_gallery_add_page')){
	function contest_gallery_add_page() {
		add_menu_page( 'Contest-Gallery uploads', 'Contest Gallery', 'edit_posts', __FILE__, 'contest_gallery_action', plugins_url('css/star_48_reduced.png', __FILE__ ));
	}
}


// WP Media Upload wird hier aktiviert!!!!!
	if (is_admin ()){
		add_action ( 'admin_enqueue_scripts', 'wp_enqueue_media');
	}
// WP Media Upload wird hier aktiviert!!!!! ---- ENDE



//------------------------------------------------------------
// ----------------------------------------------------------- Pro Version Abschnitt ----------------------------------------------------------





	add_shortcode( 'cg_users_login', 'contest_gal1ery_users_login' );

	if(!function_exists('contest_gal1ery_users_login')){

		function contest_gal1ery_users_login($atts){

			// PLUGIN VERSION CHECK HERE

			contest_gal1ery_db_check();

			// PLUGIN VERSION CHECK HERE --- END


			wp_enqueue_style( 'cg_form_style', plugins_url('/css/cg_form_style.css', __FILE__), false, '6.1.0' );

			wp_enqueue_script( 'cg_login', plugins_url( '/admin/users/js/users_login.js', __FILE__ ), array('jquery'), '6.1.0' );

			wp_localize_script( 'cg_login', 'post_cg_login_wordpress_ajax_script_function_name', array(
				'cg_login_ajax_url' => admin_url( 'admin-ajax.php' )
			));


			ob_start();
			include('admin/users/frontend/users-login.php');
			$contest_gal1ery_users_login = ob_get_clean();

			apply_filters( 'cg_filter_users_login', $contest_gal1ery_users_login );

			return $contest_gal1ery_users_login;

		}

	}

	add_action( 'wp_ajax_nopriv_post_cg_login', 'post_cg_login' );
	add_action( 'wp_ajax_post_cg_login', 'post_cg_login' );

	if(!function_exists('post_cg_login')){

		function post_cg_login(){

			global $wpdb;

			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

				require_once(dirname(__FILE__).'/admin/users/frontend/users-login-check-ajax.php');

				die();
			}
			else {

				exit();
			}
		}

	}



	// Prüfen ob eingeloggt und welche Role
	if(!function_exists('cg_remove_admin_bar_links')){

		function cg_remove_admin_bar_links() {
			global $wp_admin_bar, $current_user;

		   if(in_array("contest_gallery_user",$current_user->roles)==true){
				$wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
				$wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
				$wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
				$wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
				$wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
				$wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
				$wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
				$wp_admin_bar->remove_menu('view-site');        // Remove the view site link
				$wp_admin_bar->remove_menu('updates');          // Remove the updates link
				$wp_admin_bar->remove_menu('comments');         // Remove the comments link
				$wp_admin_bar->remove_menu('new-content');      // Remove the content link
				$wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
				$wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
				$wp_admin_bar->remove_menu('search');       // Remove the user details tab

				$AccountTitle = __("Account","contest-gallery");
				$LogoutTitle = __("Logout?","contest-gallery");

				$args = array(
					'id'    => 'contest_gallery_user_bar',
					'title' => "$AccountTitle: $current_user->user_login",
				);
				$wp_admin_bar->add_node($args);

				$args = array(
					'id'    => 'contest_gallery_user_bar_logout',
					'parent'    => 'contest_gallery_user_bar',
					'title' => $LogoutTitle,
					'href' => wp_logout_url(get_permalink())
				);
				$wp_admin_bar->add_node($args);       // Remove the user details tab
			}
		}

	}
	add_action( 'wp_before_admin_bar_render', 'cg_remove_admin_bar_links' );


	// NOTE: Of course change 3 to the appropriate user ID
	//$u = new WP_User( 106 );

	// Remove role
	//$u->remove_role( 'contest_gallery_user' );

	// Add role
	//$u->add_role( 'contest_gallery_user' );


// ----------------------------------------------------------- Pro Version Abschnitt ----------------------ENDE


//------------------------------------------------------------
// ----------------------------------------------------------- Hauptseite fÃ¼r hochgeladene Bilder ----------------------------------------------------------
//------------------------------------------------------------

if(!function_exists('contest_gallery_action')){
function contest_gallery_action() {

	// PLUGIN VERSION CHECK HERE

	contest_gal1ery_db_check();

	// PLUGIN VERSION CHECK HERE --- END


//$path = dirname(__FILE__) . "/admin/gallery/license.txt";

//$fh = fopen($path, 'r');
//$contents = fread($fh,filesize($path));
//echo "<br><br><b>You are using a $contents</b><br><br>";
//fclose($fh);
//------------------------------------------------------------
// ----------------------------------------------------------- Edit CSS file ----------------------------------------------------------
//------------------------------------------------------------

/*if (@$_GET['editCSSsend'] == true) {

require('admin/change-css.php');
require_once('admin/gallery/gallery.php');

exit('');

}		*/



if(@$_POST['changeSize']==true OR @$_GET['reset_users_votes'] == true OR @$_GET['reset_votes'] == true OR @$_GET['reset_votes2'] == true){

require_once('admin/options/change-options-and-sizes.php');

}


//------------------------------------------------------------
// ----------------------------------------------------------- Change options of gallery ----------------------------------------------------------
//------------------------------------------------------------

if (@$_GET['edit_options'] == true OR @$_POST['edit_options']==true OR @$_POST['changeSize']==true OR @$_GET['reset_users_votes'] == true ) {
//wp_enqueue_script( 'jquery.minicolors', plugins_url( '/js/color-picker/jquery.minicolors.js', __FILE__ ), array('jquery'), false, true );
//wp_enqueue_script( 'jquery.minicolors.min', plugins_url( '/js/color-picker/jquery.minicolors.min.js', __FILE__ ), array('jquery'), false, true );
//wp_enqueue_script( 'jquery_frontend_color_picker', plugins_url( '/js/color-picker/jquery_frontend_color_picker.js', __FILE__ ), array('jquery'), false, true );


wp_enqueue_script( 'cg_color_picker_js', plugins_url( '/admin/options/color-picker.js', __FILE__ ), array('jquery'), '6.1.0' );
wp_enqueue_script( 'cg_options_tabcontent_js', plugins_url( '/admin/options/cg_options_tabcontent.js', __FILE__ ), array('jquery'), '6.1.0' );
wp_enqueue_script( 'edit_options', plugins_url( '/js/edit_options.js', __FILE__ ), array('jquery'), '6.1.0' );
wp_enqueue_script( 'jquery-ui-sortable' );
wp_enqueue_script( 'jquery-ui-datepicker' );
wp_register_style( 'contest-style-css', plugins_url('admin/options/datepicker.css', __FILE__), false, '6.1.0' );
wp_enqueue_style( 'contest-style-css', plugins_url('admin/options/datepicker.css', __FILE__), false, '6.1.0' );
wp_register_style( 'color-picker-css', plugins_url('admin/options/color-picker.css', __FILE__), false, '6.1.0' );
wp_enqueue_style( 'color-picker-css', plugins_url('admin/options/color-picker.css', __FILE__), false, '6.1.0' );


require_once('admin/options/edit-options.php');

}

//------------------------------------------------------------
// ----------------------------------------------------------- Create an Upload Form ----------------------------------------------------------
//------------------------------------------------------------


if (@$_GET['define_upload'] == true) {
wp_enqueue_script( 'cg_admin_edit_upload_create_upload', plugins_url( '/js/admin/edit-upload/create_upload.js', __FILE__ ), array('jquery'), '6.1.0' );
wp_enqueue_script( 'admin_edit_upload_tinymce', plugins_url( '/js/admin/edit-upload/tinymce.js', __FILE__ ), array('jquery'), '6.1.0' );
wp_enqueue_script( 'jquery-ui-sortable' );
require_once('admin/upload/create-upload.php');

}

//------------------------------------------------------------
// ----------------------------------------------------------- Create an User reg Form ----------------------------------------------------------
//------------------------------------------------------------


if (@$_GET['create_user_form'] == true) {

wp_enqueue_script( 'create_user_form', plugins_url( '/admin/users/js/create_user_form.js', __FILE__ ), array('jquery'), '6.1.0' );
wp_enqueue_script( 'jquery-ui-sortab le' );
require_once('admin/users/admin/registry/create-user-form.php');
}

//------------------------------------------------------------
// ----------------------------------------------------------- Create an User reg Form ----------------------------------------------------------
//------------------------------------------------------------


if (@$_GET['users_management'] == true) {

require_once('admin/users/admin/users/management.php');

}


//------------------------------------------------------------
// ----------------------------------------------------------- Define an output of a pic ----------------------------------------------------------
//------------------------------------------------------------

if (@$_GET['define_output'] == true) {
wp_enqueue_script( 'define_output', plugins_url( '/js/define_output.js', __FILE__ ), array('jquery'), '6.1.0' );
wp_enqueue_script( 'jquery-ui-sortable' );
require_once('admin/upload/define-output.php');

}



//------------------------------------------------------------
// ----------------------------------------------------------- Change email text for informing users ----------------------------------------------------------
//------------------------------------------------------------

if (@$_POST['inform_user'] == true OR @$_GET['inform_user'] == true) {
wp_enqueue_script( 'change_text_inform_user', plugins_url( '/js/change_text_inform_user.js', __FILE__ ), array('jquery'), '6.1.0' );
require_once('admin/email/change-text-inform-user.php');

}


//------------------------------------------------------------
// ----------------------------------------------------------- Neue Galerie lÃ¶schen ----------------------------------------------------------
//------------------------------------------------------------

if(@$_GET['option_id']==true AND @$_GET['delete']==true){

require_once('admin/delete-gallery.php');
require_once('admin/main-menu.php');

}

//------------------------------------------------------------
// ----------------------------------------------------------- AuswahlmenÃ¼ zum Anzeigen und Erstellen von Galerien ----------------------------------------------------------
//------------------------------------------------------------

if(@$_GET['option_id']==false and @$_POST['option_id']==false){
//require('css/style.php');


	//add_action( 'plugins_loaded', 'contest_gal1ery_db_check' );
	require_once('admin/main-menu.php');


}


//------------------------------------------------------------
// ----------------------------------------------------------- User per Email informieren oder nicht informieren Ã¤ndern/ SPEICHERN ----------------------------------------------------------
//------------------------------------------------------------

//if (@$_POST['submit'] == true AND @$_POST['informId'] == true) {

//require_once('admin/email/inform-user.php');

//}


//------------------------------------------------------------
// ----------------------------------------------------------- Upload several pics to a certain galery ----------------------------------------------------------
//------------------------------------------------------------

if(@$_GET['option_id']==true AND @$_POST['upload_pics']==true){

require_once('admin/gallery/upload-pics.php');

}

//------------------------------------------------------------
// ----------------------------------------------------------- Reset informed for all pictures ----------------------------------------------------------
//------------------------------------------------------------

if(@$_POST['reset_all']==true){

require_once('admin/gallery/reset_all.php');

}


//------------------------------------------------------------
// ----------------------------------------------------------- Edit certain galery ----------------------------------------------------------
//------------------------------------------------------------

if (@$_GET['edit_gallery'] == true) {


		//------------------------------------------------------------
		// ----------------------------------------------------------- Hochgeladene Bilder anzeigen oder nicht anzeigen Ã¤ndern und Comments Ã¤ndern oder Informieren oder Informierte reseten SPEICHERN ----------------------------------------------------------
		//------------------------------------------------------------

		if (@$_POST['submit'] == true AND @$_POST['changeGalery'] == true AND (@$_POST['chooseAction1'] == 1 OR @$_POST['chooseAction1'] == 2 OR @$_POST['chooseAction1'] == 4)) {
		wp_enqueue_script( 'gallery_admin', plugins_url( '/js/gallery_admin.js', __FILE__ ), array('jquery'), '6.1.0' );
		wp_enqueue_script( 'cg_check_wp_admin_upload', plugins_url( '/js/cg_check_wp_admin_upload.js', __FILE__ ), array('jquery'), '6.1.0' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		//echo "change";
		require_once('admin/gallery/change-gallery.php');
		require_once('admin/gallery/gallery.php');

		}

		//------------------------------------------------------------
		// ----------------------------------------------------------- Delete pics of certain galery ----------------------------------------------------------
		//------------------------------------------------------------

		elseif (@$_POST['submit'] == true AND @$_POST['chooseAction1'] == 3) {
		wp_enqueue_script( 'gallery_admin', plugins_url( '/js/gallery_admin.js', __FILE__ ), array('jquery'), '6.1.0' );
		wp_enqueue_script( 'cg_check_wp_admin_upload', plugins_url( '/js/cg_check_wp_admin_upload.js', __FILE__ ), array('jquery'), '6.1.0' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		//echo "DELETE PICS!<br>";
		require_once('admin/gallery/delete-pics.php');
		require_once('admin/gallery/gallery.php');

		}

		//------------------------------------------------------------
		// ----------------------------------------------------------- Neue Galerie kreieren ----------------------------------------------------------
		//------------------------------------------------------------

		//wpmadd =(Damit keine neue Galerie kreiert wird wenn eine gerade kreiert wurde und bilder sofort hochgeladen wurden)
		elseif(@$_GET['option_id']==true AND (@$_GET['create']==true OR @$_GET['copy']==true) AND (@$_POST['cg_create']==true OR @$_POST['cg_copy']==true)  AND @$_GET['edit_gallery'] == true AND @$_GET['wpmadd'] != true ){
		wp_enqueue_script( 'gallery_admin', plugins_url( '/js/gallery_admin.js', __FILE__ ), array('jquery'), '6.1.0' );
		wp_enqueue_script( 'cg_check_wp_admin_upload', plugins_url( '/js/cg_check_wp_admin_upload.js', __FILE__ ), array('jquery'), '6.1.0' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		require_once('admin/create-gallery.php');
		require_once('admin/gallery/gallery.php');

		}

		//------------------------------------------------------------
		// ----------------------------------------------------------- Edit certain galery ----------------------------------------------------------
		//------------------------------------------------------------


		else{
		wp_enqueue_script( 'gallery_admin', plugins_url( '/js/gallery_admin.js', __FILE__ ), array('jquery'), '6.1.0' );
		wp_enqueue_script( 'cg_check_wp_admin_upload', plugins_url( '/js/cg_check_wp_admin_upload.js', __FILE__ ), array('jquery'), '6.1.0' );
		wp_enqueue_script( 'jquery-ui-sortable' );	
		require_once('admin/gallery/gallery.php');
		}
		
		
		
}






//------------------------------------------------------------ 
// ----------------------------------------------------------- Kommentare anzeigen oder nicht anzeigen Ã¤ndern ----------------------------------------------------------
//------------------------------------------------------------	
	
 if (@$_POST['submitcomments'] == true) {

require_once('change-show-comments.php');

}	

//------------------------------------------------------------
// ----------------------------------------------------------- Kommentare eines einzelnen Bildes anzeigen ----------------------------------------------------------
//------------------------------------------------------------

if(@$_GET['show_comments']==true){

require_once('admin/gallery/show-comments.php');	

}



}
}
?>