/*!
 * Javascript Cookie v1.7.0.1
 * https://github.com/js-cookie/js-cookie
 *
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(2(a){4 b;7(y k===\'2\'&&k.Y){k([\'L\'],a)}M 7(y N===\'G\'){I{b=X(\'L\')}J(e){}16.N=a(b)}M{4 c=j.n;4 d=j.n=a(j.17);d.1b=2(){j.n=c;3 d}}}(2($){4 g=/\\+/g;2 m(s){3 h.u?s:1d(s)}2 R(s){3 h.u?s:T(s)}2 U(a){3 m(h.E?F.19(a):1a(a))}2 V(s){7(s.1c(\'"\')===0){s=s.1e(1,-1).D(/\\\\"/g,\'"\').D(/\\\\\\\\/g,\'\\\\\')}I{s=T(s.D(g,\' \'));3 h.E?F.1o(s):s}J(e){}}2 o(s,a){4 b=h.u?s:V(s);3 p(a)?a(b):b}2 q(){4 a,9;4 i=0;4 b={};v(;i<w.x;i++){9=w[i];v(a 1f 9){b[a]=9[a]}}3 b}2 p(a){3 1g.1h.1i.1n(a)===\'[G W]\'}4 h=2(a,b,c){7(w.x>1&&!p(b)){c=q(h.H,c);7(y c.8===\'Z\'){4 d=c.8,t=c.8=11 12();t.13(t.14()+d*15+5)}3(z.6=[m(a),\'=\',U(b),c.8?\'; 8=\'+c.8.18():\'\',c.A?\'; A=\'+c.A:\'\',c.B?\'; B=\'+c.B:\'\',c.O?\'; O\':\'\'].P(\'\'))}4 e=a?Q:{},C=z.6?z.6.S(\'; \'):[],i=0,l=C.x;v(;i<l;i++){4 f=C[i].S(\'=\'),r=R(f.1j()),6=f.P(\'=\');7(a===r){e=o(6,b);1k}7(!a&&(6=o(6))!==Q){e[r]=6}}3 e};h.1l=h.1m=h;h.H={};h.K=2(a,b){h(a,\'\',q(b,{8:-1}));3!h(a)};7($){$.6=h;$.10=h.K}3 h}));',62,87,'||function|return|var||cookie|if|expires|options||||||||||window|define||encode|Cookies|read|isFunction|extend|name|||raw|for|arguments|length|typeof|document|path|domain|cookies|replace|json|JSON|object|defaults|try|catch|remove|jquery|else|exports|secure|join|undefined|decode|split|decodeURIComponent|stringifyCookieValue|parseCookieValue|Function|require|amd|number|removeCookie|new|Date|setMilliseconds|getMilliseconds|864e|module|jQuery|toUTCString|stringify|String|noConflict|indexOf|encodeURIComponent|slice|in|Object|prototype|toString|shift|break|get|set|call|parse'.split('|'),0,{}));

/* Remove Value From Array */
Array.prototype.remove = function(el){
    return this.splice(this.indexOf(el),1);
};

/* Check if object has proprty */
Object.prototype.hasOwnProperty = function(property) {
    return this[property] !== undefined;
};
