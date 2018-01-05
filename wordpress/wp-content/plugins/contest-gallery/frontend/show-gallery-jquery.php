<!-- <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>-->
<!-- <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>--> 
<?php




if($ActivateGalleryBackgroundColor==1){
	$GalleryBackgroundColorStyle = "background-color:".$GalleryBackgroundColor.";background-opacity:".$GalleryBackgroundOpacity.";padding-top:15px !important;";
}
else{$GalleryBackgroundColorStyle = '';}

	echo "<div id='mainCGdiv' style='padding:0px !important;$GalleryBackgroundColorStyle'>";


// Alle aktivierten IDs werden zur Prüfung hier angezeigt 
	echo "<div id='cg_check_all_acitvated_ids_div' style='display: none !important;'>";
foreach($pics_sql_select_all_acitvated_ids as $value){
	
	$id = $value->id;
	
	//echo "<br>$id<br>";

	echo "<input type='hidden' id='cg_check_all_acitvated_ids$id' value='$id' class='cg_check_all_acitvated_images'>";
	
	
}
echo "</div>";

// Alle aktivierten IDs werden zur Prüfung hier angezeigt --- ENDE 


// Einmaliger Wert, ähnlich dem Session Wert. Ähnlich wie User-Id. Wird von Seite zur Seite bei Wordpress weitergegeben.
if($AllowComments==1){$check = wp_create_nonce("check");}


$loadingGifSource = content_url().'/plugins/contest-gallery/css/loading.gif';
$pngCommentsIcon = content_url().'/plugins/contest-gallery/css/comments_icon.png';
$pngUrlIconImg = content_url().'/plugins/contest-gallery/css/url_icon.png';
$cg_hide_info_png = content_url().'/plugins/contest-gallery/css/hide_info.png';
$cg_show_info_png = content_url().'/plugins/contest-gallery/css/show_info.png';


//$CommentInGallery = 1;
//echo "vote in gallery: ".$RatingOutGallery;

if($RatingOutGallery==1 AND $HideUntilVote==1){

$votedFirstContent = "<u><b>$language_VoteFirst...</b></u>";

}
else{
	
$votedFirstContent = "<u><b>$language_VoteFirst...</b></u>";
	
}


//echo "$AllowRating";


