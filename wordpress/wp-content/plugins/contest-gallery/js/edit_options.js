jQuery(document).ready(function($){

    $(document).on('click', '.cg_move_view_to_top', function(e){
    	var sortableView = $(this).closest('.cg_options_sortableContainer');
        sortableView.insertBefore(sortableView.prev('.cg_options_sortableContainer'));
        sortableView.next().find('.cg_move_view_to_top').removeClass('cg_hide');
        $('.cg_options_sortableContainer:first-child .cg_move_view_to_bottom, .cg_options_sortableContainer:nth-child(2) .cg_move_view_to_bottom').removeClass('cg_hide');
        $('.cg_options_sortableContainer:nth-child(3) .cg_move_view_to_bottom').addClass('cg_hide');

        $('.cg_options_sortableContainer:first-child .cg_move_view_to_top').addClass('cg_hide');
        $('.cg_options_sortableContainer:nth-child(2) .cg_move_view_to_top').removeClass('cg_hide');
        $('.cg_options_sortableContainer:nth-child(3) .cg_move_view_to_top').removeClass('cg_hide');

        v = 0;

        $( ".cg_options_order" ).each(function( i ) {
            v++;
            $(this).empty();
            $(this).append('<u>'+v+'. Order</u>');
            $(this).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
            $(this).attr('id','cg_options_order'+v+'');


        });

        var sortableViewIndex = sortableView.index()+1;
        console.log(sortableViewIndex);
        //location.href = '#cg_options_order'+sortableViewIndex+'';
		var scrollTop = $('#cg_options_order'+sortableViewIndex+'').offset().top-50;
        $(window).scrollTop(scrollTop);
      //  $(window).scrollTop(50);


    });

    $(document).on('click', '.cg_move_view_to_bottom', function(e){
    	var sortableView = $(this).closest('.cg_options_sortableContainer');
        sortableView.insertAfter(sortableView.next('.cg_options_sortableContainer'));
        $('.cg_options_sortableContainer:first-child .cg_move_view_to_bottom, .cg_options_sortableContainer:nth-child(2) .cg_move_view_to_bottom').removeClass('cg_hide');
        $('.cg_options_sortableContainer:nth-child(3) .cg_move_view_to_bottom').addClass('cg_hide');

        $('.cg_options_sortableContainer:first-child .cg_move_view_to_top').addClass('cg_hide');
        $('.cg_options_sortableContainer:nth-child(2) .cg_move_view_to_top').removeClass('cg_hide');
        $('.cg_options_sortableContainer:nth-child(3) .cg_move_view_to_top').removeClass('cg_hide');

        v = 0;

        $( ".cg_options_order" ).each(function( i ) {
            v++;
            $(this).empty();
            $(this).append('<u>'+v+'. Order</u>');
            $(this).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
            $(this).attr('id','cg_options_order'+v+'');


        });

        var sortableViewIndex = sortableView.index()+1;
        console.log(sortableViewIndex);
        //location.href = '#cg_options_order'+sortableViewIndex+'';
        var scrollTop = $('#cg_options_order'+sortableViewIndex+'').offset().top-50;
        $(window).scrollTop(scrollTop);
        //  $(window).scrollTop(50);


    });

	// Visual form options here
	
	$(document).on('click', '#FormInputWidth', function(e){

		if($("#FormInputWidth").is( ":checked" )){
			$(".FormInputWidthExample").css("width","100%");
		}
		else{
			$(".FormInputWidthExample").css("width","auto");
		}

	});
	
	$(document).on('click', '#FormButtonWidth', function(e){
		
		if($("#FormButtonWidth").is( ":checked" )){		
			$(".FormButtonWidthExample").css("width","100%");
		}
		else{
			$(".FormButtonWidthExample").css("width","auto");
		}
	
	});	
	
	$(document).on('click', '#FormRoundBorder', function(e){
		
		if($("#FormRoundBorder").is( ":checked" )){		
			$(".FormInputWidthExample").css("border-radius","5px");
			$(".FormButtonWidthExample").css("border-radius","5px");
		}
		else{
			$(".FormInputWidthExample").css("border-radius","0%");
			$(".FormButtonWidthExample").css("border-radius","0%");
		}
	
	});	
	

	
	
	// Visual form options here --- END
	

	

	$(document).on('click', '#ThumbViewBorderColor', function(e){
		$(".color-picker").css("top",$("#ThumbViewBorderColor").offset().top+27);
	});
	$(document).on('click', '#HeightViewBorderColor', function(e){
		$(".color-picker").css("top",$("#HeightViewBorderColor").offset().top+27);
	});
	$(document).on('click', '#RowViewBorderColor', function(e){
		$(".color-picker").css("top",$("#RowViewBorderColor").offset().top+27);
	});
	$(document).on('click', '#GalleryBackgroundColor', function(e){
		$(".color-picker").css("top",$("#GalleryBackgroundColor").offset().top+27);
	});
	/*	$(document).on('click', '#FormButtonColor', function(e){
		$(".color-picker").css("top",$("#FormButtonColor").offset().top+27);
	});	*/

	

	var cgColorPickerSelector1 = new CP(document.querySelector('#ThumbViewBorderColor'));
	cgColorPickerSelector1.on("change", function(color) {		
		this.target.value = '#' + color;
	});
	var cgColorPickerSelector2 = new CP(document.querySelector('#HeightViewBorderColor'));
	cgColorPickerSelector2.on("change", function(color) {
		this.target.value = '#' + color;
	});
	var cgColorPickerSelector3 = new CP(document.querySelector('#RowViewBorderColor'));
	cgColorPickerSelector3.on("change", function(color) {		
		this.target.value = '#' + color;
	});
	var cgColorPickerSelector4 = new CP(document.querySelector('#GalleryBackgroundColor'));
	cgColorPickerSelector4.on("change", function(color) {		
		this.target.value = '#' + color;
	});
	
	/*var cgColorPickerSelector0 = new CP(document.querySelector('#FormButtonColor'));
	cgColorPickerSelector0.on("change", function(color) {		
		this.target.value = '#' + color;
		$(".FormButtonWidthExample").css("background-color","#"+color+"");
	});	*/
	
	
		
	
	// Changes saved fade out
	
	
	$("#cg_changes_saved").fadeOut(3000);
	
	

	
	
	// Changes saved fade out --- ENDE 

	
$( "#ThumbViewBorderColor" ).change(function() {
  	var opacityThumbView = $('#ThumbViewBorderColor').attr("data-opacity");
	$('#ThumbViewBorderColor').attr("name","ThumbViewBorderColor["+opacityThumbView+"]");
});

$( "#HeightViewBorderColor" ).change(function() {
  	var opacityHeightView = $('#HeightViewBorderColor').attr("data-opacity");
	$('#HeightViewBorderColor').attr("name","HeightViewBorderColor["+opacityHeightView+"]");
});

$( "#RowViewBorderColor" ).change(function() {
  	var opacityRowView = $('#RowViewBorderColor').attr("data-opacity");
	$('#RowViewBorderColor').attr("name","RowViewBorderColor["+opacityRowView+"]");
});
$( "#GalleryBackgroundColor" ).change(function() {
  	var opacityBackgroundColor = $('#GalleryBackgroundColor').attr("data-opacity");
	$('#GalleryBackgroundColor').attr("name","GalleryBackgroundColor["+opacityBackgroundColor+"]");
});


	
	
	$( "#cg_questionJPG" ).hover(function() {
   $('#cg_answerJPG').toggle();
    $(this).css('cursor','pointer');
});

	$( "#cg_questionPNG" ).hover(function() {
   $('#cg_answerPNG').toggle();
    $(this).css('cursor','pointer');
});

	$( "#cg_questionGIF" ).hover(function() {
   $('#cg_answerGIF').toggle();
    $(this).css('cursor','pointer');
});


var v=0;

var getContestEndTime = $("#getContestEndTime").val();

//alert(getContestEndTime);


if(getContestEndTime>0){
	//alert(1);
var getContestEndTime = new Date(getContestEndTime*1000);

var month = parseInt(getContestEndTime.getMonth());
	month = month+1;
	
var monthUS = month;

var day = parseInt(getContestEndTime.getDate());
	

	if(month<10){
		var monthUS = "0"+monthUS;
	}
		
	if(day<10){
	var day = "0"+day;
	}


	getContestEndTime = monthUS+"/"+day+"/"+getContestEndTime.getFullYear();
	

	$("#cg_datepicker").val(getContestEndTime);
	


}	

 $(function() {
    $( "#cg_datepicker" ).datepicker();	
  });
  
  $('#cg_datepicker').keydown(function() {
  return false;
});


// Check if end contest time is on or not 

	// Check gallery
if($("#ContestEnd").is( ":checked" )){

//$( "#ScaleWidthGalery" ).attr("disabled",true);
$("#cg_datepicker").prop("disabled",false);
}

else{
$( "#cg_datepicker" ).attr("disabled",true);
$( "#cg_datepicker" ).css({ 'background': '#e0e0e0' });

}


$("#ContestEnd").click(function(){



	if($("#ContestEnd").is( ":checked" )){
	
//$( "#ScaleWidthGalery" ).attr("disabled",true);
$("#cg_datepicker").prop("disabled",false);
$( "#cg_datepicker" ).css({ 'background': '#ffffff' });
	
	}
	
	else{

	$( "#cg_datepicker" ).attr("disabled",true);
	$( "#cg_datepicker" ).css({ 'background': '#e0e0e0' });


	}
	
});


// Check if end contest time is on or not --- END


// Check if voting is activated or not


if($("#AllowRating").is( ":checked" ) || $("#AllowRating2").is( ":checked" )){


$("#HideUntilVote").prop("disabled",false);
$("#VotesPerUser").prop("disabled",false);
$("#RatingOutGallery").prop("disabled",false);
$(".RatingPositionGallery").prop("disabled",false);
$("#IpBlock").prop("disabled",false);
$("#checkLogin").prop("disabled",false);
$("#ContestEnd").prop("disabled",false);
//$("#cg_datepicker").prop("disabled",false);
}

else{
$( "#HideUntilVote" ).attr("disabled",true);
$( "#HideUntilVote" ).css({ 'background': '#e0e0e0' });
$( "#VotesPerUser" ).attr("disabled",true);
$( "#VotesPerUser" ).css({ 'background': '#e0e0e0' });
$( "#RatingOutGallery" ).attr("disabled",true);
$( "#RatingOutGallery" ).css({ 'background': '#e0e0e0' });
$( ".RatingPositionGallery" ).attr("disabled",true);
$( ".RatingPositionGallery" ).css({ 'background': '#e0e0e0' });
$( "#IpBlock" ).attr("disabled",true);
$( "#IpBlock" ).css({ 'background': '#e0e0e0' });
$( "#checkLogin" ).attr("disabled",true);
$( "#checkLogin" ).css({ 'background': '#e0e0e0' });
$( "#ContestEnd" ).attr("disabled",true);
$( "#ContestEnd" ).css({ 'background': '#e0e0e0' });
//$( "#cg_datepicker" ).attr("disabled",true);
//$( "#cg_datepicker" ).css({ 'background': '#e0e0e0' });
}


$("#AllowRating").click(function(){

	if($("#AllowRating").is( ":checked" )){	

$("#HideUntilVote").prop("disabled",false);
$("#VotesPerUser").prop("disabled",false);
$("#RatingOutGallery").prop("disabled",false);
$(".RatingPositionGallery").prop("disabled",false);
$( "#HideUntilVote" ).css({ 'background': '#ffffff' });
$( "#VotesPerUser" ).css({ 'background': '#ffffff' });
$( "#RatingOutGallery" ).css({ 'background': '#ffffff' });
$( ".RatingPositionGallery" ).css({ 'background': '#ffffff' });
$("#IpBlock").prop("disabled",false);
$( "#IpBlock" ).css({ 'background': '#ffffff' });
$("#checkLogin").prop("disabled",false);
$( "#checkLogin" ).css({ 'background': '#ffffff' });
$("#ContestEnd").prop("disabled",false);
$( "#ContestEnd" ).css({ 'background': '#ffffff' });


//$( "#AllowRating2" ).prop("checked",false);
if($("#AllowRating").is( ":checked" )){$( "#AllowRating2" ).prop("checked",false);}
	
	
	}
	
	else{

	
	$( "#HideUntilVote" ).attr("disabled",true);
	$( "#HideUntilVote" ).css({ 'background': '#e0e0e0' });
	$( "#VotesPerUser" ).attr("disabled",true);
	$( "#VotesPerUser" ).css({ 'background': '#e0e0e0' });
	$( "#RatingOutGallery" ).attr("disabled",true);
	$( "#RatingOutGallery" ).css({ 'background': '#e0e0e0' });
	$( ".RatingPositionGallery" ).attr("disabled",true);
	$( ".RatingPositionGallery" ).css({ 'background': '#e0e0e0' });
		$( "#IpBlock" ).attr("disabled",true);
	$( "#IpBlock" ).css({ 'background': '#e0e0e0' });
		$( "#checkLogin" ).attr("disabled",true);
	$( "#checkLogin" ).css({ 'background': '#e0e0e0' });
			$( "#ContestEnd" ).attr("disabled",true);
	$( "#ContestEnd" ).css({ 'background': '#e0e0e0' });
	
		//if($("#AllowRating").is( ":checked" )){	
		//$( "#AllowRating2" ).prop("checked",true);
		//}
	
	}	
});


$("#AllowRating2").click(function(){

	if($("#AllowRating2").is( ":checked" )){	

$("#HideUntilVote").prop("disabled",false);
$( "#HideUntilVote" ).css({ 'background': '#ffffff' });
$("#VotesPerUser").prop("disabled",false);
$( "#VotesPerUser" ).css({ 'background': '#ffffff' });
$("#RatingOutGallery").prop("disabled",false);
$( "#RatingOutGallery" ).css({ 'background': '#ffffff' });
$(".RatingPositionGallery").prop("disabled",false);
$( ".RatingPositionGallery" ).css({ 'background': '#ffffff' });
$("#IpBlock").prop("disabled",false);
$( "#IpBlock" ).css({ 'background': '#ffffff' });
$("#checkLogin").prop("disabled",false);
$( "#checkLogin" ).css({ 'background': '#ffffff' });
$("#ContestEnd").prop("disabled",false);
$( "#ContestEnd" ).css({ 'background': '#ffffff' });


if($("#AllowRating2").is( ":checked" )){$( "#AllowRating" ).prop("checked",false);}



//$( "#AllowRating" ).prop("checked",false);	
	}
	
	else{

	$( "#HideUntilVote" ).attr("disabled",true);
	$( "#HideUntilVote" ).css({ 'background': '#e0e0e0' });
	$( "#VotesPerUser" ).attr("disabled",true);
	$( "#VotesPerUser" ).css({ 'background': '#e0e0e0' });
	$( "#RatingOutGallery" ).attr("disabled",true);
	$( "#RatingOutGallery" ).css({ 'background': '#e0e0e0' });
	$( ".RatingPositionGallery" ).attr("disabled",true);
	$( ".RatingPositionGallery" ).css({ 'background': '#e0e0e0' });
	$( "#IpBlock" ).attr("disabled",true);
	$( "#IpBlock" ).css({ 'background': '#e0e0e0' });
	$( "#checkLogin" ).attr("disabled",true);
	$( "#checkLogin" ).css({ 'background': '#e0e0e0' });
	$( "#ContestEnd" ).attr("disabled",true);
	$( "#ContestEnd" ).css({ 'background': '#e0e0e0' });
	
	//$( "#AllowRating" ).prop("checked",true);
	}
	
});



// Check if voting is activated or not --- END


// Check if facebook like button is activated or not


if($("#FbLike").is( ":checked" )){


$("#FbLikeGallery").prop("disabled",false);
$("#FbLikeGalleryVote").prop("disabled",false);
$("#FbLikeGalleryVote").prop("disabled",false);
$("#FbLikeGoToGalleryLink").prop("disabled",false);
$( "#FbLikeGoToGalleryLink" ).css({ 'background': '#fff' });
}

else{
$("#FbLikeGoToGalleryLink").prop("disabled",true);
$( "#FbLikeGoToGalleryLink" ).css({ 'background': '#e0e0e0' });
$( "#FbLikeGallery" ).attr("disabled",true);
$( "#FbLikeGallery" ).css({ 'background': '#e0e0e0' });
$( "#FbLikeGalleryVote" ).attr("disabled",true);
$( "#FbLikeGalleryVote" ).css({ 'background': '#e0e0e0' });

}


$("#FbLike").click(function(){



	if($("#FbLike").is( ":checked" )){
	

$("#FbLikeGallery").prop("disabled",false);
$( "#FbLikeGallery" ).css({ 'background': '#ffffff' });
$("#FbLikeGalleryVote").prop("disabled",false);
$( "#FbLikeGalleryVote" ).css({ 'background': '#ffffff' });
$("#FbLikeGoToGalleryLink").prop("disabled",false);
$( "#FbLikeGoToGalleryLink" ).css({ 'background': '#ffffff' });
//$("#FbLikeGalleryVote").prop("disabled",false);
//$( "#FbLikeGalleryVote" ).css({ 'background': '#ffffff' });
	
	}
	
	else{

	$( "#FbLikeGallery" ).attr("disabled",true);
	$( "#FbLikeGallery" ).css({ 'background': '#e0e0e0' });
	$( "#FbLikeGalleryVote" ).attr("disabled",true);
	$( "#FbLikeGalleryVote" ).css({ 'background': '#e0e0e0' });
	$("#FbLikeGoToGalleryLink").prop("disabled",true);
	$( "#FbLikeGoToGalleryLink" ).css({ 'background': '#e0e0e0' });
	
	}
	
});

		//Prüfen ob Fb Like out of Gallery aktiviert ist oder nicht

		if($("#FbLikeGallery").is( ":checked" )){


		$("#FbLikeGalleryVote").prop("disabled",false);

		}

		else{
		$( "#FbLikeGalleryVote" ).attr("disabled",true);
		$( "#FbLikeGalleryVote" ).css({ 'background': '#e0e0e0' });

		}


		$("#FbLikeGallery").click(function(){



			if($("#FbLikeGallery").is( ":checked" )){
			

		$("#FbLikeGalleryVote").prop("disabled",false);
		$( "#FbLikeGalleryVote" ).css({ 'background': '#ffffff' });
			
			}
			
			else{

			$( "#FbLikeGalleryVote" ).attr("disabled",true);
			$( "#FbLikeGalleryVote" ).css({ 'background': '#e0e0e0' });
			
			}
			
		});
		
		//Prüfen ob Fb Like out of Gallery aktiviert ist oder nicht --- ENDE

// Check if facebook like button is activated or not --- END



// Check if commenting is activated or not


if($("#AllowComments").is( ":checked" )){


$("#CommentsOutGallery").prop("disabled",false);
$(".CommentPositionGallery").prop("disabled",false);

}

else{
$( "#CommentsOutGallery" ).attr("disabled",true);
$( "#CommentsOutGallery" ).css({ 'background': '#e0e0e0' });
$( ".CommentPositionGallery" ).attr("disabled",true);
$( ".CommentPositionGallery" ).css({ 'background': '#e0e0e0' });

}


$("#AllowComments").click(function(){



	if($("#AllowComments").is( ":checked" )){
	

$("#CommentsOutGallery").prop("disabled",false);
$( "#CommentsOutGallery" ).css({ 'background': '#ffffff' });
$(".CommentPositionGallery").prop("disabled",false);
$( ".CommentPositionGallery" ).css({ 'background': '#ffffff' });
	
	}
	
	else{

	$( "#CommentsOutGallery" ).attr("disabled",true);
	$( "#CommentsOutGallery" ).css({ 'background': '#e0e0e0' });
	$( ".CommentPositionGallery" ).attr("disabled",true);
	$( ".CommentPositionGallery" ).css({ 'background': '#e0e0e0' });
	
	}
	
});


// Check if commenting is activated or not --- END


// Check if sorting is activated or not

/*
if($("#AllowSort").is( ":checked" )){


$("#RandomSort").prop("disabled",false);


}

else{
$( "#RandomSort" ).attr("disabled",true);
$( "#RandomSort" ).css({ 'background': '#e0e0e0' });


}*/

/*
$("#AllowSort").click(function(){
	if($("#RandomSort").is( ":checked" )){
		$( "#RandomSort" ).prop("checked",false);		
	}	
	else{
		//$( "#RandomSort" ).prop("checked",true);	
	}	
});

$("#RandomSort").click(function(){
	if($("#AllowSort").is( ":checked" )){
		$( "#AllowSort" ).prop("checked",false);		
	}	
	else{
		//$( "#AllowSort" ).prop("checked",true);	
	}	
});*/


// Check if sorting is activated or not --- END


// Check if slider is activated or not


if($("#AllowGalleryScript").is( ":checked" )){

$("#ShowAlwaysInfoSlider").prop("disabled",false);
$( "#ShowAlwaysInfoSlider" ).css({ 'background': '#ffffff' });

$("#OnlyGalleryView").prop("disabled",false);
$( "#OnlyGalleryView" ).css({ 'background': '#ffffff' });

$("#FullSizeImageOutGallery").prop("disabled",false);
$( "#FullSizeImageOutGallery" ).css({ 'background': '#ffffff' });

$("#SinglePicView").prop("disabled",false);
$( "#SinglePicView" ).css({ 'background': '#ffffff' });

$("#FullSizeImageOutGalleryNewTab").prop("disabled",true);
$( "#FullSizeImageOutGalleryNewTab" ).css({ 'background': '#e0e0e0' });

$("#ScaleWidthGalery").prop("disabled",true);
$( "#ScaleWidthGalery" ).css({ 'background': '#e0e0e0' });

$("#ScaleSizesGalery").prop("disabled",true);
$( "#ScaleSizesGalery" ).css({ 'background': '#e0e0e0' });

$("#ScaleSizesGalery1").prop("disabled",true);
$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });

$("#ScaleSizesGalery2").prop("disabled",true);
$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });

