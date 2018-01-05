/* Themify Theme Scripts - http://themify.me/ */

// Declar object literals and variables
var FixedHeader = {}, LayoutAndFilter = {}, themifyScript, ThemifySlider, ThemifyMediaElement, qp_max_pages;

// throttledresize
!function($){var e=$.event,t,n={_:0},r=0,s,i;t=e.special.throttledresize={setup:function(){$(this).on("resize",t.handler)},teardown:function(){$(this).off("resize",t.handler)},handler:function(h,o){var a=this,l=arguments;s=!0,i||(setInterval(function(){r++,(r>t.threshold&&s||o)&&(h.type="throttledresize",e.dispatch.apply(a,l),s=!1,r=0),r>9&&($(n).stop(),i=!1,r=0)},30),i=!0)},threshold:0}}(jQuery);

// debouncedresize event
(function($){var $event=$.event,$special,resizeTimeout;$special=$event.special.debouncedresize={setup:function(){$(this).on("resize",$special.handler);},teardown:function(){$(this).off("resize",$special.handler);},handler:function(event,execAsap){var context=this,args=arguments,dispatch=function(){event.type="debouncedresize";$event.dispatch.apply(context,args);};if(resizeTimeout){clearTimeout(resizeTimeout);}execAsap?dispatch():resizeTimeout=setTimeout(dispatch,$special.threshold);},threshold:150};})(jQuery);

