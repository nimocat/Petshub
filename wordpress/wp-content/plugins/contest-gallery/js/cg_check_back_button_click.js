jQuery(document).ready(function($){
	
if ( $( "#cg_check_load_time" ).length ) {
 
    var cg_cookie_name = "cg_reload";

	if($("#cg_check_load_time").val().length > 0){
		
		function getCookie(cg_cookie_name){
			var name = cg_cookie_name + "=";
			var ca = document.cookie.split(';');
			for(var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}
		
		function checkCookie() {
			var cg_cookie_value = getCookie("cg_reload");
			if (cg_cookie_value != "") {
				if(cg_cookie_value==$("#cg_check_load_time").val()){
					location.reload(true);
				}
			} else {
				if (cg_cookie_value != "" && cg_cookie_value != null) {
					setCookie("cg_reload", cg_cookie_value, 365);
				}
			}
		}
		
		checkCookie();
		
	}
	else{
		var cg_cookie_value = Math.floor(Date.now() / 1000);
		$("#cg_check_load_time").val(cg_cookie_value);	
		setCookie("cg_reload", cg_cookie_value, 365);
	}

 
}


	function setCookie(cg_cookie_name, cg_cookie_value, exdays) {
		var d = new Date();
		
		d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
		
		var expires = "expires="+d.toUTCString();
		document.cookie = cg_cookie_name + "=" + cg_cookie_value + ";" + expires + ";path=/";
	}



});