$("#FullSize").prop("disabled",true);
$( "#FullSize" ).css({ 'background': '#e0e0e0' });

}

else{



}


$("#AllowGalleryScript").click(function(){



	if($("#AllowGalleryScript").is( ":checked" )){
	
		$( "#ShowAlwaysInfoSlider" ).prop("checked",false);
		$("#ShowAlwaysInfoSlider").prop("disabled",false);
		$( "#ShowAlwaysInfoSlider" ).css({ 'background': '#ffffff' });
		$( "#OnlyGalleryView" ).prop("checked",false);
		$("#OnlyGalleryView").prop("disabled",false);
		$( "#OnlyGalleryView" ).css({ 'background': '#ffffff' });
		$( "#FullSizeImageOutGallery" ).attr("disabled",false);
		$( "#FullSizeImageOutGallery" ).prop("checked",false);
		$( "#FullSizeImageOutGallery" ).css({ 'background': '#ffffff' });
		$( "#SinglePicView" ).attr("disabled",false);
		$( "#SinglePicView" ).prop("checked",false);
		$( "#SinglePicView" ).css({ 'background': '#ffffff' });
		$( "#HideInfo" ).prop("disabled",false);
		$( "#HideInfo" ).css({ 'background': '#ffffff' });


		$( "#ScaleWidthGalery" ).attr("disabled",true);
		$( "#ScaleWidthGalery" ).css({ 'background': '#e0e0e0' });
		$( "#FullSizeImageOutGalleryNewTab" ).attr("disabled",true);
		$( "#FullSizeImageOutGalleryNewTab" ).css({ 'background': '#e0e0e0' });
		$( "#ScaleSizesGalery" ).attr("disabled",true);
		$( "#ScaleSizesGalery" ).css({ 'background': '#e0e0e0' });
		$( "#ScaleSizesGalery1" ).attr("disabled",true);
		$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
		$( "#ScaleSizesGalery2" ).attr("disabled",true);
		$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });
		$( "#FullSize" ).attr("disabled",true);
		$( "#FullSize" ).css({ 'background': '#e0e0e0' });

		$("#OriginalSourceLinkInSlider").prop("disabled",false);
		$( "#OriginalSourceLinkInSlider" ).css({ 'background': '#ffffff' });
		$("#PreviewInSlider").prop("disabled",false);
		$( "#PreviewInSlider" ).css({ 'background': '#ffffff' });
	
	}
	
	else{
		
	var i = 0;
	
	if ($('#FullSizeImageOutGallery').is(":checked")) {i = 0;} else{i++;}
	if ($('#SinglePicView').is(":checked")) {i = 0;} else{i++;}
	if ($('#OnlyGalleryView').is(":checked")) {i = 0;} else{i++;}
	//alert(i);
	if(i == 3){
		//alert(i);
		i = 0;
		
		$( "#AllowGalleryScript" ).prop( "checked", true );
		$("#AllowGalleryScript").prop("disabled",false);
		$( "#AllowGalleryScript" ).css({ 'background': '#ffffff' });

		
		
		}//Deaktivierung des Hackens nicht möglich
	else{
		

		
	}
	}
	
});