(function($){
	
	var $sections = $('.type-section'),
		isFullPageScroll = ('undefined' !== typeof $.fn.fullpage) && (themifyScript.fullPageScroll ? true : false) && ($sections.length > 0),
		usesRows = $sections.length === 0,
		sectionClass = '.section-post',
		sectionsWrapper = 'div:not(.module-layout-part) > #loops-wrapper',
		fixedHeader = $('body').hasClass('fixed-header'),
		wowInit2;
	
// Setup variables if it uses Builder rows instead of section post type
	if (usesRows) {
		isFullPageScroll = ('undefined' !== typeof $.fn.fullpage) && (themifyScript.fullPageScroll ? true : false) && ($('.themify_builder').length > 0) && $('body').hasClass('full-section-scrolling');
		sectionClass = '.module_row';
		sectionsWrapper = 'div:not(.module-layout-part) > .themify_builder_content';
	}

	$( sectionClass + ':hidden' ).remove();
	sectionClass += ':visible';
	
	function loadIsotop(condition,callback){
		if(condition){
			if( typeof $.fn.isotope !== 'function' ) {
				Themify.LoadAsync(themifyScript.themeURI + '/js/jquery.isotope.min.js', function() {
					callback();
				},
				null,
				null,
				function () {
					return ('undefined' !== typeof $.fn.isotope);
				} );
			} else {
				callback();
			}
		}
	}
	
// Fixed Header /////////////////////////
FixedHeader = {
	headerHeight: 0,
	hasHeaderSlider: false,
	headerSlider: false,
	$pageWrap: $( '#pagewrap' ),
	$headerWrap: $( '#headerwrap' ),
	stickyHeader: themifyScript.sticky_header,
	init: function() {
		var _this = this;

		_this.calculateHeaderHeight();

		if( fixedHeader ) {
			if( isFullPageScroll ) {
				$( 'body' ).on( 'themify_onepage_afterload', function( event, $section, section_id ) {
					var $is_scroll = $( sectionClass + '.active', $( sectionsWrapper ) ).index() > 0;
					
					_this.activate( $is_scroll );

					if( $is_scroll ) {
						var $height = _this.headerHeight;

						_this.calculateHeaderHeight();
						$height != _this.headerHeight && _this.updatePageOffset()
					}
				}.bind( _this ) );
			} else {
				_this.activate( false );
				$( window ).on( 'scroll touchstart.touchScroll touchmove.touchScroll', function( e ) {
					_this.activate(false);
				}.bind( _this ) );
			}

			$( window ).load( _this.updatePageOffset.bind( _this ) )
				.on( 'debouncedresize', function(e) {
					if( ! e.isTrigger ) {
						setTimeout( function() {
							_this.calculateHeaderHeight();
							_this.updatePageOffset();
						}.bind( _this ), 400 )
					}
				});

			if( $( '#gallery-controller' ).length > 0 ) _this.hasHeaderSlider = true;
			
			if ( _this.stickyHeader ) {
				var img = '<img id="sticky_header_logo" src="' + _this.stickyHeader.src + '"';
					
				if( _this.stickyHeader.imgwidth ) img += ' width="' + _this.stickyHeader.imgwidth + '"';
				if( _this.stickyHeader.imgheight ) img += ' height="' + _this.stickyHeader.imgheight + '"';
				img += '/>';
				$( '#site-logo a' ).prepend( img );
			}

			$( 'body' ).on( 'announcement_bar_position announcement_bar_scroll_on_after announcementBarUpdate', _this.calculateHeaderHeight.bind( _this ) );
		}
	},
	activate: function( $hard ) {
		var scrollTop = $( window ).scrollTop();

		if( $hard || scrollTop >= this.headerHeight ) {
			! this.$headerWrap.hasClass( 'fixed-header' ) && this.scrollEnabled();
		} else if ( this.$headerWrap.hasClass( 'fixed-header' ) ) {
			this.scrollDisabled();
		}
	},
	scrollDisabled: function() {
		this.$headerWrap.removeClass( 'fixed-header' );
		$( '#header' ).removeClass('header-on-scroll');
		$( 'body' ).removeClass( 'fixed-header-on' );

		/**
		 * force redraw the header
		 * required in order to calculate header height properly after removing fixed-header classes
		 */
		this.$headerWrap.hide();
		this.$headerWrap.get(0).offsetHeight;
		this.$headerWrap.show();
		this.calculateHeaderHeight();
		this.updatePageOffset();
		this.triggerHeaderSlider();
		
	},
	scrollEnabled: function() {
		this.updatePageOffset();
		this.$headerWrap.addClass( 'fixed-header' );
		$( '#header' ).addClass( 'header-on-scroll' );
		$( 'body' ).addClass( 'fixed-header-on' );
		this.triggerHeaderSlider();
		$( window ).trigger( 'resize' );
	},
	triggerHeaderSlider: function() {
		if ( this.hasHeaderSlider && 'object' === typeof this.$headerWrap.data( 'backstretch' ) ) {
			this.$headerWrap.data( 'backstretch' ).resize();
			$( '#gallery-controller .slides' ).trigger( 'next' );
		}
	},
	calculateHeaderHeight : function() {
		this.headerHeight = this.$headerWrap.outerHeight( true );
		this.updateHeaderOffset();
	},
	updateHeaderOffset: function() {
		var offset = this.$headerWrap.css( 'position' ) === 'fixed' ? this.$pageWrap.offset().top + 'px' : '';
		this.$headerWrap.css( 'margin-top', offset );
	},
	updatePageOffset: function() {
		this.$pageWrap.css( 'paddingTop', Math.floor( this.headerHeight ) );
	}
};
FixedHeader.init();

// Initialize carousels //////////////////////////////
ThemifySlider = {
	recalcHeight: function(items, $obj) {
		var heights = [], height;
		$.each( items, function() {
			heights.push( $(this).outerHeight(true) );
		});
		height = Math.max.apply( Math, heights );
		$obj.closest('.carousel-wrap').find( '.caroufredsel_wrapper, .slideshow' ).each(function(){
			$(this).outerHeight( height );
		});
	},
	didResize: false,

	createCarousel: function(obj) {
		var self = this;
		obj.each(function() {
			var $this = $(this);
			$this.carouFredSel({
				responsive: true,
				prev: '#' + $this.data('id') + ' .carousel-prev',
				next: '#' + $this.data('id') + ' .carousel-next',
				pagination: {
					container: '#' + $this.data('id') + ' .carousel-pager'
				},
				circular: true,
				infinite: true,
				swipe: true,
				scroll: {
					items: $this.data('scroll'),
					fx: 'scroll',
					duration: parseInt($this.data('speed'))
				},
				auto: {
					play: ('off' !== $this.data('autoplay')),
					timeoutDuration: 'off' !== $this.data('autoplay') ? parseInt($this.data('autoplay')) : 0
				},
				items: {
					visible: {
						min: 1,
						max: $this.data('visible') ? parseInt($this.data('visible')) : 1
					},
					width: 222
				},
				onCreate: function( items ) {
					var $slideWrap = $this.closest( '.slideshow-wrap' );
					$slideWrap.css({
						'visibility': 'visible',
						'height': 'auto'
					});
					
					
					$(window).on( 'throttledresize', function() {
						self.recalcHeight(items.items, $this);	
					});
					$(window).resize();

					setTimeout( function(){
						$slideWrap.find( '.carousel-nav-wrap' ).css( 'width', ( parseInt( $slideWrap.find( '.carousel-pager' ).find( 'a' ).length ) * 18 ) + 'px' );
					}, 200 );
				}
			});
		});
	}
};

// Test if this is a touch device /////////
function is_touch_device() {
	return $('body').hasClass('touch');
}

// Scroll to Element //////////////////////////////
function themeScrollTo(offset) {
	$('body,html').animate({ scrollTop: offset }, 800);
}
// Get builder rows anchor class to ID //////////////////////////////
function getClassToId($section) {
	var classes = $section.prop('class').split(' '),
			expr = new RegExp('^tb_section-', 'i'),
			spanClass = null;
	for (var i = 0; i < classes.length; i++) {
		if (expr.test(classes[i])) {
			spanClass = classes[i];
		}
	}

	if (spanClass == null)
		return '';

	return spanClass.replace('tb_section-', '');
}
// Create fullpage scrolling //////////////////////////////
function createFullScrolling() {
	var $body = $('body'),
		initFullPage = false,
		currentHash = themifyScript.hash.replace( '#', '' ),
		sectionSelector = usesRows ? sectionsWrapper + ' > ' + sectionClass : sectionClass,
		autoScrolling = !usesRows && '' != themifyScript.hash.replace('#', '') ? false : true,
		anchors = $( sectionSelector ).map( function() {
			var $this = $( this ),
				anchorName = usesRows && $this.is('[class*="tb_section-"]') ? getClassToId( $this ) : $this.attr('id');
			return anchorName ? anchorName : '';
		} );
		
	$(sectionsWrapper).fullpage({
		resize: false,
		sectionSelector: sectionSelector,
		paddingBottom: '78px',
		scrollOverflow: true,
		navigation: true,
		lockAnchors: true,
		autoScrolling: autoScrolling,
		anchors: anchors,
		afterRender: function () {
			if (!autoScrolling) { // hack deep linking not working when use section row
				$.fn.fullpage.setAutoScrolling(true);
			}

			var $section = $(sectionClass + '.active', $(sectionsWrapper)),
					section_id = usesRows && $section.is('[class*="tb_section-"]') ? getClassToId($section) : $section.prop('id'),
					$aSectionHref = $('#main-nav').find('a[href$="#' + section_id + '"]');

			setTimeout(function () {
				$('.section_loader').hide();
				
				if ('undefined' !== typeof ThemifyBuilderModuleJs && 'undefined' !== typeof wowInit2) {
					wowInit2();
				}
			}, 1000);

			if (usesRows) {
				var extraEmptyRow = $('#fp-nav').find('li').get($('.module_row').length);
				if ('undefined' !== typeof extraEmptyRow) {
					extraEmptyRow.hide();
				}
			}

			if ($aSectionHref.length > 0) {
				$aSectionHref.closest('li').addClass('current_page_item').siblings().removeClass('current_page_item current-menu-item');
			}

			$body.trigger('themify_onepage_after_render', [$section, section_id]);
		},
		afterLoad: function () {
			var $section = $(sectionClass + '.active', $(sectionsWrapper)),
				section_id = usesRows && $section.is('[class*="tb_section-"]') ? getClassToId($section) : $section.prop('id'),
				$aSectionHref = $('#main-nav').find('a[href$="#' + section_id + '"]');

			if ($aSectionHref.length > 0) {
				$aSectionHref.closest('li').addClass('current_page_item').siblings().removeClass('current_page_item current-menu-item');
			}

			if( history.pushState && section_id ) {
				if( initFullPage && currentHash !== section_id ) {
					history.pushState(null, null, '#' + section_id);
				} else {
					history.replaceState(null, null, '#' + section_id);
				}
			}

			initFullPage = true;

			$body.trigger('themify_onepage_afterload', [$section, section_id]);
		},
		onLeave: function (index, nextIndex, direction) {

			// when lightbox is active, prevent scrolling the page
			if ($body.find('> .mfp-wrap').length > 0) {
				return false;
			}

			var $rows = usesRows ? $(sectionsWrapper).children(sectionClass) : $(sectionsWrapper).find(sectionClass);
			if ($rows.length > 0) {

				if (index > 0 && nextIndex > 0) {
					var sectionIndex = index;
					if ('up' === direction) {
						for (sectionIndex = index; sectionIndex >= nextIndex; sectionIndex--) {
							$rows.eq(sectionIndex - 1).css('visibility', 'visible');
						}
					} else {
						for (sectionIndex = index; sectionIndex <= nextIndex; sectionIndex++) {
							$rows.eq(sectionIndex - 1).css('visibility', 'visible');
						}
					}

				}
			}
		}
	});
}



// Infinite Scroll ///////////////////////////////
function doInfinite( $container, selector ) {

	if ( 'undefined' !== typeof $.fn.infinitescroll ) {

		// Get max pages for regular category pages and home
		var scrollMaxPages = parseInt(themifyScript.maxPages);

		// Get max pages for Query Category pages
		if ( typeof qp_max_pages !== 'undefined') {
			scrollMaxPages = qp_max_pages;
		}

		// infinite scroll
		$container.infinitescroll({
			navSelector  : '#load-more a:last', 		// selector for the paged navigation
			nextSelector : '#load-more a:last', 		// selector for the NEXT link (to page 2)
			itemSelector : selector, 	// selector for all items you'll retrieve
			loadingText  : '',
			donetext     : '',
			loading 	 : { img: themifyScript.loadingImg },
			maxPage      : scrollMaxPages,
			behavior	 : 'auto' !== themifyScript.autoInfinite? 'twitter' : '',
			pathParse 	 : function ( path ) {
				return path.match(/^(.*?)\b\d+\b(?!.*\b\d+\b)(.*?$)/).slice(1);
			},
			bufferPx: 50,
			pixelsFromNavToBottom: $('#footerwrap').height(),
			state: { 
				currPage: themifyScript.currentPage && Number.isInteger( +themifyScript.currentPage )
					? +themifyScript.currentPage : 1
			}
		}, function(newElements, instance, url) {
			// call Isotope for new elements
			var $newElems = $(newElements);

			// Mark new items: remove newItems from already loaded items and add it to loaded items
			$('.newItems').removeClass('newItems');
			$newElems.addClass('newItems');

			if ( 'reset' === themifyScript.resetFilterOnLoad ) {
				// Make filtered elements visible again
				LayoutAndFilter.reset();
			}

			$newElems.hide().imagesLoaded(function(){

				$newElems.fadeIn();

				$('.wp-audio-shortcode, .wp-video-shortcode').not('div').each(function() {
					var $self = $(this);
					if ( $self.closest('.mejs-audio').length === 0 ) {
						ThemifyMediaElement.init($self);
					}
				});

				if (history.pushState && !+themifyScript.infiniteURL ) {
					history.pushState(null, null, url);
				}

				// redirect to corresponding page
				$('.post').contents().find("a:not([class='comment-reply-link'], [id='cancel-comment-reply-link'], .themify_lightbox, .post-content a[href$='jpg'], .post-content a[href$='gif'], .post-content a[href$='png'], .post-content a[href$='JPG'], .post-content a[href$='GIF'], .post-content a[href$='PNG'], .post-content a[target='_new'], .post-content a[target='_blank'])").click(function(){
					var href = $(this).attr('href');
					window.parent.location.assign(href);
					return false;
				});

				// Apply lightbox/fullscreen gallery to new items
				Themify.InitGallery();
				loadIsotop('object' === typeof $container.data('isotope'),function(){
					$container.isotope('appended', $newElems );
				});
				if ($container.hasClass('auto_tiles') && $('body').hasClass('tile_enable')) {
					$container.trigger('infiniteloaded.themify', [$newElems]);
				}
				if ( LayoutAndFilter.filterActive ) {
					// If new elements with new categories were added enable them in filter bar
					LayoutAndFilter.enableFilters();

					if ( 'scroll' === themifyScript.scrollToNewOnLoad ) {
						LayoutAndFilter.restore();
					}
				}

				$('#infscr-loading').fadeOut('normal');
				if( 1 === scrollMaxPages ){
					$('#load-more, #infscr-loading').remove();
				}

				/**
			     * Fires event after the elements and its images are loaded.
			     *
			     * @event infiniteloaded.themify
			     * @param {object} $newElems The elements that were loaded.
			     */
				$('body').trigger( 'infiniteloaded.themify', [$newElems] );

				$(window).trigger( 'resize' );
			});

			scrollMaxPages = scrollMaxPages - 1;
			if( 1 < scrollMaxPages && 'auto' !== themifyScript.autoInfinite) {
				$('.load-more-button').show();
			}
		});

		// disable auto infinite scroll based on user selection
		if( 'auto' === themifyScript.autoInfinite ){
			$('#load-more, #load-more a').hide();
		}
	}
}

// Entry Filter /////////////////////////
LayoutAndFilter = {
	filterActive: false,
	init: function(el) {
		$('.post-filter+.loops-wrapper:not(.auto_tiles),.masonry:not(.list-post):not(.auto_tiles), .post-filter+.loops-wrapper .ptb_loops_wrapper',el).prepend('<div class="grid-sizer"></div><div class="gutter-sizer"></div>');
		if (!Themify.is_builder_active) {
			this.enableFilters();
			this.filterActive = true;
		}
		this.filter(el);
	},
	enableFilters: function() {
		var $filter = $('.post-filter');
		if ( $filter.find('a').length > 0 && 'undefined' !== typeof $.fn.isotope ) {
			$filter.find('li').each(function(){
				var $li = $(this),
					$entries = $li.parent().next(),
					cat = $li.attr('class').replace(/(current-cat)|(cat-item)|(-)|(active)/g, '').replace(' ', '');
				if ( $entries.find('.post.cat-' + cat).length === 0 ) {
					$li.hide();
				} else {
					$li.show();
				}
			});
		}
	},
	filter: function(el) {
		var $filter = $('.post-filter',el),
			is_left=  !$( 'body' ).hasClass( 'rtl' );

		initFilter = function() {
			$filter.each( function() {
				var f = $(this),
					isotopeArgs = {
						isOriginLeft: is_left,
						percentPosition: true
					},
					$entries = f.next();

				// PTB isotope fix
				if( $entries.has( '.ptb_loops_wrapper' ).length ) {
					$entries.find( '.grid-sizer, gutter-sizer' ).remove();
					$entries = $entries.find( '.ptb_loops_wrapper' );
				}

				isotopeArgs['masonry'] = {
					columnWidth: $entries.children('.grid-sizer').length ? '.grid-sizer' : null,
					gutter: $entries.children('.gutter-sizer').length ? '.gutter-sizer' : null
				};

				if( ! $entries.hasClass( 'masonry' ) ) {
					$entries.imagesLoaded( function() {
						isotopeArgs['itemSelector'] = '.post';
						isotopeArgs['layoutMode'] = 'fitRows';
						isotopeArgs['fitRows'] = {
							gutter: $entries.children('.gutter-sizer').length ? '.gutter-sizer' : null
						};
						$entries.addClass('masonry-done').isotope( isotopeArgs );
					} );
				}

				f.addClass('filter-visible').on('click', 'a', function( e ) {
					e.preventDefault();

					var $li = $(this).parent(),
						$cat = false,
						$auto = $('body').hasClass('tile_enable') && $entries.hasClass('auto_tiles');
					
					if ( $li.hasClass('active') ) {
						$li.removeClass('active');
						! $auto && ( isotopeArgs['filter'] = '.post' );
					} else {
						$li.addClass('active').siblings().removeClass('active');
						$cat = $li.attr('class').replace(/(current-cat)|(cat-item)|(-)|(active)/g, '').replace(' ', '');
						! $auto && ( isotopeArgs['filter'] = '.cat-' + $cat );
					}

					if (!e.isTrigger && $auto && $entries.data('themify_tiles')) {
						var $post = $entries.children('.post');

						$post.show();
						$cat && $post.not('.cat-' + $cat).hide();
						$entries.data('themify_tiles').update();

						setTimeout(function () {
							$.themify_tiles.resizeParent($entries);
							f.removeClass('filter-disable');
						}, Math.round(parseFloat($entries.css('transition-duration')) * 1000) + 100);
					} else {
						$entries.addClass( 'masonry-done' ).imagesLoaded( function() {
							$entries.isotope( isotopeArgs );
							f.removeClass('filter-disable');
						} );
					}

				});
			});
		};

		if( Themify.is_builder_active ) {
			$( 'body' ).one( 'builder_load_module_partial', function( e, el, type ) {
				! el && loadIsotop( $filter.find('a').length > 0, initFilter );
			});
		} else {
			loadIsotop( $filter.find( 'a' ).length > 0, initFilter );
		}
	},
	scrolling: false,
	reset: function() {
		$('.post-filter').find('li.active').find('a').addClass('previous-active').trigger('click');
		this.scrolling = true;
	},
	restore: function() {
		//$('.previous-active').removeClass('previous-active').trigger('click');
		var $first = $('.newItems').first(),
			self = this,
			to = $first.offset().top - ( $first.outerHeight(true)/2 ),
			speed = 800;

		if ( to >= 800 ) {
			speed = 800 + Math.abs( ( to/1000 ) * 100 );
		}
		$('html,body').stop().animate({
			scrollTop: to
		}, speed, function() {
			self.scrolling = false;
		});
	},
	layout: function(el) {
		var posts = $('.loops-wrapper.masonry:not(.list-post)',el);
		if( posts.length > 0 ) {
			var last = posts.children('.loops-wrapper > article').last();
			function callback(){
				posts
					.addClass('masonry-done')
					.isotope({
						masonry: {
							columnWidth: '.grid-sizer',
							gutter: '.gutter-sizer'
						},
						itemSelector: '.loops-wrapper > article',
						isOriginLeft : ! $( 'body' ).hasClass( 'rtl' )
					})
					.isotope( 'once', 'layoutComplete', function() {
						$(window).trigger('resize');
					});
			}
			if(last.hasClass('wow')){
				last.one('webkitAnimationEnd oanimationend msAnimationEnd animationend',function(){
					setTimeout(callback,1000);
				});
			}
			else{
				callback();
			}
		}

		if(themifyScript.shop_masonry==='yes'){
			var $products = $('.woocommerce.archive',el).find('#content').find('ul.products');
			if ( $products.length ) {
				$products.imagesLoaded(function(){
					$products.isotope({
						layoutMode: 'fitRows',
						itemSelector : '.product',
						isOriginLeft : ! $( 'body' ).hasClass( 'rtl' )
					}).addClass('masonry-done');
				});
			}
		}
		var $gallery = $('.gallery-wrapper.packery-gallery',el);
		if ( $gallery.length > 0 ) {
			$gallery.imagesLoaded(function(){
				$gallery.isotope({
					layoutMode: 'packery',
					itemSelector: '.item'
				});
			});
		}
	},
	reLayout: function() {
		$('.masonry').not('.list-post').each(function(){
			var $loopsWrapper = $(this);
			if ( 'object' === typeof $loopsWrapper.data('isotope') ) {
				$loopsWrapper.isotope('layout');
			}
		});
		var $gallery = $('.gallery-wrapper.packery-gallery');
		if ( $gallery.length > 0 && 'object' === typeof $gallery.data('isotope') ) {
			$gallery.isotope('layout');
		}
		if(themifyScript.shop_masonry==='yes'){
			var $products = $('.woocommerce.archive').find('#content').find('ul.products');
			if ( $products.length && 'object' === typeof $products.data('isotope') ) {
				$products.isotope('layout');
			}
		}
	}
};


// DOCUMENT READY
$(document).ready(function() {

	var $body = $('body'), 
            $header = $('#header'),
             $header_icons = $( '.header-icons' );
	if( $body.hasClass( 'header-leftpane' ) || $body.hasClass( 'header-rightpane' ) ) {
		$( '.cart-icon' ).appendTo( $( '.social-widget' ) );
	} else if( $body.hasClass( 'header-minbar' ) ) {
		$( '.cart-icon' ).appendTo( $( '.header-icons' ) );
	} else {
		$(window).on( 'throttledresize', function() {
			if( $header_icons.is( ':visible' ) && ! $header_icons.find( '.cart-icon' ).length ) {
				$( '.cart-icon' ).appendTo( $header_icons );
			} else if ( ! $header_icons.is( ':visible' ) && $header_icons.find( '.cart-icon' ).length ) {
				$( '.cart-icon' ).appendTo( $( '#main-nav-wrap' ) );
			}
		});
	}

	///// Header Top Widget 
	var header_top_widget_content = $('.header-top-widgets .header-widget').wrap('</p>').parent().html();
	if(header_top_widget_content != undefined){
		$('.header-top-widgets #headerwrap').prepend("<div class='header-widget-full clearfix'><div class='header-widget-inner'>"+ header_top_widget_content +"</div></div>");
	}
	/////////////////////////////////////////////
	// Scroll to row when a menu item is clicked.
	/////////////////////////////////////////////
	if ( 'undefined' !== typeof $.fn.themifyScrollHighlight ) {
		$body.themifyScrollHighlight();
	}
	
	/////////////////////////////////////////////
	// Initialize Packery Layout and Filter
	/////////////////////////////////////////////
	function LayoutAndFilterCallback(){
		var condition = $('.post-filter+.loops-wrapper,.masonry:not(.list-post)').length>0
			|| (themifyScript.shop_masonry==='yes' && $('.woocommerce.archive').find('#content').find('ul.products').length>0)
			|| $('.gallery-wrapper.packery-gallery').length>0;
			
		loadIsotop(condition,function(){
			LayoutAndFilter.init();
			$body.imagesLoaded().always(function(){
				LayoutAndFilter.layout();
			});
		});
	}
	
	if (Themify.is_builder_active) {
		$body.one('builder_load_module_partial',function(e,el,type){
			if(!el){
				LayoutAndFilterCallback();
			}
		});
	}
	else{
		LayoutAndFilterCallback();
	}
	/////////////////////////////////////////////
	// Scroll to top
	/////////////////////////////////////////////
    var $back_top = $('.back-top');
        if($back_top.length>0){
            if(!isFullPageScroll && $back_top.hasClass('back-top-float')){
                $(window).on("scroll touchstart.touchScroll touchmove.touchScroll", function() {
                    if( window.scrollY < 10){
                        $back_top.addClass('back-top-hide');
                    }else{
                        $back_top.removeClass('back-top-hide');
                    }
                });

            }
            $back_top.on('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                if(isFullPageScroll){
                    $('#footerwrap').toggleClass('expanded');
                }
                else{
                    themeScrollTo(0);
                }
            });
	 }
	/////////////////////////////////////////////
	// Toggle main nav on mobile
	/////////////////////////////////////////////
	if( is_touch_device() && typeof $.fn.themifyDropdown != 'function' ) {
		Themify.LoadAsync(themify_vars.url + '/js/themify.dropdown.js', function(){
			$( '#main-nav' ).themifyDropdown();
		});
	}

	if ( $body.hasClass( 'header-minbar' ) || $body.hasClass( 'header-leftpane' ) ) {
		/////////////////////////////////////////////
		// Side Menu for header-minbar and header-leftpane
		/////////////////////////////////////////////
		$('#menu-icon').themifySideMenu({
			close: '#menu-icon-close',
			side: 'left'
		});
		/////////////////////////////////////////////
		// NiceScroll only for header-minbar and header-leftpane
		/////////////////////////////////////////////
		var headerNicescroll = function() {
			if ( 'undefined' !== typeof $.fn.niceScroll && ! is_touch_device() ) {
				var $niceScrollTarget = $header;
				if ( $body.hasClass( 'header-minbar' ) ) {
					$niceScrollTarget = $('#mobile-menu');
				}
				$niceScrollTarget.niceScroll();
				$body.on( 'sidemenushow.themify', function(){
					setTimeout(function(){
						$niceScrollTarget.getNiceScroll().resize();
					}, 200);
				} );
			}
		};

		if( $header.length ) {
			if( typeof $.fn.niceScroll !== 'function' ) {
				Themify.LoadAsync(themifyScript.themeURI + '/js/jquery.nicescroll.min.js', headerNicescroll, null, null, function () { return ('undefined' !== typeof $.fn.niceScroll); } );
			} else {
				headerNicescroll();
			}
		}
	} 
	else if ( $body.hasClass( 'header-slide-out' ) || $body.hasClass( 'header-rightpane' ) ){
		$('#menu-icon').themifySideMenu({
			close: '#menu-icon-close',
			side: 'right'
		});

		var nicescrollMenu = function() {
			if ( 'undefined' !== typeof $.fn.niceScroll && ! is_touch_device() ) {
				var $niceScrollTarget = $header;
				if ( $body.hasClass( 'header-slide-out' ) ) {
					$niceScrollTarget = $('#mobile-menu');
				}
				$niceScrollTarget.niceScroll();
				$body.on( 'sidemenushow.themify', function(){
					setTimeout(function(){
						$niceScrollTarget.getNiceScroll().resize();
					}, 200);
				} );
			}
		};

		if( $header.length || $body.hasClass( 'header-slide-out' ) ) {
			if( typeof $.fn.niceScroll !== 'function' ) {
				Themify.LoadAsync(themifyScript.themeURI + '/js/jquery.nicescroll.min.js', nicescrollMenu, null, null, function () { return ('undefined' !== typeof $.fn.niceScroll); } );
			} else {
				nicescrollMenu();
			}
		}
	}
	else {
		/////////////////////////////////////////////
		// Side Menu for all other header designs
		/////////////////////////////////////////////
		$('#menu-icon').themifySideMenu({
			close: '#menu-icon-close'
		});
				
		var $overlay = $( '<div class="body-overlay">' );
		$body.append( $overlay ).on( 'sidemenushow.themify', function () {
			$overlay.addClass( 'body-overlay-on' );
		} ).on( 'sidemenuhide.themify', function () {
			$overlay.removeClass( 'body-overlay-on' );
		} ).on( 'click.themify touchend.themify', '.body-overlay', function () {
			$( '#menu-icon' ).themifySideMenu( 'hide' );
			$( '#cart-icon' ).themifySideMenu( 'hide' );
		} ); 
		$(window).resize(function(){
			if($('#mobile-menu').hasClass('sidemenu-on') && $( '#menu-icon' ).is(':visible')){
				$overlay.addClass( 'body-overlay-on' );
			} else {
				$overlay.removeClass( 'body-overlay-on' );
			}
		});
	}


	if ( !($body.hasClass( 'header-overlay' )) ) {

		$("#main-nav li.menu-item-has-children > a, #main-nav li.page_item_has_children > a").after(
			"<span class='child-arrow'></span>"
		);
		$('#main-nav li.menu-item-has-children > .child-arrow, #main-nav li.page_item_has_children > .child-arrow').click(function(){
			$(this).toggleClass('toggle-on');
			return true;
		});

	}

	/////////////////////////////////////////////
	// Slide cart icon
	/////////////////////////////////////////////
	$('#cart-icon').themifySideMenu({
		panel: '#slide-cart',
		close: '#cart-icon-close'
	});
	
	/////////////////////////////////////////////
	// Add class "first" to first elements
	/////////////////////////////////////////////
	$('.highlight-post:odd').addClass('odd');

	var nicescrollHeaderStuff = function() {
		if ( 'undefined' !== typeof $.fn.niceScroll && ! is_touch_device() ) {
			// NiceScroll Initialized Default
			if ($(window).width() > 1200) {
                            $(".header-horizontal .header-widget, .header-top-bar .header-widget, .boxed-compact .header-widget, .header-stripe .header-widget").niceScroll();
			}
		}
	};

	if( $(".header-horizontal .header-widget, .header-top-bar .header-widget, .boxed-compact .header-widget, .header-stripe .header-widget").length ) {
		if( typeof $.fn.niceScroll !== 'function' ) {
			Themify.LoadAsync(themifyScript.themeURI + '/js/jquery.nicescroll.min.js', nicescrollHeaderStuff, null, null, function () { return ('undefined' !== typeof $.fn.niceScroll); } );
		} else {
			nicescrollHeaderStuff();
		}
	}
	
	var $headerWidgets = $('.header-horizontal, .header-top-bar, .boxed-compact, .header-stripe').find('.header-widget');
	if ( $headerWidgets.length > 0 ) {
		// Header Horizontal, Header Topbar, Boxed Compact Add pull down wrapper
		$( '.header-horizontal #main-nav, .header-top-bar #main-nav, .boxed-compact #main-nav, .header-stripe #main-nav' ).after( $( '<a href="#" class="pull-down">' ) );
		
		// Pull Down onclick Header Horizontal, Header Topbar, Boxed Compact Only
		$('.pull-down').on('click', function(e) {
			if( ! is_touch_device() ) {
				if( typeof $.fn.niceScroll !== 'function' ) {
					Themify.LoadAsync(themifyScript.themeURI + '/js/jquery.nicescroll.min.js', function() {
						$headerWidgets.getNiceScroll().resize();
					}, null, null, function () { return ('undefined' !== typeof $.fn.niceScroll); } );
				} else {
					$headerWidgets.getNiceScroll().resize();
				}	
			}

			$('#header').toggleClass('pull-down-close');
			$headerWidgets.slideToggle( 'fast', function() {
				$('#pagewrap').css('paddingTop', $('#headerwrap').outerHeight(true));
			});
			e.preventDefault();
		});
	}
	
	// Reset NiceScroll Resize
	$(window).resize(function(){
		if ($(window).width() < 1200) {
                    var nicescrollItems = $(".header-horizontal .header-widget, .header-top-bar .header-widget, .boxed-compact .header-widget, .header-stripe .header-widget");
                    if( nicescrollItems.length && ! is_touch_device() ) {
                            if( typeof $.fn.niceScroll !== 'function' ) {
                                    Themify.LoadAsync(themifyScript.themeURI + '/js/jquery.nicescroll.min.js', function() {
                                            nicescrollItems.getNiceScroll().remove();
                                    }, null, null, function () { return ('undefined' !== typeof $.fn.niceScroll); } );
                            } else {
                                    nicescrollItems.getNiceScroll().remove();
                            }
                    }
                    nicescrollItems.attr("style","");
		}
	});

	/////////////////////////////////////////////
	// Make overlay clickable
	/////////////////////////////////////////////
	$body.on( 'click', '.loops-wrapper.grid4.polaroid .post-image + .post-content, .loops-wrapper.grid3.polaroid .post-image + .post-content, .loops-wrapper.grid2.polaroid .post-image + .post-content, .loops-wrapper.grid4.overlay .post-image + .post-content, .loops-wrapper.grid3.overlay .post-image + .post-content, .loops-wrapper.grid2.overlay .post-image + .post-content', function(){
		var $link = $( this ).closest( '.post' ).find( 'a[data-post-permalink]' );
		if($link.attr( 'href' ) && ! $link.hasClass( 'themify_lightbox' ) ) {
			window.location = $link.attr( 'href' );
		}
	});

	/////////////////////////////////////////////
	// Carousel initialization
	/////////////////////////////////////////////

	if ( $('.loops-wrapper.slider').length > 0 ) {
		var elID = 0;
		$('.loops-wrapper.slider').each(function(){
			elID++;
			var $self = $(this);

			if ( 'undefined' === typeof $self.attr( 'id' ) ) {
					// If this doesn't have an id, set dummy id
					$self.attr( 'id', 'loops-wrapper-' + elID );
			}
			var dataID = $self.attr( 'id' );

			$self.addClass( 'slideshow-wrap' );

			if ( $self.find( '.slideshow' ).length === 0 ) {
					$self.wrapInner( '<div class="slideshow" data-id="' + dataID + '" data-autoplay="off" data-speed="1000" data-effect="scroll" data-visible="3" />' );
			} else {
					$self.find( '.slideshow' ).attr( 'data-id', dataID );
			}
		});
	}
	if($('.slideshow').length>0){
		if(!$.fn.carouFredSel){
			Themify.LoadAsync(themify_vars.url+'/js/carousel.min.js',function(){
				ThemifySlider.createCarousel($('.slideshow'));
			},null,null,function(){
                            return typeof $.fn.carouFredSel!=='undefined';
                        });
		}
		else{
			ThemifySlider.createCarousel( $( '.slideshow' ) );
		}
	}
        var $headerwrap = $( '#headerwrap' );
	$body.on( 'announcement_bar_position announcement_bar_scroll_on_after', function( e, el ){
		$('#pagewrap').css( 'paddingTop', Math.floor( $headerwrap.outerHeight(true) ) );
	}).on( 'announcement_bar_position', function( e, el ){
		if( $( this ).hasClass( 'header-minbar' ) ) {
                    var w = $headerwrap.width();
			el.css( {'left': w - Math.abs( parseInt( $headerwrap.css( 'left' ), 10 ) ),'right':w - Math.abs( parseInt( $headerwrap.css( 'right' ), 10 ) )} );
		}
	} );

	var initResize = 0,
            condition = $body.hasClass( 'header-top-bar' ) || $body.hasClass( 'header-horizontal' ) || $body.hasClass( 'header-top-widgets' ) || $body.hasClass( 'boxed-compact' ) || $body.hasClass( 'header-stripe' ) || $body.hasClass( 'header-magazine' );
	$(window).resize(function(){
		/////////////////////////////////////////////
		// Mega menu width
		/////////////////////////////////////////////
		/* Adjust for scroll bar width */
                if(condition){
                    if ($(window).width() > tf_mobile_menu_trigger_point) {
                            $('#main-nav li.has-mega-column > ul, #main-nav li.has-mega-sub-menu > .mega-sub-menu').css(
                                'width', $('#header').width()
                            );
                    } else {
                        $('#main-nav li.has-mega-column > ul,#main-nav li.has-mega-sub-menu > .mega-sub-menu').removeAttr("style");
                    }
                }   
		/////////////////////////////////////////////
		// Nicescroll for mobile menu
		/////////////////////////////////////////////
		if(!initResize && typeof nicescrollMobile === 'function' && $('.mobile_menu_active').length ) {
			if( typeof $.fn.niceScroll !== 'function' ) {
				Themify.LoadAsync( themifyScript.themeURI + '/js/jquery.nicescroll.min.js', nicescrollMobile, null, null, function () { return ('undefined' !== typeof $.fn.niceScroll); } );
			} else {
				nicescrollMobile();
			}

			initResize = 1;
		}
	});
	
	/////////////////////////////////////////////
	// Header Overlay toggle-able dropdown
	/////////////////////////////////////////////
	// Set Dropdown Arrow
	$(".header-overlay #main-nav li.menu-item-has-children > a, .header-overlay #main-nav li.page_item_has_children > a").after(
		"<span class='child-arrow'></span>"
	);	
	$('.header-overlay #main-nav li.menu-item-has-children > .child-arrow, .header-overlay #main-nav li.page_item_has_children > .child-arrow').click(function(){
		$(this).toggleClass('toggle-on').next('div, ul').toggle('fast'); 
		return true;
	});

	var nicescrollMobile = function() {
		if ( 'undefined' !== typeof $.fn.niceScroll && ! is_touch_device() ) {
			if ( $body.hasClass( 'header-overlay' ) ) {
				
				var $niceScrollTarget = $('#mobile-menu');
				$niceScrollTarget.niceScroll();
				$body.on('sidemenushow.themify', function(){
					setTimeout(function(){
						$niceScrollTarget.getNiceScroll().resize();
					}, 200);
				});
			
			}
		}
	};

	if( $('.mobile_menu_active').length  ) {
		if( typeof $.fn.niceScroll !== 'function' ) {
			Themify.LoadAsync(themifyScript.themeURI + '/js/jquery.nicescroll.min.js', nicescrollMobile, null, null, function () { return ('undefined' !== typeof $.fn.niceScroll); } );
		} else {
			nicescrollMobile();
		}
	}
	/////////////////////////////////////////////
	// One Page Scroll
	/////////////////////////////////////////////
	if (isFullPageScroll && $body.hasClass('query-section')) {
		 themifyScript.hash = window.location.hash.replace('#', '').replace('!/', '');
		if ('undefined' !== typeof $.fn.themifyScrollHighlight) {
			$body.on('scrollhighlight.themify', function (e, section) {
				if ('undefined' != typeof section && '' != section) {
					$('#fp-nav').find('li').eq($('.tb_section-' + section.replace('#', '')).index()).find('a').trigger('click');
				}
			});
			$(window).trigger('scroll');
		};
		// Get rid of wow js animation since animation is managed with fullpage js
		var callbackTimer = setInterval(function () {
			if ('undefined' !== typeof ThemifyBuilderModuleJs) {
				clearInterval(callbackTimer);
				wowInit2 = ThemifyBuilderModuleJs.wowInit;
				ThemifyBuilderModuleJs.wowInit = function () {};
			}
		}, 100);

		createFullScrolling();
		$body.on('builder_toggle_frontend', function (event, is_edit) {
			if (typeof $.fn.fullpage !== 'undefined') {
				if (is_edit) {
					$.fn.fullpage.destroy('all');
					$('html,body').css('overflowY','auto');
				} else {
					createFullScrolling();
				}
			}
		});

		if( $( window.frameElement ).is( '#themify_builder_site_canvas_iframe' ) ) {
			$.fn.fullpage.destroy('all');
		}

		$body.on('themify_onepage_afterload', function (e, $panel) {
			// Trigger wow display for elements in this panel
			if (tbLocalScript && tbLocalScript.animationInviewSelectors && typeof ThemifyBuilderModuleJs !== 'undefined' && ThemifyBuilderModuleJs.wow) {
				$(tbLocalScript.animationInviewSelectors).each(function (i, selector) {
					$(selector, $panel).each(function () {
						ThemifyBuilderModuleJs.wow.show(this);
					});
				});
			}
		}).on('themify_onepage_afterload themify_onepage_after_render', function (event, $section, section_id) {
			if ($.fn.waypoint) {
				Waypoint.refreshAll();
			}
			if ('undefined' !== typeof ThemifyBuilderModuleJs && ThemifyBuilderModuleJs.wow !== null && typeof ThemifyBuilderModuleJs.wow.scrollHandler() === 'boolean') {
				ThemifyBuilderModuleJs.wow.scrollHandler();
			}

		});
	}
	else {
		$('.section_loader').hide();
	}
	if(isFullPageScroll){
		// anchor scrollTo
	   // exclude lightbox links from scrolling
		$body.on('click', 'a[href*="#"]:not(.themify_lightbox)', function (e) {
			var url = $(this).prop('href'),
				 idx = url.indexOf('#'),
				 hash = idx != -1 ? url.substring(idx + 1) : '',
				 offset = 0,
				 $sectionEl = $('.tb_section-' + hash);
			if (hash.length > 1 && $sectionEl.length > 0  && hash !== 'header') {
				 offset = $sectionEl.offset().top;
				 // If header is set to fixed, calculate this
				 if ($('.fixed-header').length > 0) {
					 offset += $('#headerwrap').outerHeight(true);
				 }
				$.fn.fullpage.moveTo($sectionEl.index(sectionClass) + 1);
				e.preventDefault();
			}
		});
	}

	//Fix overlay style regardless the featured image position above/below post title
	$(".loops-wrapper.overlay .post").each(function () {
		$(this).find('.post-image').insertBefore($(this).find('.post-content'));        
	});

	/////////////////////////////////////////////
	// Mega Menu
	/////////////////////////////////////////////
	var megaMenuInit = function() {
		if( 'undefined' !== typeof $.fn.ThemifyMegaMenu ) {
			/* add required wrappers for mega menu items */
			$( '.has-mega-sub-menu' ).each(function(){
				var $this = $( this );

				$this.find( '> ul' ).removeAttr( 'class' )
					.wrap( '<div class="mega-sub-menu sub-menu" />' )
					.after( '<div class="mega-menu-posts" />' )
					.find( 'li.menu-item-type-taxonomy' ) // only taxonomy terms can display mega posts
						.addClass( 'mega-link' );
			});

			$('.has-mega-sub-menu').ThemifyMegaMenu({
				events: themifyScript.events
			});
		}
	};
	if( $('.has-mega-sub-menu').length ) {
		if( typeof $.fn.ThemifyMegaMenu !== 'function' ) {
			Themify.LoadAsync( themifyScript.themeURI + '/themify/megamenu/js/themify.mega-menu.js', megaMenuInit );
		} else {
			megaMenuInit();
		}
	}
	
});

