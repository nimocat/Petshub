<?php

/*
AUFBAU

1. Ermitteln von Formular Daten wenn die ganze Galerie angezeigt wird
2. SQL Klasse wird aufgestellt
3. Ermitteln von Options
4. Ermitteln von Formular Daten wenn einzelnes Bild angezeigt wird
5. Ermitteln von Image Daten
6. Aufbau diverser Variablen, die von Fall zu Fall gebraucht werden. 


*/




	// Tabellennamen ermitteln, GalleryID wurde als Shortcode bereits �bermittelt.

		extract( shortcode_atts( array(
		'id' => ''
	), $atts ) );
$galeryID = $atts['id'];


global $wpdb;



	$tablename = $wpdb->prefix . "contest_gal1ery";
	$tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";
	$tablenameComments = $wpdb->prefix . "contest_gal1ery_comments";
	$tablename_f_input = $wpdb->prefix . "contest_gal1ery_f_input";
	$tablename_f_output = $wpdb->prefix . "contest_gal1ery_f_output";
	$tablename_options_visual = $wpdb->prefix . "contest_gal1ery_options_visual";
	$tablenameEntries = $wpdb->prefix . "contest_gal1ery_entries";
	$tablenameIP = $wpdb->prefix ."contest_gal1ery_ip";
	$table_posts = $wpdb->prefix ."posts";


	// Wird schon hier bestimmt, damit weniger wenn single image view gew�hlt ist