// Check if slider is activated or not --- END


// Check if Full Size Image is activated or not


if($("#FullSizeImageOutGallery").is( ":checked" )){



$("#FullSizeImageOutGallery").prop("disabled",false);
$( "#FullSizeImageOutGallery" ).css({ 'background': '#ffffff' });
$("#OnlyGalleryView").prop("disabled",false);
$( "#OnlyGalleryView" ).css({ 'background': '#ffffff' });
$("#AllowGalleryScript").prop("disabled",false);
$( "#AllowGalleryScript" ).css({ 'background': '#ffffff' });
$( "#HideInfo" ).prop("disabled",true);
$( "#HideInfo" ).css({ 'background': '#e0e0e0' });
$("#SinglePicView").prop("disabled",false);
$( "#SinglePicView" ).css({ 'background': '#ffffff' });
$("#FullSizeImageOutGalleryNewTab").prop("disabled",false);
$( "#FullSizeImageOutGalleryNewTab" ).css({ 'background': '#ffffff' });
$("#ShowAlwaysInfoSlider").prop("disabled",true);
$( "#ShowAlwaysInfoSlider" ).css({ 'background': '#e0e0e0' });
$("#ScaleWidthGalery").prop("disabled",true);
$( "#ScaleWidthGalery" ).css({ 'background': '#e0e0e0' });
$("#ScaleSizesGalery").prop("disabled",true);
$( "#ScaleSizesGalery" ).css({ 'background': '#e0e0e0' });
$("#ScaleSizesGalery1").prop("disabled",true);
$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
$("#ScaleSizesGalery2").prop("disabled",true);
$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });
$("#FullSize").prop("disabled",true);
$( "#FullSize" ).css({ 'background': '#e0e0e0' });

$("#OriginalSourceLinkInSlider").prop("disabled",true);
$( "#OriginalSourceLinkInSlider" ).css({ 'background': '#e0e0e0' });
$("#PreviewInSlider").prop("disabled",true);
$( "#PreviewInSlider" ).css({ 'background': '#e0e0e0' });



}

else{



}


