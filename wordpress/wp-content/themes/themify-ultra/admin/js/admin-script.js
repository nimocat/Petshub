(function ($) {
    'use strict';
    $(document).ready(function () {
        var $option = $('#section_full_scrolling'),
                $layoutFields = $option.closest('.themify_write_panel').find('.themify_field-layout'),
                $layout = $layoutFields.find('[alt="sidebar-none"]').parent('a'),
                $layoutRow = $layout.closest('.themify_field_row'),
                $content = $layoutFields.find('[alt="full_width"]').parent('a'),
                $contentRow = $content.closest('.themify_field_row'),
                $sticky_header = $('#fixed_header-default'),
                $full_height = $('#full_height_header-default'),
                $hideTitle = $('#hide_page_title'),
                $hideTitleRow = $hideTitle.closest('.themify_field_row'),
                $comments = $('#comment_status,#ping_status'),
                $commentsRow = $('#commentstatusdiv').find('.meta-options'),
                $header = $('input[name="header_design"]').closest('.themify_field'),
                $header_topbar = $header.find('[alt="header-top-bar"]'),
                $header_horizontal = $header.find('[alt="header-horizontal"]'),
                $header = $header.find('img'),
                $rows = [$layoutRow, $contentRow, $hideTitleRow, $commentsRow],
                $icons = [$layout, $content],
                $headers = [$sticky_header, $full_height],
                $transparent = $('#header_wrap-transparent'),
                $not_allowed_headers = ['header-block', 'boxed-content', 'boxed-layout', 'header-leftpane', 'header-menu-split', 'header-rightpane','default'];


        $.each($rows, function (index, $value) {
            $('<div class="ui-cover" style="width: 100%; height: 15%; position: absolute; z-index: 1;" />').prependTo($value).hide();
        });

        $option.on('change', function () {
            if ('yes' === $option.val()) {
                $.each($icons, function (index, $value) {
                    $value.trigger('click');
                });
                $.each($rows, function (index, $value) {
                    $value.animate({opacity: 0.5}, 800);
                    $('.ui-cover').show();
                });
                $.each($headers, function (index, $value) {
                    $value.trigger('change');
                    $value.closest('.themify_field_row').css({'opacity': 0.5, 'cursor': 'not-allowed', 'pointer-events': 'none'});
                });
                if ($.inArray($('[name="header_design"]').val(), $not_allowed_headers) >= 0) {
                    $header_horizontal.trigger('click');
                }
                $.each($header, function (index, $value) {
                    if ($.inArray($(this).prop('alt'), $not_allowed_headers) >= 0) {
                        $(this).closest('a').css({'opacity': 0.3, 'cursor': 'not-allowed', 'pointer-events': 'none'});
                    }
                });


                $hideTitle.val('yes').find('[value=default],[value=no]').prop('disabled', true);
                $comments.prop('checked', false).prop('disabled', true);
            } else {
                $.each($headers, function (index, $value) {
                    $value.closest('.themify_field_row').css({'opacity': 1, 'cursor': 'auto', 'pointer-events': 'auto'});
                });
                $.each($rows, function (index, $value) {
                    $value.animate({opacity: 1}, 800);
                    $('.ui-cover').hide();
                });
                $.each($header, function (index, $value) {
                    if ($.inArray($(this).prop('alt'), $not_allowed_headers) >= 0) {
                        $(this).closest('a').css({'opacity': 1, 'cursor': 'auto', 'pointer-events': 'auto'});
                    }
                });
                $hideTitle.find('[value=default],[value=no]').prop('disabled', false);
                $comments.prop('disabled', false);
            }
        });
        $('[name="header_design"]').on('change',function(){
            var $transparent_label = $transparent.next('label');
            if ($.inArray($(this).val(),['header-leftpane','header-rightpane','header-minbar']) >= 0) {
                $transparent.fadeOut();
                $transparent_label.fadeOut();
            }
            else{
                $transparent.fadeIn();
                $transparent_label.fadeIn();
            }
           
        }).trigger('change');
        $option.trigger('change');
        $('#query-posts input[name="layout"],#query-portfolio input[name="portfolio_layout"]').change(function (e) {
            var $val = $(this).val(),
                    $post_type = $(this).parents('#query-posts').length > 0 ? '' : 'portfolio_',
                    $media = $post_type == '' ? $('#media_position').closest('.themify_field_row') : $(),
                    $masonary = $('#' + $post_type + 'disable_masonry').closest('.themify_field_row'),
                    $content_layout = $post_type == '' ? $('#post_content_layout') : $('#portfolio_content_layout'),
                    $content_layout = $content_layout.closest('.themify_field_row'),
					$gutter = $('#' + $post_type + 'post_gutter').closest('.themify_field_row'),
                    $category = $('#' + $post_type + 'query_category').val() || $('#' + $post_type + 'query_category + input').val();


            // SlideUp/animation doesn't work when element is hidden
            if (!$category) {
                $masonary.hide();
                $media.hide();
                $content_layout.hide();
                return;
            }
            if ($val === 'list-post' || $val === 'auto_tiles' || $val === 'slider' || $val === 'list-large-image' || $val === 'list-thumb-image' || $val === 'grid2-thumb') {
                $masonary.slideUp();
                if ($val !== 'auto_tiles') {
                    $gutter.slideUp();
                    if($val==='list-post'){
                        $content_layout.slideDown();
                        $media.slideDown();
                    }
                    else{
                        $content_layout.slideUp();
                    }
                }
                else {
                    $media.slideUp();
                    $gutter.slideDown();
                    $content_layout.slideUp();
                }
            }
            else {
                $masonary.slideDown()
                $media.slideDown()
                $content_layout.slideDown();
                $gutter.slideDown();
            }
        });
        function query_change($this){
            var $post_type = $this.closest('#query_category').length > 0 ? '' : 'portfolio_';
            $('input[name="' + $post_type + 'layout"],#' + $post_type + 'more_posts').trigger('change');
        }
        $('#query_category,#portfolio_query_category').change(function () {
            query_change($(this));
        });

        $('#portfolio_more_posts, #more_posts').change(function (e) {
            var $val = $(this).val(),
                    $post_type = $(this).parents('#query-posts').length > 0 ? '' : 'portfolio_',
                    $pagination = $('#' + $post_type + 'hide_navigation'),
                    $category = $('#' + $post_type + 'query_category').val();

            $pagination = $pagination.closest('.themify_field_row');
            if (!$category) {
                $pagination.hide();
                return;
            }
            if ($val === 'infinite' || !$('#' + $post_type + 'query_category').val()) {
                $pagination.slideUp();
            }
            else {
                $pagination.slideDown();
            }
        });

        $('input[name="post_layout"]').change(function () {
            var $sidebar = $(this).closest('.themify_write_panel').find('input[name="layout"]').closest('.themify_field'),
                $sidebar_layouts = $sidebar.find('a'),
                $image_deminission = $('#image_width').closest('.themify_field_row');
            $image_deminission.show();
            if ($(this).val() === 'split') {
                $sidebar_layouts.css('pointer-events', 'none').last().trigger('click');
                $sidebar.css({'opacity': 0.5, 'cursor': 'not-allowed'});
                $.each($header, function () {
                    if ($(this).prop('alt') !== $header_horizontal.prop('alt') && $(this).prop('alt') !== $header_topbar.prop('alt')) {
                        $(this).parent('a').css({'pointer-events': 'none'}).animate({opacity: 0.5}, 800);
                    }
                });
                $header.closest('.themify_field').css('cursor', 'not-allowed');
                var $selected_header = $header.find('selected img').prop('alt');
                if ($selected_header !== $header_horizontal.prop('alt') && $selected_header !== $header_topbar.prop('alt')) {
                    $header_topbar.trigger('click');
                }
            }
            else {
                if ($(this).val() === 'gallery') {
                    $image_deminission.hide();
                }
                $sidebar_layouts.css('pointer-events', 'auto');
                $sidebar.css({'opacity': 1, 'cursor': 'auto'});
                $header.closest('.themify_field').css('cursor', 'auto');
                $header.parent('a').removeAttr('style');
            }
        });
        $('input[name="setting-default_page_post_layout_type"],input[name="setting-default_portfolio_single_portfolio_layout_type"]').change(function () {
            var $image_deminission = $(this).closest('.subtab').find('input[name="setting-image_post_single_height"],input[name="setting-default_portfolio_single_image_post_width"]');
            $image_deminission = $image_deminission.closest('p');
            if ($(this).val() === 'gallery') {
                $image_deminission.hide();
            }
            else {
                $image_deminission.show();
            }
        });
		$('input[name="setting-default_post_layout"]').change(function () {
			var $content_layout = $(this).closest('.subtab').find('select[name="setting-post_content_layout"]').closest('p');
            var $masonary = $(this).closest('.subtab').find('select[name="setting-disable_masonry"]').closest('p');
			var $gutter = $(this).closest('.subtab').find('select[name="setting-post_gutter"]').closest('p');
            if ($(this).val() === 'list-large-image' || $(this).val() === 'list-thumb-image' || $(this).val() === 'grid2-thumb') {
                $content_layout.hide();
				$masonary.hide();
				$gutter.hide();
            }
            else {
				$content_layout.show();
				$masonary.show();
				$gutter.show();
            }
        });

        $( 'input[name="setting-default_portfolio_single_layout"]' ).on( 'change', function() {
            var layout = $( this ).parent().next().find('a');

            if( this.value !== 'sidebar-none' ) { 
                if( layout.last().hasClass( 'selected' ) ) {
                    layout.first().trigger( 'click' );
                }
                layout.last().hide();
            } else {
                layout.last().show();
            }

        } ).trigger( 'change' );
        
        // Don't call trigger change,otherwise the query input will be empty
        query_change($('#query_category'));
        query_change($('#portfolio_query_category'));
        $('input[name="header_wrap"]:checked').trigger('click');
        $('input[name="post_layout"],input[name="setting-default_page_post_layout_type"],input[name="setting-default_post_layout"],input[name="setting-default_portfolio_single_portfolio_layout_type"]').trigger('change');
        
    });
}(jQuery));