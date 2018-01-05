<?php

// rausfinden wie viel Mega-/Kilobyte hochgeladen werden k�nnen und es anzeigen lassen


/*		function return_bytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
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

    $maxsize = return_bytes(ini_get('post_max_size'));

    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    $maxsizeConverted = formatBytes($maxsize,2);

    echo "<br/><b>";

    echo formatBytes(memory_get_peak_usage(),2);

    echo "<br/>";

    echo formatBytes(memory_get_usage(),2);

    echo "</b><br/>"; */


// rausfinden wie viel Mega-/Kilobyte hochgeladen werden k�nnen und es anzeigen lassen ---- ENDE



$id = @$_GET['option_id'];
//$id = @$_POST['option_id'];


//echo "<br>id: $id<br>";

global $wpdb;

$tablename = $wpdb->prefix . "contest_gal1ery";
$tablenameOptions = $wpdb->prefix . "contest_gal1ery_options";
$tablename_options_visual = $wpdb->prefix . "contest_gal1ery_options_visual";
$tablenameOptionsInput = $wpdb->prefix . "contest_gal1ery_options_input";
$tablename_ip = $wpdb->prefix . "contest_gal1ery_ip";
$tablename_mail_admin = $wpdb->prefix . "contest_gal1ery_mail_admin";
$tablenameemail = $wpdb->prefix . "contest_gal1ery_mail";
$tablename_f_input = $wpdb->prefix . "contest_gal1ery_f_input";
$tablename_pro_options = $wpdb->prefix . "contest_gal1ery_pro_options";



if(@$_GET['reset_users_votes']){



    $wpdb->delete( $tablename_ip, array( 'GalleryID' => $id ), array( '%d' ) );

    ?>
    <script>
        alert('All users votes were reseted.');
    </script>

    <?php



}


if(@$_GET['reset_votes']){



    $wpdb->update(
        "$tablename",
        array('CountR' => 0,'Rating' => 0),
        array('GalleryID' => $id),
        array('%d','%d'),
        array('%d')
    );

    ?>
    <script>
        alert('All votes were deleted.');
    </script>

    <?php



}

if(@$_GET['reset_votes2']){




    $wpdb->update(
        "$tablename",
        array('CountS' => 0),
        array('GalleryID' => $id),
        array('%d'),
        array('%d')
    );

    ?>
    <script>
        alert('All votes were deleted.');
    </script>

    <?php




}



