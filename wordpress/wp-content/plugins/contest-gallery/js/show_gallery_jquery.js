jQuery(document).ready(function($){

	// Daten ermitteln zur weitergabe über JS

	var id = $("#cg_galeryID").val();
	var widthMainCGallery = parseInt($('#mainCGdiv').parent().width());
	var order = $("#cg_order").val();
	var look = $("#cg_look").val();
	var start = $("#cg_start").val();
	var step = $("#cg_step").val();
	var siteURL = $("#cg_siteURL").val();
	var HeightLookHeight = $("#cg_HeightLookHeight").val();
	var cg_ShowAlways = $("#cg_ShowAlways").val();
	var cg_allow_rating = $("#cg_allow_rating").val();
	var cg_allow_comments = $("#cg_allow_comments").val();

	if(cg_allow_rating==1){var cg_info_rating_gallery_div_width = 121;}
	if(cg_allow_rating==2){var cg_info_rating_gallery_div_width = 80;}
	if(cg_allow_comments==1){var cg_info_comment_gallery_div_width = 70;}

	
	var cg_FbLikeGallery = $("#cg_FbLikeGallery").val();
	//var cg_CircleImages = 1;
	var cg_RowViewBorderRadius = parseInt($("#cg_RowViewBorderRadius").val());
	var cg_RowViewSpaceWidth = parseInt($("#cg_RowViewSpaceWidth").val());
	var cg_RowViewSpaceHeight = parseInt($("#cg_RowViewSpaceHeight").val());
	var cg_HeightViewBorderRadius = parseInt($("#cg_HeightViewBorderRadius").val());
	var cg_HeightViewSpaceWidth = parseInt($("#cg_HeightViewSpaceWidth").val());
	var cg_HeightViewSpaceHeight = parseInt($("#cg_HeightViewSpaceHeight").val());
	var cg_ThumbViewBorderRadius = parseInt($("#cg_ThumbViewBorderRadius").val());
	
	var cg_AdjustThumbLook = parseInt($("#cg_AdjustThumbLook").val());
	
	var cg_TitlePositionGallery = parseInt($("#cg_TitlePositionGallery").val());
	var cg_RatingPositionGallery = parseInt($("#cg_RatingPositionGallery").val());
	var cg_CommentPositionGallery = parseInt($("#cg_CommentPositionGallery").val());


	//var cg_horizontalSpace = 10;
	//var cg_verticalSpace = 10;
	//var cg_autoAdjustThumbViewWidth = 1;
	
	
	//var cg_InfiniteScrollGallery = $("#cg_InfiniteScrollGallery").val();
	var cg_InfiniteScrollGallery = $("#cg_InfiniteScroll").val();	
	
	if(cg_InfiniteScrollGallery>0){cg_InfiniteScrollGallery=1;}
	
	// Es würde auch so gehen: $(window).scroll(function (event) {
	//aber wie es jetzt ist scheint es viel schneller zu sein
	
	//alert(cg_InfiniteScrollGallery);

	function resizeFontSize(){

		var cgWidth = $('#mainCGallery').width();

       // if(cgJsClass.slider.vars.isMobile==true){
        if(cgWidth<=600){
        	$('#mainCGallery .cg_gallery_info_title').css('font-size','14px');
		}
        if(cgWidth<=450){
        	$('#mainCGallery .cg_gallery_info_title').css('font-size','12px');
		}
		else{
            $('#mainCGallery .cg_gallery_info_title').css('font-size','');
		}

	}

    resizeFontSize();



	
	if(cg_InfiniteScrollGallery==1){
	//window.onscroll = function(ev) {
	$( window ).scroll(function() {

// window.innerHeight: Höhe des für user SICHTBAREN Browser-Fensters 
// window.scrollY: Abstand welcher runtergescrollt wurde, die höhe des am anfang sichtbaren Browserfs-Fensters ist nicht mit einbezogen! Es zählt nur was wirklich gescrollt wurde!
// document.body.offsetHeight: ist die Höhe des gesamten Body elements mit padding! Margin ist nicht mit dabei.
//if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight || (window.innerHeight/2 + window.scrollY) >= $( "#cg_gallery_loading_gif" ).offset().top) { <<< alte version, wo man noch ganz bis zum schluss runterscrollen muss ist mit dabei

	// 24px ist die Höhe des GIFs, aber man nimmt 30 damit gif kurz zu sehen ist
if ( $( "#cg_gallery_loading_gif" ).length ) {
	if ((window.innerHeight-30 + $(this).scrollTop()) >= $( "#cg_gallery_loading_gif" ).offset().top) {
			// you're at the bottom of the page
			//alert(1);
			//setTimeout(function(){
				
				//alert("window.innerHeight: "+window.innerHeight);
				//alert("window.scrollY: "+window.scrollY);
				//alert("document.body.offsetHeight: "+document.body.offsetHeight);
				//alert($( "#cg_all_images_loaded" ).offset().top);
				
			
		// Wenn alle Bilder geladen wurden, sollten hinsichtlich pagination nichts mehr passieren 
		var cg_all_images_loaded = $('#cg_all_images_loaded').val();	
			
	//	alert(cg_all_images_loaded);
		
		// Prüfen ob Slider an ist
		if($('#cg_overlay').is(':visible')) {}
		else{
			
				if(cg_all_images_loaded==0){
					
						//setTimeout(function(){
					
					
							//Muss hier am Anfang beginnen sonst werden zu viele Bilder beim nächsten Schritt geladen. Loading Gif dient immer zur Orientierung bei Berechnung.
							$("#cg_gallery_loading_gif").remove();		
						
							if(look=="height"){
								
								setTimeout(function(){
								heightLogic();
								}, 500);
							
							}

							if(look=="row"){
								
							//	alert("go");
								rowLogic();
							
							}
							
							if(look=="thumb"){
								
							//	alert("go");
								thumbLogic();
							
							}
						
						//}, 500);
					
					}
		}	
	}	
			//}, 1000);
}
	

});
}
//if($(window).scrollTop()=="-400"){alert(1);}  

	
	//var cg_timestamp_javascript = Math.round(Date.now()/1000); 
	//var cg_timestamp_php = $("#cg_timestamp_php").val();
	
	// Set the URL to whatever it was plus "#".
//url = document.URL+"#"+cg_timestamp_javascript;
//location = "#"+cg_timestamp_javascript;
	
//	alert(cg_timestamp_php);
	//alert(cg_timestamp_javascript);
	
	
//$( document ).ready(function() {1461971156 1461971195

//$('#25').on( "click", function() { alert( 25 );});

//alert(234234);


/*$( "#25" ).click(function() {
alert("clicked");
  $( ".25" ).click();
});*/
/*


(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

$('title').after('<div id="cg_horizontalSpace"></div>'+
	'<meta property="og:url"           content="http://www.amazon.de/" />'+
	'<meta property="og:type"          content="website" />'+
	'<meta property="og:title"         content="Your Website Title" />'+
	'<meta property="og:description"   content="Your description" />'+
	'<meta property="og:image"         content="http://www.amazon.de/" />');	*/
	



	
	
	if(cg_FbLikeGallery==1){var cg_hide_hide_width=180;}
	 else{cg_hide_hide_width=130;}
	
	//	alert("cg_galeryID: " + id);
	//	alert("widthMainCGallery: " + widthMainCGallery);
	//	alert("order: " + order);
	//	alert("look: " + look);
	//	alert("start: " + start);
	//	alert("step: " + step);
	//	alert("siteURL: " + siteURL);
	//	alert("HeightLookHeight: " + HeightLookHeight);
	
	
	 var cg_change_clicked = 0;
	 	//alert("1");
//$('head').append('<title>CG-Test-1 lalala</title>').insertAfter( "<head>" );
//$( "title" ).after( document.createTextNode(' <meta property="og:type"          content="website" /> ') );
//$( "TEST123" ).insertAfter( "title" );

/*
			// Zur Ermittlung des Abstandes zum Rand des HTMLs für Pagination
			var cumulativeOffset = function(element) {
				var top = 0, left = 0;
				alert("element "+element);
				do {
					top += element.offsetTop  || 0;
					left += element.offsetLeft || 0;
					element = element.offsetParent;
				} while(element);

				return {
					top: top,
					left: left
				};
			};


			
		function getPosition(el) {
  var xPos = 0;
  var yPos = 0;
  alert("el "+el);
 
  while (el) {
    if (el.tagName == "BODY") {
      // deal with browser quirks with body/window/document and page scroll
      var xScroll = el.scrollLeft || document.documentElement.scrollLeft;
      var yScroll = el.scrollTop || document.documentElement.scrollTop;
 
      xPos += (el.offsetLeft - xScroll + el.clientLeft);
      yPos += (el.offsetTop - yScroll + el.clientTop);
    } else {
      // for all other non-BODY elements
      xPos += (el.offsetLeft - el.scrollLeft + el.clientLeft);
      yPos += (el.offsetTop - el.scrollTop + el.clientTop);
    }
 
    el = el.offsetParent;
  }
  return {
    x: xPos,
    y: yPos
  };
}	*/
			




//$( "<p>Test</p>" ).insertAfter( "<head>" );
$("#cg-change-height").click(function(){
	

		
   var cg_change_clicked = 1;
   
   	if (cg_change_clicked==1){
	//alert("2");
	var HeightLookHeight = $("#cg-height").val();
	var arg = HeightLookHeight;
	//alert(HeightLookHeight);
	heightLogic(arg);
		
	}
	
	});
	


	
//	alert("id:"+id);
//	alert("widthMainCGallery:"+widthMainCGallery);
//	alert("order:"+order);
	//alert("look:"+look);
//	alert("start:"+start);
	//alert("step:"+step);
//	alert("siteURL:"+siteURL);
	//alert("HeightLookHeight:"+HeightLookHeight);
	
	
	
	
	
	var dataArray = [];
	dataArray[0] = id;
	dataArray[1] = widthMainCGallery;
	dataArray[2] = look;
	dataArray[3] = order;
	dataArray[4] = start;
	dataArray[5] = step;
	dataArray[6] = siteURL;
	
	//alert(id);

	// Daten ermitteln zur weitergabe über JSON --- ENDE


	//var request = $.post("wp-content/plugins/contest-gal1ery/frontend/show-gallery1.php",{getData: dataArray});
	//request.done(function(data){
	//$('#mainCGdiv').html(data);	
	
	
	
	// Alle Bilder anzeigen sobald diese geladen sind
	/*$(function() {
	  $(".cg_image").css('visibility','visible');   
	});*/
	/*
	 $( ".cg_image" ).each(function() {
$(".cg_image").css('visibility','visible');   
});*/

$( ".cg_show" ).css("display","hidden");
	
	
	// HOVERINHALT VERSTECKEN
	
	var showAlways = $( "#cg_ShowAlways" ).val();

	
	
	if(showAlways!=1){
	
	
	$( "[id*=cg_hide]" ).hide();
	$( "[id*=cg_Field1IdGalleryView]" ).hide();
	
	
	// HOVERINHALT VERSTECKEN
  

   // Inhalt beim hovern aufdecken
	  $( ".cg_show" ).mouseover(function() {
	  $(this).css("cursor: default");
	  //$("[class*=cgo]").css("cursor: default");
	 // $(".ul-cgrating").css("cursor: default");
	 
	  
	  var width = $(this).width();
	  var height = $(this).height();
	  // 22 ist die Breite des Hover-Balkens
	 // height = height-32;
	  
	 // alert(width);
	 /*
	  var html_org = $(this).find("[id*=cg_Field1IdGalleryView]").html();
	//  alert(html_org);
	  var html_calc = '<span style="display:hidden;visibility: hidden;height:0px !important;line-height:0px !important;">' + html_org + '</span>';
	  $(this).find("[class*=cg_measure_Field1IdGalleryView]").html(html_calc);
	  var widthContent = $(this).find('[class*=cg_measure_Field1IdGalleryView]').find('span:first').width();
	  $(this).find("[class*=cg_measure_Field1IdGalleryView]").empty();
	 // alert(widthContent);
	 // $(this).html(html_org);
	 */

	  //alert(cg_hide_hide_width);
	  if(width>cg_hide_hide_width){
	   $(this).find( "[id*=cg_hide]").show();
	   $(this).find( "[id*=cg_Field1IdGalleryView]").show();
	   // Soll nur dann erscheinen wenn text vorhanden ist
	   if (!$(this).find( "[id*=cg_Field1IdGalleryView]").text().trim().length){$(this).find( "[id*=cg_Field1IdGalleryView]").show();}
	   
		
	 // $(this).find( "#cg_hide").css('width',width);
	//  $(this).find( "[id*=cg_Field1IdGalleryView]").css('width',width);
	  // Der Hoverbalken muss den Boden des Images treffen
	 // $(this).find( "#cg_hide").css('margin-top',height);
	  }
	  
});

	  $( ".cg_show" ).mouseout(function() {
	  $(this).css("cursor: default");
	  //$("[class*=cgo]").css("cursor: default");
	 // $(".ul-cgrating").css("cursor: default");
	 
	  
	  var width = $(this).width();
	  var height = $(this).height();
	  // 22 ist die Breite des Hover-Balkens
	 // height = height-32;
	  
	 // alert(width);
	 /*
	  var html_org = $(this).find("[id*=cg_Field1IdGalleryView]").html();
	//  alert(html_org);
	  var html_calc = '<span style="display:hidden;visibility: hidden;height:0px !important;line-height:0px !important;">' + html_org + '</span>';
	  $(this).find("[class*=cg_measure_Field1IdGalleryView]").html(html_calc);
	  var widthContent = $(this).find('[class*=cg_measure_Field1IdGalleryView]').find('span:first').width();
	  $(this).find("[class*=cg_measure_Field1IdGalleryView]").empty();
	 // alert(widthContent);
	 // $(this).html(html_org);
	 */

	  //alert(cg_hide_hide_width);
	  if(width>cg_hide_hide_width){
	   $(this).find( "[id*=cg_hide]").hide();
	   $(this).find( "[id*=cg_Field1IdGalleryView]").hide();
	   // Soll nur dann erscheinen wenn text vorhanden ist
	   if (!$(this).find( "[id*=cg_Field1IdGalleryView]").text().trim().length){$(this).find( "[id*=cg_Field1IdGalleryView]").hide();}
	   
		
	 // $(this).find( "#cg_hide").css('width',width);
	//  $(this).find( "[id*=cg_Field1IdGalleryView]").css('width',width);
	  // Der Hoverbalken muss den Boden des Images treffen
	 // $(this).find( "#cg_hide").css('margin-top',height);
	  }
	  
});

}

else{

// Auch hide wenn ShowAlways an ist. In der Ansichtsfunktion wird beim onload des images dann das Feld gezeigt
$( "[id*=cg_hide]" ).hide();	
	
}

//$( "#cg_thumb_configuration" )#cg_hide();
//$( "#cg_row_configuration" )#cg_hide();
//$( "#cg_height_configuration" )#cg_hide();


// Auswahl select Feld
//$("#select").val(order);





// Prüfen ob Reihenfolge verändert wurde und Formular senden

$( "#cg_select_order" ).change(function() {
$("#cg_change_order").click();
});

$("[class*=click-pic]").click(function(){

var myClass = $(this).attr("class");

var id = myClass.substring(10);

$("#cg-img-"+id).click();

});



//$( document ).ready(function() {

//if(document.readyState === "complete"){

//alert(12312321);  



//alert("test");


function heightLogic(){
	
	var cg_gallery_resize = $('#cg_gallery_resize').val();
	
	
 // alert(cg_gallery_resize);
	
  var HeightLookHeight = $("#cg_HeightLookHeight").val();
  
  // Wenn alle Bilder geladen wurden, sollten hinsichtlich pagination nichts mehr passieren
	var cg_all_images_loaded = $('#cg_all_images_loaded').val();	
  
  //alert(HeightLookHeight);

   // Neue Höhe 
  var newHeight = 0;
  
  // Ermitteln von Breite des parent Divs nach resize
  var widthMainCGallery = parseInt($('#mainCGdiv').parent().width());
  
  //alert(widthMainCGallery);
  
  // Breite des divs in dem sich die Galerie befindet
  var widthmain = widthMainCGallery;
  
  // Wenn pagination an ist, dann muss der erste Width Wert hier eingetragen werden
  var check_cg_widthMainCGallery = $("#cg_widthMainCGallery").val();  
  
/* // alert(cg_gallery_resize);
  if(cg_InfiniteScrollGallery==1 && cg_gallery_resize==1){$("#cg_widthMainCGallery").val(widthmain);}
  
 
  if(cg_gallery_resize==1 && cg_InfiniteScrollGallery==1){
	  widthmain=$("#cg_widthMainCGallery").val();//alert(widthmain);
	 // Resize ist zu Ende, nach dem HeightLogic ausgeführt wurde
	// $('#cg_gallery_resize').val(0);

	  }*/
	  
	 // Zur Überprüfung was beim letzten Mal für ein Width war 
	//var check_cg_widthMainCGallery = $("#cg_widthMainCGallery").val();
	//alert(check_cg_widthMainCGallery);
	if(cg_gallery_resize==0 && check_cg_widthMainCGallery!=0){widthmain=$("#cg_widthMainCGallery").val();}
	  
    else{widthmain=widthmain;}
 
	//alert(widthmain);
  
  //var widthmainReal = widthmain;
  

  
  //$(".werte").append('<br> WidthMain: '+widthmain+'<br>'); 
   
   // die einzelnen neu ermittelten Breiten die durch die vorgegebene Höhe entstehen werden gesammelt
  var widthArray = [];
  
   // die einzelnen Höhen sollen gesammelt werden. Bei Runter- und Hochskaliertung, ist es eine notwendige Angabe im Div 
  var heightArray = [];
  
  // Die Breite der Inhaltsboxen wird ermittelt
  var widthDivArray = [];
  
  // Sammeln der ursprünglichen Höhe
  var realHeightArray = [];
  
   // Sammeln der ursprünglichen Breite
  var realWidthArray = [];
  
  // Anzahl der Durchläufe muss gezählt werden um den letzten Durchlauf zu ermitteln
  var lastLoopProcess = $("#cg_count").val();
  
  // Anzahl der Durchläufe muss gezählt werden um den letzten Durchlauf zu ermitteln
  var last = [];
  
  // Anzahl der hochgeladenen Bilder
  var n = $( ".cg_show" ).length;

  
  if(cg_InfiniteScrollGallery==1){
	  
	 var cg_InfiniteScrollGalleryHeight = $(window).height();
	 
	// alert(cg_InfiniteScrollGalleryHeight);
	  
  }
  
  //var border = 30;
  
  /*$( "[data-opacity]" ).each( function() {
    var elem = $( this );
    alert( elem.data( "opacity" ) );
	$("#opacity_data").val(elem.data( "opacity" ));
   
});*/

/*
 $('input.demo').each(function(){
        var input = $(this);
        if ($(input).data().opacity) {
            alert($(input).data().opacity);
        }
    });*/

var opacity = $('#cg_HeightViewBorderOpacity').val();



 //opacity = opacity.substring(1);

//opacity = opacity.substring(1);


//alert(opacity);
  
  var borderColor = $("#cg_HeightViewBorderColor").val();
  
  //alert(borderColor);
  

 var border = parseInt($("#cg_HeightViewBorderWidth").val());

   
   
   	// Der erste horizontalSpace (von links kommend) darf nicht vorkommen, der letzte soll abgezogen werden!
	// Wenn nicht null dann muss immer von beiden seiten border hinzugefügt werden
	/*if(cg_horizontalSpace!=0){
		widthmain = widthmain-(border*2+cg_horizontalSpace)* picsInRow+cg_horizontalSpace;
	}
	// Wenn null dann muss border immer nur von einer Seite hinzugefügt werden	
	else{
		widthmain = widthmain-(border+cg_horizontalSpace)* picsInRow+cg_horizontalSpace-border;
	}*/
   
   
   

  //alert(border); 
 // alert(addBorder); 
  function hex2rgb(hexStr){
    // note: hexStr should be #rrggbb
    var hex = parseInt(hexStr.substring(1), 16);
    var r = (hex & 0xff0000) >> 16;
    var g = (hex & 0x00ff00) >> 8;
    var b = hex & 0x0000ff;
    return [r, g, b];
    //return r;
}

var rcolor = hex2rgb(borderColor);

//alert(rcolor[0]);
 
var rgba = "("+rcolor[0]+","+rcolor[1]+","+rcolor[2]+", "+opacity+")";

//alert(rgba);
 
// rgba(255, 0, 0, .5)	 

//alert(cg_HeightViewSpaceWidth);
//alert(cg_HeightViewSpaceHeight);
	
	var cg_horizontalSpace = cg_HeightViewSpaceWidth;
	var cg_verticalSpace = cg_HeightViewSpaceHeight;
	
	if(cg_HeightViewBorderRadius>5){var cg_CircleImages=1;}
	else{var cg_CircleImages=0;}
	
	
    // Hier müssen die VAR neue gesetzt werden!!! Später immer ohne VAR!
  	// Wenn horizontal space nicht an ist, dann wird Border immer nur rechts hinzugefügt. Beim ersten Mal sollte border einmal links hinzugefügt werden.
	// ACHTUNG!!! Bei height view, nur wenn kein Circle Image gesetzt ist und horizantol Border 0 ist,
	// wird am Anfang ein border gesetzt als width und dann später nur ein border rechts immer dazu addiert.
	// Ansonsten wird kein Border gesetzt. Sondern danach immer rechts und links border dazu addiert.
	if(cg_horizontalSpace==0 && cg_CircleImages==0){
		var aggregateWidth = border;
	}
	// Wenn horizontal space an ist, dann soll border immer rechts und links dazu adiert werden, wegen den Abständen.
	// Beim ersten Mal muss nichts hinzugefügt werden. Da beim ersten Bild rechts und links border hinzugefügt wird.
	else{
		var aggregateWidth = 0;	
	}

  
  // Gesamtzahl der verarbeiteten Bilder
  var countProcessedPics = 0;
  
  // Summer der Gesamtlänge, um die alle Bilder, die in die Gesamtbreite reinpassen, insgesamt reduziert werden können. 
  // Mehr als 46% kann von einem Bild nicht abgezogen werden ( zuerst 10% Höhe, danach 20% Links, 20% Rechts >>>  46% prozent insgesamt als Reduzierung bei einem Bild möglich ) 
  var aggregateAcceptableReducedSize = 0;
  
  // Orientierungsvariable bei Durcharbeiten des großen Arrays   
  var r = 0;
  
   // Orientierungsvariable bei Durcharbeiten des großen Arrays   
  var i = 0;
  
  
  // Orientierungsvariable wenn width und widthDiv berechnet wurden  
  var widthDone = 0;

  // 1. Die neue Höhe jedes einzelnen Bildes muss ermittelt werden. Diese wird in einem Array gesammelt.     
  
 //alert(widthmain);

  
  $('#mainCGallery').css('width',widthmain);
  $('#mainCGdiv').css('width',widthmain);
  
  

  
	$( ".cg_show" ).each(function( i ) {
	   
	r++;
	
	// Die original Image Größen werden ermittelt über die hidden Felder. Die beim ersten Load von php ermittelt wurden.
	var width = $(this).find(".widthOriginalImg").val();
	var height = $(this).find(".heightOriginalImg").val();
	
	realHeightArray.push(height);
	realWidthArray.push(width);
	
    var newWidth = width*HeightLookHeight/height;
	
	// die einzelnen neu ermittelten Breiten die durch die vorgegebene Höhe entstehen werden gesammelt
	widthArray.push(newWidth);
	heightArray.push(HeightLookHeight);
	widthDivArray.push(newWidth);
	
	// Wenn horizontal space nicht an ist, dann wird Border immer nur rechts hinzugefügt
	if(cg_horizontalSpace==0){
		aggregateWidth = aggregateWidth + newWidth + border;
	}
	// Wenn horizontal space an ist, dann soll border immer rechts und links dazu adiert werden, wegen den Abständen.
	else{
		widthDone = 0;
		aggregateWidth = aggregateWidth + newWidth + border*2;//ACHTUNG!!! Horizontal Space wird zum schluss der Schleife immer hinzugefügt. Dadurch wird beim letzten Element horizontalSpace nicht unnötig hinzuaddiert!
	}
	
	// zuerst 10% Höhe, danach 20% Links, 20% Rechts >>> 46% prozent insgesamt als Reduzierung möglich
	acceptableReducedSize = newWidth/100*46;
	
	// Summer der Gesamtlänge, um die alle Bilder, die in die Gesamtbreite reinpassen, insgesamt reduziert werden können. 
	aggregateAcceptableReducedSize = aggregateAcceptableReducedSize + newWidth;
	
	//widthmain = widthmain-cg_border_width*2;
	
	//$(".werte").append('<br> aggregateWidth '+r+' :'+aggregateWidth+'<br>'); 
	////$(".werte").append('<br>'+r+' :'+src+'<br>'); 
	//$(".werte").append('<br> step '+r+' :'+lastLoopProcess+'<br>'); 
	
	//$(".werte").append('<br>Durchgang: '+i+'<br>'); 
	
	
	//alert(aggregateWidth);
	
	// Wenn es der letzte Durchlauf ist und die gesammelte Breite unter 90% der Gesamtbreite ergibt dann wird nichts gemacht
	if (aggregateWidth < widthmain/100*90 && lastLoopProcess == r) {
	//alert(aggregateWidth);
		for (i = countProcessedPics; i < r; i++) {
		
			//$(".werte").append('<br> Variante 0:<br>'); 
			//$(".werte").append('<br> widthArray['+i+']: '+widthArray[i]+'<br>'); 
			
			widthArray[i] = widthArray[i];
			widthDivArray[i] = widthDivArray[i];	
			
			//alert("Variante 1");

		//$(".werte").append('<br> Neue Breite:'+newWidth+'<br>'); 
		//$(".werte").append('<br> widthArray['+i+']: '+widthArray[i]+'<br>'); 
		
		}
		
		// Gesammelte Breite wieder zurück auf Null setzen
		if(cg_horizontalSpace==0){
			aggregateWidth = border;
		}
		else{
			aggregateWidth = 0;	
		}
		
		widthDone = 1;
	
	}
	
	
	// Wenn die gesammelte Breite über 90% der Gesamtbreite ergibt, dann wird hochskaliert. Die Höhe bleibt die vorgegebene Höhe 
	else if (aggregateWidth >= widthmain/100*90 && aggregateWidth <= widthmain) {			
	//alert(aggregateWidth);
		for (i = countProcessedPics; i < r; i++) {
		
			//$(".werte").append('<br> Variante 1:<br>'); 
			//$(".werte").append('<br> widthArray['+i+']: '+widthArray[i]+'<br>'); 
			
			newWidth = widthArray[i]/(aggregateWidth*100/widthmain)*100;
			widthArray[i] = newWidth;
			widthDivArray[i] = newWidth;		
			
		//$(".werte").append('<br> Neue Breite:'+newWidth+'<br>'); 
		//$(".werte").append('<br> widthArray['+i+']: '+widthArray[i]+'<br>'); 
		
		
		//alert("Variante 2");

		
		}
		
		countProcessedPics = r;
		
		// Gesammelte Breite wieder zurück auf Null setzen
		if(cg_horizontalSpace==0){
			aggregateWidth = border;
		}
		else{
			aggregateWidth = 0;	
		}
		
		widthDone = 1;
		
		last[r] = 'on';
		//alert(last[r]);
		
		//$(".werte").append('<br> countProcessedPics: '+countProcessedPics+'<br/>');
		//$(".werte").append('<br> last['+r+']: '+last[r]+'<br>'); 
		
		//widthmain = widthmainReal;
		
		//alert(2);
	
	}
	
	// Wenn die gesammelte Breite größer als die Gesamtbreit ist, dann wird runterskaliert oder abgeschnitten
	else if(aggregateWidth > widthmain){
	//	alert(3);
	//alert(aggregateWidth);
	// Größe des Überhangs
	overhang = aggregateWidth - widthmain;
	
	overhangInPercent = overhang*100/widthmain;
	
	//$(".werte").append('<br> overhangInPercent: '+overhangInPercent+'<br/>');
	
			// Wenn der Überhang nur unter 10% ist dann werden die Bilder NUR runterskaliert auf 100% (man kann auch hochskalieren, glaubenssache :)) 		
			if (overhangInPercent <= 10) {
			
			
				for (i = countProcessedPics; i < r; i++) {
				
					//$(".werte").append('<br> Variante 2:<br>'); 
					//$(".werte").append('<br> widthArray['+i+']: '+widthArray[i]+'<br>'); 
				wholePercent=100+overhangInPercent;
				
					newWidth = widthArray[i]/wholePercent*100;
					
					//newWidth = widthArray[i]/wholePercent*100;
					newHeight = heightArray[i]/wholePercent*100;
					//newHeight = heightArray[i]/wholePercent*100;
					widthArray[i] = newWidth;
					heightArray[i] = newHeight;
					widthDivArray[i] = newWidth;
					
					//alert(newWidth);
					
					//$(".werte").append('<br> Neue Breite:'+newWidth+'<br>'); 
					//$(".werte").append('<br> widthArray['+i+']: '+widthArray[i]+'<br>'); 
				
				}
				
				countProcessedPics = r;
				
				last[r] = 'on';
				//alert(last[r]);
				
		//$(".werte").append('<br> countProcessedPics: '+countProcessedPics+'<br/>');
		//$(".werte").append('<br> last['+r+']: '+last[r]+'<br>'); 
	
		//alert(3);
	//	alert("Variante 3");
			
			}
			
			// Wenn der Überhang nur über 10% und unter 46% ist dann werden die Bilder abgeschnitten und runterskaliert			
			if (overhangInPercent > 10 && overhangInPercent <=46) {
						
				for (i = countProcessedPics; i < r; i++) {
				
				//$(".werte").append('<br> Variante 3:<br>'); 
				//$(".werte").append('<br> overhangInPercent: '+overhangInPercent+'<br/>');
				//$(".werte").append('<br> widthArray['+i+']: '+widthArray[i]+'<br>'); 
					

					newWidth = widthArray[i]/100*90;
					newHeight = heightArray[i]/100*90;
					widthArray[i] = newWidth;
					heightArray[i] = newHeight;
					//widthDivArray[i] = widthDivArray[i]/100*(100-Math.ceil((overhangInPercent-10)));
					widthDivArray[i] = newWidth;
					
					//alert(newHeight);
					//alert(newWidth);
					
				//$(".werte").append('<br> Neue Breite:'+newWidth+'<br>'); 
				//$(".werte").append('<br> Neue Div Breite:'+widthDivArray[i]+'<br>'); 
				//$(".werte").append('<br> widthArray['+i+']: '+widthArray[i]+'<br>'); 
				 				
				}
				
				countProcessedPics = r;
				
				last[r] = 'on';
				//alert(last[r]);
				//$(".werte").append('<br> countProcessedPics: '+countProcessedPics+'<br/>');
				//$(".werte").append('<br> last['+r+']: '+last[r]+'<br>'); 
				
			//alert(4);
			
			//alert("Variante 4");
			
			}
			
			// Wenn der Überhang über 46% ist dann werden die Bilder abgeschnitten und runterskaliert. Beim letzten in der Reihe wird alles was über 46% ist komplett abgeschnitten.		
			if (overhangInPercent > 46) {
			
			
				for (i = countProcessedPics; i < r; i++) {
				
				//$(".werte").append('<br> Variante 4: '+newWidth+'<br>'); 
				
				newWidth = widthArray[i]/100*90;
				newHeight = heightArray[i]/100*90;
				widthArray[i] = newWidth;
				heightArray[i] = newHeight;
				widthDivArray[i] = widthDivArray[i]/100*(100-36);
				 				
				}
				
				countProcessedPics = r;
				
				last[r] = 'on';
				//alert(last[r]);
				//$(".werte").append('<br> countProcessedPics: '+countProcessedPics+'<br/>');
				//$(".werte").append('<br> last['+r+']: '+last[r]+'<br>'); 
				
				//alert(5);
				
				//alert("Variante 5");
			
			}
			
			// Gesammelte Breite wieder zurück auf Null setzen
			if(cg_horizontalSpace==0){
				aggregateWidth = border;
			}
			else{
				aggregateWidth = 0;	
			}
			
			widthDone = 1;
	
	//widthmain = widthmainReal;
	
	}  
	
    if(cg_horizontalSpace!=0 && widthDone==0){
		aggregateWidth = aggregateWidth + cg_horizontalSpace;
		//alert(aggregateWidth);
	}	
	
	
	  
 
	   });
	   
  
  // var h = 0;
   var i = 0;
   var r = 0;
   

   
		// Hier müssen die VAR neue gesetzt werden!!! Später immer ohne VAR!
  		// Gesammelte Breite wieder zurück auf Null setzen
		// ACHTUNG!!! Bei height view, nur wenn kein Circle Image gesetzt ist und horizantol Border 0 ist,
		// wird am Anfang ein border gesetzt als width und dann später nur ein border rechts immer dazu addiert.
		// Ansonsten wird kein Border gesetzt. Sondern danach immer rechts und links border dazu addiert.
		if(cg_horizontalSpace==0 && cg_CircleImages==0){
			var aggregateWidth = border;
		}
		else{
			var aggregateWidth = 0;	
		}
   
   
  // var checkAggregateWidth = border;
   
   var firstRow = 0;  
   
   // Zur Ermittlung des Abstandes zum Rand des HTMLs für Pagination
   var cg_imgOffsetHTML = 0;
   
   // Sobald cg_pagination 1 erreicht hat, dann loading gif anzeigen und nicht mehr laden, bis weiter gescrollt wird
   var cg_pagination = 0;
   
   // Wie viele Rows wurden verarbeitet
   var rowsDone = 0;
   
   // Ermittlung der Durchgänge in einer Reihe
   var rInRow = 0;
   
$( ".cg_show" ).each(function( i ) {
	
	 r++;
	 rInRow++;
	
	//Achtung entspricht nicht der Anzahl der tatsächlich geladenen Bilder. Fängt mit 0 an. Wird auch für das gesammelte Height und Width Array verwendet.
	var cg_pagination_position_count = $('#cg_pagination_position_count').val();
	//alert(0);
	//alert(cg_pagination_position_count);
	
	
	
	// Wenn resize gemacht wurde gerade und logic läuft, dann sollen ALLE nochmal abgearbeitet werden
	
	if(i<=cg_pagination_position_count && cg_InfiniteScrollGallery==1 && cg_pagination_position_count!=0 && cg_gallery_resize!=1){
		//var cg_do_nothing = 1;	
		}
	//else if (cg_InfiniteScrollGallery==1 && cg_gallery_resize!=1  && cg_pagination_position_count=1) {var cg_do_nothing = 1;} 
	else{
	//alert(i);
	

	 
	 var realId = $(this).find('.realId').val();

	 //alert(realId);
	 
	 var srcOriginalImg = $(this).find('.srcOriginalImg').val();
	 var src1920width = $(this).find('.src1920width').val();
	 var src1600width = $(this).find('.src1600width').val();
	 var src1024width = $(this).find('.src1024width').val();
	 var src624width = $(this).find('.src624width').val();
	 var src300width = $(this).find('.src300width').val();
	 var hrefCGpic = $(this).find('.hrefCGpic').val();
	  
	  
	 var cg_Use_as_URL = $('#cg_Use_as_URL').val();
	 var cg_ForwardToURL = $('#cg_ForwardToURL').val();
	 var cg_ForwardType = $('#cg_ForwardType').val();
	 var cg_ForwardFrom = $('#cg_ForwardFrom').val();
	 var cg_FullSizeImageOutGalleryNewTab = $('#cg_FullSizeImageOutGalleryNewTab').val();
	 var cg_FullSizeImageOutGallery = $('#cg_FullSizeImageOutGallery').val();


			
			// width für Bilder array
			width = Math.floor(widthArray[i]);
			
			// width für div array
			height = Math.floor(heightArray[i])-3;
			
			
			widthDiv = Math.floor(widthDivArray[i]);		

	        // Perfekte Anpassung letztes Bild der Zeile
			if (last[r] == 'on'){

				// Wenn horizontal Space nicht an ist, wird Border nur einmal von einer Seite abgezogen (später im CSS kommt wird es rechts hinzuaddiert)
				// ACHTUNG!!! Bei height view, nur wenn kein Circle Image gesetzt ist und horizantol Border 0 ist,
				// wird am Anfang ein border gesetzt als width und dann später nur ein border rechts immer dazu addiert.
				// Ansonsten wird kein Border gesetzt. Sondern danach immer rechts und links border dazu addiert.
				if(cg_horizontalSpace==0 && cg_CircleImages==0){
					widthDiv = widthmain - aggregateWidth-border;
				}
				// Wenn horizontal Space an ist, dann wird Border von einer und anderen Seite abgezogen (später im CSS kommt wird es rechts und links hinzuaddiert)
				else{
					widthDiv = widthmain - aggregateWidth-border*2;					
				}		

				
				if(width<widthDiv){ width = widthDiv;}
				
				
				// So wie als ob oben vor dem ersten Durchlauf
			  	// Gesammelte Breite wieder zurück auf Null setzen
				// ACHTUNG!!! Bei height view, nur wenn kein Circle Image gesetzt ist und horizantol Border 0 ist,
				// wird am Anfang ein border gesetzt als width und dann später nur ein border rechts immer dazu addiert.
				// Ansonsten wird kein Border gesetzt. Sondern danach immer rechts und links border dazu addiert.
				if(cg_horizontalSpace==0 && cg_CircleImages==0){
					aggregateWidth = border;
				}
				else{
					aggregateWidth = 0;	
				}				

			}
			
			else{
				// ACHTUNG!!! Bei height view, nur wenn kein Circle Image gesetzt ist und horizantol Border 0 ist,
				// wird am Anfang ein border gesetzt als width und dann später nur ein border rechts immer dazu addiert.
				// Ansonsten wird kein Border gesetzt. Sondern danach immer rechts und links border dazu addiert.
				if(cg_horizontalSpace==0 && cg_CircleImages==0){
					aggregateWidth = aggregateWidth + widthDiv + border;
				}
				// Wenn horizontal space an ist, dann soll border immer rechts und links dazu adiert werden, wegen den Abständen.
				else{
					
					aggregateWidth = aggregateWidth + widthDiv + border*2 + cg_horizontalSpace;
					
					
				}
				
			}
			
			
			
			paddingLeftRight = (width-widthDiv)/2;
			
			
			// Image zentrieren
			realHeight = Math.floor(realHeightArray[i]);
			realWidth = Math.floor(realWidthArray[i]);
			
			
			heightIMG = width*realHeight/realWidth;
			
			
			paddingTopDown = (heightIMG-height)/2;
			
			
			// Image zentrieren --- ENDE
			
			
			 $(this).find( "[id*=cg_hide]").css('width',widthDiv);
			 $(this).find( "[id*=cg_Field1IdGalleryView]").css('width',widthDiv);			 
			 
			
			
			//paddinLeftRight = paddinLeftRight+'px';
			//var cg_border_width = $("#cg-border-width").val();
			//var cg_border_color = $("#cg-border-color").val();
			//var border = ""+cg_border_width+"px solid "+cg_border_color+";";
			
			//alert(widthDiv);
			
			//if(cg_CircleImages==1){widthDiv = widthDiv-cg_horizontalSpace;}
			
		
			$(this).css('width',widthDiv);
			$(this).css('height',height);
			//alert(height);
			$(this).css('float','left');
			 $(this).css('display','inline');
			$(this).css('box-sizing','content-box');
			//$(this).css('box-sizing','border-box');
			//$(this).css('-moz-box-sizing','border-box');
			//$(this).css('-webkit-box-sizing','border-box');			
			
			
			if(cg_CircleImages==1){$(this).css({"border-radius": ""+cg_HeightViewBorderRadius+"%"}); var circleImagesOverflow = "overflow:hidden";}
			
			
		// Verteilung Border und Margin rechts und links
		
		
		if(r == 1){
				$(this).css({"border-left": "rgba"+rgba+"  solid "+border+"px"});
		}
		
		// Jedes mal wenn eine Reihe abgearbeitet wurde, gleich beim ersten Bild border-left wieder setzten!!!
		if (last[r-1] == 'on'){
				$(this).css({"border-left": "rgba"+rgba+"  solid "+border+"px"});
		}
		else{			
			//Wenn horizontal Space vorhanden ist, dann hat auch jedes mal ein border links addiert zu werden! (Beim zweiten Bild der reihe)
			if((cg_horizontalSpace!=0 && r!=1) || (cg_CircleImages==1 && r!=1)){
				//alert(cg_CircleImages);
				$(this).css({"margin-left": ""+cg_horizontalSpace+"px"});
				$(this).css({"border-left": "rgba"+rgba+"  solid "+border+"px"});
			}			
		}
	
		//Border rechts muss immer sein!
		$(this).css({"border-right": "rgba"+rgba+"  solid "+border+"px"});
		

	
	// Verteilung Border und Margin rechts und links --- ENDE!
	
	
	
		// Positionen Info Comments und Rating auf den Galerie Images
		
		// INFO
		if(cg_TitlePositionGallery==2){
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','center');	
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-right","27px");
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-left","23px");	
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-left","23px");
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-right","23px");
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','center');	
			
			}
		else if(cg_TitlePositionGallery==3){
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','right');
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-right","27px");
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-left","23px");	
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-left","23px");
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-right","23px");
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','right');	
			
			}
		else{
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','left');
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-right","17px");
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-left","13px");	
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-left","13px");
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-right","13px");
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','left');	
			}
		
		// COMMENTS
		if(cg_CommentPositionGallery==2){
			$(this).find("div[id*=cg_hide]").find(".cg_gallery_comments_div").css("text-align","center");
		}
		else if(cg_CommentPositionGallery==3){
			$(this).find("div[id*=cg_hide]").find(".cg_gallery_comments_div").css("text-align","right");
		}
		else{
			
		}
		
		// RATING
		if(cg_RatingPositionGallery==2){
			$(this).find("div[id*=cg_hide]").find(".cg_gallery_rating_div").css("text-align","center");			
		}
		else if(cg_RatingPositionGallery==3){		
			$(this).find("div[id*=cg_hide]").find(".cg_gallery_rating_div").css("text-align","right");			
		}
		else{
			
		}	
		
		
		// Positionen Info Comments und Rating auf den Galerie Images --- ENDE
		
	
	// Verteilung Border und Margin oben und unten
	
		//Extra Bearbeitung falls gerade resized wurde
		
		if($("#cg_gallery_resize").val()==1 && border!=0){
			$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});		
		}
		
		// ACHTUNG!!!!! Wenn horizontaler Abstand gesetzt und verticaler nicht gesetzt ist, dann werdden die oberen und unteren Borders 
		//trotzdem so gesetzt als ob verticaler Abstand eingestellt ist, damit es nicht fehlerhaft aussieht.
		
		
		if(rowsDone==0){
			
			//Falls verticalSpace NULL und Pagination aktiviert ist, dann border-top nur dann setzten wenn cg_pagination_position_count NULL ist!!!!
			if(cg_pagination_position_count==0 && cg_verticalSpace==0){
				$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});
				//alert(1);
			}			
			else{
				// Vertical Space nicht null ist, dann kann immer gesetzt werden!
				if(cg_verticalSpace!=0 || (cg_verticalSpace==0 && cg_horizontalSpace!=0 ) || cg_CircleImages==1){
					$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});
				}
				else{
					// Ansonsten nur beim ersten durchlauf!
					if(rowsDone==0 && cg_InfiniteScrollGallery!=1){
						$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});
					}					
				}
				//alert(2);
			}
			
		$(this).css({"border-bottom": "rgba"+rgba+"  solid "+border+"px"});	
			if(cg_verticalSpace!=0 || (cg_verticalSpace==0 && cg_horizontalSpace!=0 )){
				$(this).css({"margin-bottom": ""+cg_verticalSpace+"px"});
			}			
		}
		else{			
			if(cg_verticalSpace!=0 || (cg_verticalSpace==0 && cg_horizontalSpace!=0 ) || cg_CircleImages==1){
				$(this).css({"margin-bottom": ""+cg_verticalSpace+"px"});				
				$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});	
				$(this).css({"border-bottom": "rgba"+rgba+"  solid "+border+"px"});
			}
			else{
				$(this).css({"border-bottom": "rgba"+rgba+"  solid "+border+"px"});	
			}			
		}

		
	
	// Verteilung Border und Margin oben und unten --- ENDE			
			
			
			

			
			
			//alert("WidthDiv: "+widthDiv)

			//$(this).find(".cg_append").append("<img src='"+srcOriginalImg+"' style='position:absolute;left: -"+paddinLeftRight+"px ;right:  -"+paddinLeftRight+"px ;max-width:none !important;' width='"+width+"px' height='"+height+"px' class='cg_image'>");
			
				/*	  if(WidthThumbPic<=300){ //alert("300");
			  $(this).find(".show-inner").find(".cg_append").append("<a href='"+hrefCGpic+"'>"+
			  "<img src='"+src300width+"' style='position:absolute;"+padding+";max-width:none !important;' width='"+WidthThumbPic+"px' height='"+HeightThumb+"px' class='cg_image'>"+
			  "</a>");
			 // alert("WidthThumb1:" + WidthThumb);		
			  }*/ 
			  

			
			
		// Wiederherstellung falls rowLogic als erstes verwendet wurde
		//$(this).find("[id*=cg_Field1IdGalleryView]").css('overflow','none');
		//$(this).find("[id*=cg_Field1IdGalleryView]").css('height','none');
		//$(this).find("[id*=cg_Field1IdGalleryView]").css('max-height','none');	
		
			
			
		
		// Höhe von input Field ausgleichen wenn es zu lang ist
		/*var heightAppend = $(this).find("div#cg_hide").height()+23; // wegen 3 bottom padding und 20 schrift
		// alert(heightAppend);
		var heightCG_Field1IdGalleryView = $(this).find("[id*=cg_Field1IdGalleryView]").height();
		// alert(heightCG_Field1IdGalleryView);
		var divHthumbHappend = height-heightAppend;
		// alert(heightCG_Field1IdGalleryView);

		
		
		
		if(heightCG_Field1IdGalleryView>divHthumbHappend){
			//alert(1);
		$(this).find("[id*=cg_Field1IdGalleryView]").css('height',divHthumbHappend);
		$(this).find("[id*=cg_Field1IdGalleryView]").css('overflow','hidden');
			
		}*/
		
		// Setzten in die Mitte falls Circle aktiviert ist
		//var testHeightLala = $(this).find("[id*=cg_hide]").height();
		//alert(testHeightLala);
		if(cg_CircleImages==1){// Achtung! Div height hier nehmen!!!
			$(this).find("[id*=cg_hide]").css('margin-bottom',height/2-45.75/2);
			//$(this).find("[id*=cg_hide]").css('padding-left',10);
		}
		
		// Höhe von input Field ausgleichen wenn es zu lang ist --- ENDE
		
		// Prüfung und bestimmung der URL Weiterleitung, falls aktiviert ist
		
			if(cg_Use_as_URL==1 && cg_ForwardToURL==1 && cg_ForwardFrom==2){

			//Prüfen ob vom user ein http bei entries der url mit eingetragen wurde, wenn nicht dann hinzufügen 
			
			var cg_img_url_entry = $("#cg_img_url"+realId+"").val();
			
			if (typeof cg_img_url_entry === 'undefined') { }
			else if(cg_img_url_entry.indexOf("http") > -1) { cg_img_url_entry = cg_img_url_entry; }	
			else{ cg_img_url_entry = "http://"+cg_img_url_entry;}
			
			
			 var cg_a_href_img = "href='"+cg_img_url_entry+"'"; 
			 
			 var cg_a_href_title = "title='Go to "+cg_img_url_entry+"'";
			 
				 if(cg_ForwardType==2){
				 
				 var cg_href_img_blank = "target='_blank'";
				 
				 }
				 
				 else{
					 
				 var cg_href_img_blank = "";	 
					 
				 }
			 
			 var cg_id_class_img = "id='cg_image_id"+realId+"'";
			 
			// alert(cg_a_href_img);
				
			}	
			else{
				
			var cg_a_href_img = "href='"+hrefCGpic+"' class='cg_href_image'";
			var cg_id_class_img = "data-cg_image_id='"+realId+"' id='cg_image_id"+realId+"' class='cg_image"+r+"'";
			 var cg_href_img_blank = "";
			 
			 				//Prüfen ob FullSizeImageOutGalleryNewTab aktiviert ist
				//alert(cg_FullSizeImageOutGalleryNewTab);
				if(cg_FullSizeImageOutGallery==1){
				if(cg_FullSizeImageOutGalleryNewTab==1){var cg_href_img_blank = "target='_blank'";}
				else{var cg_href_img_blank = "";}
				}			
			}
			
		// Prüfung und bestimmung der URL Weiterleitung, falls aktiviert ist --- ENDE	 
		
		
		// Zum Schluss wird ermittelt wieviel Zeilen bereits abgearbeitet wurden
		if (last[r] == 'on'){			
		
			rowsDone++;	
			$("#cg_gallery_resize").val(0);
			
		}
			
			
		
			  if(width<=300){ //alert("300");
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src300width+"' style='position:absolute;left: -"+paddingLeftRight+"px ;right:  -"+paddingLeftRight+"px ;"+
			  "top: -"+paddingTopDown+"px ;bottom:  -"+paddingTopDown+"px ; max-width:none !important;"+circleImagesOverflow+";' width='"+width+"px' "+cg_id_class_img+"  >"+
			  "</a>");
			  }
			  
			  else if(width<=624){ //alert("624");
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src624width+"' style='position:absolute;left: -"+paddingLeftRight+"px ;right:  -"+paddingLeftRight+"px ;"+
			  "top: -"+paddingTopDown+"px ;bottom:  -"+paddingTopDown+"px ; max-width:none !important;"+circleImagesOverflow+";' width='"+width+"px' "+cg_id_class_img+"  >"+
			  "</a>");
			  }
			  
			  else if(width<=1024){// alert("1024");
			   $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src1024width+"' style='position:absolute;left: -"+paddingLeftRight+"px ;right:  -"+paddingLeftRight+"px ;"+
			  "top: -"+paddingTopDown+"px ;bottom:  -"+paddingTopDown+"px ; max-width:none !important;"+circleImagesOverflow+";' width='"+width+"px' "+cg_id_class_img+"  >"+
			  "</a>");
			  }
			  
			  else if(width<=1100){// alert("1024");
			   $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src1024width+"' style='position:absolute;left: -"+paddingLeftRight+"px ;right:  -"+paddingLeftRight+"px ;"+
			  "top: -"+paddingTopDown+"px ;bottom:  -"+paddingTopDown+"px ; max-width:none !important;"+circleImagesOverflow+";' width='"+width+"px' "+cg_id_class_img+"  >"+
			  "</a>");
			  }
			  
			  else if(width<=1700){ //alert("src1600width: " + src1600width);
			  
			  if(src1600width==0){src1600width = srcOriginalImg;}
			  
			   $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src1600width+"' style='position:absolute;left: -"+paddingLeftRight+"px ;right:  -"+paddingLeftRight+"px ;"+
			  "top: -"+paddingTopDown+"px ;bottom:  -"+paddingTopDown+"px ; max-width:none !important;"+circleImagesOverflow+";' width='"+width+"px' "+cg_id_class_img+"  >"+
			  "</a>");
			  }
			  
			  else if(width<=2120){ //alert("src1920width: " + src1920width);
			  
			  if(src1920width==0){src1920width = srcOriginalImg;}			  
			  
			   $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src1920width+"' style='position:absolute;left: -"+paddingLeftRight+"px ;right:  -"+paddingLeftRight+"px ;"+
			  "top: -"+paddingTopDown+"px ;bottom:  -"+paddingTopDown+"px ; max-width:none !important;"+circleImagesOverflow+";' width='"+width+"px' "+cg_id_class_img+"  >"+
			  "</a>");
			  }
			  
			  else if(width>2120){// alert("1024");
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+srcOriginalImg+"' style='position:absolute;left: -"+paddingLeftRight+"px ;right:  -"+paddingLeftRight+"px ;"+
			  "top: -"+paddingTopDown+"px ;bottom:  -"+paddingTopDown+"px ; max-width:none !important;"+circleImagesOverflow+";' width='"+width+"px' "+cg_id_class_img+" >"+
			  "</a>");
			  }
			  
			// Am Ende muss diese Prüfung nicht gemacht werden
			if(i<n && cg_all_images_loaded==0){  
			  
			// Zur Ermittlung des Abstandes zum Rand des HTMLs für Pagination
			// Bei Pagination muss exakt die selbe Regel weiter unten nochmal sein
			 if(last[i+1]=="on" && cg_InfiniteScrollGallery==1 && cg_gallery_resize!=1 || cg_gallery_resize==1 && i>=cg_pagination_position_count && last[i+1]=="on" && cg_InfiniteScrollGallery==1){	

				
				var cg_offset = $("#cg_append"+realId+"").offset();
				// Ermitteln des Abstandes des ersten Bildes zum oberen Rand. Dies ist dan der Anfangswert ab dem gemessen wird.				
				if($("#firstImageOffset").val() == '0'){					
					$("#firstImageOffset").val(cg_offset.top);
				};			

				var check_cg_imgOffsetHTML = cg_imgOffsetHTML-$("#firstImageOffset").val()+200;
				
				//alert("check_cg_imgOffsetHTML "+check_cg_imgOffsetHTML);
				//alert("cg_InfiniteScrollGalleryHeight "+cg_InfiniteScrollGalleryHeight);				
				
				
				//alert(check_cg_imgOffsetHTML);
				//Prüfen ob mehr als eine Bildschirmgröße an Bildern schon geladen wurde. Dann wieder auf Null setzen.
				// Sobald cg_pagination 2 erreicht hat loading gif anzeigen und nicht mehr laden.
				// Letzter Stand: || i  cg_pagination_position_count <<< breie muss hier auch rangezogen werden. Prüfen ob es der letzte ist, wenn nicht dann noch einen durchlauf machen.
				
				// cg_InfiniteScrollGalleryHeight = Höhe des Browser Fensters
				// Sobald mehr als ein gesamter Bildschirm geladen wurde erscheint der Loading button
				// Bei Pagination muss exakt die selbe Regel weiter unten nochmal sein
				if(check_cg_imgOffsetHTML > cg_InfiniteScrollGalleryHeight && cg_gallery_resize!=1 || cg_gallery_resize==1 && i>=cg_pagination_position_count && last[i+1]=="on"){
					
					// firstImageOffset wieder auf Null setzen
					$("#firstImageOffset").val(0);
					
					//cg_pagination++; cg_imgOffsetHTML = 0;
										
					//$(this).css('display','none');// Dieser Befehl ist notwendig. Ansonsten bleibt ein Div mit Border zum Schluss noch stehen, ohne Bild drin.
					//$(this).find(".cg_append").empty();// Das Bild ist da schon erschienen, der div muss geleert werden.
					//alert("false");
					 var cg_loadingGifSource = $('#cg_loadingGifSource').val();
					 
					 
					$('#cg_pagination_position_count').val(i);
					$('#cg_aggregateWidth_gallery').val(aggregateWidth);
					//alert(cg_gallery_img_position.top);
					
					//Width welches aktuell durchlaufen wurde, wird eingetragen
					$("#cg_widthMainCGallery").val(widthmain); 
					
						$("#cg_image_id"+realId+"").on('load', function(){
						  // hide/remove the loading image
					//	 if(cg_gallery_resize==1){$(this).css("visibility","visible");}
					//	 else{ 
						// $(this).css("visibility","visible").hide().fadeIn(3000);
						// $("#cover"+realId+"").fadeOut(3000);
						 // $("#cover"+realId+"").css("display","none");
					//	 }
						 // alert(3);
						  //$(this).css("visibility","visible");
						  
									if(showAlways==1){  
									  
									  
									  // Ansonsten wird bei pagination nicht das aller letzte Bild before loading.gif gewählt, um cg_hide zu zeigen
									  var cg_i_last_image_before_gif = i+1;

										//$(this).find("[id*=cg_hide]").show();
										$("#cg_hide"+cg_i_last_image_before_gif+"").show();
										//$(this).find( "[id*=cg_Field1IdGalleryView]").show();
										$("#cg_Field1IdGalleryView"+i+"").show();
									  
									  
										if(widthDiv<cg_FbLikeGallery){
											
										//$(this).find("[id*=cg_hide]").hide();
										$("#cg_hide"+i+"").hide();
										//$(this).find( "[id*=cg_Field1IdGalleryView]").hide();
										$("#cg_Field1IdGalleryView"+i+"").hide();
											
										}
									
									}
					 
					 
					 
					 
					$("#cg_gallery_loading_gif").remove();
					$("#mainCGdiv").append("<img id='cg_gallery_loading_gif' src='"+cg_loadingGifSource+"' width='24px' height='24px' style='display: block !important; margin:0 auto; '>");
					
					//alert(i);
					

						  
						  
						  
						});
					
					
					
					
					
					return false;
					
					
					}
					
					

				
				//Wenn nicht, dann weiter Abstand zum Oben dazu adieren
				else{cg_imgOffsetHTML = cg_imgOffsetHTML+cg_offset.top;}					
				
				
				
			}
			

			}  

			
			//if(cg_pagination==2){return false;}
			  
			  
			  
			  
			  
			
			//$(this).find(".cg_image").attr('width',width);
			//$(this).find(".cg_image").attr('height',height);
			
			
			//$(this).find(".cg_image").css('left',-paddinLeftRight);
			//$(this).find(".cg_image").css('right',-paddinLeftRight); 
			
			
			//$(this).css("display","inline");
			

	  
					// cg_hide Klasse ist die Div Box zum Hovern 
			/*	echo <<<HEREDOC
		<div style='float:left;display:inline;width:$widthDiv;height:$height;' class='show'><a href="$urlGalleryHref#begin" $scriptOnOff title="$commentText" >
		<img style="left: -$paddinLeftRight !important; right: -$paddinLeftRight !important; position: absolute !important; max-width:none !important;" alt="$commentText" src="$uploads/$Timestamp$imageThumb" class='cg_image' $JqgGalery1 width='$width' height='$height'></a>
		<div style='position:absolute;' class='cg_hide'>
		<p style='display:block;padding-top:1px;padding-bottom:5px;background: rgba(0, 128, 128, .6);background: #008080;background-image: url($commentIcon); background-repeat:no-repeat;background-position: 40px 4px;'>
		<img src="$sourceStar">($countRating)
		&nbsp;&nbsp;($countComments)</p></div></div>
HEREDOC;

	*/   
	
	$("#cg_image_id"+realId+"").on('load', function(){
  // hide/remove the loading image
 //if(cg_gallery_resize==1){$(this).css("visibility","visible");}
 //else{ 
// $(this).css("visibility","visible").hide().fadeIn(3000);
 //$("#cover"+realId+"").fadeOut(300);
// $("#cover"+realId+"").css("display","none");
 // }
 // alert(3);
  //$(this).css("visibility","visible");
  
			if(showAlways==1){
			  

				//$(this).find("[id*=cg_hide]").show();
				$("#cg_hide"+i+"").show();
				//$(this).find( "[id*=cg_Field1IdGalleryView]").show();
				$("#cg_Field1IdGalleryView"+i+"").show();
			  
			  
				if(widthDiv<cg_FbLikeGallery){
					
				//$(this).find("[id*=cg_hide]").hide();
				$("#cg_hide"+i+"").hide();
				//$(this).find( "[id*=cg_Field1IdGalleryView]").hide();
				$("#cg_Field1IdGalleryView"+i+"").hide();
					
				}
			
			}
  
  
  
});
	
	
	
	
	
	
}
i++;
if(i==n){$("#cg_all_images_loaded").val(1);}	  
	  });
	  
	 // if(document.readyState === "complete"){ 
	  	 /* $( ".cg_show" ).each(function( i ) {


 //$( window ).load(function() {
	

	  
$(this).css("display","inline");
			


//});

	  
	  });	*/
	
	
}




