/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global d4plib_admin_data*/
var d4plib_media_image, d4plib_shared;

;(function($, window, document, undefined) {
    d4plib_media_image = {
        handler: null,
        init: function() {
            if (wp && wp.media) {
                if (typeof wp.media.frames.d4plib_media_image_frame === "undefined") {
                    wp.media.frames.d4plib_media_image_frame = wp.media({
                        title: d4plib_admin_data.string_media_image_title,
                        className: "media-frame d4plib-mediaimage-frame",
                        frame: "post",
                        multiple: false,
                        library: { 
                            type: "image" 
                        },
                        button: {
                            text: d4plib_admin_data.string_media_image_button
                        }
                    });

                    wp.media.frames.d4plib_media_image_frame.on("insert", function() {
                        var image = wp.media.frames.d4plib_media_image_frame.state().get("selection").first().toJSON();

                        if (d4plib_media_image.handler) {
                            d4plib_media_image.handler(image);
                        }
                    });
                }
            }
        },
        open: function(handler, hide_menu) {
            d4plib_media_image.handler = handler;

            wp.media.frames.d4plib_media_image_frame.open();

            $(".d4plib-mediaimage-frame .media-frame-title h1").html(d4plib_admin_data.string_media_image_title);
            $(".d4plib-mediaimage-frame .media-frame-toolbar .media-toolbar-primary .media-button-insert").html(d4plib_admin_data.string_media_image_button);

            if (hide_menu) {
                $(".d4plib-mediaimage-frame").addClass("hide-menu");
            }
        }
    };

    d4plib_shared = {
        active_element: null,
        init: function() {
            d4plib_media_image.init();
        },
        image: function() {
            $(".d4plib-image-preview").click(function(e){
                e.preventDefault();

                $(this).parent().find("img").slideToggle(function(){
                    if ($(this).is(":visible")) {
                        $(this).css("display", "block");
                    }
                });
            });

            $(".d4plib-image-remove").click(function(e){
                e.preventDefault();

                if (confirm(d4plib_admin_data.string_are_you_sure)) {
                    $(this).parent().find(".d4plib-image").val("0");
                    $(this).parent().find("img").attr("src", "").hide();
                    $(this).parent().find(".d4plib-image-name").html(d4plib_admin_data.string_image_not_selected);

                    $(this).parent().find(".d4plib-image-preview, .d4plib-image-remove").hide();
                }
            });

            $(".d4plib-image-add").click(function(e){
                e.preventDefault();

                d4plib_shared.active_element = $(this).parent();
                d4plib_media_image.open(d4plib_shared.handlers.image, true);
            });
        },
        images: function() {
            $(document).on("click", ".d4plib-images-preview", function(e){
                e.preventDefault();

                $(this).parent().find("img").slideToggle(function(){
                    if ($(this).is(":visible")) {
                        $(this).css("display", "block");
                    }
                });
            });

            $(document).on("click", ".d4plib-images-remove", function(e){
                e.preventDefault();

                if (confirm(d4plib_admin_data.string_are_you_sure)) {
                    if ($(this).parent().parent().find(".d4plib-images-image").length === 1) {
                        $(this).parent().parent().find(".d4plib-images-none").show();
                    }

                    $(this).parent().remove();
                }
            });

            $(".d4plib-images-add").click(function(e){
                e.preventDefault();

                d4plib_shared.active_element = $(this).parent();
                d4plib_media_image.open(d4plib_shared.handlers.images, true);
            });
        },
        handlers: {
            image: function(obj) {
                var $this = d4plib_shared.active_element;

                $(".d4plib-image", $this).val(obj.id);
                $(".d4plib-image-name", $this).html("(" + obj.id + ") " + obj.name);
                $("img", $this).attr("src", obj.url).hide();

                $(".d4plib-image-preview, .d4plib-image-remove", $this).show();
            },
            images: function(obj) {
                var $this = d4plib_shared.active_element,
                    name = $($this).find(".d4plib-selected-image").data("name");

                var div = $("<div class='d4plib-images-image'>");
                div.append("<input type='hidden' value='" + obj.id + "' name='" + name + "[]' />");
                div.append("<a class='button d4plib-button-action d4plib-images-remove' aria-label='" + d4plib_admin_data.string_media_image_remove + "'><i aria-hidden='true' class='fa fa-ban'></i></a>");
                div.append("<a class='button d4plib-button-action d4plib-images-preview' aria-label='" + d4plib_admin_data.string_media_image_preview + "'><i aria-hidden='true' class='fa fa-search'></i></a>");
                div.append("<span class='d4plib-image-name'>(" + obj.id + ") " + obj.name + '</span>');
                div.append("<img src='" + obj.url + "' />");

                $(".d4plib-selected-image", $this).append(div);

                $(".d4plib-images-none", $this).hide();
            }
        }
    };

    d4plib_shared.init();
    d4plib_shared.image();
    d4plib_shared.images();
})(jQuery, window, document);
