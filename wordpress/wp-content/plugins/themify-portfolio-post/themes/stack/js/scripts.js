var themify_portfolio_posts;
(function(w, $){

	"use strict";

	themify_portfolio_posts = {
		LayoutAndFilter : {
			filterActive: false,
			masonryActive: false,
			init: function() {
				// if ( 'disable-masonry' ) {
					// this.enableFilters();
					// this.filter();
					// this.filterActive = true;
				// }
			},
			enableFilters: function() {
				var $filter = $('.post-filter');
				if ( $filter.find('a').length > 0 && 'undefined' !== typeof $.fn.isotope ) {
					$filter.find('li').each(function(){
						var $li = $(this),
							$entries = $li.parent().next(),
							cat = $li.attr('class').replace(/(current-cat)|(cat-item)|(-)|(active)/g, '').replace(' ', '');
						if ( $entries.find('.portfolio-post.cat-' + cat).length <= 0 ) {
							$li.hide();
						} else {
							$li.show();
						}
					});
				}
			},
			filter: function() {
				var $filter = $('.post-filter');
				if ( $filter.find('a').length > 0 && 'undefined' !== typeof $.fn.isotope ) {
					$filter.addClass('filter-visible').on('click', 'a', function( e ) {
						e.preventDefault();
						var $li = $(this).parent(),
							$entries = $li.parent().next();
						if ( $li.hasClass('active') ) {
							$li.removeClass('active');
							$entries.isotope({
								layoutMode: 'packery',
								filter: '.portfolio-post'
							});
						} else {
							$li.siblings('.active').removeClass('active');
							$li.addClass('active');
							$entries.isotope({
								filter: '.cat-' + $li.attr('class').replace(/(current-cat)|(cat-item)|(-)|(active)/g, '').replace(' ', '')
							});
						}
					});
				}
			},
			scrolling: false,
			reset: function() {
				$('.themify-portfolio-posts .post-filter').find('li.active').find('a').addClass('previous-active').trigger('click');
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
			layout: function() {
				if( $( 'body' ).hasClass( 'tpp-masonry-enabled' ) ) {
					$( '.themify-portfolio-posts .loops-wrapper.portfolio' ).each(function(){
						var $this = $( this );
						$this.imagesLoaded(function(){
							$this.prepend( '<div class="grid-sizer"></div>' );
							$this.masonry( {
								columnWidth: '.grid-sizer',
								itemSelector : '.post',
								percentPosition: true
							} );
						});
					});

					var $gallery = $('.tpp-gallery-wrapper');
					if ( $gallery.length > 0 ) {
						$gallery.imagesLoaded(function(){
							$gallery.masonry( {
								columnWidth: '.grid-sizer',
								itemSelector : '.item',
								percentPosition: true
							} );
						});
					}
				}
			},
			reLayout: function() {
				var $loopsWrapper = $('.themify-portfolio-posts .loops-wrapper'), $gallery = $('.gallery-wrapper.packery-gallery');
				if ( $loopsWrapper.hasClass('masonry-done') && 'object' == typeof $loopsWrapper.data('isotope') ) {
					if ( this.masonryActive ) {
						$loopsWrapper.isotope('layout');
					}
				}
				if ( $gallery.length > 0 && 'object' == typeof $gallery.data('isotope') ) {
					if ( this.masonryActive ) {
						$gallery.isotope('layout');
					}
				}
			}
		},

		doCoverBackgrounds : function() {
			if ( 'undefined' !== typeof $.fn.backstretch && $('.masonry-layout').length > 0 ) {
				$('.themify-portfolio-posts .portfolio-post').each(function() {
					var $postImage = $(this).find('.post-image'),
						$img = $postImage.find('img'),
						src = $img.attr('src');
					if ( 'undefined' !== typeof src && $postImage.find('.backstretch').length <= 0 ) {
						$postImage.backstretch(src);
						var $a = $postImage.find('a'),
							$saveA = $a;
						$a.remove();
						$img.remove();
						$postImage.find('img').wrap($saveA);
					}
				});
			}
		},

		dom_ready : function(){
			themify_portfolio_posts.doCoverBackgrounds();
			themify_portfolio_posts.LayoutAndFilter.init();
			themify_portfolio_posts.LayoutAndFilter.layout();
		}
	}

	$( themify_portfolio_posts.dom_ready );

})(window, jQuery, undefined);