function thumbLogic(){
	
 //alert(123213);
 
	var cg_gallery_resize = $('#cg_gallery_resize').val();
	
	  // Wenn alle Bilder geladen wurden, sollten hinsichtlich pagination nichts mehr passieren
	var cg_all_images_loaded = $('#cg_all_images_loaded').val();
 
  // Ermitteln von Breite des parent Divs nach resize
  var widthMainCGallery = parseInt($('#mainCGdiv').parent().width());
  
  
  // Breite des divs in dem sich die Galerie befindet
  var widthmain = widthMainCGallery;
  
    // Wenn pagination an ist, dann muss der erste Width Wert hier eingetragen werden
  var check_cg_widthMainCGallery = $("#cg_widthMainCGallery").val();
  
  
  
  	if(cg_gallery_resize==0 && check_cg_widthMainCGallery!=0){widthmain=$("#cg_widthMainCGallery").val();}	
	  
    else{widthmain=widthmain;}
  
  
  $('#mainCGallery').css('width',widthmain);  
  
  
  var opacity = $('#cg_ThumbViewBorderOpacity').val();
 //opacity = opacity.substring(1);

//opacity = opacity.substring(1);


//alert(opacity);
  
  var borderColor = $("#cg_ThumbViewBorderColor").val();
  
 // alert(borderColor);
  
	var border = parseInt($("#cg_ThumbViewBorderWidth").val());
  
  
  
  function hex2rgb(hexStr){
    // note: hexStr should be #rrggbb
    var hex = parseInt(hexStr.substring(1), 16);
    var r = (hex & 0xff0000) >> 16;
    var g = (hex & 0x00ff00) >> 8;
    var b = hex & 0x0000ff;
    return [r, g, b];
    //return r;
}

var rcolor = hex2rgb(borderColor);

//alert(rcolor[0]);
 
var rgba = "("+rcolor[0]+","+rcolor[1]+","+rcolor[2]+", "+opacity+")";

//alert(rgba);
 
// rgba(255, 0, 0, .5) 

	 

	
  // Summe der einzelnen Breiten
  var aggregateWidth = 0;
  

  // Anzahl der hochgeladenen Bilder
  var n = $( ".cg_show" ).length;
  
   if(cg_InfiniteScrollGallery==1){
	  
	 var cg_InfiniteScrollGalleryHeight = $(window).height();
	 
	// alert(cg_InfiniteScrollGalleryHeight);
	  
  }
  
	//   $("#mainCGallery").css('overflow','auto');
	//   $("#mainCGallery").css('vertica-align','top');
	//   $("#mainCGallery").css('clear','left');
	 //  $("#mainCGallery").css('clear','both');
  
   var h = 0;
   
   // wird am Ende verabeitet
   var i = 0;
   
   //var aggregateWidth = 0;
   

  var newHeight = 0;   
   
   //alert('checkSumOfElementsWidth'+aggregateWidth);
   
   //var checkFirstTimeWholeWidth = 0;
   
 // wird am Anfang verabeitet
   var r = 0;
   
   
   // Zur Ermittlung des Abstandes zum Rand des HTMLs für Pagination
   var cg_imgOffsetHTML = 0;
   
   // Sobald cg_pagination 1 erreicht hat, dann loading gif anzeigen und nicht mehr laden, bis weiter gescrollt wird
   var cg_pagination = 0;


	var WidthThumb = parseInt($('#cg_WidthThumb').val());
	var HeightThumb = parseInt($('#cg_HeightThumb').val());
	
	    var DistancePicsX = parseInt($('#cg_DistancePicsX').val());
	var DistancePicsV = parseInt($('#cg_DistancePicsV').val());
	
//alert("DistancePicsV "+DistancePicsV);

	 var border = parseInt($("#cg_ThumbViewBorderWidth").val());
	
		if(cg_ThumbViewBorderRadius>5){var cg_CircleImages=1;}
		else{var cg_CircleImages=0;}
	
//	alert(cg_AdjustThumbLook);
var cg_AdjustThumbLook=1;
//var border = 10;
//alert(DistancePicsX);
//alert(border);
	//if(cg_AdjustThumbLook==1){		
		
		//0 = widthmain-(WidthThumb+2*border+DistancePicsX)*xMulti+DistancePicsX+2*border;
		
		//0 = widthmain -((WidthThumb+2*border+DistancePicsX)*xMulti-DistancePicsX);

		//0 =  widthmain+DistancePicsX+2*border-(WidthThumb+2*border+DistancePicsX)*xMulti;

		//(WidthThumb+2*border+DistancePicsX+2*border)*xMulti = widthmain-DistancePicsX;
			 
		//var xMultiFull  = (widthmain+DistancePicsX)/(WidthThumb+2*border+DistancePicsX);
	//alert(widthmain);
	//alert(DistancePicsX);
	//alert(WidthThumb);
	//alert(border);
	//alert(DistancePicsX);

	//ACHTUNG!! Das hier wichtig. Int Value geht nach einem Lauf verloren.
	widthmain = parseInt(widthmain);
	DistancePicsX = parseInt(DistancePicsX);
	
		//alert(widthmain+DistancePicsX);
	var xMultiFull  = (widthmain+DistancePicsX)/(WidthThumb+2*border+DistancePicsX);
		//var str0 = 2;
	//	alert(widthmain);
//	alert(xMultiFull);
	//console.log(xMultiFull);
	
	// Genauer Multiplikator zur Exakten Berechnung von WidthThumb beim Adjustment, welches jetzt standardmäßig immer aktiviert ist bei dieser Ansicht.
	if(xMultiFull<=1){
		var xMulti1 = xMultiFull;		
		
		//Wichtig! Zum Ausgleich in der Breite je nach Anzahl der Bilder.
		var xMultiFull1 = 0;		
	}
	
	else{
	
	xMultiFull1 = xMultiFull.toString();
	//	alert(border);
	
	
					xMultiFull1 = xMultiFull.toString();	
				//alert(xMultiFull);
				var xMulti = xMultiFull1.split(".");
				
				var picsInRow = parseInt(xMulti[0]);
				
				var cg_thumb_multiplier = parseInt(xMulti[1]);
				
				var test9485 = 1-(picsInRow+1-xMultiFull);
			//	alert(test9485);
				
			//	if(xMultiFull<=0.5){
					
				var xMulti1 = (widthmain+DistancePicsX)/(((WidthThumb+2*border+DistancePicsX)*picsInRow));
				
	}		
			//	console.log(xMulti1);
				
				//alert(xMultiFull1);
				
				//Wichtig! Zum Ausgleich in der Breite je nach Anzahl der Bilder.
				var xMultiFull1Adjustment = Math.ceil(xMultiFull1);

				
					WidthThumb=WidthThumb*xMulti1;
					WidthThumb=Math.floor(WidthThumb)-xMultiFull1Adjustment;
					//alert(WidthThumb);
					HeightThumb=HeightThumb*xMulti1;
					DistancePicsX=DistancePicsX*xMulti1;
					DistancePicsV=DistancePicsV*xMulti1;
					
				//	var DistancePicsV = parseInt($('#cg_DistancePicsV').val());
					border=border*xMulti1;
					
					

	
	var cg_pics_per_row = Math.floor(widthmain/(WidthThumb + DistancePicsX+border*2));
	//alert(cg_gallery_resize);
	
	var aggregateWidth = parseInt($('.aggregateWidth').val()); // Hidden Feld zum sammeln und abrufen von aggregateWidth über Jquery
	
	var firstRow = 0;
    
	  

   
$( ".cg_show" ).each(function( i ) {
	
	r++;
	
		//Achtung entspricht nicht der Anzahl der tatsächlich geladenen Bilder. Fängt mit 0 an. Wird auch für das gesammelte Height und Width Array verwendet.
	var cg_pagination_position_count = $('#cg_pagination_position_count').val();
	
		// Wenn resize gemacht wurde gerade und logic läuft, dann sollen ALLE nochmal abgearbeitet werden
	
	if(i<=cg_pagination_position_count && cg_InfiniteScrollGallery==1 && cg_pagination_position_count!=0 && cg_gallery_resize!=1){
		//var cg_do_nothing = 1;	
		}
		
	else{
	
	

	


	
	var checkFirstTimeWholeWidth = parseInt($('.checkFirstTimeWholeWidth').val()); // Überprüfen ob die erste Zeile der Bilder schon verarbeitetet wurde
	//var DistancePics = parseInt($(this).find('.DistancePics').val());
	//var DistancePicsV = parseInt($(this).find('.DistancePicsV').val());
	
	//alert(aggregateWidth);


	 var widthOriginalImg = parseInt($(this).find('.widthOriginalImg').val());
	 var heightOriginalImg = parseInt($(this).find('.heightOriginalImg').val());
	 //var WidthThumb = parseInt($(this).find('.WidthThumb').val());
	// var HeightThumb = parseInt($(this).find('.HeightThumb').val());
	 
	 //alert(WidthThumb);
	 //alert(WidthThumb);
	 
	 //$('#mainCGdiv').parent().width());
	 
	 
	 var hrefCGpic = $(this).find('.hrefCGpic').val();
	 
		
	 
	 var realId = $(this).find('.realId').val();
	 var srcOriginalImg = $(this).find('.srcOriginalImg').val();
	 var src1920width = $(this).find('.src1920width').val();
	 var src1600width = $(this).find('.src1600width').val();
	 var src1024width = $(this).find('.src1024width').val();
	 var src624width = $(this).find('.src624width').val();
	 var src300width = $(this).find('.src300width').val();
	 
	 
	 
	 var cg_Use_as_URL = $('#cg_Use_as_URL').val();
	 var cg_ForwardToURL = $('#cg_ForwardToURL').val();
	 var cg_ForwardType = $('#cg_ForwardType').val();
	 var cg_ForwardFrom = $('#cg_ForwardFrom').val();
	 var cg_FullSizeImageOutGalleryNewTab = $('#cg_FullSizeImageOutGalleryNewTab').val();
	 var cg_FullSizeImageOutGallery = $('#cg_FullSizeImageOutGallery').val();

	 
	 
	 
	 
	 
	 // Ermittlung der Höhe nach Skalierung. Falls unter der eingestellten Höhe, dann nächstgrößeres Bild nehmen.
					var heightScaledThumb = WidthThumb*heightOriginalImg/widthOriginalImg;
					
					//alert("heightScaledThumb: "+heightScaledThumb);
					// Falls unter der eingestellten Höhe, dann größeres Bild nehmen (normales Bild oder panorama Bild, kein Vertikalbild)
					
					if (heightScaledThumb <= HeightThumb) {
					
					var widthScaledThumb = HeightThumb*widthOriginalImg/heightOriginalImg;
					
				//	if(widthScaledThumb <= 300) {var imageThumb = src300width;}		
				//	if(widthScaledThumb > 300 && widthScaledThumb <= 624) {var imageThumb = src624width;}		
			//		if(widthScaledThumb > 624 && widthScaledThumb <= 1024) {var imageThumb = src1024width; }		
			//		if(widthScaledThumb > 1024) {var imageThumb = srcOriginalImg;}

					// Bestimmung von Breite des Bildes
					var WidthThumbPic = HeightThumb*widthOriginalImg/heightOriginalImg;
				
					
					// Bestimmung von Breite des Bildes
					var WidthThumbPic = WidthThumbPic+2;
					//$WidthThumbPic = $WidthThumbPic.'px';

					// Bestimmung wie viel links und rechts abgeschnitten werden soll
					var paddingLeftRight = (WidthThumbPic-WidthThumb)/2;
					    paddingLeftRight = paddingLeftRight+'px';
					
					var padding = "left: -"+paddingLeftRight+";right: -"+paddingLeftRight+"";	

					var HeightThumbImageValue = HeightThumb+"px";
					
					
					}
					
						
					// Falls über der eingestellten Höhe, dann kleineres Bild nehmen (kein Vertikalbild)
					if (heightScaledThumb > HeightThumb) {
					//if(WidthThumb <= 300) {var imageThumb = src300width;}		
					//if(WidthThumb > 300 && WidthThumb <= 624) {var imageThumb = src624width;}		
					//if(WidthThumb > 624 && WidthThumb <= 1024) {var imageThumb = src1024width;}		
					//if(WidthThumb > 1024) {var imageThumb = srcOriginalImg;}
					
					//alert("WidthThumb: "+WidthThumb);
					
					// Bestimmung von Breite des Bildes
					var WidthThumbPic = WidthThumb+2;
					
					//alert("WidthThumbPic: "+WidthThumbPic1);
					
					
					//$WidthThumbPic = $WidthThumbPic.'px';
					
					// Bestimmung wie viel oben und unten abgeschnitten werden soll
					var heightImageThumb = WidthThumb*heightOriginalImg/widthOriginalImg;
					var paddingTopBottom = (heightImageThumb-HeightThumb)/2;
					    paddingTopBottom = paddingTopBottom+'px';
						
					var padding = "top: -"+paddingTopBottom+";bottom: -"+paddingTopBottom+"";	
					
					var HeightThumbImageValue = "auto";
					
					}

	
	var putDistancePicsX = DistancePicsX;
			
			
	// DistancePicsX folgt dann weiter unten!
	aggregateWidth = aggregateWidth + WidthThumb + border*2;
//alert(DistancePicsX);


				
				var check3849 = r-1;
				
				
				// ACHTUNG DAS HIER IST NUR FÜR DistancePicX!
				if(check3849 % picsInRow == 0){
					//alert(r);
					//alert(picsInRow);
					putDistancePicsX=0;
					
					if(r-1!=0){firstRow++;}
			
				}
				

				
				if(r==1 && xMultiFull<=1){
					
				putDistancePicsX=0;
				//firstRow++;
					
				}
				
				if(r==2 && xMultiFull<=1){
					
				firstRow++;
					
				}
				

			
			
			
			$(this).css('width',WidthThumb);
			$(this).find( "[id*=cg_hide]").css('width',WidthThumb);
			$(this).find( "[id*=cg_Field1IdGalleryView]").css('width',WidthThumb);
			
			$(this).css('margin-left',putDistancePicsX);
			$(this).css('margin-right',0);
			//alert("firstRow "+firstRow);
			//alert("cg_pagination_position_count "+cg_pagination_position_count);
			//cg_InfiniteScrollGallery
			if(firstRow==0 && cg_pagination_position_count==0){
				$(this).css('margin-top',0);
			}
			else{$(this).css('margin-top',DistancePicsV);}
			
			$(this).css({"border": "rgba"+rgba+" solid "+border+"px"});
			if(cg_CircleImages==1){$(this).css({"border-radius": ""+cg_ThumbViewBorderRadius+"%"}); var circleImagesOverflow = "overflow:hidden";}	
			
			

			$(this).css('display','inline-block');
			$(this).css('vertical-align','top');

			$(this).css('cursor','pointer');

			
			$(this).find(".cg_append").css({"float":"left","display":"inline","width": ""+WidthThumb+"px","height": ""+HeightThumb+"px"});


		aggregateWidth = aggregateWidth + DistancePicsX;					

		
		
		// Höhe von input Field ausgleichen wenn es zu lang ist --- ENDE
		
		var heightInfoDiv = $(this).find("div[id*=cg_hide]").height();
		$(this).find("div[id*=cg_hide]").children("div").css('position','relative !important');
		var heightInfoDivInfo = $(this).find("div[id*=cg_hide]").children(".cg_info_depend_on_radius").height();
		
		//alert(heightInfoDivInfo);
		
		// Setzten in die Mitte falls Circle aktiviert ist
		//var testHeightLala = $(this).find("[id*=cg_hide]").height();
		//alert(testHeightLala);
		if(cg_CircleImages==1){// Achtung! Div height hier nehmen!!!
			$(this).find("[id*=cg_hide]").css('margin-bottom',HeightThumb/2-heightInfoDiv/2);
			//$(this).find("[id*=cg_hide]").css('padding-left',10);
			//$(this).find("[id*=cg_hide]").css('padding-right',10);
		}
		
		//alert($(this).find("div[id*=cg_hide]").width());
		//
		
		//alert(WidthThumb);
		
		
		
		
		// Positionen Info Comments und Rating auf den Galerie Images
		
		// INFO
		if(cg_TitlePositionGallery==2){
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','center');	
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','center');	
			
			}
		else if(cg_TitlePositionGallery==3){
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','right');
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','right');	
			
			}
		else{
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','left');
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','left');	
			}
		
		// COMMENTS
		if(cg_CommentPositionGallery==2){	
			$(this).find("div[id*=cg_hide]").find(".cg_info_comment_gallery_div").css("text-align","center");
		}
		else if(cg_CommentPositionGallery==3){
			$(this).find("div[id*=cg_hide]").find(".cg_info_comment_gallery_div").css("text-align","right");
		}
		else{
			
		}
		
		// RATING
		if(cg_RatingPositionGallery==2){
			$(this).find("div[id*=cg_hide]").find(".cg_gallery_rating_div").css("text-align","center");			
		}
		else if(cg_RatingPositionGallery==3){		
			$(this).find("div[id*=cg_hide]").find(".cg_gallery_rating_div").css("text-align","right");			
		}
		else{
			
		}	
		
		
		// Positionen Info Comments und Rating auf den Galerie Images --- ENDE
		
				

			
		
	if(cg_Use_as_URL==1 && cg_ForwardToURL==1 && cg_ForwardFrom==2){

    //Prüfen ob vom user ein http bei entries der url mit eingetragen wurde, wenn nicht dann hinzufügen
	
	var cg_img_url_entry = $("#cg_img_url"+realId+"").val();
	
	if (typeof cg_img_url_entry === 'undefined') { }
	else if(cg_img_url_entry.indexOf("http") > -1) { cg_img_url_entry = cg_img_url_entry; }	
	else{ cg_img_url_entry = "http://"+cg_img_url_entry;}
	//alert(cg_img_url_entry);
	
	 var cg_a_href_img = "href='"+cg_img_url_entry+"'"; 
	 
	 var cg_a_href_title = "title='Go to "+cg_img_url_entry+"'";
			 
				 if(cg_ForwardType==2){
				 
				 var cg_href_img_blank = "target='_blank'";
				 
				 }
				 
				 else{
					 
				 var cg_href_img_blank = "";	 
					 
				 }
			 
			 var cg_id_class_img = "id='cg_image_id"+realId+"'";
			 

			 
			// alert(cg_a_href_img);
				
			}
			else{
				
			var cg_a_href_img = "href='"+hrefCGpic+"' class='cg_href_image'";
			var cg_id_class_img = "data-cg_image_id='"+realId+"' id='cg_image_id"+realId+"' class='cg_image"+r+"'";
			
				//Prüfen ob FullSizeImageOutGalleryNewTab aktiviert ist
				//alert(cg_FullSizeImageOutGalleryNewTab);
				
				if(cg_FullSizeImageOutGallery==1){
				if(cg_FullSizeImageOutGalleryNewTab==1){var cg_href_img_blank = "target='_blank'";}
				else{var cg_href_img_blank = "";}
				}
			
			}
			
		// Prüfung und bestimmung der URL Weiterleitung, falls aktiviert ist --- ENDE	

		  if(WidthThumbPic<=300){// alert("300");
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src300width+"' style='position:absolute;"+padding+";max-width:none !important;"+circleImagesOverflow+";' width='"+WidthThumbPic+"px' height='"+HeightThumbImageValue+"' "+cg_id_class_img+" >"+
			  "</a>");
			 // alert("WidthThumb1:" + WidthThumb);		
			  }
			  
			  else if(WidthThumbPic<=624){//alert("600");
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src624width+"' style='position:absolute;"+padding+";max-width:none !important;"+circleImagesOverflow+";' width='"+WidthThumbPic+"px' height='"+HeightThumbImageValue+"' "+cg_id_class_img+" >"+
			  "</a>");
			  }
			  
			  else if(WidthThumbPic<=1100){//alert("1000");
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src1024width+"' style='position:absolute;"+padding+";max-width:none !important;"+circleImagesOverflow+";' width='"+WidthThumbPic+"px' height='"+HeightThumbImageValue+"' "+cg_id_class_img+" >"+
			  "</a>");
			  }
			  
			  else if(WidthThumbPic<=1700){//alert("2000");
			  
				if(src1600width==0){src1600width = srcOriginalImg;}
			  
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src1600width+"' style='position:absolute;"+padding+";max-width:none !important;"+circleImagesOverflow+";' width='"+WidthThumbPic+"px' height='"+HeightThumbImageValue+"' "+cg_id_class_img+">"+
			  "</a>");
			  }
			  
			  else if(WidthThumbPic<=2120){//alert("2000");
			  
				if(src1920width==0){src1920width = srcOriginalImg;}
			  
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+src1920width+"' style='position:absolute;"+padding+";max-width:none !important;"+circleImagesOverflow+";' width='"+WidthThumbPic+"px' height='"+HeightThumbImageValue+"' "+cg_id_class_img+">"+
			  "</a>");
			  }
			  
			  else if(WidthThumbPic>2120){//alert("2000");
			  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
			  "<img src='"+srcOriginalImg+"' style='position:absolute;"+padding+";max-width:none !important;"+circleImagesOverflow+";' width='"+WidthThumbPic+"px' height='"+HeightThumbImageValue+"' "+cg_id_class_img+">"+
			  "</a>");
			  }

	  
			  
			// Am Ende muss diese Prüfung nicht gemacht werden
			if(i<n && cg_all_images_loaded==0){
			  
			// Zur Ermittlung des Abstandes zum Rand des HTMLs für Pagination
			// Bei Pagination muss exakt die selbe Regel weiter unten nochmal sein
			 if(r % cg_pics_per_row == 0 && cg_InfiniteScrollGallery==1 && cg_gallery_resize!=1 || cg_gallery_resize==1 && i>=cg_pagination_position_count && r % cg_pics_per_row == 0 && cg_InfiniteScrollGallery==1){	
//alert("i "+i);


//$("#cg_append"+realId+"").css("display","inline");

				var cg_offset = $("#cg_append"+realId+"").offset();
				// Ermitteln des Abstandes des ersten Bildes zum oberen Rand. Dies ist dan der Anfangswert ab dem gemessen wird.				
				if($("#firstImageOffset").val() == '0'){					
					$("#firstImageOffset").val(cg_offset.top);
				};			

				var check_cg_imgOffsetHTML = cg_imgOffsetHTML-$("#firstImageOffset").val()+200;			
				
				
				//alert(check_cg_imgOffsetHTML);
				//Prüfen ob mehr als eine Bildschirmgröße an Bildern schon geladen wurde. Dann wieder auf Null setzen.
				// Sobald cg_pagination 2 erreicht hat loading gif anzeigen und nicht mehr laden.
				// Letzter Stand: || i  cg_pagination_position_count <<< breie muss hier auch rangezogen werden. Prüfen ob es der letzte ist, wenn nicht dann noch einen durchlauf machen.
				
				// cg_InfiniteScrollGalleryHeight = Höhe des Browser Fensters
				// Sobald mehr als ein gesamter Bildschirm geladen wurde erscheint der Loading button
				// Bei Pagination muss exakt die selbe Regel weiter unten nochmal sein
				
				
				if(check_cg_imgOffsetHTML > cg_InfiniteScrollGalleryHeight && cg_gallery_resize!=1 || cg_gallery_resize==1 && i>=cg_pagination_position_count && r % cg_pics_per_row == 0){
					
					// firstImageOffset wieder auf Null setzen
					$("#firstImageOffset").val(0);
										
					//$(this).css('display','none');// Dieser Befehl ist notwendig. Ansonsten bleibt ein Div mit Border zum Schluss noch stehen, ohne Bild drin.
					//$(this).find(".cg_append").empty();// Das Bild ist da schon erschienen, der div muss geleert werden.
					//alert("false");
					 var cg_loadingGifSource = $('#cg_loadingGifSource').val();
					 
					$("#cg_gallery_loading_gif").remove();
					$("#mainCGdiv").append("<img id='cg_gallery_loading_gif' src='"+cg_loadingGifSource+"' width='24px' height='24px' style='display: block !important; margin:0 auto; '>");
					
					//alert(i);
					
					$('#cg_pagination_position_count').val(i);
					$('#cg_aggregateWidth_gallery').val(aggregateWidth);
					//alert(cg_gallery_img_position.top);
					
					//Width welches aktuell durchlaufen wurde, wird eingetragen
					$("#cg_widthMainCGallery").val(widthmain); 
					
						$("#cg_image_id"+realId+"").on('load', function(){
						  // hide/remove the loading image
						  /*if(cg_gallery_resize==1){$(this).css("visibility","visible");}
						  else{ $(this).css("visibility","visible").hide().fadeIn(3000);}
						 // alert(3);
						  //$(this).css("visibility","visible");*/
						  
									if(showAlways==1){  
									  
										// Ansonsten wird bei pagination nicht das aller letzte Bild before loading.gif gewählt, um cg_hide zu zeigen
										var cg_i_last_image_before_gif = i+1;
										
										//$(this).find("[id*=cg_hide]").show();
										$("#cg_hide"+cg_i_last_image_before_gif+"").show();
										//$(this).find( "[id*=cg_Field1IdGalleryView]").show();
										$("#cg_Field1IdGalleryView"+i+"").show();
									  
									  
										if(WidthThumb<cg_FbLikeGallery){
											
										//$(this).find("[id*=cg_hide]").hide();
										$("#cg_hide"+i+"").hide();
										//$(this).find( "[id*=cg_Field1IdGalleryView]").hide();
										$("#cg_Field1IdGalleryView"+i+"").hide();
											
										}
									
									}
						  
						  
						  
						});
					
					
					
					
					
					return false;
					
					
					}
					
					

				
				//Wenn nicht, dann weiter Abstand zum Oben dazu adieren
				else{cg_imgOffsetHTML = cg_imgOffsetHTML+cg_offset.top;}					
				
				
				
			}
			

			}		  
			  
			

	//$('.aggregateWidth').val(aggregateWidth); // Hidden Feld zum sammeln und abrufen von aggregateWidth über Jquery
	
		$("#cg_image_id"+realId+"").on('load', function(){
  // hide/remove the loading image
 /* if(cg_gallery_resize==1){$(this).css("visibility","visible");}
  else{ $(this).css("visibility","visible").hide().fadeIn(3000);}*/
 // alert(3);
  //$(this).css("visibility","visible");
  
			if(showAlways==1){  
			  

				//$(this).find("[id*=cg_hide]").show();
				$("#cg_hide"+i+"").show();
				//$(this).find( "[id*=cg_Field1IdGalleryView]").show();
				$("#cg_Field1IdGalleryView"+i+"").show();
			  
			  
				if(WidthThumb<cg_FbLikeGallery){
					
				//$(this).find("[id*=cg_hide]").hide();
				$("#cg_hide"+i+"").hide();
				//$(this).find( "[id*=cg_Field1IdGalleryView]").hide();
				$("#cg_Field1IdGalleryView"+i+"").hide();
					
				}
			
			}
  
  
  
})
	  
}
	
	i++;
	
	