/*	$AllowGalleryScript = $wpdb->get_var( $wpdb->prepare(
	"
		SELECT AllowGalleryScript
		FROM $tablenameOptions 
		WHERE id = %d
	",
	$galeryID
	) );*/




		// ------------ Ermitteln der Options-Daten (Show-Image und Show-Gallery)

        $optionsSQL = $wpdb->get_row("SELECT * FROM $tablenameOptions WHERE id='$galeryID'");
        $idDoesNotExists = 0;
        if(empty($optionsSQL)){
            $idDoesNotExists = 1;
            echo "Please check your shortcode. The id does not exists.<br>";
            return false;
        }
		
		
		//Pr�fen ob bei Klick auf images weitergelitet werden soll
		@$Use_as_URL = $wpdb->get_var( "SELECT Use_as_URL FROM $tablename_f_input WHERE GalleryID='$galeryID' AND Use_as_URL='1'");
		
		//Pr�fung ob es ein input Feld gibt welches als url bestimmt wurde
		if(@$Use_as_URL==true){@$cg_Use_as_URL=1;}
		else{@$cg_Use_as_URL=0;}
		
		//@$getSQL = new sql_selects($tablenameOptions,$galeryID,$order,$start,$step,$pictureID);
		
	//	$optionsSQL = $getSQL->options_sql();
		

		
		$RowLook = $optionsSQL->RowLook; // Bilder in einer Reiehe nacheinander anzeigen oder nicht 
		$LastRow = $optionsSQL->LastRow; // Wenn $RowLook an dann wie viele Bilder sollen in letzter Spalte gezeigt werden
		$PicsPerSite = $optionsSQL->PicsPerSite; // Wie viele Bilder sollen insgesamt  gezeigt werden --- UPDATE: Wird bereits weiter oben oder bei get-data-1.php ermittelt
		$PicsInRow = $optionsSQL->PicsInRow; // Wie viele Bilder in einer Reiehe sollen gezeigt werden 
		$WidthGalery = $optionsSQL->WidthGallery;
		$HeightGalery = $optionsSQL->HeightGallery;
		$DistancePics = $optionsSQL->DistancePics;
		$DistancePicsOriginal = $optionsSQL->DistancePics;
		$DistancePicsV = $optionsSQL->DistancePicsV;
		$DistancePicsVwithoutPX = $optionsSQL->DistancePicsV;
		$OnlyGalleryView = $optionsSQL->OnlyGalleryView;
		$AllowComments = $optionsSQL->AllowComments;
		$AllowRating = $optionsSQL->AllowRating;
		$VotesPerUser = $optionsSQL->VotesPerUser;
		$AllowSort = $optionsSQL->AllowSort;
		$RandomSort = $optionsSQL->RandomSort;
		$ShowAlways = $optionsSQL->ShowAlways;
		$ShowAlwaysInfoSlider = $optionsSQL->ShowAlwaysInfoSlider;
		@$ScalePics = $optionsSQL->ScalePics;
		$ScaleAndCut = $optionsSQL->ScaleAndCut;
		$ThumbsInRow = $optionsSQL->ThumbsInRow; // Anzahl der Bilder in einer Reihe bei Auswahl von ReihenAnsicht (Semi-Flickr-Ansicht)
		$ThumbLook = $optionsSQL->ThumbLook;
		$AdjustThumbLook = $optionsSQL->AdjustThumbLook;
		$RowLook = $optionsSQL->RowLook;
		$HeightLook = $optionsSQL->HeightLook;
		$WidthThumb = $optionsSQL->WidthThumb; // Breite der ThumbBilder (Normale Ansicht mit Bewertung etc.) 
		$HeightThumb = $optionsSQL->HeightThumb;  // H�he der ThumbBilder (Normale Ansicht mit Bewertung etc.)
		$LastRow = $optionsSQL->LastRow;
		$FullSize = $optionsSQL->FullSize;
		$IpBlock = $optionsSQL->IpBlock;
		@$CheckLogin = $optionsSQL->CheckLogin;
		$AllowGalleryScript = $optionsSQL->AllowGalleryScript;
		$InfiniteScroll = $optionsSQL->InfiniteScroll;
		$FullSizeImageOutGallery = $optionsSQL->FullSizeImageOutGallery;
		$FullSizeImageOutGalleryNewTab = $optionsSQL->FullSizeImageOutGalleryNewTab;
		$CommentsOutGallery = $optionsSQL->CommentsOutGallery;
		$ForwardToURL = $optionsSQL->ForwardToURL;
		$ForwardFrom = $optionsSQL->ForwardFrom;
		$ForwardType = $optionsSQL->ForwardType;
		$RatingOutGallery = $optionsSQL->RatingOutGallery;
		
		
		//echo $ForwardFrom;
		
		// Notwendig um sp�ter die star Icons anzuzeigen
		$iconsURL = content_url().'/plugins/contest-gallery/css';

		if(@$CheckLogin==1 and ($AllowRating==1 or $AllowRating==2)){
			if(is_user_logged_in()){@$UserLoginCheck = 1;} // Allow only registered users to vote (Wordpress profile) wird dadurch aktiviert			
			else{@$UserLoginCheck=0;}//Allow only registered users to vote (Wordpress profile): wird dadurch deaktiviert
		}
		else{@$UserLoginCheck=0;}
		
		//echo $AllowGalleryScript;
		
		// Einmaliger Wert, �hnlich dem Session Wert. �hnlich wie User-Id. Wird von Seite zur Seite bei Wordpress weitergegeben.
		if($AllowComments==1){$check = wp_create_nonce("check");}

	
		
		@$FbLike = $optionsSQL->FbLike;
		@$FbLikeGallery = $optionsSQL->FbLikeGallery;
		@$FbLikeGalleryVote = $optionsSQL->FbLikeGalleryVote;
		
		
		$showFbLikeInGalleryStyle = ($FbLikeGallery==1) ? "display:block" : "display:none";
		$showFbLikeInGalleryStyleVoteOrNot = ($FbLikeGalleryVote==1) ? "" : "pointer-events: none;";
	
		
		$ScaleOnly = $optionsSQL->ScaleOnly;
		@$JqgGalery = $optionsSQL->JqgGalery;
		$HeightLookHeight= $optionsSQL->HeightLookHeight;
		@$HideUntilVote= $optionsSQL->HideUntilVote;
		@$ShowOnlyUsersVotes= $optionsSQL->ShowOnlyUsersVotes;
		@$HideInfo = $optionsSQL->HideInfo;
		@$ContestEnd= $optionsSQL->ContestEnd;
		@$ContestEndTime= $optionsSQL->ContestEndTime;
		
		$WidthGaleryWithoutPx = $optionsSQL->WidthGallery; 
		$WidthThumbWithoutPx = $optionsSQL->WidthThumb; // Breite der ThumbBilder (Normale Ansicht mit Bewertung etc.)
		$HeightThumbWithoutPx = $optionsSQL->HeightThumb;
		
		$WidthThumbPx = $WidthThumb.'px';// Breite Thumb mit px f�r Heredoc
		$HeightThumbPx = $HeightThumb.'px';// H�he Thumb mit px f�r Heredoc
		$DistancePicsPx = $DistancePics.'px'; // Abstand der Thumbs Breite mit px f�r Heredoc
		$DistancePicsOriginalPx = $DistancePicsOriginal.'px'; // Abstand der Thumbs Breite mit px f�r Heredoc
		$DistancePicsVtopBlock = $DistancePicsV+12;
		$DistancePicsVtopBlock = $DistancePicsVtopBlock."px";
		$DistancePicsV = $DistancePicsV.'px'; // Abstand der Thumbs H�he mit px f�r Heredoc
		
		//echo "<br>IpBlock: $IpBlock <br>";
		
		//F�r Facebook Iframe
		$urlSource = site_url();


		
		
		@$IPcheck = @$HideUntilVote;

		
		
    	// Ermitteln der Options-Daten ---------------- ENDE
		
	
	
	// 1. Look
	// 2. Order
	// 3. Start	
	// 4. Step
	// 5. Count (Reihenfolge des Bildes in der Anordnung)	
		
	
	
	if (!@$_GET['picture_id']  OR $AllowGalleryScript == 1) {

	
	// Reihenfolge des Galerien wird ermittelt
	$selectGalleryLookOrder = $wpdb->get_results( "SELECT ThumbLookOrder, HeightLookOrder, RowLookOrder  FROM $tablenameOptions WHERE id = '$galeryID'" );
		
	// Reihenfolge der Gallerien wird ermittelt

	$orderGalleries = array();

		foreach($selectGalleryLookOrder[0] as $key => $value){

			$orderGalleries[$value]=$key;

		}

	ksort($orderGalleries);
	

	//$arrowPngRight = content_url().'/plugins/contest-gallery/css/arrow_right.png';
	$arrowPngRight = plugins_url( 'css/arrow_right.png', __FILE__ );

	    $cssFolderUrl = plugins_url( '/../css/', __FILE__ );

	    //echo $cssFolderUrl;

		//$selected_look_thumb = content_url().'/plugins/contest-gallery/css/thumb-cg-view-off.jpg';
		$selected_look_thumb = plugins_url( '/../css/thumb-cg-view-off.jpg', __FILE__ );
		//$selected_look_height = content_url().'/plugins/contest-gallery/css/height-cg-view-off.jpg';
		$selected_look_height = plugins_url( '/../css/height-cg-view-off.jpg', __FILE__ );
		//$selected_look_row = content_url().'/plugins/contest-gallery/css/row-cg-view-off.jpg';
		$selected_look_row = plugins_url( '/../css/row-cg-view-off.jpg', __FILE__ );
	
		foreach($orderGalleries as $key => $value){
		
			//if($value=='ThumbLookOrder' AND $ThumbLook==1){$configuredLook = 'thumb'; $getLook = 1; $selected_look_thumb = content_url().'/plugins/contest-gallery/css/thumb-cg-view-on.jpg'; break;}
			if($value=='ThumbLookOrder' AND $ThumbLook==1){$configuredLook = 'thumb'; $getLook = 1; $selected_look_thumb = plugins_url( '/../css/thumb-cg-view-on.jpg', __FILE__ ); break;}
			//if($value=='HeightLookOrder' AND $HeightLook==1){$configuredLook = 'height'; $getLook = 2; $selected_look_height = content_url().'/plugins/contest-gallery/css/height-cg-view-on.jpg'; break;}
			if($value=='HeightLookOrder' AND $HeightLook==1){$configuredLook = 'height'; $getLook = 2; $selected_look_height = plugins_url( '/../css/height-cg-view-on.jpg', __FILE__ ); break;}
			//if($value=='RowLookOrder'  AND $RowLook==1){$configuredLook = 'row'; $getLook = 3; $selected_look_row = content_url().'/plugins/contest-gallery/css/row-cg-view-on.jpg'; break;}
			if($value=='RowLookOrder'  AND $RowLook==1){$configuredLook = 'row'; $getLook = 3; $selected_look_row = plugins_url( '/../css/row-cg-view-on.jpg', __FILE__ ); break;}
		
		
		}

	
		//1. Look:
		if((@$_GET['1']==1 AND $ThumbLook==1) or (@$_GET['1']==2  AND $HeightLook==1) or (@$_GET['1']==3  AND $RowLook==1)){
			if(@$_GET['1']==1){$look = 'thumb'; $getLook = 1;
			// 	$selected_look_thumb = content_url().'/plugins/contest-gallery/css/thumb-cg-view-on.jpg';
			$selected_look_thumb = plugins_url( '/../css/thumb-cg-view-on.jpg', __FILE__ );
			// 	$selected_look_height = content_url().'/plugins/contest-gallery/css/height-cg-view-off.jpg';
			$selected_look_height = plugins_url( '/../css/height-cg-view-off.jpg', __FILE__ );
			// 	$selected_look_row = content_url().'/plugins/contest-gallery/css/row-cg-view-off.jpg';
			$selected_look_row = plugins_url( '/../css/row-cg-view-off.jpg', __FILE__ );
			}
			if(@$_GET['1']==2){$look = 'height'; $getLook = 2; 
			// 	$selected_look_thumb = content_url().'/plugins/contest-gallery/css/thumb-cg-view-off.jpg';
			$selected_look_thumb = plugins_url( '/../css/thumb-cg-view-off.jpg', __FILE__ );
			// 	$selected_look_height = content_url().'/plugins/contest-gallery/css/height-cg-view-on.jpg';
			$selected_look_height = plugins_url( '/../css/height-cg-view-on.jpg', __FILE__ );
			// 	$selected_look_row = content_url().'/plugins/contest-gallery/css/row-cg-view-off.jpg';
			$selected_look_row = plugins_url( '/../css/row-cg-view-off.jpg', __FILE__ );
			}
			if(@$_GET['1']==3){$look = 'row'; $getLook = 3; 
			// 	$selected_look_thumb = content_url().'/plugins/contest-gallery/css/thumb-cg-view-off.jpg';
			$selected_look_thumb = plugins_url( '/../css/thumb-cg-view-off.jpg', __FILE__ );
			// 	$selected_look_height = content_url().'/plugins/contest-gallery/css/height-cg-view-off.jpg';
			$selected_look_height = plugins_url( '/../css/height-cg-view-off.jpg', __FILE__ );
			// 	$selected_look_row = content_url().'/plugins/contest-gallery/css/row-cg-view-on.jpg';
			$selected_look_row = plugins_url( '/../css/row-cg-view-on.jpg', __FILE__ );
			}
		}
		else{$look = $configuredLook; } // GetLook wurde oben ermittelt

		
						// 2. Order (wird bei formularen �bergeben und bei bild auswahl) (1=date-desc, 2=date-asc, 3=rating-desc, 4=rating-asc, 5=comment-desc, 6=comment-asc)

			
		if($AllowSort==1 AND (@$_GET['2']==1 or @$_GET['2']==2 or @$_GET['2']==3 or @$_GET['2']==4 or @$_GET['2']==5 or @$_GET['2']==6 or @$_GET['2']==7)){	
			if(@$_GET['2']=='1'){ $order='date-desc';  $getOrder = 1; }
			else if(@$_GET['2']=='2'){ $order='date-asc';  $getOrder = 2;}
			else if(@$_GET['2']=='3'){ $order='comment-desc';  $getOrder = 3;}
			else if(@$_GET['2']=='4'){ $order='comment-asc';  $getOrder = 4;}
			else if(@$_GET['2']=='5'){$order='rating-desc';  $getOrder = 5;}
			else if(@$_GET['2']=='6'){$order='rating-asc';  $getOrder = 6;}
			else if(@$_GET['2']=='7'){$order='random-sort';  $getOrder = 7;}
		}
		else{
			if($RandomSort == 1){
				 $order = 'random-sort';
				 $getOrder=7;
			}			
			else{
				$order = 'date-desc'; 
				$getOrder = 1;
			}			
		}
	
	
	
	// Ermittelt die gesendeten Daten

		
		// Sollte irgendwie der Wert Null gespeichert worden sein. Wird der Wert auf 10 gesetzt und in die Datenbank gespeichert!
		if($PicsPerSite == 0){
			
			$PicsPerSite=10;
			
				$wpdb->update( 
				"$tablenameOptions",
				array('PicsPerSite' => $PicsPerSite),
				array('id' => $galeryID), 
				array('%d'),
				array('%d')
			);
			
		}
		
		
		if(@$_GET['3'] % $PicsPerSite == 0 or @$_GET['3']==0){
			$start = (@$_GET['3'])  ? @$_GET['3'] : 0;
			$getStart = (@$_GET['3'])  ? @$_GET['3'] : 0;
			//$step = (@$_GET['4']) ? @$_GET['4'] : $PicsPerSite;
		}
		else{
			$start = 0;
			$getStart = 0;
			}
			
			//echo "<br>$InfiniteScroll</br>";
			
		if($InfiniteScroll==1 or $RandomSort==1){
			
			$step = 500;
			$getStep = 500;
			
		}	
		else{	
		$step = $PicsPerSite;
		$getStep = $PicsPerSite;
		}
		
		

	
		// Es wird ermittelt wie viele Bilder auf einmal auf einer Seite dargestellt werden
		$count_pics = $wpdb->get_var( "SELECT COUNT(*) AS NumberOfRows FROM $tablename WHERE GalleryID='$galeryID' AND Active='1' ");
		
		// Wenn weniger als step dann wird die ermittelte zahl genommen. Wenn mehr dann wird step genommen.
		$count = ($count_pics < $PicsPerSite or $count_pics < $step) ? $count_pics : $step;
			
	

		$permalinkURL = get_permalink();
		

		$checkPermalinkURL = explode('?',$permalinkURL);
		

		if(@$checkPermalinkURL[1]){
			$siteURL = $checkPermalinkURL[0]."?".$checkPermalinkURL[1];
			

			$pageIDname = explode('=',$checkPermalinkURL[1]);
			

			$pageIDvalue = explode('&',$pageIDname[1]);


			$siteURLsort = $checkPermalinkURL[0];			
			}
		else{@$siteURL = @$permalinkURL."?";@$siteURLsort = @$permalinkURL;}

// generate SiteURL --- ENDE


		
		
		// 2. Reihenfolge
		
		// wird bei formularen �bergeben und bei bild auswahl
		$sendOrder = $order;
		
		$selected_date_desc = '';
		$selected_date_asc = '';
		$selected_comment_desc = '';
		$selected_comment_asc = '';
		$selected_rating_desc = '';
		$selected_rating_asc = '';
		$selected_random_sort = '';
		


			if($order=='date-desc' ){$order='desc'; $getOrder=1; $sendOrder='date-desc'; $selected_date_desc = 'selected';}
			else if($order=='date-asc' ){$order='asc'; $getOrder=2; $sendOrder='date-asc'; $selected_date_asc = 'selected';}
			else if($order=='comment-desc' ){$order='desc'; $getOrder=3; $sendOrder='comment-desc'; $selected_comment_desc = 'selected';}
			else if($order=='comment-asc' ){$order='asc'; $getOrder=4; $sendOrder='comment-asc'; $selected_comment_asc = 'selected';}
			else if($order=='rating-asc' ){$order='asc';$getOrder=6; $sendOrder='rating-asc'; $selected_rating_asc = 'selected';}
			else if($order=='rating-desc' ){$order='desc';$getOrder=5; $sendOrder='rating-desc'; $selected_rating_desc = 'selected';}
			else if($order=='random-sort' ){$order='desc';$getOrder=7; $sendOrder='random-sort'; $selected_random_sort = 'selected';}
		

		

	//2. Start:
	
	$getStart = $start;
	
	// 3. Step
	$getStep = $step;
	

		
	//5.Count >>> siehe Gallerieverarbeitung

	
	// Pr�fen ob Form Feld geladen werden soll und pr�fen Visual Options
	
	
	
	@$visualOptionsSQL = $wpdb->get_row( "SELECT * FROM $tablename_options_visual WHERE GalleryID='$galeryID'");
	
	
	@$Field1IdGalleryView = @$visualOptionsSQL -> Field1IdGalleryView;
	
	if(@$Field1IdGalleryView){
	
	@$inputFieldSQL = $wpdb->get_row( "SELECT * FROM $tablename_f_input WHERE id='$Field1IdGalleryView'");
	@$inputFieldContentID = $inputFieldSQL->Field_Type;
	}
	
	//echo $inputFieldContentID;
	
	// NEU! Auswahl des Contents falls Gallerie Slider an ist und Eintragungen vorgenommen wurden
	
	if($AllowGalleryScript==1){
		
		
		// Pr�fung ob bestimmte Felder im Slider zu sehen sein sollten. Erstmal pr�fen ob AllowGalleryScript (Slider) aktiviert ist.
				@$selectFormInput = $wpdb->get_results( "SELECT id, Show_Slider FROM $tablename_f_input WHERE GalleryID = '$galeryID' AND Show_Slider = '1' ");
	

		if(@$selectFormInput){
	
				$f_input_id_collection = "";
				
					foreach (@$selectFormInput as $value) {

					$f_input_id	= $value->id;
					
					$f_input_id_collection .= "id = $f_input_id or ";	
					
					}
				//	print_r($f_input_id_collection);
					@$f_input_id_collection = substr($f_input_id_collection,0,-4);

				
						$selectFormInput = $wpdb->get_results( "SELECT id, Field_Type, Field_Order, Field_Content FROM $tablename_f_input WHERE GalleryID = '$galeryID' AND ($f_input_id_collection) AND (Field_Type = 'text-f' OR Field_Type = 'comment-f' OR Field_Type ='email-f' OR Field_Type ='select-f') ORDER BY Field_Order ASC" );

						$selectContentFieldArray = array();
						
						
						
						foreach ($selectFormInput as $value) {
						
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
						
		}				
	
	}

		// Ermitteln visual Options
		
				
		$ThumbViewBorderWidth = $visualOptionsSQL->ThumbViewBorderWidth;
		$ThumbViewBorderRadius = $visualOptionsSQL->ThumbViewBorderRadius;
		$ThumbViewBorderColor = $visualOptionsSQL->ThumbViewBorderColor;
		$ThumbViewBorderOpacity = $visualOptionsSQL->ThumbViewBorderOpacity;
		$HeightViewBorderWidth = $visualOptionsSQL->HeightViewBorderWidth;
		$HeightViewBorderRadius = $visualOptionsSQL->HeightViewBorderRadius;
		$HeightViewSpaceWidth = $visualOptionsSQL->HeightViewSpaceWidth;
		$HeightViewSpaceHeight = $visualOptionsSQL->HeightViewSpaceHeight;
		$HeightViewBorderColor = $visualOptionsSQL->HeightViewBorderColor;
		$HeightViewBorderOpacity = $visualOptionsSQL->HeightViewBorderOpacity;
		$RowViewBorderWidth = $visualOptionsSQL->RowViewBorderWidth;
		$RowViewBorderRadius = $visualOptionsSQL->RowViewBorderRadius;
		$RowViewSpaceWidth = $visualOptionsSQL->RowViewSpaceWidth;
		$RowViewSpaceHeight = $visualOptionsSQL->RowViewSpaceHeight;
		$RowViewBorderColor = $visualOptionsSQL->RowViewBorderColor;
		$RowViewBorderOpacity = $visualOptionsSQL->RowViewBorderOpacity;
		$TitlePositionGallery = $visualOptionsSQL->TitlePositionGallery;
		$RatingPositionGallery = $visualOptionsSQL->RatingPositionGallery;
		$CommentPositionGallery = $visualOptionsSQL->CommentPositionGallery;
		$ActivateGalleryBackgroundColor = $visualOptionsSQL->ActivateGalleryBackgroundColor;
		$GalleryBackgroundColor = $visualOptionsSQL->GalleryBackgroundColor;
		$GalleryBackgroundOpacity = $visualOptionsSQL->GalleryBackgroundOpacity;
        $OriginalSourceLinkInSlider = $visualOptionsSQL->OriginalSourceLinkInSlider;
        $PreviewInSlider = $visualOptionsSQL->PreviewInSlider;

				
		// Ermitteln visual Options --- ENDE
	
	
	
	$cg_slider_button = plugins_url( '/../css/close_button.png', __FILE__ );
	
	
	//Pfeile f�r Slider URLs
	
	$cg_slider_arrow_left = plugins_url( '/../css/cg_slider_arrow_left_mobile.png', __FILE__ );
	$cg_slider_arrow_right = plugins_url( '/../css/cg_slider_arrow_right_mobile.png', __FILE__ );
	$cg_slider_arrow_fade_in_user_input = plugins_url( '/../css/arrow_fade_in_user_input.png', __FILE__ );
	$cg_slider_arrow_fade_out_user_input = plugins_url( '/../css/arrow_fade_out_user_input.png', __FILE__ );
	$cg_slider_arrow_fade_out_user_input = plugins_url( '/../css/arrow_fade_out_user_input.png', __FILE__ );
	$cg_slider_full_size_view_icon = plugins_url( '/../css/slider_full_size_view_icon.png', __FILE__ );
	$cg_slider_exit_full_size_view_icon = plugins_url( '/../css/slider_exit_full_size_view_icon.png', __FILE__ );
	$cg_slider_download_full_size_icon = plugins_url( '/../css/slider_download_full_size_icon.png', __FILE__ );

	//Star on star off f�r Slider
	
	$cg_star_on_slider = plugins_url( '/../css/star_48.png', __FILE__ );
	$cg_star_off_slider = plugins_url( '/../css/star_off_48.png', __FILE__ );
	
	$cg_star_on_gallery = plugins_url( '/../css/star_48_reduced.png', __FILE__ );
	$cg_star_off_gallery = plugins_url( '/../css/star_off_48_reduced.png', __FILE__ );	
	
	
	// Auswahl aller aktviierten IDs
	
		$pics_sql_select_all_acitvated_ids = $wpdb->get_results( "SELECT id FROM $tablename WHERE GalleryID='$galeryID' AND Active='1' ORDER BY rowid $order");
		

	// Auswahl aller aktviierten IDs --- ENDE
	


	}

		// Bestimmung der Daten des weitergeleitete Arrays bei show-gallery.php ---------------- ENDE


	// Bestimmung von Daten bei aufrufen von show-image
	
	// 1. Look
	// 2. Reihenfolge
	// 3. Start	
	// 4. Step
	// 5. Count (Reihenfolge des Bildes in der Anordnung)
	
	else if (@$_GET['picture_id'] AND  $AllowGalleryScript != 1) {

	
	// Sammeln von Daten von Get zur Weitergabe

			
		//1. Look:
		if((@$_GET['1']==1 AND $ThumbLook==1) or (@$_GET['1']==2  AND $HeightLook==1) or (@$_GET['1']==3  AND $RowLook==1)){
			if(@$_GET['1']==1){$look = 'thumb'; $getLook = 1;}
			else if(@$_GET['1']==2){$look = 'height'; $getLook = 2;}
			else if(@$_GET['1']==3){$look = 'row'; $getLook = 3;}
			
		}
		else{$look = 'thumb'; $getLook = 1;}
		
						// 2. Order (wird bei formularen �bergeben und bei bild auswahl) (1=date-desc, 2=date-asc, 3=rating-desc, 4=rating-asc, 5=comment-desc, 6=comment-asc)
			//$sendOrder = (@$_GET['1']) ? @$_GET['1'] : "date-desc";	

		// 	echo @$_GET['2'];
		
		if($RandomSort == 1 && @$_GET['2']==7){
			 $order = 'desc';
			 $getOrder=7;
			 $sendOrder="random-sort";
		}
			
		else if($AllowSort==1 AND (@$_GET['2']==1 or @$_GET['2']==2 or @$_GET['2']==3 or @$_GET['2']==4 or @$_GET['2']==5 or @$_GET['2']==6 or @$_GET['2']==7)){		
			if(@$_GET['2']==1){$order='desc'; $sendOrder='date-desc'; $getOrder = 1;}
			else if(@$_GET['2']==2){$order='asc'; $sendOrder='date-asc'; $getOrder = 2;}
			else if(@$_GET['2']==3){$order='desc'; $sendOrder='comment-desc'; $getOrder = 3;}
			else if(@$_GET['2']==4){$order='asc'; $sendOrder='comment-asc'; $getOrder = 4;}
			else if(@$_GET['2']==5){$order='desc'; $sendOrder='rating-desc'; $getOrder = 5;}
			else if(@$_GET['2']==6){$order='asc'; $sendOrder='rating-asc'; $getOrder = 6;}
			
		}
		
		else{
			
			if($RandomSort == 1){
				 $order = 'desc';
				 $getOrder=7;
				 $sendOrder="random-sort";
			}			
			else{
				$order = 'desc'; 
				$sendOrder='date-desc'; 
				$getOrder = 1;
			}
			
		}	
			
			
		
		//echo "<br>getOrder: $getOrder<br>";
		
			
		if((@$_GET['3'] % $PicsPerSite == 0 or @$_GET['3']==0) && $RandomSort!=1){
			$start = (@$_GET['3'])  ? @$_GET['3'] : 0;
			$getStart = (@$_GET['3'])  ? @$_GET['3'] : 0;
			//$step = (@$_GET['4']) ? @$_GET['4'] : $PicsPerSite;
		}
		else{
			$start = 0;
			$getStart = 0;
			}
			
		//	echo "<br>PicsPerSite: $PicsPerSite<br>";
		
		
		//
		
		if($InfiniteScroll!=0){
			
		$step = 500;
		$getStep = 500;
			
		}	
		else{	
		$step = $PicsPerSite;
		$getStep = $PicsPerSite;
		}	
	
	$sendPictureID = @$_GET['picture_id'];
	
	// ID entschl�sseln
	$pictureID = (@$_GET['picture_id']-100000)/2-8;

		
// Wenn Form Output definiert ist, dann wird die Reihenfolge der Text-Bild-Anordnung ermittelt

		//$getSQL = new sql_selects($tablename_f_output,$galeryID,$order,$start,$step,$pictureID);
		//$selectFormOutput = $getSQL->select_f_output();
		
		$selectFormOutput = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID='$galeryID' ORDER BY Field_Order ASC");		
		$countSelectFormOutput = count($selectFormOutput);		


// Wenn Form Output definiert ist, dann wird die Reihenfolge der Text-Bild-Anordnung ermittelt --- ENDE	
	
	// Ermittelt wie viele aktive Bilder in der Galerie noch vor dem aktuellen Bild sind
		//$getSQL = new sql_selects($tablename,$galeryID,$order,$start,$step,$pictureID);
		//$picsBeforeStart = $getSQL->pics_before_start();
		
		$picsBeforeStart = $wpdb->get_var( $wpdb->prepare( 
			"
				SELECT COUNT(1)
				FROM $tablename 
				WHERE rowid = %d AND Active = %d
			", 
			$pictureID,1
		) );

		if ($picsBeforeStart < $PicsPerSite) {$start=0;}
		else{$start=floor($picsBeforeStart/$PicsPerSite*$PicsPerSite);}	
		
	}
	
	
	// Bestimmung von Daten bei aufrufen von show-image  --- ENDE

		
		
		// check IP if hide images before rated look

			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$userIP = $_SERVER['HTTP_CLIENT_IP'];

			} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else{
				$userIP = $_SERVER['REMOTE_ADDR'];
			}


		// check IP if hide images before rated look --- END
		
		
		
		// Reihenfolge des Galerien wird ermittelt
		$selectGalleryLookOrder = $wpdb->get_results( "SELECT ThumbLookOrder, HeightLookOrder, RowLookOrder  FROM $tablenameOptions WHERE id = '$galeryID'" );
		
		// Reihenfolge der Gallerien wird ermittelt

