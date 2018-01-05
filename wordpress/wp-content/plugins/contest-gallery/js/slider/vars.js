
    cgJsClass.slider.vars = {};
cgJsClass.slider.vars = {
ieBrowser: false,
checkIfIeBrowser: function () {

    if (navigator.appName == 'Microsoft Internet Explorer' ||  !!(navigator.userAgent.match(/Trident/) || navigator.userAgent.match(/rv:11/)) || (typeof jQuery.browser !== "undefined" && jQuery.browser.msie == 1))
    {
        this.ieBrowser = true;
    }

},
windowWidth: '',
windowHeight: '',
picsOnSite: '',
checkPicsOnSite: function(){
    this.picsOnSite = jQuery('#cg-carrousel-slider-content .cg-carrousel-img').length;
},
cg_hide_carrousel: false,
cg_activate_gallery_slider: jQuery("#cg_activate_gallery_slider").val(),
cg_OnlyGalleryView: jQuery("#cg_OnlyGalleryView").val(),
cg_hide_until_vote: jQuery("#cg_hide_until_vote").val(),
cg_hide_info:jQuery("#cg_hide_info").val(),
cg_slider_arrow_fade_in_user_input_src:jQuery('#cg_slider_arrow_fade_in_user_input img').attr('src'),
cg_slider_arrow_fade_out_user_input_src:jQuery('#cg_slider_arrow_fade_out_user_input img').attr('src'),
cg_vote_in_gallery:jQuery("#cg_vote_in_gallery").val(),
cg_hide_info_class:"cg_hide_info_div_no",
cg_hide_info_class:"cg_hide_info_div_yes",
cg_vote_in_gallery:jQuery("#cg_vote_in_gallery").val(),
cg_comment_in_gallery:jQuery("#cg_comment_in_gallery").val(),
cg_shown_images:jQuery(".cg_show").length,
cg_allow_comments:jQuery("#cg_allow_comments").val(),
cg_allow_rating:jQuery("#cg_allow_rating").val(),
cg_slider_mouseup:jQuery("#cg_slider_mouseup").val(),
cg_Use_as_URL:jQuery("#cg_Use_as_URL").val(),
cg_ForwardToURL:jQuery("#cg_ForwardToURL").val(),
cg_ForwardFrom:jQuery("#cg_ForwardFrom").val(),
cg_ForwardType:jQuery("#cg_ForwardType").val(),
cg_FbLike:jQuery("#cg_FbLike").val(),
cg_FbLikeGallery:jQuery("#cg_FbLikeGallery").val(),
cg_FbLikeGalleryVote:jQuery("#cg_FbLikeGalleryVote").val(),
starOnUrl:jQuery("#cg_star_on_slider").val(),
starOffUrl:jQuery("#cg_star_off_slider").val(),
starOnUrlGallery:jQuery("#cg_star_on_gallery").val(),
starOffUrlGallery:jQuery("#cg_star_off_gallery").val(),
cg_pngCommentsIconImg:jQuery('#cg_pngCommentsIconImg').val(),
cg_pngUrlIconImg:jQuery('#cg_pngUrlIconImg').val(),
cg_pngUrlIconImg:jQuery('#cg_pngUrlIconImg').val(),
cg_ShowAlwaysInfoSlider: jQuery("#cg_ShowAlwaysInfoSlider").val(),
cg_OriginalSourceLinkInSlider: jQuery("#cg_OriginalSourceLinkInSlider").val(),
cg_PreviewInSlider: jQuery("#cg_PreviewInSlider").val(),
userBrowserLang: '',
widthFbDiv: '',
getUserBrowserLang:function(){

    this.userBrowserLang = navigator.language || navigator.userLanguage;

    if(this.userBrowserLang.indexOf("en")==0){this.widthFbDiv = 155;}
    else if(this.userBrowserLang.indexOf("de")==0){this.widthFbDiv = 190;}
    else if(this.userBrowserLang.indexOf("fr")==0){this.widthFbDiv = 182;}
    else if(this.userBrowserLang.indexOf("es")==0){this.widthFbDiv = 207;}
    else if(this.userBrowserLang.indexOf("pt")==0){this.widthFbDiv = 179;}
    else if(this.userBrowserLang.indexOf("nl")==0){this.widthFbDiv = 195;}
    else if(this.userBrowserLang.indexOf("ru")==0){this.widthFbDiv = 217;}
    else if(this.userBrowserLang.indexOf("zh")==0){this.widthFbDiv = 136;}
    else if(this.userBrowserLang.indexOf("ja")==0){this.widthFbDiv = 177;}
    else{this.widthFbDiv = 152;}

/*    jQuery( '[id*=cg_slider_arrow]' ).hover(function() {
        jQuery(this).css("cursor","pointer");
    });

    if(cg_hide_until_vote==1 && cg_vote_in_gallery==1){

        jQuery( '.rating_cg' ).hover(function() {
            jQuery(this).css("cursor","pointer");
        });
    }

    if(cg_comment_in_gallery==1 && cg_vote_in_gallery==1){

        jQuery( '[id*=rating_cgc]' ).hover(function() {
            jQuery(this).css("cursor","pointer");
        });

    }*/

},
// device detection, damit elemente beim klicken auf imgContainer nicht verschwinden und rating und comment auf mobile funktionieren
isMobile:false, //initiate as false
checkMobile:function () {

// device detection, damit elemente beim klicken auf imgContainer nicht verschwinden und rating und comment auf mobile funktionieren
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) this.isMobile = true;

    // Pfeile verstecken bei der Mobile Version
//alert('mobile: '+this.isMobile);
    if(this.isMobile==true){
        jQuery( "#cg_slider_arrow_left" ).hide();
        jQuery( "#cg_slider_arrow_right" ).hide();
    }

},
    noViewport:false,
    viewportContent:'',
    hash:'',
    doNotResize:0,
    fullSizeResized: false,
    commentFrameOpened: false,
    actualWindowRatio: 1
}