//alert("i"+i)
	  
	  });
	  
	 // if(document.readyState === "complete"){ 
	/*  	  $( ".cg_show" ).each(function( i ) {


 //$( window ).load(function() {
	

	  
$(this).css("display","inline");
			


//});

	  
	  });	*/
$(".checkFirstTimeWholeWidth").val(0);
 $(".aggregateWidth").val(0);	
}


function rowLogic(){
	
	// Loading source für loading gif
	var cg_loadingGifSource = $('#cg_loadingGifSource').val();

var cg_gallery_resize = $('#cg_gallery_resize').val();
//alert(look);

  // Wenn alle Bilder geladen wurden, sollten hinsichtlich pagination nichts mehr passieren
	var cg_all_images_loaded = $('#cg_all_images_loaded').val();

   // alert("ROW ANSICHT");
 
 // var height1 = parseInt(height);
 
 // Array für neue Höhen
  var newHeight1 = [];
  
  var newHeight2 = 0;
  
  // Beginn des Nenners
  var ratio = 0;
  
  // Array für mehrere Nenner (Gesamtzähler)
  var denominator1 = [];
  // a bestimmt mehrere Nenner
  var a = 0;
  
  // Gesamter Zähler
  var newNumerator = 0;
	
  // Beginn des Zählers im Bruch 
  var numerator = 0;
  
  // width/height
  var divArray = [];
  
  // Neue Höhe 
  var newHeight = 0;
  
  var partNumerator = 0;
  
  // Anzahl der hochgeladenen Bilder
  var n = $( ".cg_show" ).length;
  
  if(cg_InfiniteScrollGallery==1){
	  
	 var cg_InfiniteScrollGalleryHeight = $(window).height();
	 
	// alert(cg_InfiniteScrollGalleryHeight);
	  
  }
  
  //alert("N:"+n);
  
  // Wie viele Bilder sollen in einer Reihe dargestellt werden. Einstellung erfolgt durch User in Options
 // var picsInRow = $("#cg_PicsInRow").val();
   var picsInRow = $("#cg_row_configuration_pics_in_a_row").val();

  
  //alert("PicsInRow: "+picsInRow);
  
  // Wie viele Bilder sollen pro Seite dargestellt werden. Einstellung erfolgt durch User in Options.
  //var picsPerSite = $("#cg_PicsPerSite").val();
 

  
  
  var opacity = $('#cg_RowViewBorderOpacity').val();
  
  var borderColor = $("#cg_RowViewBorderColor").val();
  
 // alert(borderColor);
  

 var border = parseInt($("#cg_RowViewBorderWidth").val());
  
  function hex2rgb(hexStr){
    // note: hexStr should be #rrggbb
    var hex = parseInt(hexStr.substring(1), 16);
    var r = (hex & 0xff0000) >> 16;
    var g = (hex & 0x00ff00) >> 8;
    var b = hex & 0x0000ff;
    return [r, g, b];
    //return r;
}

var rcolor = hex2rgb(borderColor);

//alert(rcolor[0]);
 
var rgba = "("+rcolor[0]+","+rcolor[1]+","+rcolor[2]+", "+opacity+")";

//alert(rgba);
 
// rgba(255, 0, 0, .5)
	 
  //alert("border: "+rgba);
	
  // Summe der einzelnen Breiten
  var aggregateWidth = border+cg_horizontalSpace;  
  
 //alert("border: "+border);  
  
  // Breite des divs in dem sich die Galerie befindet
     var widthMainCGallery = $('#mainCGdiv').parent().width();
	   //alert("widthMainCGallery: "+widthMainCGallery);
	
	  //alert("widthMainCGallery: "+widthMainCGallery);
		
  //var widthmain = $('#mainCGallery').width();
  var widthmain = widthMainCGallery;
  

      // Wenn pagination an ist, dann muss der erste Width Wert hier eingetragen werden
  var check_cg_widthMainCGallery = $("#cg_widthMainCGallery").val();  
  
  	if(cg_gallery_resize==0 && check_cg_widthMainCGallery!=0){widthmain=$("#cg_widthMainCGallery").val(); }	
	  
    else{widthmain=widthmain;}
  
  	
		//Width welches aktuell durchlaufen wurde, wird eingetragen
	$("#cg_widthMainCGallery").val(widthmain);   

  //var widthmainDiv = widthmain-5;
  
  //Hauptwidth der cg_gallery wird als css eingetragen
    $('#mainCGallery').css('width',widthmain);
  //alert("Widthmain: "+widthmain); 
  
	// Der erste horizontalSpace (von links kommend) darf nicht vorkommen, der letzte soll abgezogen werden!
	// Wenn nicht null dann muss immer von beiden seiten border hinzugefügt werden
	
	
		 	 //	var cg_RowViewBorderRadius = $("#cg_RowViewBorderRadius").val();
	//var cg_RowViewSpaceWidth = $("#cg_RowViewSpaceWidth").val();
	//var cg_RowViewSpaceHeight = $("#cg_RowViewSpaceHeight").val();
	
//	alert(cg_RowViewSpaceWidth);
	//alert(cg_RowViewSpaceHeight);
	//alert(cg_RowViewBorderRadius);
	
	var cg_horizontalSpace = cg_RowViewSpaceWidth;
	var cg_verticalSpace = cg_RowViewSpaceHeight;
	
	
	
	
	if(cg_RowViewBorderRadius>5){var cg_CircleImages=1;}
	else{var cg_CircleImages=0;}
	
	
	
	if(cg_horizontalSpace!=0 || cg_CircleImages==1){
		widthmain = widthmain-(border*2+cg_horizontalSpace)* picsInRow+cg_horizontalSpace;
	}
	// Wenn null dann muss border immer nur von einer Seite hinzugefügt werden	
	else{
		widthmain = widthmain-(border+cg_horizontalSpace)* picsInRow+cg_horizontalSpace-border;
	}
	
//	alert(widthmain);
	
  // Gesamtbreite wird neu berechnet, da Anzahl der Bilder (.cg_image) kleiner ist als eingestellte Anzahl der Bilder in einer Reihe in Options 
  if(n < picsInRow){
  
  widthmain = widthmain/picsInRow*n;
  picsInRow = n;
  
  //alert("Widthmain"+widthmain);
  
  }
  
  

    // alert("Widthmain: "+widthmain);
  
  
  var widthLastRow = widthmain/picsInRow*(n-Math.floor(n/picsInRow)*picsInRow);
  
    
  //var lastRow = $("#cg_last_row").val();

  
  //  alert(picsInRow);   alert(picsPerSite); alert(lastRow);


  var width2 = 0;
    
  var lastRowLeft = n-(n-Math.floor(n/picsInRow)*picsInRow);
  
  var picsInLastRow = n-lastRowLeft;
  
  //alert(picsInLastRow);

   var lastImages = 1;
  
  // Orientierungsvariable bei Durcharbeiten des großen Arrays   
  var r = 0;
  
	  $( ".cg_show" ).each(function( i ) {
	  
	  r++;
	  
	  //alert(r);
	  
	  
	  
	  //var width = $(this).width();
	  //var height = $(this).height();
	  
	  var width = $(this).find('.widthOriginalImg').val();
	 var height = $(this).find('.heightOriginalImg').val();
	 //var srcOriginalImg = $(this).find('.srcOriginalImg').val();
	  
	 // alert(width);
		
	  var div = width / height;
	  
	  ratio  = ratio + div;
	  
	  //alert(ratio);  

	  
		  if (r % picsInRow == 0) {
		  
			
				denominator1.push(ratio);
				
				newHeight = Math.floor(widthmain/ratio);
				

				
				//alert("newHeight"+newHeight);

				newHeight1.push(newHeight);
				
				newHeight2 = newHeight;
		  
		  a++;
		  
		  newNumerator = 0;
		  		  
		  newHeight = 0;
		  
		  ratio = 0;
		  
		  divArray.length = 0;
		  
		  partNumerator = 0;

		  
		  }
		  
		  
		 // Sobald es beendet ist, wird die letzte Zeile berechnet. Breite wird neu berechnet, entsprechend Anzahl der Bilder.
		  if (n/r == 1) {
			  
			  
			  
				denominator1.push(ratio);
			  
			    //Neue Breite wird berechnet
				var newWidthmain = widthmain/picsInRow*picsInLastRow;
				
				newHeight = Math.floor(newWidthmain/ratio);
				

				
				//alert("newHeight"+newHeight);

				newHeight1.push(newHeight);
				
				newHeight2 = newHeight;
		  
		  }
		  
		  
	  
	  
	  
	   });

 
   // Gibt an in welcher Zeile man sich befindet (Bezoggen auf den vorher ermittelten Array, bestimmte Anzahl an Bildern haben die gleiche Größe)
   var h = 0;
   
   var cg_pagination_position_count = parseInt($('#cg_pagination_position_count').val());
   if(cg_pagination_position_count==0){var h = 0; }
   else{
	   
	  // alert(cg_pagination_position_count);
		
		
		if(cg_gallery_resize==1){
			
			//$('#cg_pagination_position_count').val(0);
			var h = 0;
			
			}
		else{
			
			var h = (cg_pagination_position_count+1)/picsInRow;
		}
		
	var firstRow = cg_pagination_position_count;	
	
	//Für Border
	var onlyFirstTime = cg_pagination_position_count;
		
	    
		//alert(picsInRow);
   }
   
   var g = 0;
   
    var r = 0;
   
   // Horizontal Space hier nicht hinzufügen da von links kommend nur Border zählt
	var aggregateWidth=border;
	
	
var rowsDone = 0;

// Wichtig für Lazy Load. Border oben soll nur beim ersten Mal geladen werden.
var onlyFirstTime = 0;
  
   // Wird für Lazy Load verwendet. +1 wird erst am ende hinzuaddiert
   var count=0;
   
      // Zur Ermittlung des Abstandes zum Rand des HTMLs für Pagination
   var cg_imgOffsetHTML = 0;
   
   // Sobald cg_pagination 1 erreicht hat, dann loading gif anzeigen und nicht mehr laden, bis weiter gescrollt wird
   var cg_pagination = 0;
   
   
   
   
   	  $( ".cg_show" ).each(function( i ) {

	  r++;
	  
	  g++;
	
	  var setNewHeight = newHeight1[h];
	  
	 //Achtung entspricht nicht der Anzahl der tatsächlich geladenen Bilder. Fängt mit 0 an. Wird auch für das gesammelte Height und Width Array verwendet.
	 // Hier muss es nochmal sein, so wie es oben sein muss
	var cg_pagination_position_count = $('#cg_pagination_position_count').val();
	
	
	
	//alert("count: "+count);
	//alert("cg_pagination_position_count: "+cg_pagination_position_count);
	//alert("cg_InfiniteScrollGallery: "+cg_InfiniteScrollGallery);
	//alert("cg_gallery_resize: "+cg_gallery_resize);
	
	// Wenn resize gemacht wurde gerade und logic läuft, dann sollen ALLE nochmal abgearbeitet werden	
	if(count<=cg_pagination_position_count && cg_InfiniteScrollGallery==1 && cg_pagination_position_count!=0 && cg_gallery_resize!=1){
		//var cg_do_nothing = 1;	
		}
	
	else{
	  
	// alert(newHeight1[h]);
	//alert(h);
	 
	 var realId = $(this).find('.realId').val();
	 var widthOriginalImg = $(this).find('.widthOriginalImg').val();
	 var heightOriginalImg = $(this).find('.heightOriginalImg').val();
	 //var srcFirstLoad = $(this).find('.srcFirstLoad').val();
	 var srcOriginalImg = $(this).find('.srcOriginalImg').val();
	 var src1920width = $(this).find('.src1920width').val();
	 var src1600width = $(this).find('.src1600width').val();
	 var src1024width = $(this).find('.src1024width').val();
	 var src624width = $(this).find('.src624width').val();
	 var src300width = $(this).find('.src300width').val();
	 
	 if(cg_CircleImages==1){$(this).css({"border-radius": ""+cg_RowViewBorderRadius+"%"}); var circleImagesOverflow = "overflow:hidden";}
	 
	 var cg_Use_as_URL = $('#cg_Use_as_URL').val();
	 var cg_ForwardToURL = $('#cg_ForwardToURL').val();
	 var cg_ForwardType = $('#cg_ForwardType').val();
	 var cg_ForwardFrom = $('#cg_ForwardFrom').val();
	 var cg_FullSizeImageOutGalleryNewTab = $('#cg_FullSizeImageOutGalleryNewTab').val();
	 var cg_FullSizeImageOutGallery = $('#cg_FullSizeImageOutGallery').val();
	 
	 var hrefCGpic = $(this).find('.hrefCGpic').val();
	 
	 // alert(widthOriginalImg);
	 // alert(heightOriginalImg);	

			
		// Die Verteilung der Borders hat zuerst zu kommen --- ENDE
			
			
	var newWidthDiv = Math.ceil(widthOriginalImg*newHeight1[h]/heightOriginalImg);
	 
	 aggregateWidth = aggregateWidth+newWidthDiv;
//alert(aggregateWidth);
	 	if (g % picsInRow == 0) {
		  
		  	//	  alert("aggregateWidth:" +aggregateWidth);
		 
			if(onlyFirstTime==0){
			var newWidthDiv = newWidthDiv+(widthmain-aggregateWidth)+border;
			}
			else{var newWidthDiv = newWidthDiv+(widthmain-aggregateWidth);}
			
		  var setNewHeight = newWidthDiv*heightOriginalImg/widthOriginalImg;
		  
		  //alert(newWidthImage);
		  aggregateWidth=0;
		  
		  onlyFirstTime++;
			
		  }		  
	 
	//alert(newHeight1[h]);	
	 
	 //alert("setNewHeight: "+setNewHeight);	alert("newWidthImage: "+newWidthImage);
	 
	  $(this).css('height',newHeight1[h]);
	  $(this).css('width',newWidthDiv);
	  $(this).css('float','left');
	  $(this).css('box-sizing','content-box');
	
	
	// Verteilung Border und Margin rechts und links
		
		
		if(r == 1){
				$(this).css({"border-left": "rgba"+rgba+"  solid "+border+"px"});
		}
		
		// Jedes mal wenn eine Reihe abgearbeitet wurde, gleich beim ersten Bild border-left wieder setzten!!!
		if ((r-1) % picsInRow == 0){
				$(this).css({"border-left": "rgba"+rgba+"  solid "+border+"px"});
		}
		else{			
			//Wenn horizontal Space vorhanden ist, dann hat auch jedes mal ein border links addiert zu werden! (Beim zweiten Bild der reihe)
			if((cg_horizontalSpace!=0 && r!=1) || (cg_CircleImages==1 && r!=1)){
				$(this).css({"margin-left": ""+cg_horizontalSpace+"px"});
				$(this).css({"border-left": "rgba"+rgba+"  solid "+border+"px"});
			}			
		}
	
		//Border rechts muss immer sein!
		$(this).css({"border-right": "rgba"+rgba+"  solid "+border+"px"});
		

	
	// Verteilung Border und Margin rechts und links --- ENDE!
	
	// Verteilung Border und Margin oben und unten
		
		// ACHTUNG!!!!! Wenn horizontaler Abstand gesetzt und verticaler nicht gesetzt ist, dann werdden die oberen und unteren Borders 
		//trotzdem so gesetzt als ob verticaler Abstand eingestellt ist, damit es nicht fehlerhaft aussieht.
		
		if(rowsDone==0){
			
			//Falls verticalSpace NULL und Pagination aktiviert ist, dann border-top nur dann setzten wenn cg_pagination_position_count NULL ist!!!!
			if(cg_pagination_position_count==0 && cg_verticalSpace==0 ){
				$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});
				//alert(1);
			}			
			else{
				// Vertical Space nicht null ist, oder Circle border aktiviert ist, dann kann immer gesetzt werden!
				if(cg_verticalSpace!=0 || (cg_verticalSpace==0 && cg_horizontalSpace!=0 ) || cg_CircleImages==1){
					$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});
				}
				else{
					// Ansonsten nur beim ersten durchlauf! 
					if(rowsDone==0 && cg_InfiniteScrollGallery!=1){
						$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});
					}					
				}
				//alert(2);
			}
			
			$(this).css({"border-bottom": "rgba"+rgba+"  solid "+border+"px"});	
			if(cg_verticalSpace!=0 || (cg_verticalSpace==0 && cg_horizontalSpace!=0 )){
				$(this).css({"margin-bottom": ""+cg_verticalSpace+"px"});
			}			
		}
		else{			
			if(cg_verticalSpace!=0 || (cg_verticalSpace==0 && cg_horizontalSpace!=0) || cg_CircleImages==1){
				
				$(this).css({"margin-bottom": ""+cg_verticalSpace+"px"});				
				$(this).css({"border-top": "rgba"+rgba+"  solid "+border+"px"});	
				$(this).css({"border-bottom": "rgba"+rgba+"  solid "+border+"px"});
			}
			else{
				$(this).css({"border-bottom": "rgba"+rgba+"  solid "+border+"px"});	
			}			
		}
		
	
	// Verteilung Border und Margin oben und unten --- ENDE 
	
		
		// Höhe von input Field ausgleichen wenn es zu lang ist --- ACHTUNG ANDERE LOGIK BEI ROW VIEW

			//var heightAppend = $(this).find("div#cg_hide").height(); <<< funktioniert bei Row View nicht
		

