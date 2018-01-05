(function($){

	$(document).ready(function() {
		
		var $backTop = $('.back-top');
		// Scroll to top
		$(window).on('scroll touchstart.touchScroll touchmove.touchScroll', function() {
			if ($backTop.length == 0) {
				return;
			}
			if ($backTop.length > 0 && window.scrollY < 10) {
				$backTop.addClass('back-top-hide');
			} else {
				$backTop.removeClass('back-top-hide');
			}
		});

	});

})(jQuery);