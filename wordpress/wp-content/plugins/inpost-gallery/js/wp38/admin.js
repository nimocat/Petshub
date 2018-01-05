var PN_APP_SHORTCODES = function() {
    var self = {
        html_buffer: null,
        init: function() {
            jQuery.fn.life = function(types, data, fn) {
                jQuery(this.context).on(types, this.selector, data, fn);
                return this;
            };
            //***
            if (!jQuery("#pn_shortcodes_html_buffer").size()) {
                jQuery('body').append('<div id="pn_shortcodes_html_buffer" style="display: none;"></div>');
            }
            self.html_buffer = jQuery("#pn_shortcodes_html_buffer");
            //***
            jQuery(".js_shortcode_checkbox_self_update").life('click', function() {
                if (jQuery(this).is(':checked')) {
                    jQuery(this).val(1);
                } else {
                    jQuery(this).val(0);
                }
                self.changer(shortcode_name);
            });
            //***
            jQuery(".js_shortcode_radio_self_update").life('click', function() {
                jQuery("input[data-shortcode-field=" + jQuery(this).attr('name') + "]").val(jQuery(this).val()).trigger('change');
            });
            //***
            jQuery('.pn_button_upload2').life('click', function()
            {
                var input_object = jQuery(this).prev('input, textarea');
                window.send_to_editor = function(html)
                {
                    self.insert_html_in_buffer(html);
                    var imgurl = self.html_buffer.find('a').eq(0).attr('href');
                    self.insert_html_in_buffer("");
                    jQuery(input_object).val(imgurl);
                    jQuery(input_object).trigger('change');
                    tb_remove();
                };
                tb_show('', 'media-upload.php?post_id=0&type=image&TB_iframe=true');

                return false;
            });


            jQuery(".inpost_gallery_checkbox_selfupdated").click(function() {
                if (jQuery(this).is(':checked')) {
                    jQuery(this).next('input[type=hidden]').val(1);
                } else {
                    jQuery(this).next('input[type=hidden]').val(0);
                }

                return true;
            });

            jQuery('.js_pn_show_help').life('click', function() {
                var dialog = jQuery("#" + jQuery(this).data('id'));
                dialog.dialog({
                    'dialogClass': 'wp-dialog',
                    'modal': true,
                    'width': 600,
                    'autoOpen': false,
                    'closeOnEscape': true,
                    'title': 'Help',
                    'buttons': {
                        "Close": function() {
                            jQuery(this).dialog('close');
                        }
                    }
                });
                dialog.dialog('open');
            });

        },
        insert_html_in_buffer: function(html) {
            jQuery(self.html_buffer).html(html);
        },
        get_html_from_buffer: function() {
            return jQuery(self.html_buffer).html();
        },
        changer: function(shortcode) {
            var items = jQuery("#pn_shortcode_template .js_shortcode_template_changer");
            var begin_string = "[" + shortcode;
            var end_string = "[/" + shortcode + "]";
            var content = "";
            var save_as_one = {};
            jQuery.each(items, function(key, value) {
                var shortcode_field = jQuery(value).data('shortcode-field');

                if (shortcode_field !== undefined) {
                    if (shortcode_field == 'content') {
                        content = jQuery(value).val();
                    } else {
                        //save_as_one for dynamic lists
                        var vals = jQuery(value).val();
                        vals = vals.replace(/\"/gi, "\'");
                        if (!jQuery(value).hasClass('save_as_one')) {
                            begin_string = begin_string + " " + shortcode_field + '="' + vals + '"';
                        } else {
                            //taking to associative array
                            if (save_as_one[shortcode_field] === undefined) {
                                save_as_one[shortcode_field] = [];
                            }
                            save_as_one[shortcode_field].push(vals);
                        }
                    }
                }


            });
            //*** scan for save_as_one
            if (save_as_one.length !== 0) {
                jQuery.each(save_as_one, function(key, value) {
                    if (value.length !== 0) {
                        var tmp = ' ' + key + '="';
                        for (var i = 0; i < value.length; i++) {
                            if (i > 0) {
                                tmp += '^';
                            }
                            tmp += value[i];
                        }
                        tmp += '"';
                        begin_string += tmp;
                    }
                });
            }
            //***
            var shortcode_text = begin_string + ']' + content + end_string;
            self.insert_html_in_buffer(shortcode_text);
            return shortcode_text;
        },
        show_static_info_popup: function(text) {
            if (!jQuery(".pn_shortcode_info_popup").length) {
                jQuery('body').prepend('<div class="pn_shortcode_info_popup"></div>');
            }
            jQuery(".pn_shortcode_info_popup").text(text);
            jQuery(".pn_shortcode_info_popup").fadeTo(400, 0.9);
        },
        hide_static_info_popup: function() {
            window.setTimeout(function() {
                jQuery(".pn_shortcode_info_popup").fadeOut(400);
            }, 777);
        },
        get_time_miliseconds: function() {
            var d = new Date();
            return d.getTime();
        }
    };
    return self;
};
//*****



var INPOSTGALLERY_ADMIN_SLIDES = function() {

    var self = {
        html_buffer: "",
        init: function() {
            jQuery('body').append('<div id="inpost_gallery_html_buffer" style="display: none;"></div>');
            jQuery('body').append('<div id="inpost_gallery_info_popup" style="display: none;"></div>');

            self.html_buffer = jQuery("#inpost_gallery_html_buffer");


            jQuery("#inpost_gallery_slide_group").sortable({
                stop: function() {
                    self.recount_slides();
                }
            });
            //*****
            jQuery('.js_inpost_gallery_add_slide').life('click', function(event)
            {
                window.send_to_editor = function(html)
                {
                    self.insert_html_in_buffer(html);
                    var images = jQuery(self.html_buffer).find('a');
                    var by_a = true;
                    if (!images.length) {
                        var images = jQuery(self.html_buffer).find('img');//russ
                        by_a = false;
                    }
                    //***
                    var img_urls = [];
                    jQuery.each(images, function(index, value) {
                        if (by_a) {
                            img_urls[index] = jQuery(value).attr('href');
                        } else {
                            img_urls[index] = jQuery(value).attr('src');
                        }

                    });

                    self.add_meta_slide_items(img_urls, 0);
                    self.insert_html_in_buffer("");
                };
                wp.media.editor.open(null);

                return false;
            });

            jQuery(".js_inpost_gallery_insert_shortcode").click(function() {
                if (tinyMCE !== null) {
                    tinyMCE.get(tinyMCE.activeEditor.editorId).controlManager.get(tinyMCE.activeEditor.editorId + "_" + 'pn_tinymce_button').settings.onclick();
                }
                return false;
            });



            jQuery(".js_inpost_gallery_delete_slide").life('click', function() {
                var self_button = this;
                jQuery(self_button).parents('li').eq(0).hide(333, function() {
                    jQuery(self_button).parents('li').eq(0).remove();
                });

                return false;
            });

            jQuery(".js_inpost_gallery_update_slide_title").life('click', function() {
                var slide_id = jQuery(this).attr('slide-id');
                var title = jQuery("[name='inpost_gallery_data[" + slide_id + "][title]']").val();
                title = prompt(inpost_gallery_lang_enter_title, title);
                if (title) {
                    jQuery("[name='inpost_gallery_data[" + slide_id + "][title]']").val(title);
                }


                return false;
            });


            self.recount_slides();

            //***

            jQuery("#inpost_gallery_settings_form").submit(function() {
                var data = {
                    action: "inpost_gallery_save_settings",
                    values: jQuery(this).serialize()
                };
                jQuery.post(ajaxurl, data, function(response) {
                    self.show_info_popup(inpost_gallery_lang_settings_saved);
                });

                return false;
            });


        },
        show_info_popup: function(text) {
            jQuery("#inpost_gallery_info_popup").text(text);
            jQuery("#inpost_gallery_info_popup").fadeTo(400, 0.9);
            window.setTimeout(function() {
                jQuery("#inpost_gallery_info_popup").fadeOut(400);
            }, 1000);
        },
        add_meta_slide_items: function(img_urls, index) {
            self.show_info_popup(inpost_gallery_lang_loading + ' ...');
            var data = {
                action: "add_inpost_gallery_slide_item",
                imgurl: img_urls[index]
            };
            jQuery.post(ajaxurl, data, function(response) {
                jQuery("#inpost_gallery_slide_group").append(response);
                self.recount_slides();
                if (index < (img_urls.length - 1)) {
                    self.add_meta_slide_items(img_urls, index + 1);
                }
            });
        },
        insert_html_in_buffer: function(html) {
            jQuery(self.html_buffer).html(html);
        },
        recount_slides: function() {
            var images = jQuery(".inpost_gallery_slide_image");
            jQuery.each(images, function(key, image) {
                var num = key + 1;
                jQuery(image).parent().find(".js_inpost_gallery_slide_counter").html(num);
            });
        }

    };

    return self;
};

var pn_ext_shortcodes = null;
var inpost_gallery = new INPOSTGALLERY_ADMIN_SLIDES();
jQuery(document).ready(function() {
    pn_ext_shortcodes = new PN_APP_SHORTCODES();
    pn_ext_shortcodes.init();
    inpost_gallery.init();
});