if(@$_POST['changeSize']==true){




// Values which should not be saved if not sended
    $unsavingValues = $wpdb->get_row("SELECT * FROM $tablename_pro_options WHERE GalleryId = '$id'");
    $ForwardAfterLoginUrl = $unsavingValues->ForwardAfterLoginUrl;
    $ForwardAfterLoginText = $unsavingValues->ForwardAfterLoginText;
    $RegUserUploadOnlyText = $unsavingValues->RegUserUploadOnlyText;


// Values which should not be saved if not sended
    $unsavingValues = $wpdb->get_row("SELECT * FROM $tablenameOptions WHERE id = '$id'");
    $PicsPerSite = $unsavingValues->PicsPerSite;
    $ScaleOnly = $unsavingValues->ScaleOnly;
    $ScaleAndCut = $unsavingValues->ScaleAndCut;
    $WidthThumb = $unsavingValues->WidthThumb;
    $HeightThumb = $unsavingValues->HeightThumb;
    $WidthGallery = $unsavingValues->WidthGallery;
    $HeightGallery = $unsavingValues->HeightGallery;
    $PicsInRow = $unsavingValues->PicsInRow;
    $HeightLookHeight = $unsavingValues->HeightLookHeight;
    $MaxResJPG = $unsavingValues->MaxResJPG;
    $MaxResPNG = $unsavingValues->MaxResPNG;
    $MaxResGIF = $unsavingValues->MaxResGIF;
    $HeightGallery = $unsavingValues->HeightGallery;
    $ContestEndTime = $unsavingValues->ContestEndTime;
    $AdjustThumbLook = $unsavingValues->AdjustThumbLook;
    $DistancePics = $unsavingValues->DistancePics;
    $DistancePicsV = $unsavingValues->DistancePicsV;
    $HideInfo = $unsavingValues->HideInfo;
    $FbLikeGoToGalleryLink = $unsavingValues->FbLikeGoToGalleryLink;



// Values which should not be saved if not sended
    $unsavingValuesVisual = $wpdb->get_row("SELECT * FROM $tablename_options_visual WHERE GalleryID = '$id'");
    $ThumbViewBorderWidth = $unsavingValuesVisual->ThumbViewBorderWidth;
    $ThumbViewBorderRadius = $unsavingValuesVisual->ThumbViewBorderRadius;
    $ThumbViewBorderColor = $unsavingValuesVisual->ThumbViewBorderColor;
    $ThumbViewBorderOpacity = $unsavingValuesVisual->ThumbViewBorderOpacity;
    $HeightViewBorderWidth = $unsavingValuesVisual->HeightViewBorderWidth;
    $HeightViewBorderRadius = $unsavingValuesVisual->HeightViewBorderRadius;
    $HeightViewSpaceWidth = $unsavingValuesVisual->HeightViewSpaceWidth;
    $HeightViewSpaceHeight = $unsavingValuesVisual->HeightViewSpaceHeight;
    $HeightViewBorderColor = $unsavingValuesVisual->HeightViewBorderColor;
    $HeightViewBorderOpacity = $unsavingValuesVisual->HeightViewBorderOpacity;
    $RowViewBorderWidth = $unsavingValuesVisual->RowViewBorderWidth;
    $RowViewBorderColor = $unsavingValuesVisual->RowViewBorderColor;
    $RowViewBorderOpacity = $unsavingValuesVisual->RowViewBorderOpacity;
    $RowViewBorderRadius = $unsavingValuesVisual->RowViewBorderRadius;
    $RowViewSpaceWidth = $unsavingValuesVisual->RowViewSpaceWidth;
    $RowViewSpaceHeight = $unsavingValuesVisual->RowViewSpaceHeight;
    $TitlePositionGallery = $unsavingValuesVisual->TitlePositionGallery;
    $RatingPositionGallery = $unsavingValuesVisual->RatingPositionGallery;
    $CommentPositionGallery = $unsavingValuesVisual->CommentPositionGallery;
    $ActivateGalleryBackgroundColor = $unsavingValuesVisual->ActivateGalleryBackgroundColor;
    $GalleryBackgroundColor = $unsavingValuesVisual->GalleryBackgroundColor;
    $GalleryBackgroundOpacity = $unsavingValuesVisual->GalleryBackgroundOpacity;
    $OriginalSourceLinkInSlider = $unsavingValuesVisual->OriginalSourceLinkInSlider;
    $PreviewInSlider = $unsavingValuesVisual->PreviewInSlider;

    if(!@$_POST['PreviewInSlider'] && !@$_POST['AllowGalleryScript']){

        $PreviewInSlider = $PreviewInSlider;

    }
    else{
        @$PreviewInSlider = (@$_POST['PreviewInSlider']) ? '1' : '0';
    }


    if(!@$_POST['OriginalSourceLinkInSlider'] && !@$_POST['AllowGalleryScript']){

        $OriginalSourceLinkInSlider = $OriginalSourceLinkInSlider;

    }
    else{
        @$OriginalSourceLinkInSlider = (@$_POST['OriginalSourceLinkInSlider']) ? '1' : '0';
    }



    $ActivateGalleryBackgroundColor = (@$_POST['ActivateGalleryBackgroundColor']) ? '1' : '0';

    $TitlePositionGallery = (@$_POST['TitlePositionGallery']==true) ? @$_POST['TitlePositionGallery'] : $TitlePositionGallery;
    $RatingPositionGallery = (@$_POST['RatingPositionGallery']==true) ? @$_POST['RatingPositionGallery'] : $RatingPositionGallery;
    $CommentPositionGallery = (@$_POST['CommentPositionGallery']==true) ? @$_POST['CommentPositionGallery'] : $CommentPositionGallery;


    $ThumbViewBorderWidth = (@$_POST['ThumbViewBorderWidth']==true OR @$_POST['ThumbViewBorderWidth']==0) ? @$_POST['ThumbViewBorderWidth'] : $ThumbViewBorderWidth;
    $ThumbViewBorderRadius = (@$_POST['ThumbViewBorderRadius']==true OR @$_POST['ThumbViewBorderRadius']==0) ? @$_POST['ThumbViewBorderRadius'] : $ThumbViewBorderRadius;


    if(!@$_POST['GalleryBackgroundColor']){
        $GalleryBackgroundColor = $GalleryBackgroundColor;
        $GalleryBackgroundOpacity = $GalleryBackgroundOpacity;
    }
    else{
        @$GalleryBackgroundColorPOST = @$_POST['GalleryBackgroundColor'];
        if(@$GalleryBackgroundColorPOST){
            foreach(@$GalleryBackgroundColorPOST as $key1 => $value1){
                @$GalleryBackgroundOpacity = @$key1; @$GalleryBackgroundColor = @$value1;
            }
        }
        else{
            $GalleryBackgroundColor = $GalleryBackgroundColor;
        }
    }

    $GalleryBackgroundOpacity = 1;





    if(!@$_POST['ThumbViewBorderColor']){
        $ThumbViewBorderColor = $ThumbViewBorderColor;
        $ThumbViewBorderOpacity = $ThumbViewBorderOpacity;
    }
    else{
        @$ThumbViewBorderColorPOST = @$_POST['ThumbViewBorderColor'];
        if(@$ThumbViewBorderColorPOST){
            foreach(@$ThumbViewBorderColorPOST as $key1 => $value1){
                @$ThumbViewBorderOpacity = @$key1; @$ThumbViewBorderColor = @$value1;
            }
        }
        else{
            $ThumbViewBorderColor = $ThumbViewBorderColor;
        }
    }
    $ThumbViewBorderOpacity = 1;

    if(!@$_POST['HeightViewBorderColor']){
        $HeightViewBorderColor = $HeightViewBorderColor;
        $HeightViewBorderOpacity = $HeightViewBorderOpacity;
    }
    else{
        @$HeightViewBorderColorPOST = @$_POST['HeightViewBorderColor'];
        if(@$HeightViewBorderColorPOST){
            foreach(@$HeightViewBorderColorPOST as $key2 => $value2){
                @$HeightViewBorderOpacity = @$key2; @$HeightViewBorderColor = @$value2;
            }
        }
        else{
            $HeightViewBorderColor = $HeightViewBorderColor;
        }
    }

    $HeightViewBorderOpacity = 1;


    if(!@$_POST['RowViewBorderColor']){
        $RowViewBorderColor = $RowViewBorderColor;
        $RowViewBorderOpacity = $RowViewBorderOpacity;
    }
    else{
        @$RowViewBorderColorPOST = @$_POST['RowViewBorderColor'];
        if(@$RowViewBorderColorPOST){
            foreach(@$RowViewBorderColorPOST as $key3 => $value3){
                @$RowViewBorderOpacity = @$key3; @$RowViewBorderColor = @$value3;
            }
        }
        else{
            $RowViewBorderOpacity = $RowViewBorderOpacity;
        }
    }

    $RowViewBorderOpacity = 1;

    @$GalleryName = (@$_POST['GalleryName']) ? @$_POST['GalleryName'] : @$GalleryName;
    @$GalleryName = trim(@$GalleryName);


    $RowViewBorderWidth = (@$_POST['RowViewBorderWidth']==true OR @$_POST['RowViewBorderWidth']==0) ? @$_POST['RowViewBorderWidth'] : $RowViewBorderWidth;
    $RowViewBorderRadius = (@$_POST['RowViewBorderRadius']==true OR @$_POST['RowViewBorderRadius']==0) ? @$_POST['RowViewBorderRadius'] : $RowViewBorderRadius;
    $RowViewSpaceWidth = (@$_POST['RowViewSpaceWidth']==true OR @$_POST['RowViewSpaceWidth']==0) ? @$_POST['RowViewSpaceWidth'] : $RowViewSpaceWidth;
    $RowViewSpaceHeight = (@$_POST['RowViewSpaceHeight']==true OR @$_POST['RowViewSpaceHeight']==0) ? @$_POST['RowViewSpaceHeight'] : $RowViewSpaceHeight;

    $HeightViewBorderWidth = (@$_POST['HeightViewBorderWidth']==true OR @$_POST['HeightViewBorderWidth']==0) ? @$_POST['HeightViewBorderWidth'] : $HeightViewBorderWidth;
    $HeightViewBorderRadius = (@$_POST['HeightViewBorderRadius']==true OR @$_POST['HeightViewBorderRadius']==0) ? @$_POST['HeightViewBorderRadius'] : $HeightViewBorderRadius;
    $HeightViewSpaceWidth = (@$_POST['HeightViewSpaceWidth']==true OR @$_POST['HeightViewSpaceWidth']==0) ? @$_POST['HeightViewSpaceWidth'] : $HeightViewSpaceWidth;
    $HeightViewSpaceHeight = (@$_POST['HeightViewSpaceHeight']==true OR @$_POST['HeightViewSpaceHeight']==0) ? @$_POST['HeightViewSpaceHeight'] : $HeightViewSpaceHeight;


//echo @$_POST['HeightViewBorderWidth'];
//echo $HeightViewBorderWidth;


//    var_dump($OriginalSourceLinkInSlider);
//    var_dump($PreviewInSlider);

    $wpdb->update(
        "$tablename_options_visual",
        array('ThumbViewBorderWidth' => @$ThumbViewBorderWidth,'ThumbViewBorderRadius' => @$ThumbViewBorderRadius,
            'ThumbViewBorderColor' => @$ThumbViewBorderColor,'ThumbViewBorderOpacity' => @$ThumbViewBorderOpacity,
            'HeightViewBorderWidth' => $HeightViewBorderWidth,'HeightViewBorderRadius' => $HeightViewBorderRadius,
            'HeightViewBorderColor' => @$HeightViewBorderColor,'HeightViewBorderOpacity' => @$HeightViewBorderOpacity,'HeightViewSpaceWidth' => @$HeightViewSpaceWidth,'HeightViewSpaceHeight' => @$HeightViewSpaceHeight,
            'RowViewBorderWidth' => @$RowViewBorderWidth,'RowViewBorderRadius' => @$RowViewBorderRadius,
            'RowViewBorderColor' => @$RowViewBorderColor,'RowViewBorderOpacity' => @$RowViewBorderOpacity,'RowViewSpaceWidth' => @$RowViewSpaceWidth,'RowViewSpaceHeight' => @$RowViewSpaceHeight,
            'TitlePositionGallery' => @$TitlePositionGallery,'RatingPositionGallery' => @$RatingPositionGallery,'CommentPositionGallery' => @$CommentPositionGallery,
            'ActivateGalleryBackgroundColor' => @$ActivateGalleryBackgroundColor,'GalleryBackgroundColor' => @$GalleryBackgroundColor,'GalleryBackgroundOpacity' => @$GalleryBackgroundOpacity,
            'OriginalSourceLinkInSlider' => @$OriginalSourceLinkInSlider,'PreviewInSlider' => @$PreviewInSlider),
        array('GalleryID' => $id),
        array('%d','%d',
            '%s','%s',
            '%d','%d',
            '%s','%s','%d','%d',
            '%d','%d',
            '%s','%s','%d','%d',
            '%d','%d','%d',
            '%d','%s','%s','%s','%s'),
        array('%d')
    );


// Unix Zeitstempel wird eingetragen. Sp�ter browserabh�ngig verarbeitet. 
// 86400 = Anzahl der Sekunden an einem Tag
// Man w�hlt immer den Tag aus an dem der Contest endet in, edit-options.php, das ist dann immer 00:00 und f�gt die Sekunden hinzu bis Ende des Taes.
// �berall anders aknn die Zeit dann direkt verarbeitet werden
//$unix = time();
//echo @$_POST['ContestEndTime'];
    @$ContestEndTime = (@$_POST['ContestEndTime']) ? strtotime(@$_POST['ContestEndTime'])+86400 : @$ContestEndTime;



//echo "<br>ContestEndTime: $ContestEndTime<br>";

    if(@$_POST['ThumbLook']){

        $AdjustThumbLook = (@$_POST['AdjustThumbLook']) ? 1 : 0;
        $DistancePics = (@$_POST['DistancePics']) ? @$_POST['DistancePics'] : 0;
        $DistancePicsV = (@$_POST['DistancePicsV']) ? @$_POST['DistancePicsV']: 0;

    }
    else{

        $AdjustThumbLook = $AdjustThumbLook;
        $DistancePics = $DistancePics;
        $DistancePicsV = $DistancePicsV;

    }




    $WidthThumb = (@$_POST['WidthThumb']) ? @$_POST['WidthThumb'] : $WidthThumb;
    $HeightThumb = (@$_POST['HeightThumb']) ? @$_POST['HeightThumb'] : $HeightThumb;
    $WidthGallery = (@$_POST['WidthGallery']) ? @$_POST['WidthGallery'] : $WidthGallery;
    $HeightGallery = (@$_POST['HeightGallery']) ? @$_POST['HeightGallery'] : $HeightGallery;



    // echo "<br>WidthGalery: $WidthGalery<br>";
    // echo "<br>HeightGalery: $HeightGalery<br>";

// Ermittel die gesendeten Werte f�r die Gr��e der Bilder --- ENDE


// Ermittel zuerst die gesendeten Zahlenwerte der Einstellungen



//$DistancePics = (@$_POST['DistancePics']) ? "DistancePics='".@$_POST['DistancePics']."'," : "DistancePics='0',";
//$DistancePicsV = (@$_POST['DistancePicsV']) ? "DistancePicsV='".@$_POST['DistancePicsV']."'" : "DistancePicsV='0'";


    //$querySETvaluesThumbs = "UPDATE $tablenameOptions SET $WidthThumb $HeightThumb $WidthGallery $HeightGallery
    //$DistancePics $DistancePicsV  WHERE id = '$id'";
    //$wpdb->query($querySETvaluesThumbs);


    $wpdb->update(
        "$tablenameOptions",
        array('WidthThumb' => $WidthThumb,'HeightThumb' => $HeightThumb,'WidthGallery' => $WidthGallery,'HeightGallery' => $HeightGallery,
            'DistancePics' => $DistancePics,'DistancePicsV' => $DistancePicsV, 'ContestEndTime' => $ContestEndTime),
        array('id' => $id),
        array('%d','%d','%d','%d','%d','%d','%d'),
        array('%d')
    );



//$PicsPerSite = @$_POST['PicsPerSite'];
//$DistancePics = @$_POST['DistancePics'];
//$DistancePicsV = @$_POST['DistancePicsV'];

//echo $PicsInRow; echo "<br/>";
//echo $LastRow; echo "<br/>";
//echo $DistancePics; echo "<br/>";
//echo $DistancePicsV; echo "<br/>";


// Ermittel zuerst die gesendeten Zahlenwerte der Einstellungen --- ENDE

// Ermittelt die gesendeten Einstellungen (checkboxes)

    //$maxRes = @$_POST['maxRes']; $maxRes = serialize($maxRes);

    //print_r(@$_POST['order']);

    $PicsPerSite = (@$_POST['PicsPerSite']) ? @$_POST['PicsPerSite'] : $PicsPerSite;

    $order = @$_POST['order'];
    $i = 0;
    //echo "<br>Order:<br>";
    //print_r($order);
    //echo "<br>";

    foreach($order as $key => $value){

        $i++;

        if($value=='t'){$t=$i;}
        if($value=='h'){$h=$i;}
        if($value=='r'){$r=$i;}

    }

    $ThumbLook = (@$_POST['ThumbLook']) ? '1' : '0';
    $HeightLook = (@$_POST['HeightLook']) ? '1' : '0';
    $RowLook = (@$_POST['RowLook']) ? '1' : '0';

    $ThumbLookOrder = $t;
    $HeightLookOrder = $h;
    $RowLookOrder = $r;




    $OnlyGalleryView = (@$_POST['OnlyGalleryView']) ? '1' : '0';
    $SinglePicView = (@$_POST['SinglePicView']) ? '1' : '0';

    if(!@$_POST['ScaleWidthGalery'] or !@$_POST['ScaleSizesGalery']){



        if(@$ScaleAndCut==1  AND !@$_POST['ScaleWidthGalery']){@$ScaleSizesGalery=1;}
        else if(@$ScaleOnly==1 AND !@$_POST['ScaleSizesGalery']){@$ScaleWidthGalery=1;}
        else if(@$ScaleOnly!=1 AND !@$_POST['ScaleSizesGalery']){@$ScaleWidthGalery=1; }
        else{@$ScaleSizesGalery=1;}

    }
    else{
        $ScaleWidthGalery = (@$_POST['ScaleWidthGalery']) ? '1' : '0';
        @$ScaleSizesGalery = (@$_POST['ScaleSizesGalery']) ? '1' : '0';
    }

    @$AllowGalleryScript = (@$_POST['AllowGalleryScript']) ? '1' : '0';

    if(@$_POST['AllowGalleryScript']){
        @$HideInfo = (@$_POST['HideInfo']) ? '1' : 0;
    }
    else{
        @$HideInfo = (@$_POST['HideInfo']) ? '1' : $HideInfo;
    }


    // 1 = Height, 2 = Thumb, 3 = Row
    if(@$_POST['InfiniteScrollHeight']){$InfiniteScroll = 1;}
    else if(@$_POST['InfiniteScrollThumb']){$InfiniteScroll = 2;}
    else if(@$_POST['InfiniteScrollRow']){$InfiniteScroll = 3;}
    else {$InfiniteScroll = 0;}

    //	echo @$_POST['InfiniteScrollThumb'];

    //echo "InfiniteScroll: $InfiniteScroll";


    $FullSizeImageOutGallery = (@$_POST['FullSizeImageOutGallery']) ? '1' : '0';
    //$FullSizeImageOutGalleryNewTab = (@$_POST['FullSizeImageOutGalleryNewTab']) ? '1' : '0';
    $FullSizeImageOutGalleryNewTab = '1'; //Bei aktuellem Entwicklungsstand immer 1
    $ShowAlwaysInfoSlider = (@$_POST['ShowAlwaysInfoSlider']) ? '1' : '0';

    // $HeightLookHeight = (@$_POST['HeightLookHeight']) ? "HeightLookHeight='".@$_POST['HeightLookHeight']."'," : "";
    $HeightLookHeight = (@$_POST['HeightLookHeight']) ? @$_POST['HeightLookHeight'] : $HeightLookHeight;
    @$VotesPerUser = (@$_POST['VotesPerUser']) ? @$_POST['VotesPerUser'] : @$VotesPerUser;


    @$FbLikeGoToGalleryLink = (@$_POST['FbLikeGoToGalleryLink']) ? @$_POST['FbLikeGoToGalleryLink'] : @$FbLikeGoToGalleryLink;

    $wp_upload_dir = wp_upload_dir();

    // Zuerst insert
    $backToGalleryFile = $wp_upload_dir["basedir"]."/contest-gallery/gallery-id-$id/backtogalleryurl.js";
    $backToGalleryFileContent = "function loadButton(){
			document.getElementById('cgBackToGallery').innerHTML='$FbLikeGoToGalleryLink'
}";
    // Dann HTML Entities
    @$FbLikeGoToGalleryLink = sanitize_text_field(htmlentities(@$FbLikeGoToGalleryLink, ENT_QUOTES, 'UTF-8'));

    $fp = fopen($backToGalleryFile, 'w');
    fwrite($fp, $backToGalleryFileContent);
    fclose($fp);


    //$PicsInRow = (@$_POST['PicsInRow']) ? "PicsInRow='".@$_POST['PicsInRow']."'," : '';
    $PicsInRow = (@$_POST['PicsInRow'] OR @$_POST['PicsInRow'] == 0) ? @$_POST['PicsInRow'] : $PicsInRow;
    if($PicsInRow==0){$PicsInRow=1;}
    $LastRow = (@$_POST['LastRow']) ? '1' : '0';
    $HideUntilVote = (@$_POST['HideUntilVote']) ? '1' : '0';
    $ShowOnlyUsersVotes = (@$_POST['ShowOnlyUsersVotes']) ? '1' : '0';
    $ActivateUpload = (@$_POST['ActivateUpload']) ? '1' : '0';
    $ContestEnd = (@$_POST['ContestEnd']) ? '1' : '0';
    $ThumbsInRow = (@$_POST['ThumbsInRow']) ? '1' : '0';
    $FullSize = (@$_POST['FullSize']) ? '1' : '0';
    $AllowSort = (@$_POST['AllowSort']) ? '1' : '0';
    $RandomSort = (@$_POST['RandomSort']) ? '1' : '0';

    $AllowComments = (@$_POST['AllowComments']) ? '1' : '0';

    $CommentsOutGallery = (@$_POST['CommentsOutGallery']) ? '1' : '0';
    $ShowAlways = (@$_POST['ShowAlways']) ? '1' : '0';



    $AllowRating = (@$_POST['AllowRating']) ? '1' : '0';

    if((@$_POST['AllowRating'])){$AllowRating=1;}
    else if((@$_POST['AllowRating2'])){$AllowRating=2;}
    else{$AllowRating=0;}

    $RatingOutGallery = (@$_POST['RatingOutGallery']) ? '1' : '0';
    $IpBlock = (@$_POST['IpBlock']) ? '1' : '0';
    $checkLogin = (@$_POST['checkLogin']) ? '1' : '0';
    $FbLike = (@$_POST['FbLike']) ? '1' : '0';
    $FbLikeGallery = (@$_POST['FbLikeGallery']) ? '1' : '0';
    $FbLikeGalleryVote = (@$_POST['FbLikeGalleryVote']) ? '1' : '0';

    $InformUsers = (@$_POST['InformUsers']) ? '1' : '0';

    // Forward Images to URL options


    //$ForwardToURL = (@$_POST['ForwardToURL']) ? '1' : '0';

    //Pr�fen ob bei Klick auf images weitergelitet werden soll
    ///	@$Use_as_URL = $wpdb->get_var( "SELECT Use_as_URL FROM $tablename_f_input WHERE GalleryID='$id' AND Use_as_URL='1'");

    //if(){}
    $ForwardToURL = 1;

    $ForwardType = (@$_POST['ForwardType']) ? '2' : '1';

    // Pauschal auf 1 wenn nichts gesendet wird
    // Slider = 1, Gallery = 2, SinglePic = 3
    $ForwardFrom = $wpdb->get_var("SELECT ForwardFrom FROM $tablenameOptions WHERE id = '$id'");
    // Wenn Gallerie Jquery gew�hlt ist dann 1 (Forward from Slider)
    if($AllowGalleryScript==1 && !@$_POST['ForwardFromGallery']){$ForwardFrom=1;}
    // Wenn ForwardFromGallery mitgeschickt wurde dann 2
    else if(@$_POST['ForwardFromGallery']){$ForwardFrom=2;}
    // Wenn SinglePic Ansicht gew�hlt ist dann 3
    else if($SinglePicView==1){$ForwardFrom=3;}
    else{$ForwardFrom=$ForwardFrom;}


    //echo "$ForwardFrom";
    //if(@$_POST['ForwardFromSlider']){@$ForwardFrom=1;}
    //else if(@$_POST['ForwardFromGallery']){@$ForwardFrom=2;}
    //else if(@$_POST['ForwardFromSinglePic']){@$ForwardFrom=3;}
    //else {$ForwardFrom=$ForwardFrom;}

    //echo @$_POST['maxResJPGon'];
    //	echo @$_POST['maxResPNGon'];
    //echo @$_POST['maxResGIFon'];



    // Ermitteln der maximalen Uploads beim Hochalden in MB

    $ActivatePostMaxMB = (@$_POST['ActivatePostMaxMB']) ? '1' : '0';

    $PostMaxMB = $wpdb->get_var("SELECT PostMaxMB FROM $tablenameOptions WHERE id = '$id'");

    if(@$_POST['ActivatePostMaxMB']){

        if(@$_POST['PostMaxMB']){$PostMaxMB=@$_POST['PostMaxMB'];}
        //Leeren Wert kann man by MySQL nicht einf�gen. Es entsteht immer eine NULL
        else{$PostMaxMB='';}

    }

    else{
        //Wert aus der Datenbank wird genommen
        $PostMaxMB = $PostMaxMB;

    }

    // Ermitteln des maximalen Uploads beim Hochladen in MB --- ENDE


    // Ermitteln ob und der Anzahl des Bulk Uploads

    $ActivateBulkUpload = (@$_POST['ActivateBulkUpload']) ? '1' : '0';

    $BulkUploadQuantity = $wpdb->get_var("SELECT BulkUploadQuantity FROM $tablenameOptions WHERE id = '$id'");

    if(@$_POST['ActivateBulkUpload']){

        if(@$_POST['BulkUploadQuantity']){$BulkUploadQuantity=@$_POST['BulkUploadQuantity'];}
        //Leeren Wert kann man by MySQL nicht einf�gen. Es entsteht immer eine NULL
        else{$BulkUploadQuantity='';}

    }

    else{
        //Wert aus der Datenbank wird genommen
        $BulkUploadQuantity = $BulkUploadQuantity;

    }


    $BulkUploadMinQuantity = $wpdb->get_var("SELECT BulkUploadMinQuantity FROM $tablenameOptions WHERE id = '$id'");

    if(@$_POST['ActivateBulkUpload']){

        if(@$_POST['BulkUploadMinQuantity']){$BulkUploadMinQuantity=@$_POST['BulkUploadMinQuantity'];}
        //Leeren Wert kann man by MySQL nicht einf�gen. Es entsteht immer eine NULL
        else{$BulkUploadMinQuantity='';}

    }

    else{
        //Wert aus der Datenbank wird genommen
        $BulkUploadMinQuantity = $BulkUploadMinQuantity;

    }

    // Ermitteln ob und der Anzahl des Bulk Uploads	 --- ENDE



    // Ermitteln der m�glichen Aufl�sung beim Hochalden

    $MaxResJPGon = (@$_POST['MaxResJPGon']) ? '1' : '0';

    $MaxResJPGwidth = $wpdb->get_var("SELECT MaxResJPGwidth FROM $tablenameOptions WHERE id = '$id'");
    $MaxResJPGheight = $wpdb->get_var("SELECT MaxResJPGheight FROM $tablenameOptions WHERE id = '$id'");

    if(@$_POST['MaxResJPGon']){

        if(@$_POST['MaxResJPGwidth']){$MaxResJPGwidth=@$_POST['MaxResJPGwidth'];}
        //Leeren Wert kann man by MySQL nicht einf�gen. Es entsteht immer eine NULL
        else{$MaxResJPGwidth='';}
        if(@$_POST['MaxResJPGheight']){$MaxResJPGheight=@$_POST['MaxResJPGheight'];}
        //Leeren Wert kann man by MySQL nicht einf�gen. Es entsteht immer eine NULL
        else{$MaxResJPGheight='';}

    }

    else{
        //Wert aus der Datenbank wird genommen
        $MaxResJPGwidth = $MaxResJPGwidth;
        $MaxResJPGheight = $MaxResJPGheight;

    }

    $MaxResPNGon = (@$_POST['MaxResPNGon']) ? '1' : '0';

    $MaxResPNGwidth = $wpdb->get_var("SELECT MaxResPNGwidth FROM $tablenameOptions WHERE id = '$id'");
    $MaxResPNGheight = $wpdb->get_var("SELECT MaxResPNGheight FROM $tablenameOptions WHERE id = '$id'");

    if(@$_POST['MaxResPNGon']){

        if(@$_POST['MaxResPNGwidth']){$MaxResPNGwidth=@$_POST['MaxResPNGwidth'];}
        else{$MaxResPNGwidth='';}
        if(@$_POST['MaxResPNGheight']){$MaxResPNGheight=@$_POST['MaxResPNGheight'];}
        else{$MaxResPNGheight='';}

    }

    else{
        //Wert aus der Datenbank wird genommen
        $MaxResPNGwidth = $MaxResPNGwidth;
        $MaxResPNGheight = $MaxResPNGheight;

    }


    $MaxResGIFon = (@$_POST['MaxResGIFon']) ? '1' : '0';

    $MaxResGIFwidth = $wpdb->get_var("SELECT MaxResGIFwidth FROM $tablenameOptions WHERE id = '$id'");
    $MaxResGIFheight = $wpdb->get_var("SELECT MaxResGIFheight FROM $tablenameOptions WHERE id = '$id'");

    if(@$_POST['MaxResGIFon']){

        if(@$_POST['MaxResGIFwidth']){$MaxResGIFwidth=@$_POST['MaxResGIFwidth'];}
        else{$MaxResGIFwidth='';}
        if(@$_POST['MaxResGIFheight']){$MaxResGIFheight=@$_POST['MaxResGIFheight'];}
        else{$MaxResGIFheight='';}


    }

    else{
        //Wert aus der Datenbank wird genommen
        $MaxResGIFwidth = $MaxResGIFwidth;
        $MaxResGIFheight = $MaxResGIFheight;

    }


    // Ermitteln der m�glichen Aufl�sung beim Hochalden --- ENDE



