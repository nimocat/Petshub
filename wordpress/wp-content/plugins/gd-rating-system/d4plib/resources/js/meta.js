/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
var d4plib_metabox;

;(function($, window, document, undefined) {
    d4plib_metabox = {
        init: function() {
            $(".d4plib-metabox-wrapper .wp-tab-bar a").click(function(e){
                e.preventDefault();

                var tab = $(this).attr("href").substr(1);

                $(this).closest("ul").find("li").removeClass("wp-tab-active");
                $(this).parent().addClass("wp-tab-active");

                $(this).closest(".d4plib-metabox-wrapper").find(".wp-tab-panel")
                                                          .removeClass("tabs-panel-active")
                                                          .addClass("tabs-panel-inactive");

                $(this).closest(".d4plib-metabox-wrapper").find("#" + tab)
                                                          .removeClass("tabs-panel-inactive")
                                                          .addClass("tabs-panel-active");
            });

            $(".d4plib-metabox-check-uncheck a").click(function(e){
                e.preventDefault();

                var checkall = $(this).attr("href").substr(1) === "checkall";

                $(this).parent().parent().find("input[type=checkbox]").prop("checked", checkall);
            });
        }
    };

    d4plib_metabox.init();
})(jQuery, window, document);
