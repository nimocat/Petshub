/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
/*global gdrts_data, ajaxurl*/
var gdrts_transfer_core;

;(function($, window, document, undefined) {
    gdrts_transfer_core = {
        status: {
            active: false,
            stop: false,
            total: 0,
            current: 0,
            pages: 0,
            step: 500,
            settings: {}
        },
        init: function() {
            gdrts_transfer_core.status.step = gdrts_data.step_transfer;

            $(".d4p-content-left .button-primary").click(function(e){
                e.preventDefault();

                if (gdrts_transfer_core.status.active) {
                    gdrts_transfer_core.transfer.stop();
                } else {
                    gdrts_transfer_core.transfer.settings();
                    gdrts_transfer_core.transfer.start();
                }
            });
        },
        transfer: {
            settings: function() {
                var s = {
                    plugin: $(".gdrts-tr-plugin").val(),
                    data: []
                };

                $(".gdrts-tr-check:checked").each(function(){
                    var key = $(this).val(),
                        box = {
                            rating: key
                        };

                    $(".gdrts-tr-checked-" + key).each(function(){
                        box[$(this).attr("name")] = $(this).val();
                    });

                    s.data.push(box);
                });

                gdrts_transfer_core.status.settings = s;
            },
            start: function() {
                gdrts_transfer_core.status.active = true;

                $(".d4p-panel-buttons .button-primary").val(gdrts_data.button_stop);
                $("#gdrts-remotecall-intro").slideUp();
                $("#gdrts-remotecall-process").slideDown();

                gdrts_transfer_core.transfer._call({
                    operation: "start",
                    settings: gdrts_transfer_core.status.settings
                }, gdrts_transfer_core.transfer.callback_start);
            },
            stop: function() {
                gdrts_transfer_core.status.stop = true;

                $(".d4p-panel-buttons .button-primary").attr("disabled", true);
            },
            callback_start: function(json) {
                gdrts_transfer_core.status.total = json.objects;
                gdrts_transfer_core.status.pages = Math.ceil(json.objects / gdrts_transfer_core.status.step);

                gdrts_transfer_core.transfer._write(json.message);

                gdrts_transfer_core.transfer.run();
            },
            callback_stop: function(json) {
                gdrts_transfer_core.status.active = false;
                gdrts_transfer_core.transfer._write(json.message);
            },
            callback_process: function(json) {
                if (gdrts_transfer_core.status.stop) {
                    gdrts_transfer_core.transfer._call({
                        operation: "break"
                    }, gdrts_transfer_core.transfer.callback_stop);
                } else {
                    gdrts_transfer_core.status.current++;

                    gdrts_transfer_core.transfer._write(json.message);

                    if (gdrts_transfer_core.status.current < gdrts_transfer_core.status.pages) {
                        gdrts_transfer_core.transfer.run();
                    } else {
                        gdrts_transfer_core.transfer.stop();
                        gdrts_transfer_core.transfer._call({
                            operation: "stop"
                        }, gdrts_transfer_core.transfer.callback_stop);
                    }
                }
            },
            run: function() {
                gdrts_transfer_core.transfer._call({
                    operation: "run",
                    total: gdrts_transfer_core.status.total,
                    current: gdrts_transfer_core.status.current,
                    step: gdrts_transfer_core.status.step,
                    settings: gdrts_transfer_core.status.settings
                }, gdrts_transfer_core.transfer.callback_process);
            },
            _write: function(message) {
                $("#gdrts-remotecall-progress pre").prepend(message + "\r\n");
            },
            _call: function(data, callback) {
                $.ajax({
                    url: ajaxurl + "?action=gdrts_transfer_process&_ajax_nonce=" + gdrts_data.nonce,
                    timeout: 0, type: "post", dataType: "json", data: data, success: callback
                });
            }
        }
    };

    gdrts_transfer_core.init();
})(jQuery, window, document);