//alert(heightAppend);
		
		//var divHthumbHappend = newHeight1[h]-56; // height von cg_height
		
//alert(divHthumbHappend);

		//$(this).find("[id*=cg_Field1IdGalleryView]").css('max-height',divHthumbHappend);
		$(this).find("[id*=cg_Field1IdGalleryView]").css('overflow','hidden');
		
		// Setzten in die Mitte falls Circle aktiviert ist
		//var testHeightLala = $(this).find("[id*=cg_hide]").height();
		//alert(testHeightLala);
		if(cg_CircleImages==1){
			$(this).find("[id*=cg_hide]").css('margin-bottom',newHeight1[h]/2-45.75/2);
			//$(this).find("[id*=cg_hide]").css('padding-left',10);
		}		
		
		// Höhe von input Field ausgleichen wenn es zu lang ist --- ENDE
	  
	  
	  
	  var newWidthImage = newWidthDiv+2;
	  
	  //alert(newWidthImageTest);
	  
	  
	  	//	var cg_hideRowViewWidth = $(this).find( "#cg_hide").width();
		//alert(newWidthImage);
		  
		$(this).find( "[id*=cg_hide]").css('width',newWidthDiv);	
		$(this).find( "[id*=cg_Field1IdGalleryView]").css('width',newWidthDiv);
		
		
		if(showAlways==1){  
		
		$(this).find("[id*=cg_hide]").show();
		$(this).find( "[id*=cg_Field1IdGalleryView]").show();

		
		
		if(newWidthDiv<cg_hide_hide_width){
			
		$(this).find("[id*=cg_hide]").hide();
		$(this).find("[id*=cg_Field1IdGalleryView]").hide();
			
		}
	 
		}
		
	// Prüfung und bestimmung der URL Weiterleitung, falls aktiviert ist


		// Positionen Info Comments und Rating auf den Galerie Images
		
		// INFO
		if(cg_TitlePositionGallery==2){
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','center');	
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-right","27px");
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-left","23px");	
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-left","23px");
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-right","23px");
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','center');	
			
			}
		else if(cg_TitlePositionGallery==3){
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','right');
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-right","27px");
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-left","23px");	
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-left","23px");
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-right","23px");
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','right');	
			
			}
		else{
			$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css('text-align','left');
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-right","17px");
		//	$(this).find("div[id*=cg_hide]").find(".cg_info_depend_on_radius").css("padding-left","13px");	
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-left","13px");
		//	$(this).find("[id*=cg_Field1IdGallery]").find("div").css("padding-right","13px");
			$(this).find("[id*=cg_Field1IdGallery]").css('text-align','left');	
			}
		
		// COMMENTS
		if(cg_CommentPositionGallery==2){	
			$(this).find("div[id*=cg_hide]").find(".cg_info_comment_gallery_div").css("text-align","center");
		}
		else if(cg_CommentPositionGallery==3){
			$(this).find("div[id*=cg_hide]").find(".cg_info_comment_gallery_div").css("text-align","right");
		}
		else{
			
		}
		
		// RATING
		if(cg_RatingPositionGallery==2){
			$(this).find("div[id*=cg_hide]").find(".cg_gallery_rating_div").css("text-align","center");			
		}
		else if(cg_RatingPositionGallery==3){		
			$(this).find("div[id*=cg_hide]").find(".cg_gallery_rating_div").css("text-align","right");			
		}
		else{
			
		}
		
		// Positionen Info Comments und Rating auf den Galerie Images --- ENDE	

		
	if(cg_Use_as_URL==1 && cg_ForwardToURL==1  && cg_ForwardFrom==2){

    //Prüfen ob vom user ein http bei entries der url mit eingetragen wurde, wenn nicht dann hinzufügen 
	
	var cg_img_url_entry = $("#cg_img_url"+realId+"").val();
	
	if (typeof cg_img_url_entry === 'undefined') { }
	else if(cg_img_url_entry.indexOf("http") > -1) { cg_img_url_entry = cg_img_url_entry;}	
	else{ cg_img_url_entry = "http://"+cg_img_url_entry;}
	//alert(cg_img_url_entry);
	
	 var cg_a_href_img = "href='"+cg_img_url_entry+"'"; 
	 
	 var cg_a_href_title = "title='Go to "+cg_img_url_entry+"'";

	 
		 if(cg_ForwardType==2){
		 
		 var cg_href_img_blank = "target='_blank'";
		 
		 }
		 
		 else{
			 
		 var cg_href_img_blank = "";	 
			 
		 }
	 
	 var cg_id_class_img = "id='cg_image_id"+realId+"'";
	 
	// alert(cg_a_href_img);
		
	}	
	else{
		
	var cg_a_href_img = "href='"+hrefCGpic+"' class='cg_href_image'";
	var cg_id_class_img = "data-cg_image_id='"+realId+"' id='cg_image_id"+realId+"' class='cg_image"+r+"'";
	 var cg_href_img_blank = "";
	 
				if(cg_FullSizeImageOutGallery==1){
				if(cg_FullSizeImageOutGalleryNewTab==1){var cg_href_img_blank = "target='_blank'";}
				else{var cg_href_img_blank = "";}
				}

	
	}
	
	// Prüfung und bestimmung der URL Weiterleitung, falls aktiviert ist -- ENDE
		
	 // $(this).find(".cg_append").append("<img src='"+srcFirstLoad+"' style='float:left !important;position:absolute;left: -2px ;right:  -2px ;max-width:none !important;' width='"+newWidthImageTest+"' height='"+setNewHeight+"' class='cg_image'>");
	 
	 			 /* if(width<=300){ //alert("300");
			  $(this).find(".cg_append").append("<a href='"+hrefCGpic+"'>"+
			  "<img src='"+src300width+"' style='position:absolute;left: -"+paddinLeftRight+"px ;right:  -"+paddinLeftRight+"px ;max-width:none !important;' width='"+width+"px' height='"+height+"px' class='cg_image'>"+
			  "</a>");
			  }*/
			  
		// Zum Schluss wird ermittelt wieviel Zeilen bereits abgearbeitet wurden
		if (r % picsInRow == 0){			
		
		rowsDone++;	
			
		}		  
			  
			  
		  
	// SEHR WICHTIG!!!! Das muss vorher gemacht werden. Damit später der richtige OffSet zum Document angezeigt wird! 
	$(this).css('display','inline');
			  
	  
	  if(newWidthImage<=300){ //alert("300");
	  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
	//  "<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+cg_loadingGifSource+"' width='24px' height='24px' style='display:block;margin-left: auto;margin-right: auto;margin-top:50%;'>"+
	  "<img src='"+src300width+"' style='float:left !important;position:absolute;left: -2px ;right:  -2px ;max-width:none !important;"+circleImagesOverflow+";' width='"+newWidthImage+"' height='"+setNewHeight+"' "+cg_id_class_img+">"+
	  "</a>");
	  }
	  
	  else if(newWidthImage<=624){ //alert("624");
	  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
	//  "<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+cg_loadingGifSource+"' width='24px' height='24px' style='display:block;margin-left: auto;margin-right: auto;margin-top:50%;'>"+
	  "<img src='"+src624width+"' style='float:left !important;position:absolute;left: -2px ;right:  -2px ;max-width:none !important;"+circleImagesOverflow+";'' width='"+newWidthImage+"' height='"+setNewHeight+"' "+cg_id_class_img+">"+
	  "</a>");
	  }
	  
	  else if(newWidthImage<=1100){// alert("1024");
	  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
	//  "<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+cg_loadingGifSource+"' width='24px' height='24px' style='display:block;margin-left: auto;margin-right: auto;margin-top:50%;'>"+
	  "<img src='"+src1024width+"' style='float:left !important;position:absolute;left: -2px ;right:  -2px ;max-width:none !important;"+circleImagesOverflow+";'' width='"+newWidthImage+"' height='"+setNewHeight+"' "+cg_id_class_img+">"+
	  "</a>");
	  }
	  
	  else if(newWidthImage<=1700){// alert("1024");

	  if(src1600width==0){src1600width = srcOriginalImg;}
	  
	  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
	//  "<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+cg_loadingGifSource+"' width='24px' height='24px' style='display:block;margin-left: auto;margin-right: auto;margin-top:50%;'>"+
	  "<img src='"+src1600width+"' style='float:left !important;position:absolute;left: -2px ;right:  -2px ;max-width:none !important;"+circleImagesOverflow+";'' width='"+newWidthImage+"' height='"+setNewHeight+"' "+cg_id_class_img+">"+
	  "</a>");
	  }
	  
	  else if(newWidthImage<=2120){// alert("1024");
	  
	  if(src1920width==0){src1920width = srcOriginalImg;}
	  
	  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
	//  "<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"'  src='"+cg_loadingGifSource+"' width='24px' height='24px' style='display:block;margin-left: auto;margin-right: auto;margin-top:50%;'>"+
	  "<img src='"+src1920width+"' style='float:left !important;position:absolute;left: -2px ;right:  -2px ;max-width:none !important;"+circleImagesOverflow+";'' width='"+newWidthImage+"' height='"+setNewHeight+"' "+cg_id_class_img+">"+
	  "</a>");
	  }
	  
	  else if(newWidthImage>2120){// alert("1024");
	  $(this).find(".cg_append").append("<a "+cg_a_href_img+" "+cg_href_img_blank+" "+cg_a_href_title+" >"+
	  //"<img  class='cg_loading_gif_img' id='cg_loading_gif_img_"+realId+"' src='"+cg_loadingGifSource+"' width='24px' height='24px' style=display:block;margin-left: auto;margin-right: auto;margin-top:50%;'>"+
	  "<img src='"+srcOriginalImg+"' style='float:left !important;position:absolute;left: -2px ;right:  -2px ;max-width:none !important;"+circleImagesOverflow+";'' width='"+newWidthImage+"' height='"+setNewHeight+"' "+cg_id_class_img+" >"+
	  "</a>");
	  }
	  
	  //$(this).find('.cg_image').attr('height',setNewHeight);

	  		//<img alt="$commentText" src="$uploads/$Timestamp$imageThumb" class='cg_image' style='left:-2px; right: -2px;position:absolute;max-width:none;overflow:hidden;float:left !important;border:0 !important;visibility:hidden;cursor: pointer;'
       // width='$newWidthImagePx;' height='$newHeightImagePx;'>
	   
	   
	   			// Am Ende muss diese Prüfung nicht gemacht werden
			if(count<n && cg_all_images_loaded==0){  
			  
			// Zur Ermittlung des Abstandes zum Rand des HTMLs für Pagination
			// Bei Pagination muss exakt die selbe Regel weiter unten nochmal sein
			 if(g % picsInRow == 0 && cg_InfiniteScrollGallery==1 && cg_gallery_resize!=1 || cg_gallery_resize==1 && count>=cg_pagination_position_count && g % picsInRow == 0 && cg_InfiniteScrollGallery==1){	
// alert(count);
/*
if ( $( "#cg_show"+realId+"" ).length ) {
 
    alert("yes");
	
	var offset = $( "#cg_show"+realId+""  ).offset();
 alert("The image is located at: " + offset.top + ", " + offset.left);
}


				var cg_gallery_img_element = document.querySelector("#cg_show1367"); 
				var cg_gallery_img_position = cumulativeOffset(cg_gallery_img_element);*/
				//alert("The image is located at: " + cg_gallery_img_position.top + ", " + cg_gallery_img_position.left);
				
				var cg_offset = $("#cg_append"+realId+"").offset();
				// Ermitteln des Abstandes des ersten Bildes zum oberen Rand. Dies ist dan der Anfangswert ab dem gemessen wird.				
				if($("#firstImageOffset").val() == '0'){					
					$("#firstImageOffset").val(cg_offset.top);
				};			

				var check_cg_imgOffsetHTML = cg_imgOffsetHTML-$("#firstImageOffset").val()+200;
				//alert(check_cg_imgOffsetHTML);
				//alert(cg_InfiniteScrollGalleryHeight);

			//	alert(0);
				// alert("check_cg_imgOffsetHTML "+check_cg_imgOffsetHTML);
				// alert("cg_InfiniteScrollGalleryHeight "+cg_InfiniteScrollGalleryHeight);
				// alert("realId "+realId);
				//alert("cg_gallery_resize"+cg_gallery_resize);
			//	alert("count "+count);
			//	alert("cg_pagination_position_count "+cg_pagination_position_count);
			//	alert("g "+g);
			//	alert("picsInRow "+picsInRow);
			
							//Prüfen ob mehr als eine Bildschirmgröße an Bildern schon geladen wurde. Dann wieder auf Null setzen.
				// Sobald cg_pagination 2 erreicht hat loading gif anzeigen und nicht mehr laden.
				// Letzter Stand: || i  cg_pagination_position_count <<< breie muss hier auch rangezogen werden. Prüfen ob es der letzte ist, wenn nicht dann noch einen durchlauf machen.
				
				// cg_InfiniteScrollGalleryHeight = Höhe des Browser Fensters
				// Sobald mehr als ein gesamter Bildschirm geladen wurde erscheint der Loading button
				// Bei Pagination muss exakt die selbe Regel weiter unten nochmal sein
				
				if(check_cg_imgOffsetHTML*2 > cg_InfiniteScrollGalleryHeight && cg_gallery_resize!=1 || cg_gallery_resize==1 && count>=cg_pagination_position_count && g % picsInRow == 0){
					
					// firstImageOffset wieder auf Null setzen
					$("#firstImageOffset").val(0);
										
					//$(this).css('display','none');// Dieser Befehl ist notwendig. Ansonsten bleibt ein Div mit Border zum Schluss noch stehen, ohne Bild drin.
					//$(this).find(".cg_append").empty();// Das Bild ist da schon erschienen, der div muss geleert werden.
					// alert("false");
					 
					 
					$("#cg_gallery_loading_gif").remove();
					$("#mainCGdiv").append("<img id='cg_gallery_loading_gif' src='"+cg_loadingGifSource+"' width='24px' height='24px' style='display: block !important; margin:0 auto; '>");
					
					//alert(i);
					
					$('#cg_pagination_position_count').val(count);
					$('#cg_aggregateWidth_gallery').val(aggregateWidth);
					//alert(cg_gallery_img_position.top);
					

					
						$("#cg_image_id"+realId+"").on('load', function(){
						  // hide/remove the loading image
						    /*if(cg_gallery_resize==1){$(this).css("visibility","visible");}
							else{ $(this).css("visibility","visible").hide().fadeIn(3000);}*/
						 // alert(3);
						  //$(this).css("visibility","visible");
						  
								if(showAlways==1){  
								  

									//$(this).find("[id*=cg_hide]").show();
									$("#cg_hide"+count+"").show();
									//$(this).find( "[id*=cg_Field1IdGalleryView]").show();
									$("#cg_Field1IdGalleryView"+count+"").show();
								  
								  
									if(newWidthDiv<cg_hide_hide_width){
										
									//$(this).find("[id*=cg_hide]").hide();
									$("#cg_hide"+count+"").hide();
									//$(this).find( "[id*=cg_Field1IdGalleryView]").hide();
									$("#cg_Field1IdGalleryView"+count+"").hide();
										
									}
								
								}
						  
						  
						  
						});
					
					
					
					
					
					return false;
					
					
					}
					
					

				
				//Wenn nicht, dann weiter Abstand zum Oben dazu adieren
				else{cg_imgOffsetHTML = cg_imgOffsetHTML+cg_offset.top;}					
				
				
				
			}
			

			}	   
	   
	   
	   
	 		$("#cg_image_id"+realId+"").on('load', function(){
  // hide/remove the loading image
  //$(this).fadeIn(1000);
  //alert(3);
  
//  $("#cg_loading_gif_img_"+realId).css("display","none")
  
  /*if(cg_gallery_resize==1){$(this).css("visibility","visible");}
  else{ $(this).css("visibility","visible").hide().fadeIn(3000);}*/

  
			if(showAlways==1){  
			  

				//$(this).find("[id*=cg_hide]").show();
				$("#cg_hide"+count+"").show();
				//$(this).find( "[id*=cg_Field1IdGalleryView]").show();
				$("#cg_Field1IdGalleryView"+count+"").show();
			  
			  
				if(newWidthDiv<cg_hide_hide_width){
					
				//$(this).find("[id*=cg_hide]").hide();
				$("#cg_hide"+count+"").hide();
				//$(this).find( "[id*=cg_Field1IdGalleryView]").hide();
				$("#cg_Field1IdGalleryView"+count+"").hide();
					
				}
			
			}
  
  
  
})  


	  
		//Zeile durch
		  if (g % picsInRow == 0) {
		  
		  h++;
		  
		  }
	  
	  }
	  