$("#FullSizeImageOutGallery").click(function(){



	if($("#FullSizeImageOutGallery").is( ":checked" )){
$("#OnlyGalleryView").prop("checked",false);	
$( "#OnlyGalleryView" ).attr("disabled",false);
$( "#OnlyGalleryView" ).css({ 'background': '#ffffff' });
$( "#FullSizeImageOutGalleryNewTab" ).attr("disabled",false);
$( "#FullSizeImageOutGalleryNewTab" ).css({ 'background': '#ffffff' });
$("#AllowGalleryScript").prop("disabled",false);
$("#AllowGalleryScript").prop("checked",false);
$( "#AllowGalleryScript" ).css({ 'background': '#ffffff' });
$( "#HideInfo" ).prop("disabled",true);
$( "#HideInfo" ).css({ 'background': '#e0e0e0' });
$( "#SinglePicView" ).attr("disabled",false);
$( "#SinglePicView" ).attr("checked",false);
$( "#SinglePicView" ).css({ 'background': '#ffffff' });


$("#ScaleWidthGalery").attr("disabled",true);
$( "#ScaleWidthGalery" ).css({ 'background': '#e0e0e0' });
$("#ShowAlwaysInfoSlider").attr("disabled",true);
$( "#ShowAlwaysInfoSlider" ).css({ 'background': '#e0e0e0' });
$( "#ScaleSizesGalery" ).attr("disabled",true);
$( "#ScaleSizesGalery" ).css({ 'background': '#e0e0e0' });
$( "#ScaleSizesGalery1" ).attr("disabled",true);
$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
$( "#ScaleSizesGalery2" ).attr("disabled",true);
$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });
$( "#FullSize" ).attr("disabled",true);
$( "#FullSize" ).css({ 'background': '#e0e0e0' });

        $("#OriginalSourceLinkInSlider").prop("disabled",true);
        $( "#OriginalSourceLinkInSlider" ).css({ 'background': '#e0e0e0' });
        $("#PreviewInSlider").prop("disabled",true);
        $( "#PreviewInSlider" ).css({ 'background': '#e0e0e0' });

	
	}
	
	else{
		
	var i = 0;
	
	if ($('#AllowGalleryScript').is(":checked")) {i = 0;} else{i++;}
	if ($('#SinglePicView').is(":checked")) {i = 0;} else{i++;}
	if ($('#OnlyGalleryView').is(":checked")) {i = 0;} else{i++;}
	//alert(i);
	if(i == 3){
		//alert(i);
		i = 0;
		
		$( "#FullSizeImageOutGallery" ).prop( "checked", true );
		$("#FullSizeImageOutGallery").prop("disabled",false);
		$( "#FullSizeImageOutGallery" ).css({ 'background': '#ffffff' });
		
		
		}//Deaktivierung des Hackens nicht möglich
	else{	

	
	}
	
	}
	
});


// Check if Full Size Image is activated or not --- END





// Check if Single pic Image is activated or not --- END


if($("#SinglePicView").is( ":checked" )){



$("#FullSizeImageOutGallery").prop("disabled",false);
$( "#FullSizeImageOutGallery" ).css({ 'background': '#ffffff' });
$("#OnlyGalleryView").prop("disabled",false);
$( "#OnlyGalleryView" ).css({ 'background': '#ffffff' });
$("#AllowGalleryScript").prop("disabled",false);
$( "#AllowGalleryScript" ).css({ 'background': '#ffffff' });
$( "#HideInfo" ).prop("disabled",true);
$( "#HideInfo" ).css({ 'background': '#e0e0e0' });


$( "#ShowAlwaysInfoSlider" ).attr("disabled",true);
$( "#ShowAlwaysInfoSlider" ).css({ 'background': '#e0e0e0' });

$( "#FullSizeImageOutGalleryNewTab" ).attr("disabled",true);
$( "#FullSizeImageOutGalleryNewTab" ).css({ 'background': '#e0e0e0' });


$("#ScaleWidthGalery").prop("disabled",false);
$( "#ScaleWidthGalery" ).css({ 'background': '#ffffff' });
$("#ScaleSizesGalery").prop("disabled",false);
$( "#ScaleSizesGalery" ).css({ 'background': '#ffffff' });
$("#ScaleSizesGalery1").prop("disabled",false);
$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
$("#ScaleSizesGalery2").prop("disabled",false);
$( "#ScaleSizesGalery2" ).css({ 'background': '#ffffff' });
$("#FullSize").prop("disabled",false);
$( "#FullSize" ).css({ 'background': '#ffffff' });

    $("#OriginalSourceLinkInSlider").prop("disabled",true);
    $( "#OriginalSourceLinkInSlider" ).css({ 'background': '#e0e0e0' });
    $("#PreviewInSlider").prop("disabled",true);
    $( "#PreviewInSlider" ).css({ 'background': '#e0e0e0' });



}

else{



}


$("#SinglePicView").click(function(){



	if($("#SinglePicView").is( ":checked" )){
		
		
		
	
$( "#FullSizeImageOutGallery" ).attr("disabled",false);
$("#FullSizeImageOutGallery").prop("checked",false);
$( "#FullSizeImageOutGallery" ).css({ 'background': '#ffffff' });

$( "#OnlyGalleryView" ).attr("disabled",false);
$("#OnlyGalleryView").prop("checked",false);
$( "#OnlyGalleryView" ).css({ 'background': '#ffffff' });



$("#AllowGalleryScript").attr("disabled",false);
$("#AllowGalleryScript").prop("checked",false);
$( "#AllowGalleryScript" ).css({ 'background': '#ffffff' });
$( "#HideInfo" ).prop("disabled",true);
$( "#HideInfo" ).css({ 'background': '#e0e0e0' });

$( "#ScaleWidthGalery" ).attr("disabled",false);
$( "#ScaleWidthGalery" ).css({ 'background': '#ffffff' });






$( "#ScaleSizesGalery" ).attr("disabled",false);
$( "#ScaleSizesGalery" ).css({ 'background': '#ffffff' });
$( "#ScaleSizesGalery1" ).attr("disabled",false);
$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
$( "#ScaleSizesGalery2" ).attr("disabled",false);
$( "#ScaleSizesGalery2" ).css({ 'background': '#ffffff' });
$( "#FullSize" ).attr("disabled",false);
$( "#FullSize" ).css({ 'background': '#ffffff' });



$("#ShowAlwaysInfoSlider").prop("disabled",true);
$( "#ShowAlwaysInfoSlider" ).css({ 'background': '#e0e0e0' });
$("#FullSizeImageOutGalleryNewTab").prop("disabled",true);
$( "#FullSizeImageOutGalleryNewTab" ).css({ 'background': '#e0e0e0' });

$("#OriginalSourceLinkInSlider").prop("disabled",true);
$( "#OriginalSourceLinkInSlider" ).css({ 'background': '#e0e0e0' });
$("#PreviewInSlider").prop("disabled",true);
$( "#PreviewInSlider" ).css({ 'background': '#e0e0e0' });



if($("#ScaleWidthGalery").is( ":checked" )){
	
$( "#ScaleSizesGalery2" ).attr("disabled",true);
$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });	
	
}	
	
	
	
	}
	
	else{
		
	var i = 0;
	
	if ($('#AllowGalleryScript').is(":checked")) {i = 0;} else{i++;}
	if ($('#FullSizeImageOutGallery').is(":checked")) {i = 0;} else{i++;}
	if ($('#OnlyGalleryView').is(":checked")) {i = 0;} else{i++;}
	//alert(i);
	if(i == 3){
		//alert(i);
		i = 0;
		
		$( "#SinglePicView" ).prop( "checked", true );
		$("#SinglePicView").prop("disabled",false);
		$( "#SinglePicView" ).css({ 'background': '#ffffff' });
		
		
		}//Deaktivierung des Hackens nicht möglich
	
	
	else{

	
	}
	
	}
	
});


// Check if Single pic Image is activated or not --- END



// Check if only gallery view is activated or not


if($("#OnlyGalleryView").is( ":checked" )){


$("#OnlyGalleryView").prop("disabled",false);
$( "#OnlyGalleryView" ).css({ 'background': '#ffffff' });
$("#FullSizeImageOutGallery").prop("disabled",false);
$( "#FullSizeImageOutGallery" ).css({ 'background': '#ffffff' });
$("#AllowGalleryScript").prop("disabled",false);
$( "#AllowGalleryScript" ).css({ 'background': '#ffffff' });
$( "#HideInfo" ).prop("disabled",true);
$( "#HideInfo" ).css({ 'background': '#e0e0e0' });
$("#SinglePicView").prop("disabled",false);
$( "#SinglePicView" ).css({ 'background': '#ffffff' });
$("#FullSizeImageOutGalleryNewTab").prop("disabled",false);
$( "#FullSizeImageOutGalleryNewTab" ).css({ 'background': '#ffffff' });
$("#ShowAlwaysInfoSlider").prop("disabled",true);
$( "#ShowAlwaysInfoSlider" ).css({ 'background': '#e0e0e0' });
$("#ScaleWidthGalery").prop("disabled",true);
$( "#ScaleWidthGalery" ).css({ 'background': '#e0e0e0' });
$("#ScaleSizesGalery").prop("disabled",true);
$( "#ScaleSizesGalery" ).css({ 'background': '#e0e0e0' });
$("#ScaleSizesGalery1").prop("disabled",true);
$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
$("#ScaleSizesGalery2").prop("disabled",true);
$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });
$("#FullSize").prop("disabled",true);
$( "#FullSize" ).css({ 'background': '#e0e0e0' });



}

else{



}


