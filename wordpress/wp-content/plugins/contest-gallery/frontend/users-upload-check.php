<?php

// Aurufen von WP-Config hier notwendig
//require_once("../../../../wp-config.php");

//echo "testtest";



// User ID �berpr�fung ob es die selbe ist
$CheckCheck = wp_create_nonce("check");

$check = @$_POST['check'];
$sendUserMail = '';
$userMail = '';


if($CheckCheck==$check && $_POST["cg_upload_action"]==true){


    $galeryID = intval(@$_POST['GalleryID']);

    //echo "galeryID: $galeryID";

    $unix = time();
    $unixadd = $unix+2;

    if(!function_exists('return_bytes1')){
        function return_bytes1($val) {
            $val = trim($val);
            $last = strtolower($val{strlen($val)-1});
            switch($last) {
                // The 'G' modifier is available since PHP 5.1.0
                case 'g':
                    $val *= 1024;
                case 'm':
                    $val *= 1024;
                case 'k':
                    $val *= 1024;
            }

            return $val;
        }
    }

    //echo "test1";


    $maxigroesse = return_bytes1(ini_get('upload_max_filesize'));


    //----------------------------Prove if user tries to reload ---------------->

    global $wpdb;

    $tablename1 = $wpdb->prefix . "contest_gal1ery";
    $tablename_f_input = $wpdb->prefix . "contest_gal1ery_f_input";
    $tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";
    $selectSQL1 = $wpdb->get_row( "SELECT * FROM $tablenameOptions WHERE id = '$galeryID'" );
    $tablenameentries = $wpdb->prefix . "contest_gal1ery_entries";
    $table_posts = $wpdb->prefix."posts";
    $wpUsers = $wpdb->base_prefix . "users";


    // These files need to be included as dependencies when on the front end.
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );

    $content_url = wp_upload_dir();
    $content_url = $content_url['baseurl']; // Pfad zum Bilderordner angeben

    $ActivateBulkUpload = $selectSQL1->ActivateBulkUpload;
    $BulkUploadQuantity = $selectSQL1->BulkUploadQuantity;
    $InformUser = $selectSQL1->Inform;
    $ActivateUpload = $selectSQL1->ActivateUpload;
    $files = $_FILES["data"];
    $uploadQuantity = count($files["name"]);

    if(empty($BulkUploadQuantity)){
        $BulkUploadQuantity = 100;
    }

    if($ActivateBulkUpload==1 && $uploadQuantity > $BulkUploadQuantity){echo "Plz don't manipulate upload quantity.";die;}
    if($ActivateBulkUpload==0 && $uploadQuantity > 1){echo "Plz don't manipulate upload quantity.";die;}

    // echo var_dump($files["name"]);

    //die;
    if(!empty($files['name']) && is_array($files["name"])){


        foreach ($files['name'] as $key => $value) {

            if ($files['name'][$key]) {
                $file = array(
                    'name' => $files['name'][$key],
                    'type' => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error' => $files['error'][$key],
                    'size' => $files['size'][$key]
                );


            }

            $dateityp = GetImageSize($file["tmp_name"]);


            /*            $imageTypeArray = array
            (
                0=>'UNKNOWN',
                1=>'GIF',
                2=>'JPEG',
                3=>'PNG',
                4=>'SWF',
                5=>'PSD',
                6=>'BMP',
                7=>'TIFF_II',
                8=>'TIFF_MM',
                9=>'JPC',
                10=>'JP2',
                11=>'JPX',
                12=>'JB2',
                13=>'SWC',
                14=>'IFF',
                15=>'WBMP',
                16=>'XBM',
                17=>'ICO',
                18=>'COUNT'
            );*/


            if ($dateityp[2] != 1 && $dateityp[2] != 2 && $dateityp[2] != 3) {

                // File size wird als 0 ausgegeben wenn die hoch zu ladende Datei gr��er ist als Server erlaubt. File type und andere Infos dann auch nicht vorhanden.
                echo "Don't manipulate the upload: wrong file type or file size"; die;

            }


            if (function_exists('exif_read_data')){

                // Nicht funktionierende Rotate Möglichkeit für Bilder im TMP Folder:
                // https://codex.wordpress.org/Function_Reference/wp_get_image_editor

                $tmpFilename = $files['tmp_name'][$key];



                $exif = exif_read_data($tmpFilename);

               //var_dump($exif['Orientation']);
            //   die;



                if (!empty($exif['Orientation'])) {



                     // provided that the image is jpeg. Use relevant function otherwise
                    switch ($exif['Orientation']) {
                        case 3:
                            if($dateityp[2] == 1){$imageResource = imagecreatefromgif($tmpFilename);}
                            if($dateityp[2] == 2){$imageResource = imagecreatefromjpeg($tmpFilename);}
                            if($dateityp[2] == 3){$imageResource = imagecreatefrompng($tmpFilename);}
                            $image = imagerotate($imageResource, 180, 0);
                            if($dateityp[2] == 1){imagegif($image, $tmpFilename, 90);}
                            if($dateityp[2] == 2){imagejpeg($image, $tmpFilename, 90);}
                            if($dateityp[2] == 3){imagepng($image, $tmpFilename, 90);}
                            imagedestroy($imageResource);
                            imagedestroy($image);
                            break;
                        case 6:
                            if($dateityp[2] == 1){$imageResource = imagecreatefromgif($tmpFilename);}
                            if($dateityp[2] == 2){$imageResource = imagecreatefromjpeg($tmpFilename);}
                            if($dateityp[2] == 3){$imageResource = imagecreatefrompng($tmpFilename);}
                            $image = imagerotate($imageResource, -90, 0);
                            if($dateityp[2] == 1){imagegif($image, $tmpFilename, 90);}
                            if($dateityp[2] == 2){imagejpeg($image, $tmpFilename, 90);}
                            if($dateityp[2] == 3){imagepng($image, $tmpFilename, 90);}
                            imagedestroy($imageResource);
                            imagedestroy($image);
                            break;
                        case 8:
                            if($dateityp[2] == 1){$imageResource = imagecreatefromgif($tmpFilename);}
                            if($dateityp[2] == 2){$imageResource = imagecreatefromjpeg($tmpFilename);}
                            if($dateityp[2] == 3){$imageResource = imagecreatefrompng($tmpFilename);}
                            $image = imagerotate($imageResource, 90, 0);
                            if($dateityp[2] == 1){imagegif($image, $tmpFilename, 90);}
                            if($dateityp[2] == 2){imagejpeg($image, $tmpFilename, 90);}
                            if($dateityp[2] == 3){imagepng($image, $tmpFilename, 90);}
                            imagedestroy($imageResource);
                            imagedestroy($image);
                            break;
                        default:
                            $doNothing = '';
                    }
                }


            }

// $test->save( $files['tmp_name'][$key] );


            $_FILES = array ("data" => $file);
            //var_dump($_FILES);
            foreach ($_FILES as $file => $array) {
                // $newupload = my_handle_attachment($file,$post_id);

                // Use the wordpress function to upload
                // test_upload_pdf corresponds to the position in the $_FILES array
                // 0 means the content is not associated with any other posts

                $attach_id = media_handle_upload($file,0);
                //var_dump($attach_id);

                if ( is_wp_error( $attach_id ) ) {
                    echo "There was an error uploading the image. Plz contact support@contest-gallery.com."; die;
                } else {
                    //echo "The image was uploaded successfully!";
                    //var_dump($attachment_id);
                }

            }

//		$content_url = wp_upload_dir();
            //	$content_url = $content_url['baseurl']; // Pfad zum Bilderordner angeben


            $collectImageIDs = array();




            //	var_dump($_FILES['data']['tmp_name']);die;


            //	$uploads = wp_upload_dir();
            //$WPdestination = $uploads['basedir'].'/contest-gallery/gallery-id-'.$galeryID.'/';  //   Pfad zum Bilderordner angeben


            //----------------------------Upload file and save in database ---------------->

            /*
            if ((isset(@$_POST['submit']) && @$_POST['submit']==true) AND $_FILES['date']['size'] <= 0) {
            echo "<strong>Sie haben kein Bild ausgew&auml;hlt zum Hochladen.</strong><br/><br/>";
            }*/

            if ($files['size'] > 0) {


                $tempname = $files['tmp_name'][$key];
                $dateiname = $files['name'][$key];
                $dateiname = strtolower($dateiname);
                $dateigroesse = $files['size'][$key];


                $wp_image_info = $wpdb->get_row("SELECT * FROM $table_posts WHERE ID = '$attach_id'");
                $image_url = $wp_image_info->guid;
                $post_title = $wp_image_info->post_title;
                $post_type = $wp_image_info->post_mime_type;
                $wp_image_id = $wp_image_info->ID;

                // Notwendig: wird in convert-several-pics so verabeitet. Darf keine Sonderzeichen enthalten!
                $search = array(" ", "!", '"', "#", "$", "%", "&", "'", "(", ")", "*", "+", ",", "/", ":", ";", "=", "?", "@", "[","]","�");
                $post_title = str_replace($search,"_",$post_title);
                // var_dump($post_title); die;
                $dateiname = $post_title;


                $doNotProcess=0;

                if($post_type=="image/jpeg"){$post_type="jpg";$imageType="jpg";}
                else if($post_type=="image/png"){$post_type="png";$imageType="png";}
                else if($post_type=="image/gif"){$post_type="gif";$imageType="gif";}
                else{
                    $doNotProcess=1;
                }
                //echo "post_type $post_type<br>";
                $uploads = wp_upload_dir();

                $check = explode($uploads['baseurl'],$image_url);

                //echo $uploads['basedir'].$check[1].$post_title.".".$post_type;

                $filename = $uploads['basedir'].$check[1];

                //  var_dump($filename); die;
                //  var_dump($dateiname); die;


                //----------------------------Create Thumbs and Galery pics ---------------->

                //echo "yes<br>";


                // destination of the uploaded original image
                //$filename = $WPdestination . $unixadd . '_' . $dateiname.".".$imageType;

                $unix = time();
                $unixadd = $unix+2;

                require(dirname(__FILE__) . "/../convert-several-pics.php");


                //----------------------------Create Thumbs and Galery pics END ----------------//

                //$wpdb->insert( $tablename1, array( 'id' => '', 'rowid' => "$nextId", 'Timestamp' => $unixadd, 'NamePic' => $dateiname, 'ImgType' => $imageType, 'CountC' => 0, 'CountR' => '', 'Rating' => '', 'GalleryID' => $galeryID, 'Active' => 0, 'Informed' => 0  ) );

                if(is_user_logged_in()){
                    $WpUserId = get_current_user_id();
                }
                else{
                    $WpUserId = '';
                }

                $wpdb->query( $wpdb->prepare(
                    "
					INSERT INTO $tablename1
					( id, rowid, Timestamp, NamePic,
					ImgType, CountC, CountR, Rating,
					GalleryID, Active, Informed, WpUpload, Width, Height, WpUserId)
					VALUES ( %s,%s,%d,%s,
					%s,%d,%s,%s,
					%d,%s,%s,%s,%s,%s,%s)
				",
                    '','',$unixadd,$dateiname,
                    $post_type,0,'','',
                    $galeryID,'','',$wp_image_id,$current_width,$current_height,$WpUserId
                ) );


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

                // Insert Upload Fields for pic if exists

                $nextId = $wpdb->get_var("SELECT id FROM $tablename1 WHERE Timestamp='$unixadd' AND NamePic='$dateiname'");

                $wpdb->update(
                    "$tablename1",
                    array('rowid' => $nextId),
                    array('id' => $nextId),
                    array('%d'),
                    array('%d')
                );

                // Sp�ter f�r Inform Image wichtig
                $collectImageIDs[] = $nextId;



                if (@$_POST['form_input']){

                    //	print_r($form_input);

                    //$form_input = sanitize_text_field(@$_POST['form_input']);
                    $form_input = @$_POST['form_input'];

                    //print_r($form_input);


                    $i=0;


                    // 1. Feldtyp <<< Zur Bestimmung der Feldart f�r weitere Verarbeitung in der Datenbank, Admin etc.
                    // 2. Feldnummer <<<  Zur Bestimmung der Feldreihenfolge in Frontend und Admin.
                    // 3. Feldreihenfolge
                    // 4. Feldinhalt

                    foreach ($form_input as $key => $value) {

                        $i++;

                        // Short_Text Entries werden eingef�gt (Name, E-Mail)

                        if ($i==1 AND (@$ft!='kf')){$ft = $value; continue;}

                        if ($i==2 AND ($ft=='nf' or $ft=='ef' or $ft=='se')){$f_input_id = $value; continue;}

                        if ($i==3 AND ($ft=='nf' or $ft=='ef' or $ft=='se')){$field_order = $value;  continue;}

                        if ($i==4 AND ($ft=='nf' or $ft=='ef' or $ft=='se')){

                            //echo "<br>insert $ft<br>";
                            //echo "<br>f_input_id $f_input_id<br>";
                            //echo "<br>field_order $field_order<br>";

                            if(is_user_logged_in() && $ft=='ef'){

                                global $current_user;
                                get_currentuserinfo();
                                $content = $current_user->user_email;

                            }
                            else{
                                $content = $value;
                            }



                            $content = stripslashes($content);
                            $content = sanitize_text_field($content);
                            $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

                            if($ft=='nf'){
                                //$wpdb->insert( $tablenameentries, array( 'id' => '', 'pid' => $nextId, 'f_input_id' => $f_input_id, 'GalleryID' => $galeryID, "Field_Type" => 'text-f', 'Field_Order' => $field_order, 'Short_Text' => $content, 'Long_Text' => '') );

                                $wpdb->query( $wpdb->prepare(
                                    "
                                    INSERT INTO $tablenameentries
                                    ( id, pid, f_input_id, GalleryID, Field_Type, Field_Order, Short_Text, Long_Text)
                                    VALUES ( %s,%d,%d,%d,%s,%d,%s,%s )
                                ",
                                    '',$nextId,$f_input_id,$galeryID,'text-f',$field_order,$content,''
                                ) );

                            }

                            if($ft=='se'){

                                //$wpdb->insert( $tablenameentries, array( 'id' => '', 'pid' => $nextId, 'f_input_id' => $f_input_id, 'GalleryID' => $galeryID, "Field_Type" => 'text-f', 'Field_Order' => $field_order, 'Short_Text' => $content, 'Long_Text' => '') );

                                $wpdb->query( $wpdb->prepare(
                                    "
                                    INSERT INTO $tablenameentries
                                    ( id, pid, f_input_id, GalleryID, Field_Type, Field_Order, Short_Text, Long_Text)
                                    VALUES ( %s,%d,%d,%d,%s,%d,%s,%s )
                                ",
                                    '',$nextId,$f_input_id,$galeryID,'select-f',$field_order,$content,''
                                ) );

                            }

                            if($ft=='ef'){
                                //$wpdb->insert( $tablenameentries, array( 'id' => '', 'pid' => $nextId, 'f_input_id' => $f_input_id, 'GalleryID' => $galeryID, "Field_Type" => 'email-f', 'Field_Order' => $field_order, 'Short_Text' => $content, 'Long_Text' => '') );

                                $sendUserMail = $content;


                                $wpdb->query( $wpdb->prepare(
                                    "
					INSERT INTO $tablenameentries
					( id, pid, f_input_id, GalleryID, Field_Type, Field_Order, Short_Text, Long_Text)
					VALUES ( %s,%d,%d,%d,%s,%d,%s,%s )
				",
                                    '',$nextId,$f_input_id,$galeryID,'email-f',$field_order,$content,''
                                ) );

                            }

                            $ft=false;
                            $f_input_id=false;
                            $field_order=false;
                            $i=0;
                            continue;
                        }

                        // Short_Text Entries werden eingef�gt ---- ENDE

                        // Long Entries werden eingef�gt

                        if ($i==1 AND ($ft!='nf' or $ft!='ef')){$ft = $value; continue;}

                        if ($i==2 AND ($ft=='kf')){$f_input_id = $value; continue;}

                        if ($i==3 AND ($ft=='kf')){$field_order = $value; continue;}

                        if ($i==4 AND ($ft=='kf')){

                            //echo "<br>insert $ft<br>";
                            //echo "<br>f_input_id $f_input_id<br>";
                            //echo "<br>field_order $field_order<br>";

                            $content = $value;

                            $content = stripslashes($content);
                            $content = sanitize_textarea_field(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));

                            //echo "<br>content $content<br>";


                            //$wpdb->insert( $tablenameentries, array( 'id' => '', 'pid' => $nextId, 'f_input_id' => $f_input_id, 'GalleryID' => $galeryID, "Field_Type" => 'comment-f', 'Field_Order' => $field_order, 'Short_Text' => '', 'Long_Text' => $content) );

                            $wpdb->query( $wpdb->prepare(
                                "
					INSERT INTO $tablenameentries
					( id, pid, f_input_id, GalleryID, Field_Type, Field_Order, Short_Text, Long_Text)
					VALUES ( %s,%d,%d,%d,%s,%d,%s,%s )
				",
                                '',$nextId,$f_input_id,$galeryID,'comment-f',$field_order,'',$content
                            ) );

                            $ft=false;
                            $f_input_id=false;
                            $field_order=false;
                            $i=0;
                            continue;
                        }

                        // Long Entries werden eingef�gt ---- ENDE


                    }

                }

                // Activate and send e-mail


                //@$ActivateUpload = $wpdb->get_var( "SELECT ActivateUpload FROM $tablenameOptions WHERE ActivateUpload='1' and id = '$galeryID' " );

                if($ActivateUpload==1){


                    $wpdb->update(
                        "$tablename1",
                        array('Active' => '1'),
                        array('id' => $nextId),
                        array('%d'),
                        array('%d')
                    );



                    // Gr��e der Bilder bei ThumbAnsicht (gew�hnliche Ansicht mit Bewertung)
                    $uploadFolder = wp_upload_dir();
                    $urlSource = site_url();
                    $blog_title = get_bloginfo();




                    $dirHTML = $uploadFolder['basedir'].'/contest-gallery/gallery-id-'.$galeryID.'/'.$unixadd."_".$dateiname."413.html";

                    if(!file_exists($dirHTML)){

                        $scrImgMeta624 = $uploadFolder['baseurl'].'/contest-gallery/gallery-id-'.$galeryID.'/'.$unixadd."_".$dateiname."-624width.".$imageType."";
                        $scrImgMeta1024 = $uploadFolder['baseurl'].'/contest-gallery/gallery-id-'.$galeryID.'/'.$unixadd."_".$dateiname."-1024width.".$imageType."";

                        $urlForFacebook= $uploadFolder['baseurl'].'/contest-gallery/gallery-id-'.$galeryID."/".$unixadd."_".$dateiname."413.html";


                        //$urlForFacebook= $urlSource.'/wp-content/uploads/contest-gallery/gallery-id-'.$galeryID."/".$unixadd."_".$dateiname.".html";


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



                    if(is_user_logged_in()==true){
                        $userMail = $wpdb->get_var("SELECT user_email FROM $wpUsers WHERE ID = $WpUserId");
                    }
                    else{
                        $userMail = $sendUserMail;
                    }



                    if(!empty($userMail) && $InformUser == 1){



                        @$tablenameemail = $wpdb->prefix . "contest_gal1ery_mail";
                        @$selectSQLemail = $wpdb->get_row( "SELECT * FROM $tablenameemail WHERE GalleryID = '$galeryID'" );

                        @$Subject = $selectSQLemail->Header;
                        @$Admin = $selectSQLemail->Admin;
                        @$Reply = $selectSQLemail->Reply;
                        @$cc = $selectSQLemail->CC;
                        @$bcc = $selectSQLemail->BCC;
                        //@$Msg = nl2br($selectSQLemail->Content);
                        //$Msg = html_entity_decode(stripslashes($Msg1));

                        // echo "<br>MSG:<br>";
                        // echo "Msg: $Msg";
                        // echo "<br>";
                        //$Msg = $selectSQLemail->Content;


                        @$url = @$selectSQLemail->URL;
                        @$url = (strpos(@$url,'?')) ? @$url.'&' : @$url.'?';

                        @$contentMail = nl2br(@$selectSQLemail->Content);

                        @$posUrl = "\$url\$";

                        // echo $posUrl;

                        @$urlCheck = (stripos(@$contentMail,@$posUrl)) ? 1 : 0;




                        @$To = sanitize_text_field($userMail);


                        if (filter_var(@$To, FILTER_VALIDATE_EMAIL)) {

                            if(@$urlCheck==1 and @$url==true){

                                @$codedPictureId = ($nextId+8)*2+100000; // Verschl�sselte ID ermitteln. Gecachte Sites sind mit verschl�sselter ID gespeichert.

                                @$url1 = @$url."picture_id=$codedPictureId#cg-begin";

                                @$replacePosUrl = '$url$';

                                @$Msg = str_ireplace(@$replacePosUrl, @$url1, @$contentMail);

                            }


                            $headers = '';

                            require_once(dirname(__FILE__)."/class-inform-user.php");
                            $headers .= "Reply-To: ". strip_tags(@$Reply) . "\r\n";
                            $headers .= "CC: $cc\r\n";
                            $headers .= "BCC: $bcc\r\n";
                            $headers .= "MIME-Version: 1.0\r\n";
                            $headers .= "Content-Type: text/html; charset=utf-8\r\n";



                            add_filter('wp_mail_from', 'new_mail_from');
                            add_filter('wp_mail_from_name', 'new_mail_from_name');



                            @wp_mail(@$To, @$Subject, @$Msg, @$headers);

                            //echo "<br>To: $To<br>";
                            //echo "<br>Subject: $Subject<br>";
                            //echo "<br>Msg: $Msg<br>";
                            //echo "<br>headers: $headers<br>";





                            //	}


                            $wpdb->update(
                                "$tablename1",
                                array('Informed' => '1'),
                                array('id' => $nextId),
                                array('%d'),
                                array('%d')
                            );



                        }

                    }


                }


                // Inform Admin if configured

                @$InformAdmin = $wpdb->get_var( "SELECT InformAdmin FROM $tablenameOptions WHERE id = '$galeryID'" );




                if(@$InformAdmin==1){


                    @$tablenameemailadmin = $wpdb->prefix . "contest_gal1ery_mail_admin";
                    @$selectSQLemailAdmin = $wpdb->get_row( "SELECT * FROM $tablenameemailadmin WHERE GalleryID = '$galeryID'" );

                    @$Subject = $selectSQLemailAdmin->Header;
                    @$Admin = $selectSQLemailAdmin->Admin;
                    @$AdminMail = $selectSQLemailAdmin->AdminMail;
                    @$Reply = $selectSQLemailAdmin->Reply;
                    @$cc = $selectSQLemailAdmin->CC;
                    @$bcc = $selectSQLemailAdmin->BCC;
                    //@$Msg = nl2br($selectSQLemailAdmin->Content);


                    //var_dump($Reply);die;


                    // Alle image IDs werden durchgegangen die gerade neu angelegt wurden
                    foreach($collectImageIDs as $key => $imageID){

                        $UserEntries = '<br/>';


                        //$Msg = html_entity_decode(stripslashes($Msg1));

                        // echo "<br>MSG:<br>";
                        // echo "Msg: $Msg";
                        // echo "<br>";
                        //$Msg = $selectSQLemail->Content;


                        //@$url = @$selectSQLemailAdmin->URL;
                        //@$url = (strpos(@$url,'?')) ? @$url.'&' : @$url.'?';

                        @$contentMail = nl2br(@$selectSQLemailAdmin->Content);

                        @$posUserInfo = "\$info\$";

                        // echo $posUrl;

                        @$posUserInfoCheck = (stripos(@$contentMail,@$posUserInfo)) ? 1 : 0;

                        @$To = sanitize_text_field(@$AdminMail);

                        //@$replacePosUrl = '$info$';

                        //Get User Info from entries

                        if($posUserInfoCheck==1){


                            @$selectFormInput = $wpdb->get_results( "SELECT id, Field_Type, Field_Order, Field_Content FROM $tablename_f_input WHERE GalleryID = '$galeryID' AND (Field_Type = 'text-f' OR Field_Type = 'comment-f' OR Field_Type ='email-f') ORDER BY Field_Order ASC" );




                            $selectContentFieldArray = array();

                            foreach (@$selectFormInput as $value) {

                                // 1. Feld Typ
                                // 2. ID des Feldes in F_INPUT
                                // 3. Feld Reihenfolge
                                // 4. Feld Content

                                $selectFieldType = 	$value->Field_Type;
                                $id = $value->id;// prim�re unique id der form wird auch gespeichert und sp�ter in entries inserted zur erkennung des verwendeten formular feldes
                                $fieldOrder = $value->Field_Order;// Die originale Field order in f_input Tabelle. Zwecks Vereinheitlichung.
                                $selectContentFieldArray[] = $selectFieldType;
                                $selectContentFieldArray[] = $id;
                                $selectContentFieldArray[] = $fieldOrder;
                                $selectContentField = unserialize($value->Field_Content);
                                $selectContentFieldArray[] = $selectContentField["titel"];

                            }

                            foreach($selectContentFieldArray as $key => $formvalue){

                                //	echo "<br>$i<br>";

                                // 1. Feld Typ
                                // 2. ID des Feldes in F_INPUT
                                // 3. Feld Reihenfolge
                                // 4. Feld Content



                                if(@$formvalue=='text-f'){$fieldtype="nf"; $n=1; continue;}
                                if(@$fieldtype=="nf" AND $n==1){$formFieldId=$formvalue; $n=2; continue;}
                                if(@$fieldtype=="nf" AND $n==2){$fieldOrder=$formvalue; $n=3; continue;}
                                if (@$fieldtype=="nf" AND $n==3) {

                                    $getEntries = $wpdb->get_var( $wpdb->prepare(
                                        "
															SELECT Short_Text
															FROM $tablenameentries 
															WHERE pid = %d and f_input_id = %d
														",
                                        $imageID,$formFieldId
                                    ) );

                                    $UserEntries .= "<br/><strong>$formvalue:</strong><br/>";
                                    $UserEntries .= "$getEntries<br/>";

                                    $fieldtype='';
                                    $i=0;

                                }

                                if($formvalue=='email-f'){@$fieldtype="ef";  $n=1; continue;}
                                if(@$fieldtype=="ef" AND $n==1){$formFieldId=$formvalue; $n=2; continue;}
                                if(@$fieldtype=="ef" AND $n==2){$fieldOrder=$formvalue; $n=3; continue;}
                                if (@$fieldtype=='ef' AND $n==3) {



                                    $getEntries = $wpdb->get_var( $wpdb->prepare(
                                        "
															SELECT Short_Text
															FROM $tablenameentries 
															WHERE pid = %d and f_input_id = %d
														",
                                        $imageID,$formFieldId
                                    ) );

                                    $UserEntries .= "<br/><strong>$formvalue:</strong><br/>";
                                    $UserEntries .= "$getEntries<br/>";

                                    $fieldtype='';
                                    $i=0;


                                }

                                if($formvalue=='comment-f'){$fieldtype="kf"; $n=1; continue;}
                                if($fieldtype=="kf" AND $n==1){$formFieldId=$formvalue; $n=2; continue;}
                                if($fieldtype=="kf" AND $n==2){$fieldOrder=$formvalue; $n=3; continue;}
                                if ($fieldtype=='kf' AND $n==3) {


                                    $getEntries = nl2br($wpdb->get_var( $wpdb->prepare(
                                        "
															SELECT Long_Text
															FROM $tablenameentries 
															WHERE pid = %d and f_input_id = %d
														",
                                        $imageID,$formFieldId
                                    )));

                                    $UserEntries .= "<br/><strong>$formvalue:</strong><br/>";
                                    $UserEntries .= "$getEntries<br/>";

                                    $fieldtype='';
                                    $i=0;


                                }


                            }


                            // Daten zur User URL sammeln


                            $wpImgId = $wpdb->get_var("SELECT WpUpload FROM $tablename1 WHERE GalleryID = '$galeryID' AND  id = '$imageID'");
                            $selectImageData = $wpdb->get_var( "SELECT guid FROM $table_posts WHERE ID = '$wpImgId' ");


                            $UserEntries .= "<br/><br/><strong>Original Image URL:</strong><br/>";
                            $UserEntries .= $selectImageData;


                            // User Info wird implementiert anstelle von $info$
                            @$Msg = str_ireplace(@$posUserInfo, @$UserEntries, @$contentMail);





                        }



                        /*
                            if (filter_var(@$To, FILTER_VALIDATE_EMAIL)) {



                                if(@$urlCheck==1 and @$url==true){



                                @$codedPictureId = ($nextId+8)*2+100000; // Verschl�sselte ID ermitteln. Gecachte Sites sind mit verschl�sselter ID gespeichert.

                                @$url1 = @$url."picture_id=$codedPictureId#cg-begin";

                                @$replacePosUrl = '$info$';



                                }*/


                        $headers1 = '';

                        require_once(dirname(__FILE__)."/class-inform-user.php");
                        $headers1 .= "Reply-To: ". strip_tags(@$Reply) . "\r\n";
                        $headers1 .= "CC: $cc\r\n";
                        $headers1 .= "BCC: $bcc\r\n";
                        $headers1 .= "MIME-Version: 1.0\r\n";
                        $headers1 .= "Content-Type: text/html; charset=utf-8\r\n";



                        add_filter('wp_mail_from', 'new_mail_from');
                        add_filter('wp_mail_from_name', 'new_mail_from_name');



                        @wp_mail(@$To, @$Subject, @$Msg, @$headers1);

                        //echo "<br>To: $To<br>";
                        //echo "<br>Subject: $Subject<br>";
                        //echo "<br>Msg: $Msg<br>";
                        //echo "<br>headers: $headers<br>";


                    }

                }





                // Inform Admin if configured --- ENDE




                // Activate and send e-mail --- END



                // Prove and insert send data --- END



            }


        }
    }
















    // Forward confirmation texte after upload




    $contest_gal1ery_options_input = $wpdb->prefix . "contest_gal1ery_options_input";

    $inputOptionsSQL = $wpdb->get_row( "SELECT * FROM $contest_gal1ery_options_input WHERE GalleryID='$galeryID'"); // hier aufgeh�rt. Die Gallery ID wird nicht �bertragen, muss her geholt werden aus dem Jquery Post vorher oder aus dem Wordpress-PHP
    $Forward = $inputOptionsSQL->Forward;

    if($Forward==1){

        $Forward_URL = $inputOptionsSQL->Forward_URL;
        $Forward_URL = html_entity_decode(stripslashes($Forward_URL));

        $Forward_URLcheck = substr($Forward_URL, 0, 3);
        $Forward_URLcheck = strtolower($Forward_URLcheck);

        if($Forward_URLcheck=='www'){
            $Forward_URL = "http://".$Forward_URL;
        }



        ?>
        <script>

            // funtzt nur so als vorher bestimmte variable
            var redirectURL = <?php echo json_encode($Forward_URL);?>;

            // similar behavior as an HTTP redirect
            window.location.replace(redirectURL);


        </script>
        <?php
        //require("forward-url.php");

        //exit;
        //echo $Forward_URL;

    }

    else{

        $permalinkURL = get_permalink();

        $checkPermalinkURL = explode('?',$permalinkURL);

        if(@$checkPermalinkURL[1]){
            $cg_upload_forward_siteURL = $checkPermalinkURL[0]."?".$checkPermalinkURL[1];

            $siteURLsort = $checkPermalinkURL[0];
        }
        else{$cg_upload_forward_siteURL = $permalinkURL."?";}

        ?>
        <script>



            // funtzt nur so als vorher bestimmte variable
            var galeryID = <?php echo json_encode($galeryID);?>;
            var cg_upload_forward_siteURL = <?php echo json_encode($cg_upload_forward_siteURL);?>;

            // funtzt nur so als vorher bestimmte variable
            var redirectURL = ""+cg_upload_forward_siteURL+"&cg_upload=success&cg_id="+galeryID+"#cg_success";
            //alert(redirectURL);
            // similar behavior as an HTTP redirect
            window.location.replace(redirectURL);


        </script>
        <?php


    }

    echo "<br/>";


}

else{

    ?>
    <script>
        console.log("Plz don't fiddle the upload.");
    </script>
    <?php

//echo "action";


}


?>