/* jQuery Alphanumeric
 * 
 * https://github.com/johnantoni/jquery.alphanumeric
*/
(function($){$.fn.alphanumeric=function(p){var b=$(this),az="abcdefghijklmn�opqrstuvwxyz",options=$.extend({ichars:'!@#$%^&*()+=[]\\\';,/{}|":<>?~`.- _�����',nchars:'',allow:''},p),s=options.allow.split(''),i=0,ch,regex;for(i;i<s.length;i++){if(options.ichars.indexOf(s[i])!=-1){s[i]='\\'+s[i]}}if(options.nocaps){options.nchars+=az.toUpperCase()}if(options.allcaps){options.nchars+=az}options.allow=s.join('|');regex=new RegExp(options.allow,'gi');ch=(options.ichars+options.nchars).replace(regex,'');b.keypress(function(e){var a=String.fromCharCode(!e.charCode?e.which:e.charCode);if(ch.indexOf(a)!=-1&&!e.ctrlKey){e.preventDefault()}});b.blur(function(){var a=b.val(),j=0;for(j;j<a.length;j++){if(ch.indexOf(a[j])!=-1){b.val('');return false}}return false});return b};$.fn.numeric=function(p){var a='abcdefghijklmn�opqrstuvwxyz',aZ=a.toUpperCase();return this.each(function(){$(this).alphanumeric($.extend({nchars:a+aZ},p))})};$.fn.alpha=function(p){var a='1234567890';return this.each(function(){$(this).alphanumeric($.extend({nchars:a},p))})}})(jQuery);

/* jQuery Numeric
 * 
 * Copyright (c) 2006-2014 Sam Collett (http://www.texotela.co.uk)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version 1.4.1
 * Demo: http://www.texotela.co.uk/code/jquery/numeric/
 */
