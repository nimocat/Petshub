/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global gdrts_rating_data*/
var gdrts_rating_core, 
    gdrts_rating_help, 
    gdrts_rating_dynamic;

;(function($, window, document, undefined) {
    gdrts_rating_help = {
        remote: {
            url: function() {
                return gdrts_rating_data.url + "?action=" + gdrts_rating_data.handler;
            },
            call: function(args, callback, callerror) {
                $.ajax({
                    url: this.url(),
                    type: "post",
                    dataType: "json",
                    data:  {
                        req: JSON.stringify(args)
                    },
                    success: callback,
                    error: callerror
                });
            },
            error: function(jqXhr, textStatus, errorThrown) {
                var message = "Uncaught Error: " + errorThrown;

                if (jqXhr.status === 0) {
                    message = "No internet connection, please verify network settings.";
                } else if (jqXhr.status === 404) {
                    message = "Error 404: Requested page not found.";
                } else if (jqXhr.status === 500) {
                    message = "Error 500: Internal Server Error.";
                } else if (textStatus === "timeout") {
                    message = "Request timed out.";
                } else if (textStatus === "abort") {
                    message = "Request aborted.";
                }

                message = 'GD Rating System - AJAX Error: ' + message;

                if (gdrts_rating_data.ajax_error === "alert") {
                    alert(message);
                } else if (gdrts_rating_data.ajax_error === "console") {
                    if (window.console) {
                        console.log(message);
                    }
                }
            }
        },
        char: function(char) {
            if (char.substring(0, 3) === "&#x") {
                char = char.replace(";", "");
                char = parseInt(char.substring(3), 16);
                char = String.fromCharCode(char);
            } else if (char.substring(0, 2) === "&#") {
                char = char.replace(";", "");
                char = parseInt(char.substring(2), 10);
                char = String.fromCharCode(char);
            }

            return char;
        }
    };

    gdrts_rating_dynamic = {
        init: function() {
            var args = {
                todo: "dynamic",
                items: []
            };

            $(".gdrts-dynamic-block").each(function(){
                args.items.push(gdrts_rating_dynamic.process(this));
            });

            gdrts_rating_help.remote.call(args, gdrts_rating_dynamic.load, gdrts_rating_help.remote.error);
        },
        process: function(el) {
            var data = JSON.parse($(".gdrts-rating-data", $(el)).html());

            data.did = gdrts_rating_core.storage.did;

            $(el).attr("id", "gdrts-dynamic-id-" + gdrts_rating_core.storage.did)
                 .addClass("gdrts-dynamic-loading");

            gdrts_rating_core.storage.did++;

            return data;
        },
        load: function(json) {
            if (json.status && json.items && json.status === "ok") {
                $.each(json.items, function(idx, item){
                    gdrts_rating_dynamic.item(item);
                });
            }
        },
        item: function(item) {
            var obj = $(item.render).hide();

            $("#gdrts-dynamic-id-" + item.did).fadeOut(150, function(){
                $(this).replaceWith(obj);

                obj.fadeIn(300, function(){
                    gdrts_rating_core.common.process(this);

                    if ($(this).hasClass("gdrts-method-stars-rating")) {
                        gdrts_rating_core.stars_rating.single.process($(".gdrts-stars-rating", this));
                    }
                });
            });
        }
    };

    gdrts_rating_core = {
        storage: {
            uid: 1,
            did: 1,
            stars: []
        },

        _b: function(el) {
            var uid = $(el).data("uid");
            return $("#gdrts-unique-id-" + uid);
        },
        _d: function(el) {
            return this._b(el).data("rating");
        },

        init: function() {
            if ($(".gdrts-dynamic-block").length > 0) {
                gdrts_rating_dynamic.init();
            }

            $(".gdrts-rating-block, .gdrts-rating-list").each(function(){
                gdrts_rating_core.common.process(this);
            });

            gdrts_rating_core.common.methods();
        },
        live: function() {
            $(document).on("click", ".gdrts-toggle-distribution", function(e){
                e.preventDefault();

                var open = $(this).hasClass("gdrts-toggle-open");

                if (open) {
                    $(this).removeClass("gdrts-toggle-open");
                    $(this).html($(this).data("show"));

                    $(".gdrts-rating-distribution", $(this).closest(".gdrts-rating-block")).slideUp();
                } else {
                    $(this).addClass("gdrts-toggle-open");
                    $(this).html($(this).data("hide"));

                    $(".gdrts-rating-distribution", $(this).closest(".gdrts-rating-block")).slideDown();
                }
            });
        },
        style: {
            star: function(key, obj) {
                if ($.inArray(key, gdrts_rating_core.storage.stars) === -1) {
                    var base = ".gdrts-with-fonticon.gdrts-fonticon-" + obj.name + ".gdrts-" + obj.name + "-" + obj.font + ".gdrts-stars-length-" + obj.length,
                        rule = base + " .gdrts-stars-empty::before, " + 
                               base + " .gdrts-stars-active::before, " + 
                               base + " .gdrts-stars-current::before { " +
                               "content: \"" + obj.content + "\"; }",
                        desc = "/* stars: " + obj.name + " - " + obj.font + " - " + obj.length + " */",
                        id = "gdrts-style-stars-" + obj.name + "-" + obj.font+ "-" + obj.length;

                    $("<style type=\"text/css\" id=\"" + id + "\">\r\n" + desc + "\r\n" + rule + "\r\n\r\n</style>").appendTo("head");

                    gdrts_rating_core.storage.stars.push(key);
                }
            }
        },
        common: {
            process: function(el) {
                var data = JSON.parse($(".gdrts-rating-data", $(el)).html());

                data.uid = gdrts_rating_core.storage.uid;

                $(el).attr("id", "gdrts-unique-id-" + gdrts_rating_core.storage.uid)
                     .data("uid", gdrts_rating_core.storage.uid)
                     .data("rating", data);

                $(".gdrts-rating-element", el).data("uid", gdrts_rating_core.storage.uid);

                gdrts_rating_core.storage.uid++;
            },
            methods: function() {
                gdrts_rating_core.stars_rating.single.init();
                gdrts_rating_core.stars_rating.list.init();
            },
            error: function(json) {
                $("#gdrts-unique-id-" + json.uid).removeClass("gdrts-vote-saving")
                                                 .append('<div class="gdrts-error-message">' + 
                                                         json.message + '</div>');
            }
        },

        stars_rating: {
            single: {
                init: function() {
                    $(".gdrts-rating-block .gdrts-stars-rating").each(function(){
                        gdrts_rating_core.stars_rating.single.process(this);
                    });
                },
                call: function(el, rating) {
                    var data = gdrts_rating_core._d(el),
                        args = {
                            todo: "vote",
                            method: "stars-rating",
                            item: data.item.item_id,
                            nonce: data.item.nonce,
                            render: data.render,
                            uid: data.uid,
                            meta: {
                                value: rating,
                                max: data.stars.max
                            }
                        };

                    gdrts_rating_help.remote.call(args, gdrts_rating_core.stars_rating.single.voted, gdrts_rating_help.remote.error);
                },
                voted: function(json) {
                    if (json.status === "error") {
                        gdrts_rating_core.common.error(json);
                    } else {
                        var obj = $(json.render).hide();

                        $("#gdrts-unique-id-" + json.uid).fadeOut(150, function(){
                            $(this).replaceWith(obj);

                            obj.fadeIn(300, function(){
                                gdrts_rating_core.common.process(this);
                                gdrts_rating_core.stars_rating.single.process($(".gdrts-stars-rating", this));

                                $(this).attr("role", "alert");
                            });
                        });
                    }
                },
                process: function(el) {
                    var data = gdrts_rating_core._d(el).stars,
                        labels = gdrts_rating_core._d(el).labels;

                    if ($(el).hasClass("gdrts-with-fonticon")) {
                        var key = data.type + "-" + data.name + "-" + data.max,
                            obj = {font: data.name, 
                                   name: data.type,
                                   length: data.max, 
                                   content: Array(data.max + 1).join(gdrts_rating_help.char(data.char))};

                        gdrts_rating_core.style.star(key, obj);
                    }

                    if ($(el).hasClass("gdrts-state-active")) {
                        gdrts_rating_core.stars_rating.single.activity(el, data, labels);
                    }

                    if (data.responsive) {
                        $(window).bind("load resize orientationchange", {el: el, data: data}, gdrts_rating_core.responsive.stars);

                        gdrts_rating_core.responsive._s({el: el, data: data});
                    }
                },
                activity: function(el, data, labels) {
                    $(".gdrts-stars-empty", el).mouseleave(function(e){
                        if ($(this).hasClass("gdrts-vote-saving")) return;

                        var reset = $(this).parent().find("input").val(),
                            width = $(this).width(),
                            star = width / data.max,
                            current = star * reset;

                        $(el).data("selected", reset).attr("title", "");
                        $(".gdrts-stars-active", this).width(current);
                    });

                    $(".gdrts-stars-empty", el).mousemove(function(e){
                        if ($(this).hasClass("gdrts-vote-saving")) return;

                        var offset = $(this).offset(),
                            width = $(this).width(),
                            star = width / data.max,
                            res = data.resolution,
                            step = res * (star / 100),
                            x = e.pageX - offset.left,
                            parts = Math.ceil(x / step),
                            current = parseFloat((parts * (res / 100)).toFixed(2)),
                            lid = Math.ceil(current * 1),
                            label = labels[lid - 1],
                            active = parts * step;

                        $(el).data("selected", current).attr("title", current + ": " + label);
                        $(".gdrts-stars-active", this).width(active);
                    });

                    $(".gdrts-sr-button", el).click(function(e) {
                        e.preventDefault();

                        var select = $(this).parent().find(".gdrts-sr-rating");

                        gdrts_rating_core.stars_rating.single.do_rating($(select).val(), select.parent(), el);
                    });

                    $(".gdrts-stars-empty", el).click(function(e){
                        e.preventDefault();

                        gdrts_rating_core.stars_rating.single.do_rating($(el).data("selected"), this, el);
                    });
                },
                do_rating: function(rating, t, el) {
                    if ($(t).hasClass("gdrts-vote-saving")) return;

                    $(t).parent().find("input").val(rating);

                    if ($(t).parent().hasClass("gdrts-passive-rating")) return;

                    $(t).addClass("gdrts-vote-saving");

                    gdrts_rating_core._b(t).addClass("gdrts-vote-saving");

                    gdrts_rating_core.stars_rating.single.call(el, rating);
                }
            },
            list: {
                init: function() {
                    $(".gdrts-rating-list .gdrts-stars-rating").each(function(){
                        gdrts_rating_core.stars_rating.list.process(this);
                    });
                },
                process: function(el) {
                    var data = gdrts_rating_core._d(el).stars;

                    if ($(el).hasClass("gdrts-with-fonticon")) {
                        var key = data.type + "-" + data.name + "-" + data.max,
                            obj = {font: data.name, 
                                   name: data.type,
                                   length: data.max, 
                                   content: Array(data.max + 1).join(gdrts_rating_help.char(data.char))};

                        gdrts_rating_core.style.star(key, obj);
                    }

                    if (data.responsive) {
                        $(window).bind("load resize orientationchange", {el: el, data: data}, gdrts_rating_core.responsive.stars);

                        gdrts_rating_core.responsive._s({el: el, data: data});
                    }
                }
            }
        },

        responsive: {
            _s: function(input) {
                var el = input.el,
                    data = input.data,
                    available = $(el).parent().width(),
                    new_size = Math.floor(available / data.max);

                new_size = new_size > data.size ? data.size : new_size;

                if (data.type === "image") {
                    $(".gdrts-stars-empty, .gdrts-stars-active, .gdrts-stars-current", el).css("background-size", new_size + "px");
                    $(el).css("height", new_size + "px").css("width", data.max * new_size + "px");
                } else {
                    $(".gdrts-stars-empty", el).css("font-size", new_size + "px").css("line-height", new_size + "px");
                    $(el).css("line-height", new_size + "px").css("height", new_size + "px");
                }
            },
            stars: function(e) {
                gdrts_rating_core.responsive._s(e.data);
            }
        }
    };

    window.wp = window.wp || {};
    window.wp.gdrts = window.wp.gdrts || {};

    window.wp.gdrts.help = gdrts_rating_help;
    window.wp.gdrts.dynamic = gdrts_rating_dynamic;
    window.wp.gdrts.core = gdrts_rating_core;

    window.wp.gdrts.core.init();
    window.wp.gdrts.core.live();
})(jQuery, window, document);