$("#OnlyGalleryView").click(function(){



	if($("#OnlyGalleryView").is( ":checked" )){

	
$("#FullSizeImageOutGallery").attr("checked",false);
$( "#FullSizeImageOutGallery" ).attr("disabled",false);
$( "#FullSizeImageOutGallery" ).css({ 'background': '#ffffff' });

$("#AllowGalleryScript").attr("disabled",false);
$("#AllowGalleryScript").attr("checked",false);
$( "#AllowGalleryScript" ).css({ 'background': '#ffffff' });
$( "#HideInfo" ).prop("disabled",true);
$( "#HideInfo" ).css({ 'background': '#e0e0e0' });
$( "#SinglePicView" ).attr("disabled",false);
$( "#SinglePicView" ).attr("checked",false);
$( "#SinglePicView" ).css({ 'background': '#ffffff' });


$("#FullSizeImageOutGalleryNewTab").prop("disabled",true);
$( "#FullSizeImageOutGalleryNewTab" ).css({ 'background': '#e0e0e0' });
$("#ScaleWidthGalery").prop("disabled",true);
$( "#ScaleWidthGalery" ).css({ 'background': '#e0e0e0' });
$("#ShowAlwaysInfoSlider").prop("disabled",true);
$( "#ShowAlwaysInfoSlider" ).css({ 'background': '#e0e0e0' });
$( "#ScaleSizesGalery" ).attr("disabled",true);
$( "#ScaleSizesGalery" ).css({ 'background': '#e0e0e0' });
$( "#ScaleSizesGalery1" ).attr("disabled",true);
$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
$( "#ScaleSizesGalery2" ).attr("disabled",true);
$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });
$( "#FullSize" ).attr("disabled",true);
$( "#FullSize" ).css({ 'background': '#e0e0e0' });
	
	}
	
	else{
		
	var i = 0;
	
	if ($('#AllowGalleryScript').is(":checked")) {i = 0;} else{i++;}
	if ($('#SinglePicView').is(":checked")) {i = 0;} else{i++;}
	if ($('#FullSizeImageOutGallery').is(":checked")) {i = 0;} else{i++;}

	//alert(i);
	if(i == 3){
		//alert(i);
		i = 0;
		
		$( "#OnlyGalleryView" ).prop( "checked", true );
		$("#OnlyGalleryView").prop("disabled",false);
		$( "#OnlyGalleryView" ).css({ 'background': '#ffffff' });
		
		
		}//Deaktivierung des Hackens nicht möglich
	else{	

	
	}
	
	}
	
});


// Check if only gallery view is activated or not --- END







$(function() {
/*		$( "#cg_options_sortable" ).sortable({cursor: "move", placeholder: "ui-state-highlight", stop: function( event, ui ) {

	if(document.readyState === "complete"){

		$( ".cg_options_sortableDiv" ).each(function( i ) {
			
			v++;
			  
			$(this).find('.cg_options_order').contents().filter(function(){ return this.nodeType == 1; }).remove();
			
			$(this).append('<p class="cg_options_order"><u>'+v+'. order</u></p>');
						
			
					  
			   });  
			   
			   v = 0;
			   
			   }
			   
	   }	
		
		});*/
//$('#cg_options_sortable').css('cursor', 'move');
});



// Allow only to press numbers as keys in input boxes

  //called when key is pressed in textbox
  $("#ScaleSizesGalery1, #ScaleSizesGalery2, #DistancePicsV, #DistancePicsV, #PicsInRow, #PicsPerSite,#ThumbViewBorderRadius,#RowViewBorderRadius,#HeightViewBorderRadius,#HeightViewSpaceHeight,#WidthThumb,"+
  "#PostMaxMB, #VotesPerUser, #BulkUploadQuantity,#BulkUploadMinQuantity, #DistancePics, #MaxResJPGwidth, #MaxResJPGheight, #MaxResPNGwidth, #MaxResPNGheight, #MaxResGIFwidth, #MaxResGIFheight, #cg_row_look_border_width,#cg_height_look_border_width,#HeightViewBorderWidth").keypress(function (e) {
     //if the letter is not digit then display error and don't type anything
     if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        //display error message
        $("#cg_options_errmsg").html("Only numbers are allowed").show().fadeOut("slow");
               return false;
    }
   });


// Allow only to press numbers as keys in input boxes --- END

// Allow only to choose color not to put color with keys
/*$("#cg_row_look_border_color,#HeightViewBorderColor,#cg_thumb_look_border_color").keypress(function (evt) {

  var keycode = evt.charCode || evt.keyCode;
  if (keycode  == 13) { //Enter key's keycode
    return false;
  }
});*/


// Allow only to choose color not to put color with keys --- END


// Slider Ansicht erlauben, normale Ansicht wird deaktiviert
/*
$("#AllowGalleryScript").click(function(){



	if($("#AllowGalleryScript").is( ":checked" )){
	
	$("#ScaleWidthGalery").prop("disabled",true);
	$("#ScaleSizesGalery").prop("disabled",true);
	$("#FullSize").prop("disabled",true);
	$( "#ScaleSizesGalery1" ).attr("disabled",true);
	$( "#ScaleSizesGalery2" ).attr("disabled",true);
	$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
	$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });
	
	}
	
	else{

	$("#ScaleWidthGalery").prop("disabled",false);
	$("#ScaleSizesGalery").prop("disabled",false);
	$("#FullSize").prop("disabled",false);
	$( "#ScaleSizesGalery1" ).attr("disabled",true);
	$( "#ScaleSizesGalery2" ).attr("disabled",true);
	$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
	$( "#ScaleSizesGalery2" ).css({ 'background': '#ffffff' });
	
	}
	
});*/


// Slider Ansicht erlauben, normale Ansicht wird deaktiviert --- ENDE





  
  
 // Check input when site is load

		
	// Check gallery
	
if($("#ScaleSizesGalery").is( ":checked" )){

//$( "#ScaleWidthGalery" ).attr("disabled",true);

if($("#SinglePicView").is( ":checked" )){$("#ScaleWidthGalery").prop("disabled",false);}
else{}

}

else{
$( "#ScaleSizesGalery1" ).attr("disabled",true);
$( "#ScaleSizesGalery2" ).attr("disabled",true);
$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });

}

if($("#ScaleWidthGalery").is( ":checked" )){
//$( "#ScaleSizesGalery" ).attr("disabled",true);
$( "#ScaleSizesGalery2" ).attr("disabled",true);
$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });

}

if($("#ScaleWidthGalery").is( ":checked" )){

if($("#SinglePicView").is( ":checked" )){
$( "#ScaleSizesGalery1" ).attr("disabled",false);
$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
}

else{}

}


		// Check gallery END

// Check input when site is load END




// Click input checkboxes



	// Check gallery
$("#ScaleSizesGalery").click(function(){



	if($("#ScaleSizesGalery").is( ":checked" )){
	
	$("#ScaleWidthGalery").prop("checked",false);
	$( "#ScaleSizesGalery1" ).attr("disabled",false);
	$( "#ScaleSizesGalery2" ).attr("disabled",false);
	$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
	$( "#ScaleSizesGalery2" ).css({ 'background': '#ffffff' });
	
	}
	
	else{

	$("#ScaleWidthGalery").prop("disabled",false);
	$( "#ScaleSizesGalery1" ).attr("disabled",true);
	$( "#ScaleSizesGalery2" ).attr("disabled",true);
	$( "#ScaleSizesGalery1" ).css({ 'background': '#e0e0e0' });
	$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });
	
		if($("#ScaleWidthGalery").is( ":checked" )){}
		else{
		$( "#ScaleWidthGalery" ).prop("checked",true);
		$( "#ScaleSizesGalery1" ).attr("disabled",false);
		$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
		}
	
	}
	
});

$("#ScaleWidthGalery").click(function(){

	if($("#ScaleWidthGalery").is( ":checked" )){
	
	$("#ScaleSizesGalery").prop("checked",false);
	$("#ScaleSizesGalery1").prop("disabled",false);
	$("#ScaleSizesGalery2").prop("disabled",true);
	$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
	$( "#ScaleSizesGalery2" ).css({ 'background': '#e0e0e0' });

	
	}
	
	else{

	$( "#ScaleSizesGalery" ).prop("checked",true);
	$("#ScaleSizesGalery").prop("disabled",false);
	$("#ScaleSizesGalery1").prop("disabled",false);
	$("#ScaleSizesGalery2").prop("disabled",false);
	$( "#ScaleSizesGalery2" ).css({ 'background': '#ffffff' });
	$( "#ScaleSizesGalery1" ).css({ 'background': '#ffffff' });
	
	}
	
});

	// Check gallery END
	
	
	
// Check upload size


	
	$("#ActivatePostMaxMB").click(function(){

		if($("#ActivatePostMaxMB").is( ":checked" )){
		
			$("#PostMaxMB").attr("disabled",false);
			$("#PostMaxMB").css({ 'background': '#ffffff' });
		
		//$( ".checkRESjpg" ).remove();
		//$( "#maxJPGresCheck" ).remove();
		
		}
		
		else{
		
		//var maxRESval = $("#maxJPGres").val();
		
		//$('.maxJPGres').append('<input type="hidden" value="'+maxRESval+'" name="maxRes[]" id="maxJPGresCheck" >');
		
			$("#PostMaxMB").attr("disabled",true);
			$("#PostMaxMB").css({ 'background': '#e0e0e0' });
		
		
		//$( "#checkRESjpg" ).append('<input type="hidden" value="off" name="maxRes[]" class="checkRESjpg" >');
		
		}
		
	});
	
	
	$("#ActivateBulkUpload").click(function(){

		if($("#ActivateBulkUpload").is( ":checked" )){
		
			$("#BulkUploadQuantity").attr("disabled",false);
			$("#BulkUploadQuantity").css({ 'background': '#ffffff' });
			$("#BulkUploadMinQuantity").attr("disabled",false);
			$("#BulkUploadMinQuantity").css({ 'background': '#ffffff' });
		
		//$( ".checkRESjpg" ).remove();
		//$( "#maxJPGresCheck" ).remove();
		
		}
		
		else{
		
		//var maxRESval = $("#maxJPGres").val();
		
		//$('.maxJPGres').append('<input type="hidden" value="'+maxRESval+'" name="maxRes[]" id="maxJPGresCheck" >');
		
			$("#BulkUploadQuantity").attr("disabled",true);
			$("#BulkUploadQuantity").css({ 'background': '#e0e0e0' });
			$("#BulkUploadMinQuantity").attr("disabled",true);
			$("#BulkUploadMinQuantity").css({ 'background': '#e0e0e0' });
		
		
		//$( "#checkRESjpg" ).append('<input type="hidden" value="off" name="maxRes[]" class="checkRESjpg" >');
		
		}
		
	});

	