(function($){$.fn.numeric=function(config,callback){if(typeof config==="boolean"){config={decimal:config,negative:true,decimalPlaces:-1}}config=config||{};if(typeof config.negative=="undefined"){config.negative=true}var decimal=config.decimal===false?"":config.decimal||".";var negative=config.negative===true?true:false;var decimalPlaces=typeof config.decimalPlaces=="undefined"?-1:config.decimalPlaces;callback=typeof callback=="function"?callback:function(){};return this.data("numeric.decimal",decimal).data("numeric.negative",negative).data("numeric.callback",callback).data("numeric.decimalPlaces",decimalPlaces).keypress($.fn.numeric.keypress).keyup($.fn.numeric.keyup).blur($.fn.numeric.blur)};$.fn.numeric.keypress=function(e){var decimal=$.data(this,"numeric.decimal");var negative=$.data(this,"numeric.negative");var decimalPlaces=$.data(this,"numeric.decimalPlaces");var key=e.charCode?e.charCode:e.keyCode?e.keyCode:0;if(key==13&&this.nodeName.toLowerCase()=="input"){return true}else if(key==13){return false}var allow=false;if(e.ctrlKey&&key==97||e.ctrlKey&&key==65){return true}if(e.ctrlKey&&key==120||e.ctrlKey&&key==88){return true}if(e.ctrlKey&&key==99||e.ctrlKey&&key==67){return true}if(e.ctrlKey&&key==122||e.ctrlKey&&key==90){return true}if(e.ctrlKey&&key==118||e.ctrlKey&&key==86||e.shiftKey&&key==45){return true}if(key<48||key>57){var value=$(this).val();if($.inArray("-",value.split(""))!==0&&negative&&key==45&&(value.length===0||parseInt($.fn.getSelectionStart(this),10)===0)){return true}if(decimal&&key==decimal.charCodeAt(0)&&$.inArray(decimal,value.split(""))!=-1){allow=false}if(key!=8&&key!=9&&key!=13&&key!=35&&key!=36&&key!=37&&key!=39&&key!=46){allow=false}else{if(typeof e.charCode!="undefined"){if(e.keyCode==e.which&&e.which!==0){allow=true;if(e.which==46){allow=false}}else if(e.keyCode!==0&&e.charCode===0&&e.which===0){allow=true}}}if(decimal&&key==decimal.charCodeAt(0)){if($.inArray(decimal,value.split(""))==-1){allow=true}else{allow=false}}}else{allow=true;if(decimal&&decimalPlaces>0){var dot=$.inArray(decimal,$(this).val().split(""));if(dot>=0&&$(this).val().length>dot+decimalPlaces){allow=false}}}return allow};$.fn.numeric.keyup=function(e){var val=$(this).val();if(val&&val.length>0){var carat=$.fn.getSelectionStart(this);var selectionEnd=$.fn.getSelectionEnd(this);var decimal=$.data(this,"numeric.decimal");var negative=$.data(this,"numeric.negative");var decimalPlaces=$.data(this,"numeric.decimalPlaces");if(decimal!==""&&decimal!==null){var dot=$.inArray(decimal,val.split(""));if(dot===0){this.value="0"+val;carat++;selectionEnd++}if(dot==1&&val.charAt(0)=="-"){this.value="-0"+val.substring(1);carat++;selectionEnd++}val=this.value}var validChars=[0,1,2,3,4,5,6,7,8,9,"-",decimal];var length=val.length;for(var i=length-1;i>=0;i--){var ch=val.charAt(i);if(i!==0&&ch=="-"){val=val.substring(0,i)+val.substring(i+1)}else if(i===0&&!negative&&ch=="-"){val=val.substring(1)}var validChar=false;for(var j=0;j<validChars.length;j++){if(ch==validChars[j]){validChar=true;break}}if(!validChar||ch==" "){val=val.substring(0,i)+val.substring(i+1)}}var firstDecimal=$.inArray(decimal,val.split(""));if(firstDecimal>0){for(var k=length-1;k>firstDecimal;k--){var chch=val.charAt(k);if(chch==decimal){val=val.substring(0,k)+val.substring(k+1)}}}if(decimal&&decimalPlaces>0){var dot=$.inArray(decimal,val.split(""));if(dot>=0){val=val.substring(0,dot+decimalPlaces+1);selectionEnd=Math.min(val.length,selectionEnd)}}this.value=val;$.fn.setSelection(this,[carat,selectionEnd])}};$.fn.numeric.blur=function(){var decimal=$.data(this,"numeric.decimal");var callback=$.data(this,"numeric.callback");var negative=$.data(this,"numeric.negative");var val=this.value;if(val!==""){var re=new RegExp(negative?"-?":""+"^\\d+$|^\\d*"+decimal+"\\d+$");if(!re.exec(val)){callback.apply(this)}}};$.fn.removeNumeric=function(){return this.data("numeric.decimal",null).data("numeric.negative",null).data("numeric.callback",null).data("numeric.decimalPlaces",null).unbind("keypress",$.fn.numeric.keypress).unbind("keyup",$.fn.numeric.keyup).unbind("blur",$.fn.numeric.blur)};$.fn.getSelectionStart=function(o){if(o.type==="number"){return undefined}else if(o.createTextRange&&document.selection){var r=document.selection.createRange().duplicate();r.moveEnd("character",o.value.length);if(r.text=="")return o.value.length;return Math.max(0,o.value.lastIndexOf(r.text))}else{try{return o.selectionStart}catch(e){return 0}}};$.fn.getSelectionEnd=function(o){if(o.type==="number"){return undefined}else if(o.createTextRange&&document.selection){var r=document.selection.createRange().duplicate();r.moveStart("character",-o.value.length);return r.text.length}else return o.selectionEnd};$.fn.setSelection=function(o,p){if(typeof p=="number"){p=[p,p]}if(p&&p.constructor==Array&&p.length==2){if(o.type==="number"){o.focus()}else if(o.createTextRange){var r=o.createTextRange();r.collapse(true);r.moveStart("character",p[0]);r.moveEnd("character",p[1]-p[0]);r.select()}else{o.focus();try{if(o.setSelectionRange){o.setSelectionRange(p[0],p[1])}}catch(e){}}}}})(jQuery);