$orderGalleries = array();

if(@$selectGalleryLookOrder){
	foreach($selectGalleryLookOrder[0] as $key => $value){

		@$orderGalleries[$value]=$key;

	}
	}

ksort($orderGalleries);

// Reihenfolge der Gallerien wird ermittelt --- ENDE
	
		
		
		if (!@$_GET['picture_id'] OR  $AllowGalleryScript == 1) {
		
			//@$getSQL = new sql_selects($tablename,$galeryID,$order,$start,$step,$pictureID);
			
			if($RandomSort == 1 && $sendOrder =='random-sort'){
				
				$picsSQL = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID='$galeryID' AND Active='1' ORDER BY rand()");
				
			}
			
			else if($sendOrder =='date-desc' or $sendOrder=='date-asc' or $sendOrder=='desc'){
			
				//$picsSQL = $getSQL->pics_sql_date_order();
				$picsSQL = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID='$galeryID' AND Active='1' ORDER BY rowid $order LIMIT $start, $step ");

			}
			
			else if($sendOrder=='rating-desc' or $sendOrder=='rating-asc'){
					
					if($AllowRating==1){
						$picsSQL = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID='$galeryID' AND Active='1' ORDER BY CountR $order, rowid $order LIMIT $start, $step ");
					}
					if($AllowRating==2){
						$picsSQL = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID='$galeryID' AND Active='1' ORDER BY CountS $order, rowid $order LIMIT $start, $step ");
					}
			
			}

			else if($sendOrder=='comment-desc' or $sendOrder=='comment-asc'){
				$picsSQL = $wpdb->get_results( "SELECT * FROM $tablename WHERE GalleryID='$galeryID' AND Active='1' ORDER BY CountC $order, rowid $order LIMIT $start, $step ");

			}	

			else{
				
			}

			
			$rows = $count_pics;	
			
		}
		


		// Ermitteln aller Bilder die angezeigt werden sollen und die Anzahl der Bilder die angezeigt werden k�nnen --- ENDE


	
	
	
	
	// Ermitteln weiter FormularDaten  --- ENDE
	
	
			// Ermitteln ob checked oder nicht ---- ENDE		




		// Ermitteln der Tabellendaten
	
		// --------------- Ermitteln der Image Daten (Show-Image)
		
		if(@$_GET['picture_id']  AND $AllowGalleryScript != 1){
		
			// Einmaliger Wert, �hnlich dem Session Wert. �hnlich wie User-Id. Wird von Seite zur Seite bei Wordpress weitergegeben.
			if($AllowComments==1){$check = wp_create_nonce("check");}
		
			// Objekt � Erzeugung 

			$picSQL = $wpdb->get_results( "SELECT * FROM $tablename WHERE id='$pictureID' and Active = '1'" );

			if(!empty($picSQL)){

                foreach($picSQL as $value){

                    $id = $value->id;
                    $rowid = $value->rowid;
                    $Timestamp = $value->Timestamp.'_';
                    $NamePic = $value->NamePic;
                    $ImgType = $value->ImgType;
                    $rating = $value->Rating;
                    $countR = $value->CountR;
                    $countS = $value->CountS;
                    $widthOriginalImg = $value->Width;
                    $heightOriginalImg = $value->Height;
                    $WpUpload = $value->WpUpload;

                    $countR = ($countR) ? $countR : 0;

                    $cg_uploadFolder_dir = wp_upload_dir();
                    $cg_upload_dir = $cg_uploadFolder_dir['basedir'].'/contest-gallery/gallery-id-'.$galeryID.'';

                    if(file_exists("$cg_upload_dir/".$Timestamp."".$NamePic."413.html")){
                        $urlForFacebook=$urlSource.'/wp-content/uploads/contest-gallery/gallery-id-'.$galeryID.'/'.$Timestamp.$NamePic."413.html";
                    }
                    else{
                        echo '<input type="hidden" id="cg_fb_reload'.$realId.'" value="1">';

                    }


                }

            }
			


			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$userIP = $_SERVER['HTTP_CLIENT_IP'];

			} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			else{
				$userIP = $_SERVER['REMOTE_ADDR'];
			}


					// 4 Varianten hier m�glich wenn andere Einstellungen getroffen wurden als standard					
					
					// Pr�fen f�r Rating mit einem Stern und nicht eingeloggtem User
					if($ShowOnlyUsersVotes==1 && $CheckLogin!=1 && $AllowRating==2 || $HideUntilVote==1 && $CheckLogin!=1 && $AllowRating==2){
						
						//Achtung! Reihenfolge wichtig! $countShideUntilVote muss in diesem Fall den Gesamtwert zugewiesen bekommen
						if($HideUntilVote==1 && $ShowOnlyUsersVotes!=1){
							$countShideUntilVote = $countS;
						}

							$countS = $wpdb->get_var( $wpdb->prepare(
							"
								SELECT COUNT(*) AS NumberOfRows
								FROM $tablenameIP 
								WHERE GalleryID = %d and IP = %s and RatingS = %d and pid = %d
							", 
							$galeryID,$userIP,1,$id
							) );
							
							//echo $countS;
						
						if($HideUntilVote==1 && $ShowOnlyUsersVotes==1){
							$countShideUntilVote = $countS;
						}							
					
					}		
					
					// Pr�fen f�r Rating mit einem Stern und eingeloggtem User
					if($ShowOnlyUsersVotes==1 && $CheckLogin==1 && $AllowRating==2 || $HideUntilVote==1 && $CheckLogin==1 && $AllowRating==2){
						
						if(is_user_logged_in()){
							
							//Achtung! Reihenfolge wichtig! $countShideUntilVote muss in diesem Fall den Gesamtwert zugewiesen bekommen
							if($HideUntilVote==1 && $ShowOnlyUsersVotes!=1){
								$countShideUntilVote = $countS;
							}
							
							$countS = $wpdb->get_var( $wpdb->prepare(
							"
								SELECT COUNT(*) AS NumberOfRows
								FROM $tablenameIP
								WHERE GalleryID = %d and WpUserId = %s and RatingS = %d and pid = %d
							", 
							$galeryID,get_current_user_id(),1,$id
							) );
							
							if($HideUntilVote==1 && $ShowOnlyUsersVotes==1){
								$countShideUntilVote = $countS;
							}
						}
						
						// Extra Condition f�r HideUntilVote
						if($HideUntilVote==1 && !is_user_logged_in()){
							$countS = 0;
						}
						
					}
					
					
					// Pr�fen f�r Rating mit f�nf Sternen und nicht eingeloggtem User. countR und rating ist hier notwendig zu wissen
					if($ShowOnlyUsersVotes==1 && $CheckLogin!=1 && $AllowRating==1 || $HideUntilVote==1 && $CheckLogin!=1 && $AllowRating==1){
						
						//Achtung! Reihenfolge wichtig! $countRhideUntilVote und $ratingHideUntilVote muss in diesem Fall den Gesamtwert zugewiesen bekommen
						if($HideUntilVote==1 && $ShowOnlyUsersVotes!=1){
							$countRhideUntilVote = $countR;
							$ratingHideUntilVote = $rating;
						}
						
						$countR = $wpdb->get_var( $wpdb->prepare(
						"
							SELECT COUNT(*) AS NumberOfRows
							FROM $tablenameIP 
							WHERE GalleryID = %d and IP = %s and Rating >= %d and pid = %d
						", 
						$galeryID,$userIP,1,$id
						) );

						
						$rating = $wpdb->get_var( $wpdb->prepare(
						"
							SELECT SUM(Rating)
							FROM $tablenameIP 
							WHERE GalleryID = %d and IP = %s and Rating >= %d and pid = %d
						", 
						$galeryID,$userIP,1,$id
						) );
						
						if($HideUntilVote==1 && $ShowOnlyUsersVotes==1){
							$countRhideUntilVote = $countR;
							$ratingHideUntilVote = $rating;
						}
					
					}

					// Pr�fen f�r Rating mit f�nf Sternen und nicht eingeloggtem User. countR und rating ist hier notwendig zu wissen --- ENDE
					
					// Pr�fen f�r Rating mit f�nf Sternen und eingeloggtem User. countR und rating ist hier notwendig zu wissen
					
					if($ShowOnlyUsersVotes==1 && $CheckLogin==1 && $AllowRating==1 || $HideUntilVote==1 && $CheckLogin==1 && $AllowRating==1){
						
						if(is_user_logged_in()){
							
							//Achtung! Reihenfolge wichtig! $countRhideUntilVote und $ratingHideUntilVote muss in diesem Fall den Gesamtwert zugewiesen bekommen
							if($HideUntilVote==1 && $ShowOnlyUsersVotes!=1){
								$countRhideUntilVote = $countR;
								$ratingHideUntilVote = $rating;
							}
							
							$countR = $wpdb->get_var( $wpdb->prepare(
							"
								SELECT COUNT(*) AS NumberOfRows
								FROM $tablenameIP
								WHERE GalleryID = %d and WpUserId = %s and Rating >= %d and pid = %d
							", 
							$galeryID,get_current_user_id(),1,$id
							) );
							
							$rating = $wpdb->get_var( $wpdb->prepare(
							"
								SELECT SUM(Rating)
								FROM $tablenameIP
								WHERE GalleryID = %d and WpUserId = %s and Rating >= %d and pid = %d
							", 
							$galeryID,get_current_user_id(),1,$id
							) );
							
							if($HideUntilVote==1 && $ShowOnlyUsersVotes==1){
								$countRhideUntilVote = $countR;
								$ratingHideUntilVote = $rating;
							}
						
						}
						
						// Extra Condition f�r HideUntilVote
						if($HideUntilVote==1 && !is_user_logged_in()){
							$countR = 0;
							$rating = 0;						
						}
					
					}					

					// Pr�fen f�r Rating mit f�nf Sternen und eingeloggtem User. countR und rating ist hier notwendig zu wissen --- ENDE

			
					
			
	$uploads = wp_upload_dir();		
			
	
	if($WpUpload!=0){
		
		$sourceOriginalImgUrl = $wpdb->get_var("SELECT guid FROM $table_posts WHERE ID = '$WpUpload'");
		$check = explode($uploads['baseurl'],$sourceOriginalImgUrl);
		$sourceOriginalImg = $uploads['basedir'].$check[1];
		list($widthOriginalImg, $heightOriginalImg) = getimagesize($sourceOriginalImg); // Breite und H�he von original Image		
		
	}
	//R�ckw�rtskompatiblit�t zu hochgeladenen Bildern aus versionen vor 5.00
	else{
		
		// Feststellen der Quelle des Original Images		
		$sourceOriginalImg = $uploads['basedir'].'/contest-gallery/gallery-id-'.$galeryID.'/'.$Timestamp.$NamePic.'.'.$ImgType; // Pfad zum Bilderordner angeben
		list($widthOriginalImg, $heightOriginalImg) = getimagesize($sourceOriginalImg); // Breite und H�he von original Image		

	}

	

			
	if($ScaleAndCut==0){	
		


	// je nach Breite wird entsprechendes Bild gew�hlt
		if($WidthGalery <= 300) {$imageGalery = $Timestamp.$NamePic."-300width.".$ImgType;}		
		if($WidthGalery > 300 AND $WidthGalery <= 624) {$imageGalery = $Timestamp.$NamePic."-624width.".$ImgType;}		
		if($WidthGalery > 624 AND $WidthGalery <= 1024) {$imageGalery = $Timestamp.$NamePic."-1024width.".$ImgType;}		
		if($WidthGalery > 1024 AND $WidthGalery <= 1600) {$imageGalery = $Timestamp.$NamePic."-1600width.".$ImgType; }
		if($WidthGalery > 1600 AND $WidthGalery <= 1920) {$imageGalery = $Timestamp.$NamePic."-1920.".$ImgType; }
		if($WidthGalery > 1920) {
				if($WpUpload!=0){
					$imageGalery = $sourceOriginalImgUrl;
				}
				else{
					$imageGalery = $Timestamp.$NamePic.".".$ImgType;
				}
		}
		
		//$styleCenterImage = "width='$WidthGalery'";
		
		@$WidthGaleryPic = $WidthGaleryPic.'px';
		
		$WidthGalery = $WidthGalery.'px';
		
		
		
		

		

		
		// Ermittlung der H�he nach Skalierung. Falls unter der eingestellten H�he, dann n�chstgr��eres Bild nehmen.
		@$heightScaledGalery = @$WidthGalery*@$heightOriginalImg/@$widthOriginalImg;
		
		
		$HeightThumbWithoutPx = $heightScaledGalery;
		
		//$styleCenterImageDiv = "style='display:inline;width: $WidthGalery;overflow:hidden;position:relative;'";
		
	}	
		
							// Definition der Variablen, notwendig f�r die Ausgabe
							
	if($ScaleAndCut==1){
	
					// destination of the uploaded original image
					
					if($WpUpload!=0){
							$check = explode($uploads['baseurl'],$sourceOriginalImgUrl);
							$sourceOriginalImg = $uploads['basedir'].$check[1];
					}
					else{
						// Feststellen der Quelle des Original Images		
						$sourceOriginalImg = $uploads['basedir'].'/contest-gallery/gallery-id-'.$galeryID.'/'.$Timestamp.$NamePic.'.'.$ImgType; // Pfad zum Bilderordner angeben						
					}


					

					list($widthOriginalImg, $heightOriginalImg) = getimagesize($sourceOriginalImg); // Breite und H�he von original Image
					
					// Ermittlung der H�he nach Skalierung. Falls unter der eingestellten H�he, dann n�chstgr��eres Bild nehmen.
					$heightScaledGalery = $WidthGalery*$heightOriginalImg/$widthOriginalImg;
					
					
					// Falls unter der eingestellten H�he, dann gr��eres Bild nehmen (normales Bild oder panorama Bild, kein Vertikalbild)
					
					if ($heightScaledGalery <= $HeightGalery) {
					
					$widthScaledGalery = $HeightGalery*$widthOriginalImg/$heightOriginalImg;
					
					if($widthScaledGalery <= 300) {$imageGalery = $Timestamp.$NamePic."-300width.".$ImgType;}		
					if($widthScaledGalery > 300 AND $widthScaledGalery <= 624) {$imageGalery = $Timestamp.$NamePic."-624width.".$ImgType;}		
					if($widthScaledGalery > 624 AND $widthScaledGalery <= 1024) {$imageGalery = $Timestamp.$NamePic."-1024width.".$ImgType; }		
					if($widthScaledGalery > 1024 AND $widthScaledGalery <= 1600) {$imageGalery = $Timestamp.$NamePic."-1600width.".$ImgType; }
					if($widthScaledGalery > 1600 AND $widthScaledGalery <= 1920) {$imageGalery = $Timestamp.$NamePic."-1920.".$ImgType; }
					if($widthScaledGalery > 1920) {
							if($WpUpload!=0){
								$imageGalery = $sourceOriginalImgUrl;
							}
							else{
								$imageGalery = $Timestamp.$NamePic.".".$ImgType;
							}
					}
					
					
					// Bestimmung von Breite des Bildes
					$WidthGaleryPic = $HeightGalery*$widthOriginalImg/$heightOriginalImg;
					$WidthGaleryPic = $WidthGaleryPic.'px';
					
					// Bestimmung wie viel links und rechts abgeschnitten werden soll 
					$paddingLeftRight = ($WidthGaleryPic-$WidthGalery)/2;
					$paddingLeftRight = $paddingLeftRight.'px';
					
					$padding = "left: -$paddingLeftRight;right: -$paddingLeftRight";
					
					}
					
					
					// Falls �ber der eingestellten H�he, dann kleineres Bild nehmen (kein Vertikalbild)
					if ($heightScaledGalery > $HeightGalery) {
					if($WidthGalery <= 300) {$imageGalery = $Timestamp.$NamePic."-300width.".$ImgType;}		
					if($WidthGalery > 300 AND $WidthGalery <= 624) {$imageGalery = $Timestamp.$NamePic."-624width.".$ImgType;}		
					if($WidthGalery > 624 AND $WidthGalery <= 1024) {$imageGalery = $Timestamp.$NamePic."-1024width.".$ImgType;}		
					if($WidthGalery > 1024 AND $WidthGalery <= 1600) {$imageGalery = $Timestamp.$NamePic."-1600width.".$ImgType; }
					if($WidthGalery > 1600 AND $WidthGalery <= 1920) {$imageGalery = $Timestamp.$NamePic."-1920.".$ImgType; }
					if($WidthGalery > 1920) {
							if($WpUpload!=0){
								$imageGalery = $sourceOriginalImgUrl;
							}
							else{
								$imageGalery = $Timestamp.$NamePic.".".$ImgType;
							}
					}
					
					// Bestimmung von Breite des Bildes
					$WidthGaleryPic = $WidthGalery.'px';
					
					// Bestimmung wie viel oben und unten abgeschnitten werden soll
					$heightImageGalery = $WidthGalery*$heightOriginalImg/$widthOriginalImg;
					$paddingTopBottom = ($heightImageGalery-$HeightGalery)/2;
					$paddingTopBottom = $paddingTopBottom.'px';
					
					$padding = "top: -$paddingTopBottom;bottom: -$paddingTopBottom";
					
					}	
					
		 // Falls ScaleAndCut aktiviert ist wird der Div Box eingestellte Breite und H�he vergeben
		 
		 $HeightThumbWithoutPx = $heightScaledGalery;
		 
		 $WidthGalery = $WidthGalery.'px';
		 $HeightGalery = $HeightGalery.'px';
		 
		 $styleCenterImageDiv = "style='display:inline;width: $WidthGalery;height: $HeightGalery;overflow:hidden;position:relative;'";
		 
		 $styleCenterImage = "style='z-index:1;$padding;position: relative !important;max-width:none !important;' width='$WidthGaleryPic'";
		 

		 
		 
			}
			
			
		if($WpUpload!=0){
			$fullSizeLink = $sourceOriginalImgUrl;
		}
		else{
			$fullSizeLink = "$uploads/$imageGalery";
		}	
			
		
		}
		
		// Ermitteln der Image Daten (Show-Image) ---------------------- ENDE
		
		
		
	
	// Ermitteln der Tabellendaten --- ENDE
	
	
			// Pfade Ordner
	
		//$uploads = content_url().'/uploads/contest-gallery/gallery-id-'.$galeryID;
		
		
		$uploads = wp_upload_dir();
		@$uploads = @$uploads['baseurl'].'/contest-gallery/gallery-id-'.$galeryID; // Pfad zum Bilderordner angeben
		
		/*
		Chacha, per the PHP documentation: "Set to a non-empty value if the script was queried through the HTTPS protocol."
		 So your if statement there will return false in many cases where HTTPS is indeed on.
		 You'll want to verify that $_SERVER['HTTPS'] exists and is non-empty.
		 In cases where HTTPS is not set correctly for a given server,
		 you can try checking if $_SERVER['SERVER_PORT']  == 443.
		*/
			if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443){				
				if(substr($uploads,0,5)!='https'){
					@$uploads = @str_replace('http', "https", @$uploads );
				}
			}

		//$urlTransparentPic = content_url().'/plugins/contest-gallery/css/transparent.png';
		
			// Pfade Ordner --- ENDE
	
	
	// Abgeleitete Daten, teilweise aus Tabellendaten notwendig zur Darstellung
	
		// Ermitteln Anzahl der Bilder
	
		$JqgGalery1 = ($RowLook==1 OR ($RowLook==0 AND $ThumbLook==0)) ? "style='float:left !important;padding:0 !important;margin:0 !important;border:0 !important;visibility:hidden;cursor: pointer;'" : '';
		
	// Abgeleitete Daten, teilweise aus Tabellendaten notwendig zur Darstellung --- ENDE	
	
			// Arrows source	
				
		// $arrowPngLeft = content_url().'/plugins/contest-gallery/css/arrow_left.png';
		$arrowPngLeft = plugins_url( 'css/arrow_left.png', __FILE__ );
		// $arrowPngRight = content_url().'/plugins/contest-gallery/css/arrow_right.png';
		$arrowPngRight = plugins_url( 'css/arrow_right.png', __FILE__ );

		// Arrows source -- END	
	
	// Die Reihenfolge der Bilder wird bestimmt (show-image.php)
	
		if (@$_GET['order']==true){
			if (@$_GET['order']=='DESC') {$rowImages = 'DESC';}
			if (@$_GET['order']=='ASC') {$rowImages = 'ASC';}
			if (@$_GET['order']=='DB') {	$rowImages = 'DB';}
			if (@$_GET['order']=='DC') {	$rowImages = 'DC';}
		}
		else{$rowImages = 'DESC';}
		
	//  Die Reihenfolge der Bilder wird in show-image.php bestimmt --- ENDE
	