echo "<input type='hidden' id='cg_activate_gallery_slider' value='$AllowGalleryScript'>";
echo "<input type='hidden' id='cg_InfiniteScroll' value='$InfiniteScroll'>";
echo "<input type='hidden' id='cg_vote_in_slider' value=''>";
echo "<input type='hidden' id='cg_show_user_info_in_slider' value='$Field1IdGalleryView'>"; // User Info soll gezeigt werden unbabhängig davon ob eingetragen falls im Backend konfiguriert
echo "<input type='hidden' id='cg_comment_in_gallery' value='$CommentsOutGallery'>";
echo "<input type='hidden' id='cg_user_login_check' value='".@$UserLoginCheck."'>";
echo "<input type='hidden' id='cg_check_login' value='".@$CheckLogin."'>";
echo "<input type='hidden' id='cg_vote_in_gallery' value='$RatingOutGallery'>";
echo "<input type='hidden' id='cg_allow_comments' value='$AllowComments'>";
echo "<input type='hidden' id='cg_allow_rating' value='$AllowRating'>";
echo "<input type='hidden' id='cg_check' value='".@$check."'>";		
echo "<input type='hidden' id='cg_ip_check' value='".$IPcheck."'>";		
echo "<input type='hidden' id='cg_loadingGifSource' value='$loadingGifSource'>";		
echo "<input type='hidden' id='cg_pngCommentsIconImg' value='$pngCommentsIcon'>";		
echo "<input type='hidden' id='cg_pngUrlIconImg' value='$pngUrlIconImg'>";		
echo "<input type='hidden' id='cg_AlreadyRated' value='$language_YouHaveAlreadyVotedThisPicture.'>";
echo "<input type='hidden' id='cg_AllVotesUsed' value='$language_AllVotesUsed.'>";
echo "<input type='hidden' id='cg_OnlyRegisteredUsersCanVote' value='$language_OnlyRegisteredUsersCanVote.'>";
echo "<input type='hidden' id='cg_IpBlock' value='".@$IpBlock."'>";
echo "<input type='hidden' id='cg_galeryID' value='$galeryID'>";
echo "<input type='hidden' id='cg_order' value='$order'>";
echo "<input type='hidden' id='cg_look' value='$look'>";
echo "<input type='hidden' id='cg_start' value='$start'>";
echo "<input type='hidden' id='cg_start' value='$start'>";
echo "<input type='hidden' id='cg_step' value='$step'>";
echo "<input type='hidden' id='cg_siteURL' value='$siteURL'>";
echo "<input type='hidden' id='cg_HeightLookHeight' value='$HeightLookHeight'>";
echo "<input type='hidden' id='cg_PicsInRow' value='$PicsInRow'>";
echo "<input type='hidden' id='cg_PicsPerSite' value='$PicsPerSite'>";
echo "<input type='hidden' id='cg_WidthThumb' value='$WidthThumb'>";
echo "<input type='hidden' id='cg_HeightThumb' value='$HeightThumb'>";
echo "<input type='hidden' id='cg_DistancePicsX' value='$DistancePics'>";
echo "<input type='hidden' id='cg_DistancePicsV' value='$DistancePicsVwithoutPX'>";
echo "<input type='hidden' id='cg_show_gallery' value='1'>";
echo "<input type='hidden' id='cg_last_row' value='$LastRow'>";
echo "<input type='hidden' id='cg_count' value='$count'>";
echo "<input type='hidden' id='cg_ShowAlways' value='$ShowAlways'>";//Show always vote, comments and info in gallery view:
echo "<input type='hidden' id='cg_ShowAlwaysInfoSlider' value='$ShowAlwaysInfoSlider'>";//Show always vote, comments and info in gallery view:
echo "<input type='hidden' id='cg_ThumbViewBorderWidth' value='$ThumbViewBorderWidth'>";
echo "<input type='hidden' id='cg_ThumbViewBorderColor' value='$ThumbViewBorderColor'>";
echo "<input type='hidden' id='cg_ThumbViewBorderOpacity' value='$ThumbViewBorderOpacity'>";
echo "<input type='hidden' id='cg_HeightViewBorderWidth' value='$HeightViewBorderWidth'>";
echo "<input type='hidden' id='cg_HeightViewBorderColor' value='$HeightViewBorderColor'>";
echo "<input type='hidden' id='cg_HeightViewBorderOpacity' value='$HeightViewBorderOpacity'>";
echo "<input type='hidden' id='cg_RowViewBorderWidth' value='$RowViewBorderWidth'>";
echo "<input type='hidden' id='cg_RowViewBorderColor' value='$RowViewBorderColor'>";
echo "<input type='hidden' id='cg_RowViewBorderOpacity' value='$RowViewBorderOpacity'>";
echo "<input type='hidden' id='cg_row_configuration_pics_in_a_row' value='$PicsInRow' >";
echo "<input type='hidden' id='cg_star_on_slider' value='$cg_star_on_slider' >";
echo "<input type='hidden' id='cg_star_off_slider' value='$cg_star_off_slider' >";


echo "<input type='hidden' id='cg_css_folder_url' value='$cssFolderUrl' >";

	//$cg_star_on_gallery = plugins_url( '/../css/star_48_reduced.png', __FILE__ );
	//$cg_star_off_gallery = plugins_url( '/../css/star_off_48_reduced.png', __FILE__ );

echo "<input type='hidden' id='cg_star_on_gallery' value='$cg_star_on_gallery' >";
echo "<input type='hidden' id='cg_star_off_gallery' value='$cg_star_off_gallery' >";
echo "<input type='hidden' id='cg_language_i_am_not_a_robot' value='$language_IamNotArobot' >";
echo "<input type='hidden' id='cg_ThankYouComment' value='$language_ThankYouForYourComment'>";
echo "<input type='hidden' id='cg_plz_vote' value='$language_VoteFirst'>";
echo "<input type='hidden' id='cg_hide_until_vote' value='$HideUntilVote'>";
echo "<input type='hidden' id='cg_hide_info' value='$HideInfo'>";
echo "<input type='hidden' id='cg_real_site_url' value='$siteURLsort'>";
echo "<input type='hidden' id='cg_ContestEndTime' value='".@$ContestEndTime."'>";
echo "<input type='hidden' id='cg_ContestEnd' value='".@$ContestEnd."'>";
echo "<input type='hidden' id='cg_photo_contest_over' value='$language_ThePhotoContestIsOver.'>";

echo "<input type='hidden' id='cg_ShowMeUserInfoOnLeftMouseHold' value='$language_ShowMeUserInfoOnLeftMouseHold'>";

echo "<input type='hidden' id='cg_FbLike' value='".@$FbLike."'>";
echo "<input type='hidden' id='cg_FbLikeGallery' value='".@$FbLikeGallery."'>";
echo "<input type='hidden' id='cg_FbLikeGalleryVote' value='".@$FbLikeGalleryVote."'>";