count++;


if(i==n){$("#cg_all_images_loaded").val(1);}	  
	 });
	   
	   
	   	$( ".cg_show" ).each(function( i ) {




			  
		$(this).css("display","inline");
					


	  
	  });	
	
}	



 if (look=='thumb') {
	 
	// alert("thumb");
//$('.cg_append').empty();
thumbLogic();

	 
	   }








if (look=='height') {

//$('.cg_append').empty();
heightLogic();

	   
	   }
//alert("test");







	   
	if (look=='row') {

  //$('.cg_append').empty();
	rowLogic();   

	   
	   } 
	   
	   
//}

	
	
	
function changeLook(prev_look){
	
	var prev_look = prev_look;
	//alert(""+prev_look);
	var look = $("#cg_look").val();
	//alert(""+look);
	
			if(prev_look=='thumb'){var prev_look=1;}
			if(prev_look=='height'){var prev_look=2;}
			if(prev_look=='row'){var prev_look=3;}
			
			if(look=='thumb'){var look=1;}
			if(look=='height'){var look=2;}
			if(look=='row'){var look=3;}
			
		//	alert(look);
			
			
			$('.cg_href_image').each(function() {
			var cg_src = $(this).attr('href');
			//alert(cg_src);
			var cg_src_replace = cg_src.replace('&1='+prev_look+'&', '&1='+look+'&'); 
			//alert(cg_src_replace);
			$(this).attr("href",""+cg_src_replace+"")
			});
			
			
	
}

