/**
 * Creates the header fullwidth slider functionality
 */
;(function($){

	function highlight( items ) {
		items.addClass('current-slide');
	}
	function unhighlight() {
		$('#gallery-controller li').removeClass('current-slide');
	}
	function changeImage( items ){
		var bgImage = items.filter(':first').attr('data-bg');
		if('object-fit' in document.body.style===false) { 
			$('#headerwrap').backstretch(bgImage,{
				fade:themifyVars.speed
			});
		}
		else{			
			$('#headerwrap').append('<img src="'+bgImage+'" class="headerwrap-bg-deletable">');
			$('.headerwrap-bg-deletable').animate({
					opacity:1
				},themifyVars.speed,function(){
					$(this).remove();
					$('#headerwrap').find('.headerwrap-bg').attr('src',bgImage);
				});
		}
		
	}
	// Toggle Content and go fullscreen
	var toggleContent = function(event){
		if( 'keyup' == event.type ){
			if( 27 != event.which || !$('body').hasClass('fullscreen-active') ){
				return;
			}
		}
		if(!$('#fullscreen-button').is(':animated')){
			$('#fullscreen-button').toggleClass('active');

			if($('#fullscreen-button').hasClass('active')){
				// go fullscreen
				$('#fullscreen-button').animate({
					right: '-40px'
				}, 100).animate({
						top: '10px'
					}, 100).animate({
						right: '10px'
					});
			} else {
				// back from fullscreen
				$('#fullscreen-button').css({
					'right' : '-40px',
					'top' : ''
				}, 100).animate({
						bottom: '10px'
					}, 100).animate({
						right: '10px'
					});
			}
		}
	};

	$(document).ready(function() {

		/////////////////////////////////////////////
		// Parse injected vars
		/////////////////////////////////////////////
		themifyVars.autoplay = parseInt(themifyVars.autoplay, 10);
		if ( themifyVars.autoplay <= 10 ) {
			themifyVars.autoplay *= 1000;
		}
		themifyVars.speed = parseInt(themifyVars.speed, 10);
		themifyVars.play = themifyVars.play != 'no';
		themifyVars.wrap = themifyVars.wrap != 'no';

		////////////////////////
		// Add wrap for styling
		////////////////////////
		$('#gallery-controller img').each(function() {
			$(this).wrap('<span class="image-wrap" style="width: auto; height: auto;"/>');
			$(this).removeAttr('class');
		});

		/////////////////////////////////////////////
		// Go fullscreen
		/////////////////////////////////////////////
		$('#fullscreen-button').on( 'click', toggleContent);
		$('body').on( 'keyup', toggleContent);

		/////////////////////////////////////////////
		// Pause carousel
		/////////////////////////////////////////////
		$('.carousel-playback').click(function(){
			$(this).toggleClass('paused');
		});
		
		// Adjust backstretch on window resizing
		$(window).on('resize', function () {
			if($('#gallery-controller').length > 0){
				var headerSlider = $('#headerwrap').data('backstretch');
				if(headerSlider)
					headerSlider.resize();
			}
		});

	});

	$(window).load(function(){

		/////////////////////////////////////////////
		// Slider
		/////////////////////////////////////////////
	    if($('#gallery-controller').length > 0){
			
		if('object-fit' in document.body.style===false) {  
		   Themify.LoadAsync(themify_vars.url + '/js/backstretch.min.js',null,null, null, function(){
	        	return ('undefined' !== typeof $.fn.backstretch);
			});
		}
		
	    Themify.LoadAsync(themify_vars.url+'/js/carousel.min.js',ThemifyGallerySlider, null, null, function(){
	        	return ('undefined' !== typeof $.fn.carouFredSel);
	        });
	    }
		
		
		function ThemifyGallerySlider(){
                    
			var itemIndex = ($('#gallery-controller li').length > 5)? '0': '1';
			$('#gallery-controller .slides').carouFredSel({
				responsive: true,
				circular: themifyVars.wrap,
				infinite: themifyVars.wrap,
				prev: {
					button: '#gallery-controller .carousel-prev',
					key: 'left',
					onBefore: function(items) {
						var newItems = items.items.visible;
						unhighlight();
						changeImage( newItems );
					},
					onAfter	: function(items) {
						var newItems = items.items.visible;
						highlight( newItems.filter(':eq(0)') );
					}
				},
				next: {
					button: '#gallery-controller .carousel-next',
					key: 'right',
					onBefore: function(items) {
						var newItems = items.items.visible;
						unhighlight();
						changeImage( newItems );
					},
					onAfter	: function(items) {
						var newItems = items.items.visible;
						highlight( newItems.filter(':eq('+itemIndex+')') );
					}
				},
				width: '100%',
				auto: {
					play : themifyVars.play,
					timeoutDuration: themifyVars.autoplay,
					button: '#gallery-controller .carousel-playback'
				},
				swipe: true,
				scroll: {
					items: 1,
					duration: themifyVars.speed,
					onBefore: function(items) {
						var newItems = items.items.visible;
						unhighlight();
						changeImage( newItems );
					},
					onAfter	: function(items) {
						var newItems = items.items.visible;
						highlight( newItems.filter(':eq('+itemIndex+')') );
					}
				},
				items: {
					visible: 5,
					minimum: 1,
					width: 20
				},
				onCreate : function (){
					$('#gallery-controller').css( {
						'height': 'auto',
						'visibility' : 'visible'
					});

					$('#headerwrap.header-gallery').addClass( 'header-gallery-ready' );

					$('#gallery-controller .carousel-next, #gallery-controller .carousel-prev').wrap('<div class="carousel-arrow"/>');
					$('#gallery-controller .caroufredsel_wrapper + .carousel-nav-wrap').remove();

					$('#gallery-controller li:first').addClass('current-slide');

					if($('#gallery-controller li').length > 2){
						$('.carousel-playback').css('display', 'inline-block');
					}

					if(!themifyVars.play) {
						$('.carousel-playback').hide();
					}
				}
			}).find("li").click( function() {
					$('#gallery-controller li').removeClass('current-slide');
					$(this).addClass('current-slide');
					$('#gallery-controller li').trigger("slideTo", [
						$(this),
						0,
						false,
						{
							items: 1,
							duration: 300,
							onBefore: function(items) { },
							onAfter	: function(items) { }
						},
						null,
						'next']);


					// Set image and index using current data properties
					changeImage( $(this) );

				}
			).css('cursor', 'pointer');

			/////////////////////////////////////////////
			// Initialize fullscreen background
			/////////////////////////////////////////////

			var themifyImages = [];

			// Initialize images array with URLs
			$('#gallery-controller li').each(function(){
				themifyImages.push( $(this).attr('data-bg') );
			});

			$(themifyImages).each(function() {
				$("<img/>").attr('src', this);
			});
			
			// Call backstretch for the first time			
			if('object-fit' in document.body.style===false) {
					$('#headerwrap').backstretch(themifyImages[0], {
					fade : themifyVars.speed
				});
			}
			else{
				$('#headerwrap').append('<img src="'+themifyImages[0]+'" class="headerwrap-bg">');
				$('.headerwrap-bg').animate({
					opacity:1
				});
			}

			// Header Background Color
			var $headerColor = $('#headerwrap[data-bgcolor]');
			if ( $headerColor.length > 0 ) {
				var bgColor = $headerColor.data('bgcolor');
				if ( $headerColor.hasClass('header-gallery') ) {
					$('.backstretch').css('background-color', '#' + bgColor);
				}
			}

		} // end if #gallery-controller

	});

}(jQuery));