/* jQuery Selectboxes
 *
 * Copyright (c) 2006-2009 Sam Collett (http://www.texotela.co.uk)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version 2.2.6
 * Demo: http://www.texotela.co.uk/code/jquery/select/
*/
(function($){$.fn.addOption=function(){var j=function(a,v,t,b,c){var d=document.createElement("option");d.value=v,d.text=t;var o=a.options;var e=o.length;if(!a.cache){a.cache={};for(var i=0;i<e;i++){a.cache[o[i].value]=i}}if(c||c==0){var f=d;for(var g=c;g<=e;g++){var h=a.options[g];a.options[g]=f;o[g]=f;a.cache[o[g].value]=g;f=h}}if(typeof a.cache[v]=="undefined")a.cache[v]=e;a.options[a.cache[v]]=d;if(b){d.selected=true}};var a=arguments;if(a.length==0)return this;var l=true;var m=false;var n,v,t,startindex=0;if(typeof(a[0])=="object"){m=true;n=a[0]}if(a.length>=2){if(typeof(a[1])=="boolean"){l=a[1];startindex=a[2]}else if(typeof(a[2])=="boolean"){l=a[2];startindex=a[1]}else{startindex=a[1]}if(!m){v=a[0];t=a[1]}}this.each(function(){if(this.nodeName.toLowerCase()!="select")return;if(m){var c=this;jQuery.each(n,function(a,b){if(typeof(b)=="object"){jQuery.each(b,function(k,v){a=k;b=v})}j(c,a,b,l,startindex);startindex+=1})}else{j(this,v,t,l,startindex)}});return this};$.fn.ajaxAddOption=function(b,c,d,e,f){if(typeof(b)!="string")return this;if(typeof(c)!="object")c={};if(typeof(d)!="boolean")d=true;this.each(function(){var a=this;$.getJSON(b,c,function(r){$(a).addOption(r,d);if(typeof e=="function"){if(typeof f=="object"){e.apply(a,f)}else{e.call(a)}}})});return this};$.fn.removeOption=function(){var a=arguments;if(a.length==0)return this;var d=typeof(a[0]);var v,index;if(d=="string"||d=="object"||d=="function"){v=a[0];if(v.constructor==Array){var l=v.length;for(var i=0;i<l;i++){this.removeOption(v[i],a[1])}return this}}else if(d=="number")index=a[0];else return this;this.each(function(){if(this.nodeName.toLowerCase()!="select")return;if(this.cache)this.cache=null;var b=false;var o=this.options;if(!!v){var c=o.length;for(var i=c-1;i>=0;i--){if(v.constructor==RegExp){if(o[i].value.match(v)){b=true}}else if(o[i].value==v){b=true}if(b&&a[1]===true)b=o[i].selected;if(b){o[i]=null}b=false}}else{if(a[1]===true){b=o[index].selected}else{b=true}if(b){this.remove(index)}}});return this};$.fn.sortOptions=function(f){var g=$(this).selectedValues();var a=typeof(f)=="undefined"?true:!!f;this.each(function(){if(this.nodeName.toLowerCase()!="select")return;var o=this.options;var d=o.length;var e=[];for(var i=0;i<d;i++){e[i]={v:o[i].value,t:o[i].text}}e.sort(function(b,c){o1t=b.t.toLowerCase(),o2t=c.t.toLowerCase();if(o1t==o2t)return 0;if(a){return o1t<o2t?-1:1}else{return o1t>o2t?-1:1}});for(var i=0;i<d;i++){o[i].text=e[i].t;o[i].value=e[i].v}}).selectOptions(g,true);return this};$.fn.selectOptions=function(b,d){var v=b;var e=typeof(b);if(e=="object"&&v.constructor==Array){var f=this;$.each(v,function(){f.selectOptions(this,d)})};var c=d||false;if(e!="string"&&e!="function"&&e!="object")return this;this.each(function(){if(this.nodeName.toLowerCase()!="select")return this;var o=this.options;var a=o.length;for(var i=0;i<a;i++){if(v.constructor==RegExp){if(o[i].value.match(v)){o[i].selected=true}else if(c){o[i].selected=false}}else{if(o[i].value==v){o[i].selected=true}else if(c){o[i].selected=false}}}});return this};$.fn.copyOptions=function(b,c){var w=c||"selected";if($(b).size()==0)return this;this.each(function(){if(this.nodeName.toLowerCase()!="select")return this;var o=this.options;var a=o.length;for(var i=0;i<a;i++){if(w=="all"||(w=="selected"&&o[i].selected)){$(b).addOption(o[i].value,o[i].text)}}});return this};$.fn.containsOption=function(b,c){var d=false;var v=b;var e=typeof(v);var f=typeof(c);if(e!="string"&&e!="function"&&e!="object")return f=="function"?this:d;this.each(function(){if(this.nodeName.toLowerCase()!="select")return this;if(d&&f!="function")return false;var o=this.options;var a=o.length;for(var i=0;i<a;i++){if(v.constructor==RegExp){if(o[i].value.match(v)){d=true;if(f=="function")c.call(o[i],i)}}else{if(o[i].value==v){d=true;if(f=="function")c.call(o[i],i)}}}});return f=="function"?this:d};$.fn.selectedValues=function(){var v=[];this.selectedOptions().each(function(){v[v.length]=this.value});return v};$.fn.selectedTexts=function(){var t=[];this.selectedOptions().each(function(){t[t.length]=this.text});return t};$.fn.selectedOptions=function(){return this.find("option:selected")}})(jQuery);

/* Remove Value From Array */
Array.prototype.remove = function(el){
    return this.splice(this.indexOf(el),1);
};

/* Check if object has proprty */
Object.prototype.hasOwnProperty = function(property) {
    return this[property] !== undefined;
};

/*jslint regexp: true, nomen: true, undef: true, sloppy: true, eqeq: true, vars: true, white: true, plusplus: true, maxerr: 50, indent: 4 */
var d4plib_admin;

