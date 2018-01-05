/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
var d4plib_widgets;

;(function($, window, document, undefined) {
    d4plib_widgets = {
        init: function() {
            $(document).on("click", ".d4p-check-uncheck a", function(e){
                e.preventDefault();

                var checkall = $(this).attr("href").substr(1) === "checkall";

                $(this).parent().parent().find("input[type=checkbox]").prop("checked", checkall);
            });

            $(document).on("click", ".d4plib-widget-tab", function(e){
                e.preventDefault();

                var tabs = $(this).parent(),
                    content = tabs.next(),
                    tab = $(this).attr("href").substr(1),
                    tab_name = typeof $(this).data("tabname") !== 'undefined' ? $(this).data("tabname") : tab;

                $(".d4plib-widget-active-tab", tabs).val(tab_name);
                $(".d4plib-widget-tab", tabs).removeClass("d4plib-tab-active").attr("aria-selected", "false");
                $(".d4plib-tabname-" + tab, tabs).addClass("d4plib-tab-active").attr("aria-selected", "true");

                $(".d4plib-tab-content", content).removeClass("d4plib-content-active").attr("aria-hidden", "true");
                $(".d4plib-tabname-" + tab, content).addClass("d4plib-content-active").attr("aria-hidden", "false");
            });

            $(document).on("keydown", ".d4plib-widget-tab[role='tab']", function(e){
                if (e.which === 13) {
                    $(this).click();
                } else if (e.which === 39) {
                    $(this).next().focus().click();
                } else if (e.which === 37) {
                    $(this).prev().focus().click();
                }
            });

            $(document).on("change", ".d4plib-widget-save", function(e){
                $(this).closest("form").find(".widget-control-actions input.button").click();
            });

            $(document).on("change", ".d4plib-div-switch", function(){
                var method = $(this).val(), 
                    parent = $(this).closest(".widget-content");

                $(".d4p-div-block", parent).hide();
                $(".d4p-div-block-" + method, parent).show();
            });

            $(document).on("change", ".d4plib-block-switch", function(e){
                var block = $(this).data("block"),
                    selected = $(this).val(),
                    parent = $(this).closest("table");

                $(".cellblock-" + block, parent).hide();
                $(".cellblockname-" + selected, parent).show();
            });

            $(document).ajaxStop(function() {
                d4plib_widgets.settings();
            });
        },
        settings: function() {
            $(".d4p-color-picker:not(.wp-color-picker)").on("focus", function(){
                $(this).wpColorPicker();
            });
        }
    };

    d4plib_widgets.init();
    d4plib_widgets.settings();
})(jQuery, window, document);