// Check upload size --- END	
	
	
	
// Check resolution

//JPG
$("#allowRESjpg").click(function(){

	if($("#allowRESjpg").is( ":checked" )){
	
	$("#MaxResJPGwidth").attr("disabled",false);
	$("#MaxResJPGwidth").css({ 'background': '#ffffff' });
	$("#MaxResJPGheight").attr("disabled",false);
	$("#MaxResJPGheight").css({ 'background': '#ffffff' });
	
	
	//$( ".checkRESjpg" ).remove();
	//$( "#maxJPGresCheck" ).remove();
	
	}
	
	else{
	
	//var maxRESval = $("#maxJPGres").val();
	
	//$('.maxJPGres').append('<input type="hidden" value="'+maxRESval+'" name="maxRes[]" id="maxJPGresCheck" >');
	
	$("#MaxResJPGwidth").attr("disabled",true);
	$("#MaxResJPGwidth").css({ 'background': '#e0e0e0' });
	$("#MaxResJPGheight").attr("disabled",true);
	$("#MaxResJPGheight").css({ 'background': '#e0e0e0' });
	
	
	//$( "#checkRESjpg" ).append('<input type="hidden" value="off" name="maxRes[]" class="checkRESjpg" >');
	
	}
	
});

//PNG
$("#allowRESpng").click(function(){

	if($("#allowRESpng").is( ":checked" )){
	
	$("#MaxResPNGwidth").attr("disabled",false);
	$("#MaxResPNGwidth").css({ 'background': '#ffffff' });
	$("#MaxResPNGheight").attr("disabled",false);
	$("#MaxResPNGheight").css({ 'background': '#ffffff' });
	
	}
	
	else{
	
	$("#MaxResPNGwidth").attr("disabled",true);
	$("#MaxResPNGwidth").css({ 'background': '#e0e0e0' });
	$("#MaxResPNGheight").attr("disabled",true);
	$("#MaxResPNGheight").css({ 'background': '#e0e0e0' });
		
	}
	
});

//GIF
$("#allowRESgif").click(function(){

	if($("#allowRESgif").is( ":checked" )){
	
	$("#MaxResGIFwidth").attr("disabled",false);
	$("#MaxResGIFwidth").css({ 'background': '#ffffff' });
	$("#MaxResGIFheight").attr("disabled",false);
	$("#MaxResGIFheight").css({ 'background': '#ffffff' });
	
	}
	
	else{
		
	$("#MaxResGIFwidth").attr("disabled",true);
	$("#MaxResGIFwidth").css({ 'background': '#e0e0e0' });
	$("#MaxResGIFheight").attr("disabled",true);
	$("#MaxResGIFheight").css({ 'background': '#e0e0e0' });
	
	}
	
});




// Check resolution END	
	

// Click input checkboxes END


// Check Background color


$("#ActivateGalleryBackgroundColor").click(function() {

	if($(this).is(":checked")){
		
		$("#GalleryBackgroundColor").attr("disabled",false);
		$("#GalleryBackgroundColor").css({ 'background': '#ffffff' });
		
	}

	else{
		
		$("#GalleryBackgroundColor").attr("disabled",true);
		$("#GalleryBackgroundColor").css({ 'background': '#e0e0e0' });

	}

});

// Check Background color --- ENDE




// Check if Height Look fields are checked or not

$("#HeightLook").click(function() {

	if($(this).is(":checked")){
		checkHeightView();
		$( "#InfiniteScrollHeight" ).prop("disabled",false);//darf nur hier angewendet werden, ansonsten würde es auch bei anderen ansichten verfügbar sein, die nicht gecheckt sind

	}

	else{		

		
	if (($('#RowLook').is(":checked") && $('#RowLook').is(':enabled')) || ($('#ThumbLook').is(":checked") && $('#ThumbLook').is(':enabled'))) {
			
			// Eigene View darf unchecked werden, falls noch irgend eine View sichtbar ist
			uncheckHeightView();
			$( "#HeightLook" ).prop("disabled",false);
		
	}

	else{
			//uncheckThumbView();
			$(this).prop("checked",true);

		}		
		
	}

});

// Check if Height Look fields are checked or not --- ENDE


// Check if Row Fields are checked or not

$("#RowLook").click(function() {

	if($(this).is(":checked")){
		checkRowView();
		$( "#InfiniteScrollRow" ).prop("disabled",false);//darf nur hier angewendet werden, ansonsten würde es auch bei anderen ansichten verfügbar sein, die nicht gecheckt sind
	}

	else{		

		
	if (($('#HeightLook').is(":checked") && $('#HeightLook').is(':enabled')) || ($('#ThumbLook').is(":checked") && $('#ThumbLook').is(':enabled'))) {
			
			// Eigene View darf unchecked werden, falls noch irgend eine View sichtbar ist
			uncheckRowView();
			$( "#RowLook" ).prop("disabled",false);
		
	}

	else{
			//uncheckThumbView();
			$(this).prop("checked",true);

		}		
		
	}

});

// Check if Row Fields are checked or not  --- END

// Check if Row Fields are checked or not

$("#ThumbLook").click(function() {


	if($(this).is(":checked")){
		checkThumbView();
		$( "#InfiniteScrollThumb" ).prop("disabled",false);//darf nur hier angewendet werden, ansonsten würde es auch bei anderen ansichten verfügbar sein, die nicht gecheckt sind
	}

	else{		

		
	if(($('#HeightLook').is(":checked") && $('#HeightLook').is(':enabled')) || ($('#RowLook').is(":checked") && $('#RowLook').is(':enabled'))) {
		
			// Eigene View darf unchecked werden, falls noch irgend eine View sichtbar ist
			uncheckThumbView();
			$( "#ThumbLook" ).prop("disabled",false);
		
	}

	else{
			//uncheckThumbView();
			$(this).prop("checked",true);

		}		
		
	}

});

// Check if Row Fields are checked or not  --- END

// Check if forward upload fields are checked or not

$("#cg_confirm_text").click(function() {

	if($(this).is(":checked")){

		$( "#forward" ).prop("checked",false);
		$( "#cg_confirm_text" ).prop("checked",true);
		$( "#forward_url" ).prop("disabled",true);
		$( "#confirmation_text" ).prop("disabled",false);
		$( "#forward_url" ).css({ 'background': '#e0e0e0' });
		$( "#confirmation_text" ).css({ 'background': '#ffffff' });
	}

	else{
		
		$( "#forward" ).prop("checked",true);
		$( "#forward_url" ).prop("disabled",false);
		$( "#cg_confirm_text" ).prop("checked",false);
		$( "#confirmation_text" ).prop("disabled",true);
		$( "#forward_url" ).css({ 'background': '#ffffff' });
		$( "#confirmation_text" ).css({ 'background': '#e0e0e0' });

	}

});


$("#forward").click(function() {

	if($(this).is(":checked")){
		$( "#forward_url" ).prop("disabled",false);
		$( "#cg_confirm_text" ).prop("checked",false);
		$( "#confirmation_text" ).prop("disabled",true);
		$( "#forward_url" ).css({ 'background': '#ffffff' });
		$( "#confirmation_text" ).css({ 'background': '#e0e0e0' });
	}

	else{
		$( "#cg_confirm_text" ).prop("checked",true);
		$( "#forward_url" ).prop("disabled",true);
		$( "#confirmation_text" ).prop("disabled",false);
		$( "#forward_url" ).css({ 'background': '#e0e0e0' });
		$( "#confirmation_text" ).css({ 'background': '#ffffff' });
	}

});

// Check if forward upload fields are checked or not  --- END


// Check if forward login fields are checked or not

$("#ForwardAfterLoginUrlCheck").click(function() {

	if($(this).is(":checked")){

		$( "#ForwardAfterLoginTextCheck" ).prop("checked",false);
		$( "#ForwardAfterLoginText" ).prop("disabled",true);
		$( "#ForwardAfterLoginText" ).css({ 'background': '#e0e0e0' });
		$( "#ForwardAfterLoginUrl" ).prop("disabled",false);
		$( "#ForwardAfterLoginUrl" ).css({ 'background': '#ffffff' });

	}

	else{
		
		$( "#ForwardAfterLoginTextCheck" ).prop("checked",true);
		$( "#ForwardAfterLoginText" ).prop("disabled",false);
		$( "#ForwardAfterLoginText" ).css({ 'background': '#ffffff' });
		$( "#ForwardAfterLoginUrl" ).prop("disabled",true);
		$( "#ForwardAfterLoginUrl" ).css({ 'background': '#e0e0e0' });

	}

});