// Pfade Ordner bei show-image.php
	
		if(@$_GET['picture_id'] AND $AllowGalleryScript != 1){

		// Pfade Ordner
	
		/*$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		
		$path = $_SERVER['REQUEST_URI'];
		
		$host = $_SERVER['HTTP_HOST'];
		
		$siteUrlOff = (strpos($path,'?')) ? $host.$path.'&' : $host.$path.'?';
		
		$siteURL = $pageURL.$siteUrlOff;
		
		//echo "<br>SiteUrl1: $siteURL<br>";
		
		// Wenn der zweite Teil des Explodes existiert, dann die URL wieder zur�ck machen
		$siteURL = (strpos($siteURL,'&&')) ? str_replace("&&", "&","$siteURL") : $siteURL;
		
		//echo "<br>SiteUrl2: $siteURL<br>";
		
		$explode = explode('&',$siteURL);
		
		$siteURL = ($explode[2]) ? $explode[0].'&'. $explode[1].'&' : $siteURL;
		
		//echo "<br>SiteUrl3: $siteURL<br>";
		
	//	echo "siteURL: $siteURL";
		
		$originSiteURL = explode('picture_id',$siteURL);
		$originSiteURL = $originSiteURL[0];
		$originSiteURL = substr($originSiteURL, 0, -1);*/
		
		//echo "originSiteURL: $originSiteURL";
		
		
		
// generate SiteURL

		$permalinkURL = get_permalink();
		
		//echo "<br>$permalinkURL<br>";
		
		$checkPermalinkURL = explode('?',$permalinkURL);
		
		//$siteURL = ($checkPermalinkURL) ?  : "?";
		
	// 	echo "<br>checkPermalinkURL:".$checkPermalinkURL[0]; 
	
	// <input type="hidden" name="page_id" value="<?php echo "204"; 
		
		if(@$checkPermalinkURL[1]){
			$siteURL = $checkPermalinkURL[0]."?".$checkPermalinkURL[1];
			
			// echo "<br>";
		// 	echo $checkPermalinkURL[1];
		// 	echo "<br>";
			
			$pageIDname = explode('=',$checkPermalinkURL[1]);
			
		// 	echo "<br>";
		// 	echo $pageIDname[0];
		// 	echo "<br>";
			
			
			$pageIDvalue = explode('&',$pageIDname[1]);
			
			
		// 	echo "<br>";
		// 	echo $pageIDvalue[0];
		// 	echo "<br>";
			
			
			
			
			//$siteURLsort = $checkPermalinkURL[0]."?".$checkPermalinkURL[1];			
			$siteURLsort = $checkPermalinkURL[0];			
			}
		else{$siteURL = $permalinkURL."?";}


// generate SiteURL --- ENDE
	

		} 
		/*
		if(@$_GET['picture_id']){

		// Configuration back to galery link
		$backToGalery = strpos($siteURL,'page_id') ? explode('&',$siteURL) : explode('/?',$siteURL);
		
		
		// Configuration link rating
		$ratingUrl = strpos($siteURL,'&sc=tr') ? str_replace("&sc=tr", "", "$siteURL") : $siteURL;

		}*/
		
			//	print_r($picsSQL);

	
// Pfade Ordner bei show-image.php  --- ENDE

?>