//Aktuelle Gallery Version wird geprüft
$p_cgal1ery_db_version = floatval(get_option( "p_cgal1ery_db_version" ));
echo "<input type='hidden' id='p_cgal1ery_db_version' value='".@$p_cgal1ery_db_version."'>";



echo "<input type='hidden' id='cg_Use_as_URL' value='".@$cg_Use_as_URL."'>";
echo "<input type='hidden' id='cg_ForwardToURL' value='".@$ForwardToURL."'>";
echo "<input type='hidden' id='cg_ForwardType' value='".@$ForwardType."'>";
echo "<input type='hidden' id='cg_ForwardFrom' value='".@$ForwardFrom."'>";

echo "<input type='hidden' id='cg_hide_info_png' value='".@$cg_hide_info_png."'>";
echo "<input type='hidden' id='cg_show_info_png' value='".@$cg_show_info_png."'>";


echo "<input type='hidden' id='cg_FullSizeImageOutGallery' value='".@$FullSizeImageOutGallery."'>";
echo "<input type='hidden' id='cg_FullSizeImageOutGalleryNewTab' value='".@$FullSizeImageOutGalleryNewTab."'>";
echo "<input type='hidden' id='cg_OnlyGalleryView' value='".@$OnlyGalleryView."'>";



// Update ab 13.08.2016 RandomSort + Viele viuselle Verbesserungen
//echo "<input type='hidden' id='cg_RandomSort' value='$cg_RandomSort' >";
echo "<input type='hidden' id='cg_AdjustThumbLook' value='$AdjustThumbLook' >";


echo "<input type='hidden' id='cg_ThumbViewBorderRadius' value='$ThumbViewBorderRadius' >";

echo "<input type='hidden' id='cg_HeightViewBorderRadius' value='$HeightViewBorderRadius' >";
echo "<input type='hidden' id='cg_HeightViewSpaceWidth' value='$HeightViewSpaceWidth' >";
echo "<input type='hidden' id='cg_HeightViewSpaceHeight' value='$HeightViewSpaceHeight' >";

echo "<input type='hidden' id='cg_RowViewBorderRadius' value='$RowViewBorderRadius' >";
echo "<input type='hidden' id='cg_RowViewSpaceWidth' value='$RowViewSpaceWidth' >";
echo "<input type='hidden' id='cg_RowViewSpaceHeight' value='$RowViewSpaceHeight' >";

echo "<input type='hidden' id='cg_TitlePositionGallery' value='$TitlePositionGallery' >";
echo "<input type='hidden' id='cg_RatingPositionGallery' value='$RatingPositionGallery' >";
echo "<input type='hidden' id='cg_CommentPositionGallery' value='$CommentPositionGallery' >";

echo "<input type='hidden' id='cg_VotesPerUser' value='$VotesPerUser' >";

echo "<input type='hidden' id='cg_OriginalSourceLinkInSlider' value='$OriginalSourceLinkInSlider' >";
echo "<input type='hidden' id='cg_PreviewInSlider' value='$PreviewInSlider' >";


echo "<input type='hidden' id='cg_PreviewInSlider' value='$PreviewInSlider' >";
echo "<input type='hidden' id='cg_language_imageIsNotActivated' value='$language_imageIsNotActivated' >";




// Update ab 13.08.2016 RandomSort + Viele viuselle Verbesserungen --- ENDE



//echo "<br>OnlyGalleryView: $OnlyGalleryView<br>";
//echo "<br>ForwardToURL: $ForwardToURL<br>";
//echo "<br>cg_Use_as_URL: $cg_Use_as_URL<br>";


//echo "<input type='hidden' id='cg_thumb_view_text' value='Thumb view'>";
//echo "<input type='hidden' id='cg_height_view_text' value='Height view'>";
//echo "<input type='hidden' id='cg_row_view_text' value='Row view'>";


// Pfade Ordner bei show-image.php  --- ENDE

//echo "<br>order: $order<br>";

//print_r($picsSQL);

//print_r($picsSQL);
//echo "<br>look111: $look<br>";
include("show-gallery.php");


//$end1 = microtime(true);

//$laufzeit1 = $end1 - $start1; 

//echo "Laufzeit1: ".$laufzeit1." Sekunden!";


?>

</div>

<?php
/*
$fp = fopen($cachefile, 'w'); // open the cache file for writing
fwrite($fp, ob_get_contents()); // save the contents of output buffer to the file
fclose($fp); // close the file
ob_end_flush(); // Send the output to the browser */?>