$("#ForwardAfterLoginTextCheck").click(function() {

	if($(this).is(":checked")){
		$( "#ForwardAfterLoginUrlCheck" ).prop("checked",false);
		$( "#ForwardAfterLoginUrl" ).prop("disabled",true);
		$( "#ForwardAfterLoginUrl" ).css({ 'background': '#e0e0e0' });
		$( "#ForwardAfterLoginText" ).prop("disabled",false);
		$( "#ForwardAfterLoginText" ).css({ 'background': '#ffffff' });
	}

	else{
		$( "#ForwardAfterLoginUrlCheck" ).prop("checked",true);
		$( "#ForwardAfterLoginUrl" ).prop("disabled",false);
		$( "#ForwardAfterLoginUrl" ).css({ 'background': '#ffffff' });
		$( "#ForwardAfterLoginText" ).prop("disabled",true);
		$( "#ForwardAfterLoginText" ).css({ 'background': '#e0e0e0' });
	}

});

// Check if forward login fields are checked or not  --- END


// Check show text instead of upload form or not

$("#RegUserUploadOnly").click(function() {

	if($(this).is(":checked")){
		
		
		$( "#RegUserUploadOnly" ).prop("checked",true);
		$( "#RegUserUploadOnlyText" ).prop("disabled",false);
		$( "#RegUserUploadOnlyText" ).css({ 'background': '#ffffff' });
	}

	else{		
		$( "#RegUserUploadOnly" ).prop("checked",false);
		$( "#RegUserUploadOnlyText" ).prop("disabled",true);
		$( "#RegUserUploadOnlyText" ).css({ 'background': '#e0e0e0' });
	}

});

// Check show text instead of upload form or not  --- END


// Check if forward images to url fields are checked or not

	// Wenn gar kein URL Feld kreiert wurde, dann sollen diese Options nicht angezeigt werden
	
	
if($("#Use_as_URL").val()==1){
//alert(1);
		$("#ForwardType").prop("disabled",false);
		$("#ForwardType").css({ 'background': '#fff' });
		$("#ForwardFromGallery").prop("disabled",false);
		$("#ForwardFromGallery").css({ 'background': '#fff' });

}
else{
	//alert(2);
		$( "#ForwardType" ).attr("disabled",true);
		$( "#ForwardType" ).css({ 'background': '#e0e0e0' });
		$( "#ForwardFromGallery" ).attr("disabled",true);
		$( "#ForwardFromGallery" ).css({ 'background': '#e0e0e0' });
}

$("#ForwardToURL").click(function() {


if($(this).is(":checked")){
	$( "#ForwardType" ).prop("disabled",false);
	$( "#ForwardType" ).css({ 'background': '#ffffff' });
	$( "#ForwardFromSlider" ).prop("disabled",false);
	$( "#ForwardFromSlider" ).css({ 'background': '#ffffff' });
	$( "#ForwardFromGallery" ).prop("disabled",false);
	$( "#ForwardFromGallery" ).css({ 'background': '#ffffff' });
	$( "#ForwardFromSinglePic" ).prop("disabled",false);
	$( "#ForwardFromSinglePic" ).css({ 'background': '#ffffff' });
	
}

else{
	$( "#ForwardType" ).prop("disabled",true);
	$( "#ForwardType" ).css({ 'background': '#e0e0e0' });
	$( "#ForwardFromSlider" ).prop("disabled",true);
	$( "#ForwardFromSlider" ).css({ 'background': '#e0e0e0' });
	$( "#ForwardFromGallery" ).prop("disabled",true);
	$( "#ForwardFromGallery" ).css({ 'background': '#e0e0e0' });
	$( "#ForwardFromSinglePic" ).prop("disabled",true);
	$( "#ForwardFromSinglePic" ).css({ 'background': '#e0e0e0' });
}

});


$("#ForwardFromSlider").click(function() {
if($(this).is(":checked")){
	$( "#ForwardFromGallery" ).prop("checked",false);
	$( "#ForwardFromSinglePic" ).prop("checked",false);
}
else{
	$( "#ForwardFromSlider" ).prop("checked",true);
}
});

/*$("#ForwardFromGallery").click(function() {
if($(this).is(":checked")){
	$( "#ForwardFromSlider" ).prop("checked",false);
	$( "#ForwardFromSinglePic" ).prop("checked",false);
}
else{
	$( "#ForwardFromGallery" ).prop("checked",true);
}
});

$("#ForwardFromSinglePic").click(function() {
if($(this).is(":checked")){
	$( "#ForwardFromSlider" ).prop("checked",false);
	$( "#ForwardFromGallery" ).prop("checked",false);
}
else{
	$( "#ForwardFromSinglePic" ).prop("checked",true);
}
});*/

// Check if forward images to url fields are checked or not  --- END





// Inifinite Scroll activation

	// Regeln für Thumb infinite Scroll
		$("#InfiniteScrollThumb").click(function() {
			if($(this).is(":checked")){uncheckRowView();uncheckHeightView();checkThumbView();uncheckPagination();$(".cg_options_order").hide();}
			else{			

				$( "#RowLook" ).prop("disabled",false);
				$( "#HeightLook" ).prop("disabled",false);
				
				if($('#RowLook').is(":checked")){
					checkRowView();
					$("#RowLook").prop("disabled",false);
				}
				if($('#HeightLook').is(":checked")){
					checkHeightView();
					$( "#HeightLook" ).prop("disabled",false);
				}				
				
				checkPagination();$("#HeightLook").prop("disabled",false);$(".cg_options_order").show();
				
				}
			});

		if($("#InfiniteScrollThumb").is( ":checked" )){uncheckRowView();uncheckHeightView();uncheckPagination();$("#HeightLook").prop("disabled",true);$("#RowLook").prop("disabled",true);$(".cg_options_order").hide();}
	// Regeln für Thumb infinite Scroll --- ENDE
	
		// Regeln für Height infinite Scroll
		$("#InfiniteScrollHeight").click(function() {
			if($(this).is(":checked")){uncheckRowView();uncheckThumbView();checkHeightView();uncheckPagination();$(".cg_options_order").hide();}
			else{
				
				$( "#RowLook" ).prop("disabled",false);
				$( "#ThumbLook" ).prop("disabled",false);
				
				if($('#RowLook').is(":checked")){
					checkRowView();
					$("#RowLook").prop("disabled",false);
				}
				if($('#ThumbLook').is(":checked")){
					checkThumbView();
					$( "#ThumbLook" ).prop("disabled",false);
				}			
				
				checkPagination();$("#HeightLook").prop("disabled",false);$(".cg_options_order").show();				
				
				}
			});

		if($("#InfiniteScrollHeight").is( ":checked" )){uncheckRowView();uncheckThumbView();uncheckPagination();$("#RowtLook").prop("disabled",true);$("#ThumbLook").prop("disabled",true);$(".cg_options_order").hide();}
	// Regeln für Height infinite Scroll --- ENDE
	
		// Regeln für Row infinite Scroll
		$("#InfiniteScrollRow").click(function() {
			if($(this).is(":checked")){uncheckHeightView();uncheckThumbView();checkRowView();uncheckPagination();$(".cg_options_order").hide();}
			else{
				
				$( "#HeightLook" ).prop("disabled",false);
				$( "#ThumbLook" ).prop("disabled",false);
				
				if($('#HeightLook').is(":checked")){
					checkHeightView();
					$("#HeightLook").prop("disabled",false);
				}
				if($('#ThumbLook').is(":checked")){
					checkThumbView();
					$( "#ThumbLook" ).prop("disabled",false);
				}
				
				checkPagination();$("#RowLook").prop("disabled",false);$(".cg_options_order").show();
				
				}
			});

		if($("#InfiniteScrollRow").is( ":checked" )){uncheckHeightView();uncheckThumbView();uncheckPagination();$("#HeightLook").prop("disabled",true);$("#ThumbLook").prop("disabled",true);$(".cg_options_order").hide();}
		
	// Regeln für Row infinite Scroll --- ENDE 



// Inifinite Scroll activation --- END 


// ACHTUNG!!!!! Die jeweilige Funktion bezieht sich nur auf die Felder der jeweiligen View!!!! Der Rest muss oben ausgeglichen werden bei Bedarf.

//Function check uncheck row view

function uncheckRowView(){
	
	$( "#RowLook" ).prop("disabled",true);
	$( "#PicsInRow" ).prop("disabled",true);
	$( "#LastRow" ).prop("disabled",true);
	$( "#RowViewSpaceWidth" ).prop("disabled",true);
	$( "#RowViewSpaceHeight" ).prop("disabled",true);
	$( "#RowViewBorderWidth" ).prop("disabled",true);
	$( "#RowViewBorderColor" ).prop("disabled",true);
	$( "#RowViewBorderRadius" ).prop("disabled",true);
	$( "#InfiniteScrollRow" ).prop("disabled",true);
	$( "#PicsInRow" ).css({ 'background': '#e0e0e0' });
	$( "#LastRow" ).css({ 'background': '#e0e0e0' });
	$( "#RowViewSpaceWidth" ).css({ 'background': '#e0e0e0' });
	$( "#RowViewSpaceHeight" ).css({ 'background': '#e0e0e0' });
	$( "#RowViewBorderWidth" ).css({ 'background': '#e0e0e0' });
	$( "#RowViewBorderColor" ).css({ 'background': '#e0e0e0' });
	$( "#RowViewBorderRadius" ).css({ 'background': '#e0e0e0' });
	$( "#InfiniteScrollRow" ).css({ 'background': '#e0e0e0' });
	
	//alert(1);
	
}

