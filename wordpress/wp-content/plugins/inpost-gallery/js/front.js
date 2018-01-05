var pn_inpost_stop_click = false;
jQuery(function() {

    jQuery('body').prepend('<div>').first().find('div').eq(0).addClass('inpost_gallery_info_popup').hide();

    jQuery('.pn_inpost_gallery_in_popup').click(function() {

        if (pn_inpost_stop_click) {
            return;
        }

        pn_inpost_stop_click = true;
        pn_show_static_info_popup(inpost_lang_loading);
        
        var _this = this;
        var data = {
            action: "inpost_gallery_get_gallery",
            popup_shortcode_key: jQuery(this).data('shortcode-key'),
            popup_shortcode_attributes: jQuery(this).data('shortcode-attributes')
        };
        jQuery.post(ajaxurl, data, function(html) {
            pn_hide_static_info_popup();

            var popup_params = {
                content: html,
                title: jQuery(_this).data('popup-title'),
                overlay: true,
                width: jQuery(_this).data('popup-width'),
                max_height: jQuery(_this).data('popup-max-height'),
                open: function() {
                    //***
                },
                buttons: {
                    0: {
                        name: 'Close',
                        action: 'close'
                    }
                }
            };
            pn_inpost_stop_click = false;
            pn_advanced_wp_popup2.popup(popup_params);
        });


        return false;

    });
});

function pn_show_static_info_popup(text) {
    jQuery(".inpost_gallery_info_popup").html(text);
    jQuery(".inpost_gallery_info_popup").fadeTo(400, 0.9);
}

function pn_hide_static_info_popup() {
    window.setTimeout(function() {
        jQuery(".inpost_gallery_info_popup").fadeOut(200);
    }, 200);
}