// 1=thumb look
// 2=height look
// 3=row look


var userDefineFrontend = 0;



	$(document).on('click', '#cg_thumb_look_frontend', function(e){
		
		//$("#cg_thumb_configuration").css('visibility','visible');
		//$("#cg_height_configuration").css('visibility','hidden');
		//$("#cg_row_configuration").css('visibility','hidden');
		/*
		$( "#cg_configuration_div" ).empty();	
		
		var WidthThumb = $("#cg_WidthThumb").val();
		var HeightThum = $("#cg_HeightThumb").val();
		var DistancePics = $("#cg_DistancePics").val();
		var DistancePicsV = $("#cg_DistancePicsV").val();
		

		

		$("#cg_configuration_div").append("Height: <input type='text' style='display:inline;' id='cg_thumb_configuration_height' value='"+HeightThum+"' >"+
		 "Width: <input type='text' style='display:inline;' id='cg_thumb_configuration_width' value='"+WidthThumb+"' >"+
		 "Distance: <input type='text' style='display:inline;' id='cg_thumb_configuration_distance_pics' value='"+DistancePics+"' >"+
		 "Distance V: <input type='text' style='display:inline;' id='cg_thumb_configuration_distance_pics_v' value='"+DistancePicsV+"' >"+
		 "<input type='hidden' id='cg_actual_configuration' value='thumb'>");*/

		//var cg_configuration = $( "#cg_height_configuration_height" ).val();
	//	$("#cg_HeightLookHeight").val(cg_configuration);

		
		var prev_look = $("#cg_look").val();
	var look = $("#cg_look").val();
		//alert(look);
		if(look!='thumb'){
		//alert("thumbs");
		/*var cg_thumb_input = this;
        cg_thumb_input.disabled = true;
		cg_height_input.disabled = false;*/
		
			$(".cg_show").attr('style','');
			$('.cg_append').attr('style','');
			$('.cg_append').empty();
		
/*			//var cg_icon_forward = $(this).attr('src');
			var cg_src_thumb_icon = $(this).attr('src');
			cg_src_thumb_icon = cg_src_thumb_icon.substr(0,cg_src_thumb_icon.length - 7);
			//alert(cg_src_height_icon);
			cg_src_thumb_icon = cg_src_thumb_icon+'on.jpg';
			//alert(cg_src_height_icon);*/

			var cg_css_folder_url = $('#cg_css_folder_url').val();

			var cg_src_thumb_icon = cg_css_folder_url+'thumb-cg-view-on.jpg';



			$(this).attr('src',cg_src_thumb_icon);
			
/*			var cg_icon_forward = $(this).attr('src');
			cg_icon_forward = cg_icon_forward.substr(0,cg_icon_forward.length - 20);*/

			var cg_icon_forward_height = cg_css_folder_url+"height-cg-view-off.jpg";
			var cg_icon_forward_row = cg_css_folder_url+"row-cg-view-off.jpg";
			
			$("#cg_height_look_frontend").attr('src',cg_icon_forward_height);

			$("#cg_row_look_frontend").attr('src',cg_icon_forward_row);
			
	
			
			$("#cg_look").val('thumb');
			$(this).css('cursor','default');
			$("#cg_height_look_frontend").css('cursor','pointer');
			$("#cg_row_look_frontend").css('cursor','pointer');
			
			
	// Beim Durchklicken und Sortieren muss der richtige Parameter des gerade ausgewählten Looks übertragen werden		
			$("#cg_sort_images_look").val(1);	
			
			if(prev_look=='height'){							
				
	$('.cg_further_images').each(function() {
    var oldHref = $(this).find('a').attr('href');//alert(text);
    var newHref = oldHref.replace('1=2', '1=1');
    $(this).find('a').attr('href',newHref);
});
				
			}
			
						if(prev_look=='row'){							
				
	$('.cg_further_images').each(function() {
    var oldHref = $(this).find('a').attr('href');//alert(text);
    var newHref = oldHref.replace('1=3', '1=1');
    $(this).find('a').attr('href',newHref);
});
				
			}
	// Beim Durchklicken und Sortieren muss der richtige Parameter des gerade ausgewählten Looks übertragen werden	--- ENDE		
			
			
			var look = $("#cg_look").val();
			
			
			
			if(userDefineFrontend==1){
				
			$( "#submit_border" ).click();					
				
			}
			else{
				
				thumbLogic();
				
			}
			
			
			
			
			
			
			changeLook(prev_look);
			
			}

});

	$(document).on('click', '#cg_height_look_frontend', function(e){
		

		
		//$("#cg_thumb_configuration").css('visibility','hidden');
		//$("#cg_height_configuration").css('visibility','visible');
		//$("#cg_row_configuration").css('visibility','hidden');
		
	//	$( "#cg_configuration_div" ).empty();
	//	var HeightLookHeight = $("#cg_HeightLookHeight").val();
		
	//	$("#cg_configuration_div").append("Height: <input type='text' style='display:inline;' id='cg_height_configuration_height' name='cg-height' value='"+HeightLookHeight+"' >"+
//		 "<input type='hidden' id='cg_actual_configuration' value='height'>");
		//var cg_configuration = $( "#cg_height_configuration_height" ).val();
		//$("#cg_HeightLookHeight").val(cg_configuration);
		
		
		
		
		
		var prev_look = $("#cg_look").val();
	var look = $("#cg_look").val();
				if(look!='height'){
	
		// var cg_height_input = this;
        //cg_height_input.disabled = true;
		//cg_thumb_input.disabled = false;
        /*setTimeout(function() {
           input.disabled = false;
        }, 3000);*/


	$(".cg_show").attr('style','');
	//$('.show-inner').attr('style','');
	$('.cg_append').attr('style','');
	$('.cg_append').empty();
	


		var cg_css_folder_url = $('#cg_css_folder_url').val();

		var cg_src_height_icon = cg_css_folder_url+'height-cg-view-on.jpg';


		$(this).attr('src',cg_src_height_icon);

		var cg_icon_forward_thumb = cg_css_folder_url+"thumb-cg-view-off.jpg";
		var cg_icon_forward_row = cg_css_folder_url+"row-cg-view-off.jpg";


	
	$("#cg_thumb_look_frontend").attr('src',cg_icon_forward_thumb);

	$("#cg_row_look_frontend").attr('src',cg_icon_forward_row);
	
	
	$("#cg_look").val('height');
	$(this).css('cursor','default');
	$("#cg_row_look_frontend").css('cursor','pointer');
	$("#cg_thumb_look_frontend").css('cursor','pointer');
	
	
		// Beim Durchklicken und Sortieren muss der richtige Parameter des gerade ausgewählten Looks übertragen werden		
			$("#cg_sort_images_look").val(2);	
			
			if(prev_look=='thumb'){							
				
	$('.cg_further_images').each(function() {
    var oldHref = $(this).find('a').attr('href');//alert(text);
    var newHref = oldHref.replace('1=1', '1=2');
    $(this).find('a').attr('href',newHref);
});
				
			}
			
						if(prev_look=='row'){							
				
	$('.cg_further_images').each(function() {
    var oldHref = $(this).find('a').attr('href');//alert(text);
    var newHref = oldHref.replace('1=3', '1=2');
    $(this).find('a').attr('href',newHref);
});
				
			}
	// Beim Durchklicken und Sortieren muss der richtige Parameter des gerade ausgewählten Looks übertragen werden	--- ENDE	
	
	
	
	var look = $("#cg_look").val();
	
	//alert(look);
	
				if(userDefineFrontend==1){
				
			$( "#submit_border" ).click();					
				
			}
			else{
				
				heightLogic();
				
			}
	

	

	changeLook(prev_look);

				}

});


	$(document).on('click', '#cg_row_look_frontend', function(e){
		
		//$("#cg_thumb_configuration").css('visibility','hidden');
		//$("#cg_height_configuration").css('visibility','hidden');
		//$("#cg_row_configuration").css('visibility','visible');
		
	//	var cg_configuration = $( "#cg_configuration_row" ).val();
	//	$( "#cg_configuration_append" ).empty();
	//	$( "#cg_configuration_append" ).append(cg_configuration);
		
		var prev_look = $("#cg_look").val();
		var look = $("#cg_look").val();
			if(look!='row'){
			

		// var cg_height_input = this;
        //cg_height_input.disabled = true;
		//cg_thumb_input.disabled = false;
        /*setTimeout(function() {
           input.disabled = false;
        }, 3000);*/
		
	

	$(".cg_show").attr('style','');
	//$('.show-inner').attr('style','');
	$('.cg_append').attr('style','');
	$('.cg_append').empty();


		var cg_css_folder_url = $('#cg_css_folder_url').val();
		var cg_src_row_icon = cg_css_folder_url+'row-cg-view-on.jpg';

		$(this).attr('src',cg_src_row_icon);

		var cg_icon_forward_height = cg_css_folder_url+"height-cg-view-off.jpg";
		var cg_icon_forward_thumb = cg_css_folder_url+"thumb-cg-view-off.jpg";

	$("#cg_height_look_frontend").attr('src',cg_icon_forward_height);
	$("#cg_thumb_look_frontend").attr('src',cg_icon_forward_thumb);
	
	
	$("#cg_look").val('row');
	$(this).css('cursor','default');
	$("#cg_height_look_frontend").css('cursor','pointer');
	$("#cg_thumb_look_frontend").css('cursor','pointer');
	
	
			// Beim Durchklicken und Sortieren muss der richtige Parameter des gerade ausgewählten Looks übertragen werden		
			$("#cg_sort_images_look").val(3);	
			
			if(prev_look=='thumb'){							
				
	$('.cg_further_images').each(function() {
    var oldHref = $(this).find('a').attr('href');//alert(text);
    var newHref = oldHref.replace('1=1', '1=3');
    $(this).find('a').attr('href',newHref);
});
				
			}
			
						if(prev_look=='height'){							
				
	$('.cg_further_images').each(function() {
    var oldHref = $(this).find('a').attr('href');//alert(text);
    var newHref = oldHref.replace('1=2', '1=3');
    $(this).find('a').attr('href',newHref);
});
				
			}
	// Beim Durchklicken und Sortieren muss der richtige Parameter des gerade ausgewählten Looks übertragen werden	--- ENDE		
	
	
	var look = $("#cg_look").val();
	
	//alert(look);
	
					if(userDefineFrontend==1){
				
			$( "#submit_border" ).click();					
				
			}
			else{
				
				rowLogic();
				
			}
	

	
	
	changeLook(prev_look);

				}
 

});