// Ermittelt die gesendeten Einstellungen (checkboxes) --- ENDE 

    // Update non scale or cut values

    /*$querySETvalues = "UPDATE $tablenameOptions SET PicsPerSite='$PicsPerSite', MaxResJPGon='$MaxResJPGon', MaxResPNGon='$MaxResPNGon', MaxResGIFon='$MaxResGIFon',
    $MaxResJPG $MaxResPNG $MaxResGIF
    ScaleOnly='$ScaleWidthGalery', ScaleAndCut='$ScaleSizesGalery', FullSize = '$FullSize', AllowSort = '$AllowSort',
    AllowComments = '$AllowComments', AllowRating = '$AllowRating', IpBlock = '$IpBlock', FbLike = '$FbLike', AllowGalleryScript='$AllowGalleryScript',
    ThumbLook = '$ThumbLook', HeightLook = '$HeightLook', RowLook = '$RowLook',
    ThumbLookOrder = '$ThumbLookOrder', HeightLookOrder = '$HeightLookOrder', RowLookOrder = '$RowLookOrder',
    $HeightLookHeight ThumbsInRow = '$ThumbsInRow', $PicsInRow LastRow = '$LastRow'
    WHERE id = '$id'";*/

    //$wpdb->query($querySETvalues);


    $wpdb->update(
        "$tablenameOptions",
        array('PicsPerSite' => $PicsPerSite,'GalleryName' => @$GalleryName,'MaxResJPGon' => $MaxResJPGon,'MaxResPNGon' => $MaxResPNGon,'MaxResGIFon' => $MaxResGIFon,
            'MaxResJPGwidth' => $MaxResJPGwidth,'MaxResJPGheight' => $MaxResJPGheight,'MaxResPNGwidth' => $MaxResPNGwidth,'MaxResPNGheight' => $MaxResPNGheight,'MaxResGIFwidth' => $MaxResGIFwidth,'MaxResGIFheight' => $MaxResGIFheight,
            'OnlyGalleryView' => $OnlyGalleryView,'SinglePicView' => $SinglePicView,'ScaleOnly' => @$ScaleWidthGalery,'ScaleAndCut' => @$ScaleSizesGalery,'FullSize' => $FullSize,'AllowSort' => $AllowSort,'RandomSort' => $RandomSort,'ShowAlways' => $ShowAlways,
            'AllowComments' => $AllowComments,'CommentsOutGallery' => $CommentsOutGallery,'AllowRating' => $AllowRating,'VotesPerUser' => $VotesPerUser,'RatingOutGallery' => $RatingOutGallery,'IpBlock' => $IpBlock,
            'CheckLogin' => $checkLogin, 'FbLike' => $FbLike,'FbLikeGallery' => $FbLikeGallery,'FbLikeGalleryVote' => $FbLikeGalleryVote,
            'AllowGalleryScript' => $AllowGalleryScript,'InfiniteScroll' => $InfiniteScroll,'FullSizeImageOutGallery' => $FullSizeImageOutGallery,'FullSizeImageOutGalleryNewTab' => $FullSizeImageOutGalleryNewTab,
            'Inform' => $InformUsers, 'ShowAlwaysInfoSlider' => $ShowAlwaysInfoSlider,'ThumbLook' => $ThumbLook,'AdjustThumbLook' => $AdjustThumbLook,'HeightLook' => $HeightLook,'RowLook' => $RowLook,
            'ThumbLookOrder' => $ThumbLookOrder,'HeightLookOrder' => $HeightLookOrder,'RowLookOrder' => $RowLookOrder,
            'HeightLookHeight' => $HeightLookHeight, 'ThumbsInRow' => $ThumbsInRow, 'PicsInRow' => $PicsInRow, 'LastRow' => $LastRow,'HideUntilVote' => $HideUntilVote, 'ShowOnlyUsersVotes' => $ShowOnlyUsersVotes,'HideInfo' => $HideInfo, 'ActivateUpload' => $ActivateUpload, 'ContestEnd' => $ContestEnd,
            'ForwardToURL' => $ForwardToURL, 'ForwardFrom' => $ForwardFrom, 'ForwardType' => $ForwardType,
            'ActivatePostMaxMB' => $ActivatePostMaxMB, 'PostMaxMB' => $PostMaxMB, 'ActivateBulkUpload' => $ActivateBulkUpload,
            'BulkUploadQuantity' => $BulkUploadQuantity, 'BulkUploadMinQuantity' => $BulkUploadMinQuantity),
        array('id' => $id),
        array('%d','%s','%d','%d','%d',
            '%d','%d','%d','%d','%d','%d',
            '%d','%d','%d','%d','%d','%d',
            '%d','%d','%d','%d','%d','%d',
            '%d','%d','%d','%d','%d','%d',
            '%d','%d','%d','%d','%d','%d',
            '%d','%d','%d','%d','%d','%d','%d',
            '%d','%d','%d','%d','%d','%d','%d',
            '%d','%d','%d',
            '%d','%d','%d','%d','%d'),
        array('%d')
    );

    // Extra update hier notwendig
    $wpdb->update(
        "$tablenameOptions",
        array('FbLikeGoToGalleryLink'=>$FbLikeGoToGalleryLink),
        array('id' => $id),
        array('%s'),
        array('%d')
    );



    // input Options

    // $forward = (@$_POST['forward']) ? '1' : '0';
    // $forward_url = (@$_POST['forward_url']) ? "Forward_url='".htmlentities(@$_POST['forward_url'], ENT_QUOTES, 'UTF-8')."'," : '';
    // $confirmation_text = (@$_POST['confirmation_text']) ? "Confirmation_Text='".htmlentities(@$_POST['confirmation_text'], ENT_QUOTES, 'UTF-8')."'," : '';


    // Values which should not be saved if not sended
    $unsavingValuesInput = $wpdb->get_row("SELECT * FROM $tablenameOptionsInput WHERE GalleryID = '$id'");
    $forward_url = $unsavingValuesInput->Forward_URL;
    $confirmation_text = $unsavingValuesInput->Confirmation_Text;


    //echo @$_POST['forward'];
    //	echo @$_POST['forward_url'];
    //echo @$_POST['confirmation_text'];
    //echo $id;



    $forward = (@$_POST['forward']) ? '1' : '0';
    $forward_url = (@$_POST['forward_url']) ? htmlentities(@$_POST['forward_url'], ENT_QUOTES, 'UTF-8') : $forward_url;
    $confirmation_text = (@$_POST['confirmation_text']) ? htmlentities(@$_POST['confirmation_text'], ENT_QUOTES, 'UTF-8') : $confirmation_text;

    // input Options --- ENDE

    //$querySETvaluesInputOptions = "UPDATE $tablenameOptionsInput SET $forward_url $confirmation_text Forward = '$forward' WHERE id = '$id'";
    //$wpdb->query($querySETvaluesInputOptions);

    $wpdb->update(
        "$tablenameOptionsInput",
        array('Forward' => $forward,'Forward_URL' => $forward_url,'Confirmation_Text' => $confirmation_text),
        array('GalleryID' => $id),
        array('%d','%s','%s'),
        array('%d')
    );



    // Save changes in table name admin


    $content = @$_POST["editpost"];

    //Ganz wichtig, ansonsten werden bei vielen Servern immer / (Backslashes bei Anf�hrungszeichen und aneren speziellen Sonderzeichen) hinzugef�gt
    $content = preg_replace('/\\\\/', '', $content);


    //nl2br($content);

    //print_r($content);

    //$content = htmlentities($content, ENT_QUOTES, 'UTF-8');

    // Magic Quotes on?
    if (get_magic_quotes_gpc()) { // eingeschaltet?
        @$_POST["from"] = stripslashes(@$_POST["from"]);
        @$_POST["reply"] = stripslashes(@$_POST["reply"]);
        @$_POST["AdminMail"] = stripslashes(@$_POST["AdminMail"]);
        @$_POST["cc"] = stripslashes(@$_POST["cc"]);
        @$_POST["bcc"] = stripslashes(@$_POST["bcc"]);
        @$_POST["header"] = stripslashes(@$_POST["header"]);
        @$_POST["url"] = stripslashes(@$_POST["url"]);
        //@$_POST["content"] = stripslashes(@$_POST["content"]);
    }


    // Escape values wordpress sql

    $from = sanitize_text_field(@$_POST["from"]);
    $reply = sanitize_text_field(@$_POST["reply"]);
    $AdminMail = sanitize_text_field(@$_POST["AdminMail"]);
    $cc = sanitize_text_field(@$_POST["cc"]);
    $bcc = sanitize_text_field(@$_POST["bcc"]);
    $header = sanitize_text_field(@$_POST["header"]);
    $url = sanitize_text_field(@$_POST["url"]);
    //$content = sanitize_text_field($content);

    // Make htmlspecialchars

    htmlentities($from);
    htmlentities($reply);
    htmlentities($AdminMail);
    htmlentities($cc);
    htmlentities($bcc);
    htmlentities($header);
    htmlentities($url);
    //htmlentities($content);

    //$content = nl2br($content);


    // Update email-table content

    //$querySETemail = "UPDATE $tablenameemail SET Admin='$from', Header = '$header', Reply='$reply', BCC='$bcc',
    //CC='$cc', URL='$url', Content='$content' WHERE GalleryID = '$GalleryID' ";
    //$updateSQLemail = $wpdb->query($querySETemail);

    $wpdb->update(
        "$tablename_mail_admin",
        array(
            'Admin' => "$from",'AdminMail' => "$AdminMail",'Header' => "$header",'Reply' => "$reply",'BCC' => "$bcc",
            'CC' => "$cc",'URL' => "$url",'Content' => "$content"
        ),
        array('GalleryID' => $id),
        array('%s','%s','%s','%s','%s',
            '%s','%s','%s'),
        array('%d')
    );



    if (@$_POST['InformAdmin']) {

        //Echo "works";

        $wpdb->update(
            "$tablenameOptions",
            array('InformAdmin' => '1'),
            array('id' => $id),
            array('%d'),
            array('%d')
        );

    }

    else{

        $wpdb->update(
            "$tablenameOptions",
            array('InformAdmin' => '0'),
            array('id' => $id),
            array('%d'),
            array('%d')
        );

    }


    // Save changes in table name admin --- ENDE



    // Save changes in table user mail

    $contentUserMail = @$_POST["editpostUserMail"];

    //Ganz wichtig, ansonsten werden bei vielen Servern immer / (Backslashes bei Anf�hrungszeichen und aneren speziellen Sonderzeichen) hinzugef�gt
    $contentUserMail = preg_replace('/\\\\/', '', $contentUserMail);

    //$content = htmlentities($content, ENT_QUOTES, 'UTF-8');

    // Magic Quotes on?
    if (get_magic_quotes_gpc()) { // eingeschaltet?
        @$_POST["from_user_mail"] = stripslashes(@$_POST["from_user_mail"]);
        @$_POST["reply_user_mail"] = stripslashes(@$_POST["reply_user_mail"]);
        @$_POST["cc_user_mail"] = stripslashes(@$_POST["cc_user_mail"]);
        @$_POST["bcc_user_mail"] = stripslashes(@$_POST["bcc_user_mail"]);
        @$_POST["header_user_mail"] = stripslashes(@$_POST["header_user_mail"]);
        @$_POST["url_user_mail"] = stripslashes(@$_POST["url_user_mail"]);
        //	@$_POST["content"] = stripslashes($content);
        //	echo "<br>ja<br>";
    }
    //	stripslashes($content);
    //	echo "<br>content2: $content<br>";


    // Escape values wordpress sql

    $from = sanitize_text_field(@$_POST["from_user_mail"]);
    $reply = sanitize_text_field(@$_POST["reply_user_mail"]);
    $cc = sanitize_text_field(@$_POST["cc_user_mail"]);
    $bcc = sanitize_text_field(@$_POST["bcc_user_mail"]);
    $header = sanitize_text_field(@$_POST["header_user_mail"]);
    $url = sanitize_text_field(@$_POST["url_user_mail"]);
    //$content = sanitize_text_field($content); <<< ansonten verschieden html eingaben wie <br> und andere

    // Make htmlspecialchars

    htmlentities($from);
    htmlentities($reply);
    htmlentities($cc);
    htmlentities($bcc);
    htmlentities($header);
    htmlentities($url);
    //htmlentities($content); <<< ansonten verschieden html eingaben wie <br> und andere



    //$querySETemail = "UPDATE $tablenameemail SET Admin='$from', Header = '$header', Reply='$reply', BCC='$bcc',
    //CC='$cc', URL='$url', Content='$content' WHERE GalleryID = '$GalleryID' ";
    //$updateSQLemail = $wpdb->query($querySETemail);

    $wpdb->update(
        "$tablenameemail",
        array(
            'Admin' => "$from",'Header' => "$header",'Reply' => "$reply",'BCC' => "$bcc",
            'CC' => "$cc",'URL' => "$url",'Content' => "$contentUserMail"
        ),
        array('GalleryID' => $id),
        array('%s','%s','%s','%s',
            '%s','%s','%s'),
        array('%d')
    );



    // Save changes in table user mail --- ENDE


    // Save Pro options here

    $ForwardAfterLoginUrlCheck = (@$_POST['ForwardAfterLoginUrlCheck']) ? '1' : '0';
    $ForwardAfterLoginTextCheck = (@$_POST['ForwardAfterLoginTextCheck']) ? '1' : '0';
    $RegUserUploadOnly = (@$_POST['RegUserUploadOnly']) ? '1' : '0';

    $ForwardAfterLoginUrl = (@$_POST['ForwardAfterLoginUrl']) ? htmlentities(@$_POST['ForwardAfterLoginUrl'], ENT_QUOTES, 'UTF-8') : $ForwardAfterLoginUrl;
    $ForwardAfterLoginText = (@$_POST['ForwardAfterLoginText']) ? htmlentities(@$_POST['ForwardAfterLoginText'], ENT_QUOTES, 'UTF-8') : $ForwardAfterLoginText;
    $RegUserUploadOnlyText = (@$_POST['RegUserUploadOnlyText']) ? htmlentities(@$_POST['RegUserUploadOnlyText'], ENT_QUOTES, 'UTF-8') : $RegUserUploadOnlyText;


    $ForwardAfterRegUrl = htmlentities(@$_POST['ForwardAfterRegUrl'], ENT_QUOTES, 'UTF-8');
    $ForwardAfterRegText = htmlentities(@$_POST['ForwardAfterRegText'], ENT_QUOTES, 'UTF-8');
    $TextEmailConfirmation = htmlentities(@$_POST['TextEmailConfirmation'], ENT_QUOTES, 'UTF-8');
    $TextAfterEmailConfirmation = htmlentities(@$_POST['TextAfterEmailConfirmation'], ENT_QUOTES, 'UTF-8');
    $RegMailAddressor = @$_POST['RegMailAddressor'];
    $RegMailReply = @$_POST['RegMailReply'];
    $RegMailSubject = @$_POST['RegMailSubject'];



    $wpdb->update(
        "$tablename_pro_options",
        array('ForwardAfterRegUrl' => $ForwardAfterRegUrl,'ForwardAfterRegText' => $ForwardAfterRegText,
            'ForwardAfterLoginUrlCheck' => $ForwardAfterLoginUrlCheck,'ForwardAfterLoginUrl' => $ForwardAfterLoginUrl,
            'ForwardAfterLoginTextCheck' => $ForwardAfterLoginTextCheck,'ForwardAfterLoginText' => $ForwardAfterLoginText,
            'TextEmailConfirmation' => $TextEmailConfirmation,'TextAfterEmailConfirmation' => $TextAfterEmailConfirmation,
            'RegMailAddressor' => $RegMailAddressor,'RegMailReply' => $RegMailReply,'RegMailSubject' => $RegMailSubject,
            'RegUserUploadOnly' => $RegUserUploadOnly,'RegUserUploadOnlyText' => $RegUserUploadOnlyText),
        array('GalleryID' => $id),
        array('%s','%s',
            '%d','%s',
            '%d','%s',
            '%s','%s',
            '%s','%s','%s',
            '%d','%s'),
        array('%d')
    );

    // Save Pro options here --- ENDE



    echo "<p id='cg_changes_saved' style='font-size:18px;'><strong>Changes saved</strong></p>";





}


?>