/*globals window, document, $, jQuery, _, Backbone */
(function ($, _, Backbone) {
	"use strict";
	var media = wp.media,
		regex = /<!--themify_builder_static-->([\s\S]*?)<!--\/themify_builder_static-->/gi,
		regex_enti = /&lt;!--themify_builder_static--&gt;([\s\S]*?)&lt;!--\/themify_builder_static--&gt;/gi;

	wp.mce.views.register( 'themify-builder-static-badge', {
		template: media.template( 'themify-builder-static-badge' ),
		bindNode: function( editor, node ) {
			$(node).on('click', '.themify-builder-mce-view-frontend-btn', this.goToFront)
			.on('click', '.themify-builder-mce-view-backend-btn', this.goToBack);
		},
		getContent: function() {
			return this.template({});
		},
		match: function( content ) {
			var match = regex.exec( content ),
				match2 = regex_enti.exec( content );
			
			if ( match || match2 ) {
				match = _.isNull( match ) ? match2 : match;
				return {
					index: match.index,
					content: match[0],
					options: {}
				};
			}
		},
		View: {
			className: 'themify-builder-static-badge',
			template: media.template( 'themify-builder-static-badge' ),
			getHtml: function() {
				return this.template({});
			}
		},
		edit: function( node ) {
			this.goToFront();
		},
		goToFront: function(){
                    $('#themify_builder_switch_frontend').trigger('click');
		},
		goToBack: function() { 
                    themifybuilderapp._backendBuilderFocus();
		}

	} );

	wp.mce.views._tb_static_content = {
		setContent: function( editor, content ) {
			if( tinyMCE && tinyMCE.activeEditor ) {
				if( tinyMCE.activeEditor.hidden ) {
					$('#content').val(content);
				} else {
					editor.setContent( content );
				}
			} else {
				editor.val(content);
			}
		}
	};

	$(document).on('tinymce-editor-init', function( event, editor ) {
		if (editor.wp && editor.wp._createToolbar) {
			var toolbar = editor.wp._createToolbar([
				'wp_view_edit'
			]);
		}

		if (toolbar) {
			//this creates the toolbar
			editor.on('wptoolbar', function (event) {
				if (editor.dom.hasClass(event.element, 'wpview') && 'themify-builder-static-badge' === editor.dom.getAttrib( event.element, 'data-wpview-type')) {
					event.toolbar = toolbar;
				}
			});
		}

		editor.setContent( wp.mce.views.setMarkers( editor.getContent() ) );

		editor.on('beforesetcontent', function( event ) {
			event.content = wp.mce.views.setMarkers( event.content );
		});
	});

	$('body').on('themify_builder_save_data', function( event, jqxhr, textStatus ){
		var editor;

		if ( _.isEmpty( jqxhr.data.static_content ) ) return true;

		if( tinyMCE && tinyMCE.activeEditor ) {
			editor = tinyMCE.activeEditor;
			var content = false === tinyMCE.activeEditor.hidden ? tinyMCE.activeEditor.getContent() : tinymce.DOM.get('content').value,
				match = regex.exec( content );
		} else {
			editor = $('#content');
			var content = editor.val(),
				match = regex.exec( content );
		}

		if ( _.isNull( match ) ) {
			wp.mce.views._tb_static_content.setContent( editor, content + jqxhr.data.static_content );
		} else {
			wp.mce.views._tb_static_content.setContent( editor, content.replace( match[0], jqxhr.data.static_content ) );
		}
	});

}(jQuery, _, Backbone));