function checkRowView(){
	
	$( "#RowLook" ).prop("disabled",false);
	//$( "#RowLook" ).prop("checked",true);
	$( "#PicsInRow" ).prop("disabled",false);
	$( "#LastRow" ).prop("disabled",false);
	$( "#RowViewSpaceWidth" ).prop("disabled",false);
	$( "#RowViewSpaceHeight" ).prop("disabled",false);
	$( "#RowViewBorderWidth" ).prop("disabled",false);
	$( "#RowViewBorderColor" ).prop("disabled",false);
	$( "#RowViewBorderRadius" ).prop("disabled",false);
	$( "#InfiniteScrollRow" ).prop("disabled",false);
	$( "#PicsInRow" ).css({ 'background': '#ffffff' });
	$( "#LastRow" ).css({ 'background': '#ffffff' });
	$( "#RowViewSpaceWidth" ).css({ 'background': '#ffffff' });
	$( "#RowViewSpaceHeight" ).css({ 'background': '#ffffff' });
	$( "#RowViewBorderWidth" ).css({ 'background': '#ffffff' });
	$( "#RowViewBorderColor" ).css({ 'background': '#ffffff' });
	$( "#RowViewBorderRadius" ).css({ 'background': '#ffffff' });
	$( "#InfiniteScrollRow" ).css({ 'background': '#ffffff' });
	
//	alert(1);
	
}

//Function check uncheck row view --- END


//Function check uncheck height view

function uncheckHeightView(){
	
$( "#HeightLook" ).prop("disabled",true);
$( "#HeightLookHeight" ).prop("disabled",true);
$( "#HeightViewSpaceWidth" ).prop("disabled",true);
$( "#HeightViewSpaceHeight" ).prop("disabled",true);
$( "#HeightViewBorderWidth" ).prop("disabled",true);
$( "#HeightViewBorderColor" ).prop("disabled",true);
$( "#HeightViewBorderRadius" ).prop("disabled",true);
$( "#InfiniteScrollHeight" ).prop("disabled",true);
$( "#HeightLookHeight" ).css({ 'background': '#e0e0e0' });
$( "#HeightViewSpaceWidth" ).css({ 'background': '#e0e0e0' });
$( "#HeightViewSpaceHeight" ).css({ 'background': '#e0e0e0' });
$( "#HeightViewBorderWidth" ).css({ 'background': '#e0e0e0' });
$( "#HeightViewBorderColor" ).css({ 'background': '#e0e0e0' });
$( "#HeightViewBorderRadius" ).css({ 'background': '#e0e0e0' });
$( "#InfiniteScrollHeight" ).css({ 'background': '#e0e0e0' });

	
}

function checkHeightView(){
	
$( "#HeightLook" ).prop("disabled",false);
//$( "#HeightLook" ).prop("checked",true);
$( "#HeightLookHeight" ).prop("disabled",false);
$( "#HeightViewSpaceWidth" ).prop("disabled",false);
$( "#HeightViewSpaceHeight" ).prop("disabled",false);
$( "#HeightViewBorderWidth" ).prop("disabled",false);
$( "#HeightViewBorderWidth" ).prop("disabled",false);
$( "#HeightViewBorderColor" ).prop("disabled",false);
$( "#HeightViewBorderRadius" ).prop("disabled",false);
$( "#InfiniteScrollHeight" ).prop("disabled",false); 
$( "#HeightLookHeight" ).css({ 'background': '#ffffff' });
$( "#HeightViewSpaceWidth" ).css({ 'background': '#ffffff' });
$( "#HeightViewSpaceHeight" ).css({ 'background': '#ffffff' });
$( "#HeightViewBorderWidth" ).css({ 'background': '#ffffff' });
$( "#HeightViewBorderColor" ).css({ 'background': '#ffffff' });
$( "#HeightViewBorderRadius" ).css({ 'background': '#ffffff' });
$( "#InfiniteScrollHeight" ).css({ 'background': '#ffffff' });
	
}

//Function check uncheck height view --- END


//Function check uncheck thumb view


function uncheckThumbView(){
	
	$( "#ThumbLook" ).prop("disabled",true);
	$( "#DistancePics" ).prop("disabled",true);
	$( "#DistancePicsV" ).prop("disabled",true);
	$( "#AdjustThumbLook" ).prop("disabled",true);
	$( "#WidthThumb" ).prop("disabled",true);
	$( "#HeightThumb" ).prop("disabled",true);
	$( "#HeightThumb" ).prop("disabled",true);
	$( "#ThumbViewBorderWidth" ).prop("disabled",true);
	$( "#ThumbViewBorderRadius" ).prop("disabled",true);
	$( "#ThumbViewBorderColor" ).prop("disabled",true);
	$("#InfiniteScrollThumb").prop("disabled",true);
	$( "#DistancePics" ).css({ 'background': '#e0e0e0' });
	$( "#DistancePicsV" ).css({ 'background': '#e0e0e0' });
	$( "#AdjustThumbLook" ).css({ 'background': '#e0e0e0' });
	$( "#WidthThumb" ).css({ 'background': '#e0e0e0' });
	$( "#HeightThumb" ).css({ 'background': '#e0e0e0' });
	$( "#ThumbViewBorderWidth" ).css({ 'background': '#e0e0e0' });
	$( "#ThumbViewBorderColor" ).css({ 'background': '#e0e0e0' });
	$( "#ThumbViewBorderRadius" ).css({ 'background': '#e0e0e0' });
	$( "#InfiniteScrollThumb" ).css({ 'background': '#e0e0e0' });
	
}

function checkThumbView(){
	
	$( "#ThumbLook" ).prop("disabled",false);
	$( "#DistancePics" ).prop("disabled",false);
	$( "#DistancePicsV" ).prop("disabled",false);
	$( "#AdjustThumbLook" ).prop("disabled",false);
	$( "#WidthThumb" ).prop("disabled",false);
	$( "#HeightThumb" ).prop("disabled",false);
	$( "#ThumbViewBorderWidth" ).prop("disabled",false);
	$( "#ThumbViewBorderRadius" ).prop("disabled",false);
	$( "#ThumbViewBorderColor" ).prop("disabled",false);
	$("#InfiniteScrollThumb").prop("disabled",false);
	$( "#DistancePics" ).css({ 'background': '#ffffff' });
	$( "#DistancePicsV" ).css({ 'background': '#ffffff' });
	$( "#AdjustThumbLook" ).css({ 'background': '#ffffff' });
	$( "#WidthThumb" ).css({ 'background': '#ffffff' });
	$( "#HeightThumb" ).css({ 'background': '#ffffff' });
	$( "#ThumbViewBorderWidth" ).css({ 'background': '#ffffff' });
	$( "#ThumbViewBorderColor" ).css({ 'background': '#ffffff' });
	$( "#ThumbViewBorderRadius" ).css({ 'background': '#ffffff' });
	$( "#InfiniteScrollThumb" ).css({ 'background': '#ffffff' });
	
}

//Function check uncheck thumb view --- END

//Function check uncheck thumb view


function uncheckPagination(){
	
	$( "#PicsPerSite" ).prop("disabled",true);
	$( "#PicsPerSite" ).css({ 'background': '#e0e0e0' });
	
}

function checkPagination(){
	
	$( "#PicsPerSite" ).prop("disabled",false);
	$( "#PicsPerSite" ).css({ 'background': '#ffffff' });
	
}

//Function check uncheck thumb view --- END


/*if($('#RowLook').is(":checked")){ 

}

else{
	$( "#ThumbLook" ).prop("checked",true);
	$( "#DistancePics" ).prop("disabled",false);
	$( "#DistancePicsV" ).prop("disabled",false);
	$( "#DistancePics" ).css({ 'background': '#ffffff' });
	$( "#DistancePicsV" ).css({ 'background': '#ffffff' });

}*/

		$(document).on('click', '#cg_reset_votes', function(e){
			
		//var del = arg;
		//var del1 = arg1;
		 
			//var del = $(this).attr("alt");
			//var del1 = $(this).attr("titel");

			if (confirm("Are you sure? All saved votes of all images will be deleted.")) {
			   // alert("Clicked Ok");
				//confirmForm();
				//fDeleteFieldAndData(del,del1);
				return true;
			} else {
				//alert("Field not deleted.");
				
				//var test = $("#"+del).find('.fieldValue').val();
				
				// alert(test);  
				
				//alert("This option is not available in the Lite Version.");
				
				return false;
			}

		});

		// Color change tab view6 (activation options). ACHTUNG! click wird vom Plugin blockiert
		
		/*var EnableRegVotingBgColor = $("#checkLoginBgColor").val();
		$("#cg_view6").css("background-image","none");		
		$("#cg_view6").css("background-color",EnableRegVotingBgColor);
		
		$('[class*=cg_view]').mouseup(function (e){
			//$("#cg_view6").find("a").css("display","none");
			$("#cg_view6").css("background-image","none");
			$("#cg_view6").css("background-color",""+EnableRegVotingBgColor+"");
			throw new Error("Something went badly wrong!");
			return false;
			//alert(EnableRegVotingBgColor);
		});*/

});