// Change border in frontend


	$(document).on('click', '#submit_border', function(e){
		
	//alert("action");
	//$('.cg_append').empty();
	//$('.cg_append').empty();
	//heightLogic();	
	

	
	var cg_actual_configuration = $("#cg_look").val();
	
	//alert(cg_actual_configuration);
	
	if(cg_actual_configuration=='thumb'){
		
					//$('.cg_show').attr('style','');
	$(".cg_show").css('border','none');
	//$('.cg_append').attr('style','');
	//$('.cg_append').empty();
	//alert("action");

		// Werte der User werden genommen und in ursprüngliche Hidden Felder reingeschrieben
		var WidthThumb = $("#cg_thumb_configuration_width").val();
		var HeightThumb = $("#cg_thumb_configuration_height").val();
		var DistancePicsX = $("#cg_thumb_configuration_distance_pics").val();
		var DistancePicsV = $("#cg_thumb_configuration_distance_pics_v").val();	
		
		//alert(WidthThumb);
		//alert(HeightThumb);
	//	alert(DistancePics);
//		alert(DistancePicsV);
		
		$("#cg_WidthThumb").val(WidthThumb);
		$("#cg_HeightThumb").val(HeightThumb);
		$("#cg_DistancePics").val(DistancePicsX);
		$("#cg_DistancePicsV").val(DistancePicsV);	
		
		thumbLogic();
		
		}
		
		
		
	if(cg_actual_configuration=='height'){
		
			$(".cg_show").attr('style','');
	$(".cg_show").css('border','none');
	$('.cg_append').attr('style','');
	$('.cg_append').empty();
		var HeightLookHeight = $("#cg_height_configuration_height").val();
		$("#cg_HeightLookHeight").val(HeightLookHeight);
		heightLogic();
		}
		
			if(cg_actual_configuration=='row'){
		
			$(".cg_show").attr('style','');
	$(".cg_show").css('border','none');
	$('.cg_append').attr('style','');
	$('.cg_append').empty();
		rowLogic();		
		}
	
		

		
		//var HeightLookHeight = $("#cg-height").val();	

//heightLogic(); 		


});




