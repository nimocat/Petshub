/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
var d4plib_wizard;

;(function($, window, document, undefined) {
    d4plib_wizard = {
        init: function() {
            $(".gdbbx-wizard-connect-switch").change(function(){
                var connect = $(this).val() === "yes",
                    the_id = $(this).data("connect");

                if (connect) {
                    $("#" + the_id).slideDown("slow");
                } else {
                    $("#" + the_id).slideUp("fast");
                }
            });
        }
    };

    d4plib_wizard.init();
})(jQuery, window, document);