;(function($, window, document, undefined) {
    d4plib_admin = {
        scroll_offset: 40,
        active_element: null,
        init: function() {
            $(".d4p-nav-button > a").click(function(e){
                e.preventDefault();

                $(this).next().slideToggle("fast");
            });

            setTimeout(function(){
                $(".d4p-wrap .updated").slideUp("slow");
            }, 4000);

            $(window).bind("load resize orientationchange", function(){
                if (document.body.clientWidth < 800) {
                    d4plib_admin.scroll_offset = 60;
                } else {
                    d4plib_admin.scroll_offset = 40;
                }

                if (document.body.clientWidth < 640) {
                    $(".d4p-panel-scroller").removeClass("d4p-scroll-active");
                } else {
                    $(".d4p-panel-scroller").addClass("d4p-scroll-active");
                }
            });

            $(".d4p-check-uncheck a").click(function(e){
                e.preventDefault();

                var checkall = $(this).attr("href").substr(1) === "checkall";

                $(this).parent().parent().find("input[type=checkbox]").prop("checked", checkall);
            });
        },
        settings: function() {
            d4plib_media_image.init();

            $(".d4p-setting-number input, .d4p-field-number").numeric();
            $(".d4p-setting-integer input, .d4p-field-integer").numeric({decimalPlaces: 0, negative: false});

            $(".d4p-color-picker").wpColorPicker();

            $(document).on("click", ".d4p-group h3 i.fa.fa-caret-down, .d4p-group h3 i.fa.fa-caret-up", function() {
                var closed = $(this).hasClass("fa-caret-down"),
                    content = $(this).parent().next();

                if (closed) {
                    $(this).removeClass("fa-caret-down").addClass("fa-caret-up");
                    content.slideDown(300);
                } else {
                    $(this).removeClass("fa-caret-up").addClass("fa-caret-down");
                    content.slideUp(300);
                }
            });

            $(document).on("click", ".d4p-section-toggle .d4p-toggle-title", function() {
                var icon = $(this).find("i.fa"),
                    closed = icon.hasClass("fa-caret-down"),
                    content = $(this).next();

                if (closed) {
                    icon.removeClass("fa-caret-down").addClass("fa-caret-up");
                    content.slideDown(300);
                } else {
                    icon.removeClass("fa-caret-up").addClass("fa-caret-down");
                    content.slideUp(300);
                }
            });

            $(document).on("click", ".d4p-setting-expandable_pairs .button-secondary", function(e){
                e.preventDefault();

                $(this).parent().fadeOut(function(){
                    $(this).remove();
                });
            });

            $(document).on("click", ".d4p-setting-expandable_text .button-secondary", function(e){
                e.preventDefault();

                $(this).parent().fadeOut(function(){
                    $(this).remove();
                });
            });

            $(".d4p-setting-expandable_pairs a.button-primary").click(function(e) {
                e.preventDefault();

                var list = $(this).closest(".d4p-setting-expandable_pairs"),
                    next = $(".d4p-next-id", list),
                    next_id = next.val(),
                    el = $(".pair-element-0", list).clone();

                $("input", el).each(function(){
                    var id = $(this).attr("id").replace("_0_", "_" + next_id + "_"),
                        name = $(this).attr("name").replace("[0]", "[" + next_id + "]");

                    $(this).attr("id", id).attr("name", name);
                });

                el.attr("class", "pair-element-" + next_id).fadeIn();
                $(this).before(el);

                next_id++;
                next.val(next_id);
            });

            $(".d4p-setting-expandable_text a.button-primary").click(function(e) {
                e.preventDefault();

                var list = $(this).closest(".d4p-setting-expandable_text"),
                    next = $(".d4p-next-id", list),
                    next_id = next.val(),
                    el = $(".exp-text-element-0", list).clone();

                $("input", el).each(function(){
                    var id = $(this).attr("id").replace("_0_", "_" + next_id + "_"),
                        name = $(this).attr("name").replace("[0]", "[" + next_id + "]");

                    $(this).attr("id", id).attr("name", name);
                });

                el.attr("class", "exp-text-element exp-text-element-" + next_id).fadeIn();
                $("ol", list).append(el);

                next_id++;
                next.val(next_id);
            });
        },
        scroller: function() {
            var $sidebar = $(".d4p-panel-scroller"), 
                $window = $(window);

            if ($sidebar.length > 0) {
                var offset = $sidebar.offset();

                $window.scroll(function() {
                    if ($window.scrollTop() > offset.top && $sidebar.hasClass("d4p-scroll-active")) {
                        $sidebar.stop().animate({
                            marginTop: $window.scrollTop() - offset.top + d4plib_admin.scroll_offset
                        });
                    } else {
                        $sidebar.stop().animate({
                            marginTop: 0
                        });
                    }
                });
            }
        }
    };

    d4plib_admin.init();
    d4plib_admin.settings();
    d4plib_admin.scroller();
})(jQuery, window, document);