// Change border in frontend --- END




	$( window ).resize(function() {


        if(cgJsClass.slider.vars.isMobile==true){

            // Achtung! Für Mobile vergleich mit innerHeight und innwerWidth arbeiten !!!!!
            var windowWidth = window.innerWidth;
            // Achtung! Height Ermittlung hier absichtlich anders als beim Slider wegen eventuell geöffnetem comment fenster!!!
            var windowHeight = $(window).height();

            if(windowWidth/windowHeight>=1){
                var windowRatioCheck = 'horizontal';
            }
            else{
                var windowRatioCheck = 'vertical';
            }
         //   alert('topActual '+windowRatioCheck);
      //      alert('topRatio '+cgJsClass.slider.vars.actualWindowRatio);
            // Wenn es Mobile ist, Commentar Frame offen ist und Window Verhältnis sich nicht geändert hat, dann braucht man nicht auszuführen
            if(windowRatioCheck==cgJsClass.slider.vars.actualWindowRatio){
                return false;
            }
        }





	var look = $("#cg_look").val();

	
	//alert(look);	
		
if (look=='thumb') {
$('.cg_append').empty();
$('.cg_append').empty();
$('#cg_gallery_resize').val(1);	
thumbLogic();
$('#cg_gallery_resize').val(0);		   
	   }		
			
		

//------------------------------------------------------------------	
// ---------- Row Ansicht Horizontal (Gleiche Anzahl der Bilder in einer Reihe) -------------------------------
//------------------------------------------------------------------	


	if (look=='row') {
		

$('.cg_append').empty();
$('#cg_gallery_resize').val(1);	

rowLogic(); 
$('#cg_gallery_resize').val(0);		   
	   
	   }  
	   
//------------------------------------------------------------------	
// ---------- Row Ansicht Horizontal ENDE --------------------------
//------------------------------------------------------------------



//------------------------------------------------------------------	
// ---------- Same Height Ansicht Horizontal (Gleiche Anzahl der Bilder in einer Reihe) -------------------------------
//------------------------------------------------------------------	



 if (look=='height') {

 //$('.cg_append').empty();
//var HeightLookHeight = $("#cg-height").val();	


//$('#cg_gallery_resize').val(1);

	$(".cg_show").attr('style','');
	//$('.show-inner').attr('style','');
	$('.cg_append').attr('style','');
	$('.cg_append').empty();
		
		// Resize fängt an
		//var cg_gallery_resize = 1;
	$('#cg_gallery_resize').val(1);
  //alert(cg_gallery_resize);

heightLogic();

$('#cg_gallery_resize').val(0);


	   
	   } 
	   
//------------------------------------------------------------------	
// ---------- SameHeight Ansicht Horizontal ENDE -------------------------- 
//------------------------------------------------------------------		

        resizeFontSize();

        if(cgJsClass.slider.vars.isMobile==true){
            var windowWidth = window.innerWidth;
            // Achtung! Height Ermittlung hier absichtlich anders als beim Slider wegen eventuell geöffnetem comment fenster!!!
            var windowHeight = $(window).height();
            if(windowWidth/windowHeight>=1){
                cgJsClass.slider.vars.actualWindowRatio = 'horizontal';
            }
            else{
                cgJsClass.slider.vars.actualWindowRatio = 'vertical';
            }

            //alert('bottom '+cgJsClass.slider.vars.actualWindowRatio);

        }
	   
	
	
});

	
	//});
	  

  
});