// WINDOW RESIZE
$(window).resize(function() {
	
	/////////////////////////////////////////////
	// Swapping logo with Window Resize Function in Header Split Menu Layout
	/////////////////////////////////////////////
	var $body = $('body');
	if ( $body.hasClass( 'header-menu-split' ) ){
		if ($('#menu-icon').is(':visible')) {
			if ($('.header-bar').find('#site-logo').length == 0) {
				$('#site-logo').prependTo( '.header-bar' );
			}
		} else if ($('.themify-logo-menu-item').find('#site-logo').length === 0) {
                        $('.themify-logo-menu-item').append($('.header-bar').find('#site-logo'));
		}
	}

	var mgviewport = $(window).width();
	if (mgviewport > tf_mobile_menu_trigger_point) {
		if ( $body.hasClass( 'header-magazine' ) ) {
			clearTimeout(  );
			setTimeout( function() {
				$('#headerwrap').css({
					'paddingBottom': $('.navbar-wrapper').outerHeight()
				});
			}, 400 );
		}
	}
	
}).load(function() {
        var $body = $('body');
	///////////////////////////////////////////
	// Initialize infinite scroll
	///////////////////////////////////////////
	if (!Themify.is_builder_active) {
		if ( $body.hasClass( 'woocommerce' ) && $body.hasClass( 'archive' ) ) {
			doInfinite( $( '#content ul.products' ), '#content .product' );
		} else {
			doInfinite( $( '#loops-wrapper' ), '.post' );
		}
	}

	///////////////////////////////////////////
	// Header Video
	///////////////////////////////////////////
        var $header = $('#headerwrap'),
            $videos = $header.find('[data-fullwidthvideo]');
            if($header.data('fullwidthvideo')){
                $videos = $videos.add($header);
            }
	function ThemifyBideo(){
                var init = true,
                    $fixed = $header.hasClass('fixed-header');
                if ($fixed){
                    $header.removeClass('fixed-header');
                }
                $videos.each(function(i){
                     var url = $(this).data('fullwidthvideo');
                     if(url){
                        var options = {
                            url: url,
                            doLoop: true,
                            ambient:true,
                            id: i
                        };
                        if (init && $fixed){
                            init = false;
                            options['onload'] = function(){
                                $header.addClass('fixed-header');
                            }
			}
                        $(this).ThemifyBgVideo(options);
                    }
                });
	}
	if ($videos.length>0 && !is_touch_device()) {
		if(typeof $.fn.ThemifyBgVideo === 'undefined'){
			 Themify.LoadAsync(
                            themify_vars.url + '/js/bigvideo.js',
                            ThemifyBideo,
                            null,
                            null,
                            function () {
                                return ('undefined' !== typeof $.fn.ThemifyBgVideo);
                            }
                    );
		}
		else{
		   ThemifyBideo();
		}
	}

	/////////////////////////////////////////////
	// Entry Filter Layout
	/////////////////////////////////////////////
	function reLayoutCallback(){
		loadIsotop($('.masonry:not(.list-post), .gallery-wrapper.packery-gallery').length>0 || (themifyScript.shop_masonry==='yes' && $('.woocommerce.archive #content ul.products').length>0),function(){
			$body.imagesLoaded( function() {
				if(!Themify.is_builder_active){
					$(window).resize();
				}
				LayoutAndFilter.reLayout();
			});
		});
	}
	if (Themify.is_builder_active) {
		$body.one('builder_load_module_partial',function(e,el,type){
			if(!el){
				reLayoutCallback();
			}
		});
	}
	else{
		reLayoutCallback();
	}

	// Edge menu
	$(function ($) {
		$( '#main-nav li:has(ul), #footer-nav li:has(ul)' ).on( 'mouseenter dropdown_open', function (e) {
			/* prevent "edge" classname being removed by mouseleave event when flipping through menu items really fast */
			window.clearTimeout( $( this ).data( 'edge_menu_t' ) );

			var elm = $( 'ul:first', this ),
                            l = elm.offset().left,
                            w = elm.width(),
                            docW = $(window).width(),
                            isEntirelyVisible = (l + w <= docW);

			if ( ! isEntirelyVisible ) {
				$( this ).addClass( 'edge' );
			}
		})
		.on( 'mouseleave dropdown_close', function() {
			var $this = $( this ),
                            t = setTimeout( function(){
				$this.removeClass( 'edge' );
			}, 300 );
			$this.data( 'edge_menu_t', t );
		} );
	});

	if (isFullPageScroll && $body.hasClass('query-section')) {
		 // Hack Chrome browser doesn't autoplay the video background
		$body.on('themify_onepage_after_render', function () {
			$.each(tbLocalScript.animationInviewSelectors, function (index, selector) {
				$(selector).css('visibility', 'hidden');
			});

			// Section deep linking
			if (window.location.hash) {
				setTimeout(function () {
					var hashSection = themifyScript.hash;
					if ('' != hashSection && '#' != hashSection) {
						var $sectionEl = usesRows ? $('.tb_section-' + hashSection) : $('#' + hashSection);
						if ($sectionEl.length > 0) {
							$.fn.fullpage.moveTo($sectionEl.index() + 1);
							if( typeof ThemifyBuilderModuleJs !== 'undefined' ) {
								$(tbLocalScript.animationInviewSelectors).each(function (i, selector) {
									$(selector, $sectionEl).addBack().each(function () {
										ThemifyBuilderModuleJs.wow.show(this);
									});
								});
							}
						}
					}
				}, 1500);
			}
		});
		// Make row backgrounds visible.
		$('.module_row').css('visibility', 'visible');
	}

	// remove item ajax
	$(document).on('click', '.remove-item-js', function (e) {
		e.preventDefault();
		// AJAX add to cart request
		var $thisbutton = $(this),
			data = {
				action: 'theme_delete_cart',
				remove_item: $thisbutton.attr('data-product-key')
			},
			$addedButton = $body.find( '.ajax_add_to_cart ' ),
			removedURL = $thisbutton.parent().find( '.product-title a' ).attr( 'href' );

		// Ajax action
		$.post(woocommerce_params.ajax_url, data, function (response) {
			var fragments = response.fragments,
                            cart_hash = response.cart_hash;
		
			// Changes button classes
			if ($thisbutton.parent().find('.added_to_cart').size() == 0)
				$thisbutton.addClass('added');

			// Replace fragments
			if (fragments) {
                            $.each(fragments, function (key, value) {
                                    $(key).addClass('updating').replaceWith(value);
                            });
                            if(!$(fragments['#shopdock-ultra']).find('.cart-total').length) {
                                    $('#cart-icon-close').trigger( 'click' );
                            }
                        }
			if( $addedButton.length ) {
				$addedButton.each( function() {
					if( $( this ).hasClass( 'added' ) && $( this ).closest( '.post-content' ).find( '[href="' + removedURL + '"]' ).length ) {
						$( this ).removeClass( 'added' ).siblings( '.added_to_cart' ).remove();
					}
				} )
			}
			// Trigger event so themes can refresh other areas
			$body.trigger('removed_from_cart', [fragments, cart_hash]);

		});
	});
		
			
		function ThemifyTiles(container) {
			var dummy = $('<div class="post-tiled tiled-square-small" style="visibility: hidden !important; opacity: 0;" />').appendTo(container.first()),
					$gutter = themifyScript.tiledata['padding'],
					$small = parseFloat(dummy.width());
					dummy.remove();

			container.each(function () {
				var $this = $(this);
				$(this).imagesLoaded().always(function (instance) {
					$this.children('.product').addClass('post');
					var $post = $this.children('.post');
					themifyScript.tiledata['padding'] = $this.hasClass('no-gutter') ? 0 : $gutter;
					$this.themify_tiles(themifyScript.tiledata, $small);
					setClasses($post, $small);
				});
			});

		}
		;
		function AjaxThemifyTiles(container) {

			$(document).ajaxComplete(function (e, request, settings) {
				if (settings.type === 'POST' && settings.url.indexOf('wpf_search')) {
					ThemifyTiles($('.loops-wrapper.auto_tiles'))
				}
			});
		}
		var container = $('.auto_tiles');
		if (container.length > 0 && $body.hasClass('tile_enable')) {
			if ('undefined' === typeof Tiles) {
				Themify.LoadAsync(themifyScript.themeURI + '/js/tiles.min.js', function () {
					if (!$.fn.themify_tiles) {
						if('undefined' === typeof $.fn.backstretch){
							Themify.LoadAsync(themify_vars.url + '/js/backstretch.min.js', function () {
								Themify.LoadAsync(themifyScript.themeURI + '/js/themify-tiles.js', function () {
									ThemifyTiles(container);
									AjaxThemifyTiles(container);
								},
								null,
								null,
								function () {
									return ('undefined' !== typeof $.fn.themify_tiles);
								});
							},
							null,
							null,
							function () {
								return ('undefined' !== typeof $.fn.backstretch);
							});
						}
						else{
							Themify.LoadAsync(themifyScript.themeURI + '/js/themify-tiles.js', function () {
								ThemifyTiles(container);
								AjaxThemifyTiles(container);
							},
							null,
							null,
							function () {
								return ('undefined' !== typeof $.fn.themify_tiles);
							});
						}
					}
					else {
						ThemifyTiles(container);
						AjaxThemifyTiles(container);
					}
				}
				, null,
						null,
						function () {
							return ('undefined' !== typeof Tiles);
						});
			}
			else {
				ThemifyTiles(container);
				AjaxThemifyTiles(container);
			}

		}
		if (Themify.is_builder_active) {
			$body.on('builder_load_module_partial',function(e,el,type){
				loadIsotop(el && el.find('.builder-posts-wrap.masonry:not(.list-post)').length>0,function(){
					LayoutAndFilter.init(el);
					$body.imagesLoaded().always(function(){
						LayoutAndFilter.layout(el);
					});
				},true);
			});
		}
		

});
})(jQuery);