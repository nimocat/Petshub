cgJsClass.editUpload.tinymce = {
    setTinymceNames:function(item){

            cg_htmlFieldTemplate_ifr_id = item.find('iframe').attr('id');
            cg_htmlFieldTemplate_ifr_html = item.find('iframe').contents().find('head').html();
            cg_htmlFieldTemplate_ifr_html_lang_attr = item.find('iframe').contents().find('html').attr('lang');
            cg_htmlFieldTemplate_ifr_body = item.find('iframe').contents().find('body').html();
            cg_attributes = item.find('iframe').contents().find('body').prop("attributes");

    },
    copyPasteTinymceIframeContent:function(){
        console.log(cg_htmlFieldTemplate_ifr_id);
        //jQuery(document).on("load","#"+cg_htmlFieldTemplate_ifr_id+"", function () {
        if(document.readyState === "complete"){

            console.log(cg_htmlFieldTemplate_ifr_id);
            console.log(cg_htmlFieldTemplate_ifr_html);
            console.log(cg_htmlFieldTemplate_ifr_body);
            console.log(cg_attributes);

            function cg_getIframeIndex(iframe_id)
            {

                var iframes = document.getElementsByTagName('iframe');
                for(i=0; i<iframes.length;i++){
                    if(iframes[i].id == iframe_id){
                        return i ;
                    }
                }
                return null;
            }
            // call function pass id -
            var iframe_index = cg_getIframeIndex(cg_htmlFieldTemplate_ifr_id);

            console.log(iframe_index);

            jQuery('html', window.frames[iframe_index].document).attr('lang',cg_htmlFieldTemplate_ifr_html_lang_attr);
            jQuery('head', window.frames[iframe_index].document).append(cg_htmlFieldTemplate_ifr_html);
            jQuery('body', window.frames[iframe_index].document).append(cg_htmlFieldTemplate_ifr_body);

            // loop through <select> attributes and apply them on <div>
            jQuery.each(cg_attributes, function() {
                jQuery('body', window.frames[iframe_index].document).attr(this.name, this.value);
            });

/*            console.log(tinyMCE);
            tinyMCE.init({
                setup: function (editor) {
                    editor.on('init', function () {
                        editor.focus();
                        editor.selection.select(editor.getBody(), true);
                        editor.selection.collapse(false);
                    });
                }
            });*/



        }







    }
};