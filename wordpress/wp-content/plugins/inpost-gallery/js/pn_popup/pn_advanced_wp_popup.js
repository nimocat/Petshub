var PN_ADVANCED_WP_POPUP2 = function() {
    var self = {
        zindex: 1001,
        popup_li: null,
        params: null,
        init: function() {
            jQuery('body').prepend('<div id="pn_advanced_wp_popup2"></div>');
            jQuery('body').prepend('<div id="alert_advanced_wp_popup"></div>');
            jQuery('body').prepend('<div id="advanced_wp_popup_overlay"></div>');
            //***
            jQuery('.pn_ig_wp_popup_close2').life('click', function() {
                self.close(jQuery(this));
            });
            jQuery('.pn_ig_wp_popup_close2').life('click', function() {
                self.close(jQuery(this));
            });
        },
        popup: function(params) {
            params = self.validate_params(params);
            jQuery('#pn_advanced_wp_popup2').append('<div class="pn_advanced_wp_popup_li" style="width:' + params.width + 'px;margin-left:-' + params.width / 2 + 'px;max-height:' + params.max_height + 'px;">');
            this.popup_li = jQuery('#pn_advanced_wp_popup2 div:last-child');
            jQuery(this.popup_li).css('z-index', self.zindex++);
            jQuery(this.popup_li).append('<div class="advanced_wp_popup_container" style="width:' + params.width + ';">');
            jQuery(this.popup_li).append('<div class="advanced_wp_popup_buttons">');
            jQuery(this.popup_li).find('.advanced_wp_popup_buttons').append('<div class="advanced_wp_popup_adv">');
            var popup = jQuery(this.popup_li).find('.advanced_wp_popup_container');
            /***/
            jQuery(this.popup_li).find('.advanced_wp_popup_adv').html('<a title="www.pluginus.net on CodeCanyon" href="http://codecanyon.net/user/realmag777/portfolio?WT.ac=item_portfolio&amp;WT.seg_1=item_portfolio&amp;WT.z_author=realmag777&amp;ref=realmag777" target="_blank"><img src="' + pn_ext_shortcodes_app_link + 'images/buy_on_codecanyon.jpg"></a>');

            if (params.title.length > 0) {
                popup.append('<div class="pn_titlebar" />');
                popup.children('.pn_titlebar').append('<h6>').children('h6').html(params.title);
                popup.children('.pn_titlebar').append('<a href="javascript:void(0);" class="pn_ig_wp_popup_close2"></a>');
            }
            /***/
            jQuery(popup).append('<div class="advanced_wp_popup_content">');
            jQuery(popup).find('.advanced_wp_popup_content').html(params.content);
            try {
                jQuery(this.popup_li).draggable({
                    handle: '.pn_titlebar'
                });
            } catch (e) {

            }

            jQuery(this.popup_li).fadeTo(200, 1);
            self.overlay(params.overlay, self.zindex - 1);
            //***
            self.open(params, this.popup_li);
        },
        overlay: function(mode, zindex) {
            jQuery('#advanced_wp_popup_overlay').css('z-index', zindex);
            if (mode) {
                jQuery('#advanced_wp_popup_overlay').show();
            } else {
                jQuery('#advanced_wp_popup_overlay').hide();
            }
        },
        open: function(params, popup_li) {
            self.params = params;
            jQuery.each(params.buttons, function(index, button) {
                if (button.action == 'close') {
                    jQuery(popup_li).find('.advanced_wp_popup_buttons').append('<a href="javascript:void(0);" class="pn_button pn_ig_wp_popup_close2">' + button.name + '</a>');
                    return;
                }

                //*****
                if (button.display == 'undefined') {
                    button.display = 'inline-block';
                }

                if (button.close) {
                    jQuery(popup_li).find('.advanced_wp_popup_buttons').append('<a href="javascript:pn_advanced_wp_popup2.do_action(' + index + ');void(0);" data-name="' + button.name + '" class="pn_button pn_ig_wp_popup_close2" style="display:' + button.display + ';">' + button.name + '</a>');
                } else {
                    jQuery(popup_li).find('.advanced_wp_popup_buttons').append('<a href="javascript:pn_advanced_wp_popup2.do_action(' + index + ');void(0);" data-name="' + button.name + '" class="button" style="display:' + button.display + ';">' + button.name + '</a>');
                }

            });
            //***
            if (params.open !== undefined) {
                params.open();
            }
        },
        set_title: function(title) {
            jQuery(this.popup_li).find('.advanced_wp_popup_container .pn_titlebar h6').html(title);
        },
        show_button: function(name) {
            jQuery('#pn_advanced_wp_popup2').find('.advanced_wp_popup_buttons').find("a[data-name*='" + name + "']").css('display', 'inline-block');
        },
        get_content: function() {
            return jQuery(this.popup_li).find('.advanced_wp_popup_content').html();
        },
        set_content: function(html) {
            return jQuery(this.popup_li).find('.advanced_wp_popup_content').html(html);
        },
        set_height: function(height, animate, animation_time, opacity) {
            if (animate) {
                jQuery(this.popup_li).find('.advanced_wp_popup_content').animate({
                    opacity: opacity,
                    height: "toggle"
                }, animation_time);
            } else {
                jQuery(this.popup_li).find('.advanced_wp_popup_content').css('height', height);
            }
        },
        close: function(_this) {
            var popup = jQuery(_this).parents('div.pn_advanced_wp_popup_li');
            window.setTimeout(function() {
                if (inpost_is_front) {
                    self.set_height(0, true, 999, 0);
                }
                jQuery(popup).fadeOut(555, function() {
                    jQuery(this).remove();
                    self.overlay(0);
                });
            }, 100);
        },
        do_action: function(index) {
            jQuery.each(this.params.buttons, function(i, button) {
                if (i == index) {
                    button.action(self);
                    if (button.close !== undefined) {
                        if (button.close == 1) {
                            //todo
                        }
                    }
                    return false;
                }
            });
        },
        validate_params: function(params) {
            if (params.title === undefined) {
                params.title = "";
            }

            if (params.overlay === undefined) {
                params.overlay = 0;
            }

            if (params.width === undefined || params.width === null) {
                params.width = 800;
            }

            if (params.max_height === undefined || params.max_height === null) {
                params.max_height = 600;
            }

            if (params.buttons === undefined) {
                params.buttons = {
                    0: {
                        name: 'Cancel',
                        action: 'close'
                    }
                };
            }

            return params;
        }
    };
    return self;
};
//*****

var pn_advanced_wp_popup2 = null;
jQuery(document).ready(function() {
    pn_advanced_wp_popup2 = new PN_ADVANCED_WP_POPUP2();
    pn_advanced_wp_popup2.init();
});

