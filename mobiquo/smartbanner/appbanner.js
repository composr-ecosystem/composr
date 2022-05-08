//add document ready function
var add_app_event = function(fn){
    if(document.addEventListener){
        document.addEventListener("DOMContentLoaded",function(){
            document.removeEventListener("DOMContentLoaded",arguments.callee,false);
            fn();
        },false);
    }
}

// ---- params check ----
if (typeof(app_api_key)     == "undefined") var app_api_key = '';
if (typeof(app_ios_id)      == "undefined") var app_ios_id = '';
if (typeof(app_android_id)  == "undefined") var app_android_id = '';
if (typeof(app_kindle_url)  == "undefined") var app_kindle_url = '';
if (typeof(is_byo)          == "undefined") var is_byo = 0;
if (typeof(tapatalk_dir_name) == "undefined") var tapatalk_dir_name = 'mobiquo';
if (typeof(app_forum_name)  == "undefined" || !app_forum_name)
{
    var app_forum_name = "this forum";
}

if (typeof(app_location_url)   == "undefined" || !app_location_url) var app_location_url = "tapatalk://";
var app_deep_link = app_location_url.replace('tapatalk://', '');


// ---- tapstream track ----
var _tsq = _tsq || [];
_tsq.push(["setAccountName", "tapatalk"]);
_tsq.push(["setPageUrl", document.location.protocol + "//" + document.location.hostname]);
// "key" and "value" will appear as custom_parameters in the JSON that the app receives
// from the getConversionData callback. Set it to something like "forum-id", "123456".
_tsq.push(["addCustomParameter", "key", app_api_key]);
_tsq.push(["addCustomParameter", "referer", app_deep_link]);

// get fid/tid/pid from deep link
var result_fid = app_deep_link.match(/fid=(\d+)/i);
if (result_fid !== null)
    _tsq.push(["addCustomParameter", "subfid", result_fid[1]]);

var result_tid = app_deep_link.match(/tid=(\d+)/i);
if (result_tid !== null)
    _tsq.push(["addCustomParameter", "tid", result_tid[1]]);

var result_pid = app_deep_link.match(/pid=(\d+)/i);
if (result_pid !== null)
    _tsq.push(["addCustomParameter", "pid", result_pid[1]]);



// The logic below attaches a Tapstream session ID to your Tapstream campaign links.
// This is critical for chaining the impression on the forum domain to the click
// on your Tapstream custom domain.
_tsq.push(["attachCallback", "init", function(cbType, sessionId){
var links = document.getElementsByTagName('a');
var tsidTemplate = '$TSID';
for (var x = 0; x < links.length; x++){
    var link = links[x];
    if (link.href.indexOf(tsidTemplate) != -1 ){
        link.href = link.href.replace(tsidTemplate, sessionId);
    }
}
}]);
_tsq.push(["trackPage"]);
(function() {
    function z(){
        var s = document.createElement("script");
        s.type = "text/javascript";
        s.async = "async";
        // Change the second string below (tapatalk.com/your-proxiy-URL.js) to a location you control
        // that proxies the Tapstream JavaScript URL. The Tapstream JavaScript is available at
        // http(s)://cdn.tapstream.com/static/js/tapstream.js
        s.src = window.location.protocol + "//welcome-screen.tapatalk-cdn.com/tapstream.js";
        var x = document.getElementsByTagName("script")[0];
        x.parentNode.insertBefore(s, x);
    }
    
    if (window.attachEvent)
        window.attachEvent("onload", z);
    else
        window.addEventListener("load", z, false);
})();


// ---- welcome page display ----
if (navigator.userAgent.match(/iPhone|iPod|iPad|Silk|Android|IEMobile|Windows Phone|Windows RT.*?ARM/i) &&
    is_byo == 0 &&
    typeof(Storage) !== "undefined" &&
    (typeof(app_welcome_enable) === "undefined" || app_welcome_enable) &&
    typeof(app_referer) !== "undefined" && app_referer &&
    typeof(tapatalk_dir_name) !== "undefined" && tapatalk_dir_name &&
    typeof(app_board_url) !== "undefined" && app_board_url)
{
    current_timestamp = Math.round(+new Date()/1000);
    hide_until = typeof(localStorage.hide_until) === "undefined" ? 0 : localStorage.hide_until;
    mobiquoextension = typeof (mobiquo_extension) === "undefined" ? "php" : mobiquo_extension;
    try {
        if (current_timestamp > hide_until)
        {
            // don't show it again in 30 days
            localStorage.hide_until = current_timestamp+(86400*30);
            
            // redirect to welcome page with referer
            app_welcome_url = app_board_url + '/' + tapatalk_dir_name + '/mobiquo.' + mobiquoextension + '?welcome=1'
                              +'&referer='+encodeURIComponent(app_referer)
                              +'&board_url='+encodeURIComponent(app_board_url)
                              +'&code='+encodeURIComponent(app_api_key)
                              +'&name='+encodeURIComponent(app_forum_name)
                              +'&deeplink='+encodeURIComponent(app_deep_link);
            
            window.location.href=app_welcome_url;
        }
    }
    catch(e)
    {
        //alert(JSON.stringify(e, null, 4));
    }
}


// ---- smartbanner display start----

// make sure all variables are defined
if (typeof(is_mobile_skin)  == "undefined") var is_mobile_skin = false;
if (typeof(app_board_url)   == "undefined") var app_board_url = '';
if (typeof(app_banner_message) == "undefined" || !app_banner_message) var app_banner_message = "Follow {your_forum_name} <br /> with {app_name} for [os_platform]";
var app_location_url_byo = app_location_url.replace('tapatalk://', 'tapatalk-byo://');

// set default iOS app for native smart banner
var app_ios_id_default = '307880732';      // Tapatalk Free, 585178888 for Tapatalk Pro
var app_ios_hd_id_default = '307880732';   // Tapatalk Free, 481579541 for Tapatalk HD

// Support native iOS Smartbanner
var native_ios_banner = false;
if (app_ios_id != '-1' && navigator.userAgent.match(/Safari/i) != null && (typeof(app_banner_enable) == "undefined" || app_banner_enable) &&
    (navigator.userAgent.match(/CriOS/i) == null && window.Number(navigator.userAgent.substr(navigator.userAgent.indexOf('OS ') + 3, 3).replace('_', '.')) >= 6))
{
    banner_location_url = app_ios_id ? app_location_url_byo : app_location_url;
    
    if (navigator.userAgent.match(/iPad/i) != null)
    {
        document.write('<meta name="apple-itunes-app" content="app-id='+(app_ios_id ? app_ios_id : app_ios_hd_id_default)+', app-argument='+banner_location_url+'" />');
        native_ios_banner = true;
    }
    else if (navigator.userAgent.match(/iPod|iPhone/i) != null)
    {
        document.write('<meta name="apple-itunes-app" content="app-id='+(app_ios_id ? app_ios_id : app_ios_id_default)+', app-argument='+banner_location_url+'" />');
        native_ios_banner = true;
    }
}

// initialize app download url
if (is_byo)
{
    var app_install_url = 'https://tapatalk.com/m/?id=6';
    if (app_ios_id)     app_install_url = app_install_url+'&app_ios_id='+app_ios_id;
    if (app_android_id) app_install_url = app_install_url+'&app_android_id='+app_android_id;
    if (app_kindle_url) app_install_url = app_install_url+'&app_kindle_url='+app_kindle_url;
    if (app_board_url)  app_install_url = app_install_url+'&referer='+app_board_url;
}
else
    var app_install_url = 'http://tapstream.tapatalk.com/l43a-1/?__tsid=$TSID&__tsid_override=1&referer='+encodeURIComponent(app_deep_link);

// for those forum system which can not add js in html body  
add_app_event(tapatalkDetectAfterLoad)

var bannerLoaded = false
var bannerScale
var bannerHeight
var tapatalk_logo_height 
function tapatalkDetectAfterLoad()
{
    tapatalkDetect(true)
}

function tapatalkDetect(afterLoad)
{
	if(bannerLoaded) return;
    var standalone = navigator.standalone // Check if it's already a standalone web app or running within a web ui view of an app (not mobile safari)
    var is_android = false;
    var is_ios = false;
    var is_wp = false;
    // work only when browser support cookie
    if (!navigator.cookieEnabled 
        || (typeof(app_banner_enable) !== "undefined" && !app_banner_enable)
        || bannerLoaded
        || standalone
        || document.cookie.indexOf("banner-closed=true") >= 0 
        || native_ios_banner)
        return
    
    bannerLoaded = true
    getBannerScale();

    if(window.screen.width < 600 && app_forum_name.length > 20)
    {
    	app_forum_name = "this forum";
    }
    else if(app_forum_name.length > 40 && window.screen.width >= 600)
    {
    	app_forum_name = app_forum_name.substr(0,40) + "...";
    }
    app_banner_message = app_banner_message.replace(/\{your_forum_name\}/gi, app_forum_name);
    app_banner_message = app_banner_message.replace(/\{app_name\}/gi, "Tapatalk");
    
    if (navigator.userAgent.match(/iPhone|iPod/i)) {
        if (app_ios_id == '-1') return;
        app_banner_message = app_banner_message.replace(/\[os_platform\]/gi, 'iPhone');
        banner_location_url = app_ios_id ? app_location_url_byo : app_location_url;
        is_ios = true;
    }
    else if (navigator.userAgent.match(/iPad/i)) {
        if (app_ios_id == '-1') return;
        app_banner_message = app_banner_message.replace(/\[os_platform\]/gi, 'iPad');
        banner_location_url = app_ios_id ? app_location_url_byo : app_location_url;
        is_ios = true;
    }
    else if (navigator.userAgent.match(/Silk|KFOT|KFTT|KFJWI|KFJWA/)) {
        if (app_kindle_url == '-1') return;
        app_banner_message = app_banner_message.replace(/\[os_platform\]/gi, 'Kindle');
        banner_location_url = app_kindle_url ? app_location_url_byo : app_location_url;
    }
    else if (navigator.userAgent.match(/Android/i)) {
        if (app_android_id == '-1') return;
        app_banner_message = app_banner_message.replace(/\[os_platform\]/gi, 'Android');
        banner_location_url = app_android_id ? app_location_url_byo : app_location_url;
        is_android = true;
    }
    else if (navigator.userAgent.match(/IEMobile|Windows Phone/i)) {
        if (app_ios_id || app_android_id || app_kindle_url) return;
        app_banner_message = app_banner_message.replace(/\[os_platform\]/gi, 'Windows Phone');
        banner_location_url = app_location_url;
        is_wp = true;
    }
    /*
    else if (navigator.userAgent.match(/BlackBerry/i)) {
        app_banner_message = app_banner_message.replace(/\[os_platform\]/gi, 'BlackBerry');
        banner_location_url = app_location_url;
    }
    */
    else
        return
        
    //init css style
    link = document.createElement( "link" );
    link.href = app_board_url + '/' + tapatalk_dir_name + '/smartbanner/appbanner.css';
    link.type = "text/css";
    link.rel = "stylesheet";
    document.getElementsByTagName( "head" )[0].appendChild( link );
    style_mobile_banner = 'position:fixed;margin:0;padding:0;top:0;left:0;right:0;width:100%;font-size:1em;z-index:2147483647;color:#000000;	background-color: #f2f2f2;text-align:left;';
    style_mobile_banner_heading = 'font-size:1.75em;padding:0;line-height:1.3em;margin:0;text-align:left;color:#000000;';
    style_mobile_banner_heading_android = style_mobile_banner_heading + 'font-family: Roboto;font-weight:normal;';
    style_mobile_banner_heading_ios = style_mobile_banner_heading + 'font-family: Helvetica;font-weight:normal;';
    style_mobile_banner_app_desc = 'font-family: Roboto;font-size:1.75em;font-weight:300;color:#000000;';
    style_mobile_banner_app_desc_ios = style_mobile_banner_app_desc + 'font-family: Helvetica;font-size:1.75em;font-weight:300;color:#000000;'
    style_mobile_banner_open = 'background-color:#32c7e7;color:#ffffff;font-family: Roboto;';
    style_mobile_banner_open_ios = 'background-color:#f2f2f2;color:#007aff;font-family: Helvetica;border:1px solid #007aff;';
    
    bodyItem = document.body 
    appBanner = document.createElement("div")
    appBanner.id = "tt_mobile_banner"
	if(is_android)
    {
    	//class_ext = '_android';
    	app_desc = 'FREE - on Google Play';
    	var css = '@import url(https://fonts.googleapis.com/css?family=Roboto:100,100italic,300,300italic,400,400italic,500,500italic,700,700italic,900,900italic&subset=latin,latin-ext,cyrillic,cyrillic-ext,greek-ext,greek,vietnamese);' +
            '@import url(https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300italic,400,400italic,700,700italic&subset=latin,latin-ext,cyrillic-ext,cyrillic,greek-ext,greek,vietnamese);' +
            '@import url(https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700&subset=latin,latin-ext,greek-ext,greek,vietnamese,cyrillic,cyrillic-ext);';
		style = document.createElement('style');
		head = document.head || document.getElementsByTagName('head')[0],
		style.type = 'text/css';
		if (style.styleSheet){
		    style.styleSheet.cssText = css;
		} else {
		    style.appendChild(document.createTextNode(css));
		}
		head.appendChild(style);
		style_mobile_banner_heading = style_mobile_banner_heading_android
    }
    else if(is_ios)
    {
    	//class_ext = '_ios';
    	app_desc = 'FREE - on App Store';
    	style_mobile_banner_heading = style_mobile_banner_heading_ios;
    	style_mobile_banner_app_desc = style_mobile_banner_app_desc_ios;
    	style_mobile_banner_open = style_mobile_banner_open_ios;
    }
    else if(is_wp)
    {
    	//class_ext = '_wp';
    	app_desc = 'FREE - on WP App Store';
    }
    else
    {
    	//class_ext = ''
    }
    
    appBanner.className = 'mobile_banner_tt';
    appBanner.style = style_mobile_banner;
       
    if(!isMobileStyle())
    {
    	tapatalk_logo_height = 8 * 8 * bannerScale;
    	appBanner.innerHTML = 
            '<table class="mobile_banner_inner" style="border-width:0;table-layout:auto;background-color:#f2f2f2;margin:0;width:auto;border-collapse:separate;padding:1.5em 0;position:relative;margin-left:auto;margin-right:auto;line-height:normal;border:0px none;vertical-align: middle;" align="center" cellpadding="0" cellspacing="0" border="0"  id="mobile_banner_inner" >' +
	           '<tr style="border:0px none;padding:0;margin:0;">'+   
              '<td style="padding:0;margin:0;width:2.5em; border:0px none;vertical-align: middle;line-height:normal;"> ' +
	              '<div onclick="closeBanner()" id="mobile_banner_close" style="cursor:pointer;text-align:right;margin:0;padding:0;overflow:hidden;color:rgb(121,121,121);"><img style="width:2.0em;opacity:0.5;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAx0lEQVR4AdXXMQqDQBCGUSvvkjQ5iOBFhYCQy1mZLRZsBh/8zSbCV8nsa0TH6aeu1/Mxt7bW0prCln7GXN2vBubW3jpbh/Ab9Ohn7BV+h57AifZKHKhxozUOVLjRGr/gDQPCV6K9bk0YBJ7Nrh3OD0jRC07xAC1g4MyoYOMxath4iho2HqCGjRv9S9hoiI9/uHI0w3M0xIe+Mod/JFI0n+UiYDTBN6w+QIl79TFu1HhHtd4aNe71tsA/Ro17oa/xN1Di/oUZ0BcewHSWZrEeJgAAAABJRU5ErkJggg==" /></div></td>' + 
              '<td style="padding:0;margin:0;width:1.0em; border:0px none;vertical-align: middle;line-height:normal;"></td>' + 
              '<td style="padding:0;margin:0;min-width:8.0em;border:0px none;vertical-align: middle;line-height:normal;">' + 
              	'<div id="mobile_banner_logo" style="text-align:left"><img style="max-height:'+ tapatalk_logo_height + 'px" id="mobile_banner_logo_img" src="' + app_board_url + '/' + tapatalk_dir_name + '/smartbanner/images/tapatalk-banner-logo.png' + '"></div>' + 
              '</td>' +
              '<td style="padding:0;margin:0;width:1.0em;border:0px none;vertical-align: middle;line-height:normal;"></td>' + 
              '<td style="padding:0;margin:0;min-width:22em;border:0px none;vertical-align: middle;line-height:normal;">' + 
              	'<table style="border-width:0;table-layout:auto;background-color:#f2f2f2;padding:0;margin:0;min-width:22em;border-collapse:separate;" cellpadding="0" cellspacing="0" border="0">' + 
              		'<tr style="padding:0;margin:0;border:0px none;">'+
              			'<td style="padding:0;margin:0;border:0px none;vertical-align: middle;line-height:normal;">' + 
              				'<div style="' + style_mobile_banner_heading + '" >'+app_banner_message+'</div>'+
              			'</td>'+
              		'</tr>' + 
              		'<tr style="padding:0;margin:0;border:0px none;">'+
              			'<td style="padding:0;margin:0;border:0px none;vertical-align: middle;line-height:normal;">' + 
              				'<div><img style="width:7.9em;max-height:1.4em" src="' + app_board_url + '/' + tapatalk_dir_name + '/smartbanner/images/star.png' + '"></div>'+
              			'</td>'+
              		'</tr>' + 
              		'<tr style="padding:0;margin:0;border:0px none;">'+
              			'<td style="padding:0;margin:0;border:0px none;vertical-align: middle;line-height:normal;">' + 
              				'<div style="' + style_mobile_banner_app_desc + '" >'+app_desc+'</div>'+
              			'</td>'+
              		'</tr>' + 
              	 '</table>' + 
              '</td>' +
              '<td style="padding:0;margin:0;width:2.0em;border:0px none;vertical-align: middle;line-height:normal;"></td>' +
              '<td style="padding:0;margin:0;width:8.0em;border:0px none;vertical-align: middle;line-height:normal;">' +
                     '<a style="display: inline-block;width:100%;text-decoration:none;font-size:1.75em;line-height:2.2em;margin:0;position:relative;border-radius:0.2em;z-index:100;background-color:#32c7e7;color:#ffffff;cursor:pointer;text-align:center;padding:0;'+
                      style_mobile_banner_open
                      +'"  href="javascript:openOrInstall()" id="mobile_banner_open">'+'VIEW'+'</a>'+                                                    
              '</td>' +
              '<td style="padding:0;margin:0;width:1.5em;border:0px none;vertical-align: middle;line-height:normal;"></td>' +
             '</tr>' +
            '</table>';
    	bannerHeight = tapatalk_logo_height + 3 * 8 * bannerScale; 
    }
    else 
    {
    	tapatalk_logo_height = 8 * 8 * bannerScale * 0.67;
    	bannerHeight = tapatalk_logo_height + 1.5 * 8 * bannerScale; 
    	appBanner.innerHTML = 
            '<table class="mobile_banner_inner" style="border-width:0;background-color:#f2f2f2;table-layout:auto;margin:0;width:auto;border-collapse:separate;padding:0.75em 0;position:relative;margin-left:auto;margin-right:auto;line-height:normal;border:0px none;vertical-align: middle;" align="center" cellpadding="0" cellspacing="0" border="0"  id="mobile_banner_inner" >' +
	           '<tr style="border:0px none;padding:0;margin:0;">'+   
              '<td style="padding:0;margin:0;width:0.8em; border:0px none;vertical-align: middle;line-height:normal;"> ' +
	              '<div onclick="closeBanner()" id="mobile_banner_close" style="cursor:pointer;text-align:right;margin:0;padding:0;overflow:hidden;color:rgb(121,121,121);"><img style="width:0.8em;opacity:0.5;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAx0lEQVR4AdXXMQqDQBCGUSvvkjQ5iOBFhYCQy1mZLRZsBh/8zSbCV8nsa0TH6aeu1/Mxt7bW0prCln7GXN2vBubW3jpbh/Ab9Ohn7BV+h57AifZKHKhxozUOVLjRGr/gDQPCV6K9bk0YBJ7Nrh3OD0jRC07xAC1g4MyoYOMxath4iho2HqCGjRv9S9hoiI9/uHI0w3M0xIe+Mod/JFI0n+UiYDTBN6w+QIl79TFu1HhHtd4aNe71tsA/Ro17oa/xN1Di/oUZ0BcewHSWZrEeJgAAAABJRU5ErkJggg==" /></div></td>' + 
              '<td style="padding:0;margin:0;width:0.5em; border:0px none;vertical-align: middle;line-height:normal;"></td>' + 
              '<td style="padding:0;margin:0;min-width:4.0em;border:0px none;vertical-align: middle;line-height:normal;">' + 
              	'<div id="mobile_banner_logo" style="text-align:left"><img style="max-height:'+ tapatalk_logo_height + 'px" id="mobile_banner_logo_img" src="' + app_board_url + '/' + tapatalk_dir_name + '/smartbanner/images/tapatalk-banner-logo.png' + '"></div>' + 
              '</td>' +
              '<td style="padding:0;margin:0;width:1.0em;border:0px none;vertical-align: middle;line-height:normal;"></td>' + 
              '<td style="padding:0;margin:0;min-width:11em;border:0px none;vertical-align: middle;line-height:normal;">' + 
              	'<table style="border-width:0;table-layout:auto;background-color:#f2f2f2;padding:0;margin:0;min-width:11em;border-collapse:separate;" cellpadding="0" cellspacing="0" border="0">' + 
              		'<tr style="padding:0;margin:0;border:0px none;">'+
              			'<td style="padding:0;margin:0;border:0px none;vertical-align: middle;line-height:normal;">' + 
              				'<div style="' + style_mobile_banner_heading + 'font-size:1.1em" >'+app_banner_message+'</div>'+
              			'</td>'+
              		'</tr>' + 
              		'<tr style="padding:0;margin:0;border:0px none;">'+
              			'<td style="padding:0;margin:0;border:0px none;vertical-align: middle;line-height:normal;">' + 
              				'<div><img style="max-width:5.0em;max-height:1em" src="' + app_board_url + '/' + tapatalk_dir_name + '/smartbanner/images/star.png' + '"></div>'+
              			'</td>'+
              		'</tr>' + 
              		'<tr style="padding:0;margin:0;border:0px none;">'+
              			'<td style="padding:0;margin:0;border:0px none;vertical-align: middle;line-height:normal;">' + 
              				'<div style="' + style_mobile_banner_app_desc + 'font-size:1.0em" >'+app_desc+'</div>'+
              			'</td>'+
              		'</tr>' + 
              	 '</table>' + 
              '</td>' +
              '<td style="padding:0;margin:0;width:1.0em;border:0px none;vertical-align: middle;line-height:normal;"></td>' +
              '<td style="padding:0;margin:0;width:5.0em;border:0px none;vertical-align: middle;line-height:normal;">' +
                     '<a style="display: inline-block;width:100%;text-decoration:none;font-size:1.2em;line-height:2.2em;margin:0;position:relative;border-radius:0.2em;z-index:100;background-color:#32c7e7;color:#ffffff;cursor:pointer;text-align:center;padding:0;'+
                      style_mobile_banner_open
                      +'"  href="javascript:openOrInstall()" id="mobile_banner_open">'+'VIEW'+'</a>'+                                                    
              '</td>' +
              '<td style="padding:0;margin:0;width:1.0em;border:0px none;vertical-align: middle;line-height:normal;"></td>' +
             '</tr>' +
            '</table>';
    }
    
    bodyItem.insertBefore(appBanner, bodyItem.firstChild)    
    setFontSize(1)
    
    resetBannerStyle();
       
    if(navigator.userAgent.match(/chrome/i) && is_android)
	{
		open_or_install_button = document.getElementById("mobile_banner_open");
		version = parseInt(window.navigator.appVersion.match(/Chrome\/(\d+)\./i)[1], 10);
		if(version > 25)
		{
			banner_location_url = "intent://scan/#Intent;scheme=" + banner_location_url + ";package=com.quoord.tapatalkpro.activity;end";
			open_or_install_button.href = banner_location_url;
		}
	}
    //Detect whether device supports orientationchange event, otherwise fall back to
    var supportsOrientationChange = "onorientationchange" in window,
        orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";
      
    window.addEventListener(orientationEvent, function() {
    	getBannerScale();
    	tapatalk_logo_height = 8 * 8 * bannerScale;
    	setFontSize(1);
    	bannerLogo = document.getElementById("mobile_banner_logo_img");
    	bannerDiv = document.getElementById("banner_div_empty");
    	bannerLogo.style.height = tapatalk_logo_height + 'px';
    	bannerHeight = appBanner.clientHeight;
    	bannerDiv.style.height = bannerHeight + "px";
    })
}

function setFontSize(Scale)
{
	if (bannerScale > 1) {
        appBanner.style.fontSize = (8*bannerScale*Scale)+"px";
        tables = appBanner.getElementsByTagName("table");
        for(var i=0;i < tables.length;i++){
        	table = tables[i];
        	table.style.fontSize = (8*bannerScale*Scale)+"px";
        	tds = table.getElementsByTagName("td");
        	for(var j=0;j < tds.length;j++){
        		tds[j].style.fontSize = (8*bannerScale*Scale)+"px";
        	}
        } 
    }
}

function getBannerScale()
{
	bannerScale = document.body.clientWidth / window.screen.width
	if(bannerScale == 1 || isMobileStyle())
	{
		bannerScale = 1.5;
		return;
	}
    if (bannerScale < 1.5 || (is_mobile_skin && navigator.userAgent.match(/mobile/i))) bannerScale = 1.5;
	
    // mobile portrait mode may need bigger scale
    if (window.innerWidth < window.innerHeight)
    {
        if (bannerScale < 2.0 && !is_mobile_skin && document.body.clientWidth > 600) {
            bannerScale = 1.5
        }
        else if(bannerScale < 2.5) {
        	bannerScale = 2.0
        }
    }
    else
    {
        if (navigator.userAgent.match(/mobile/i) && bannerScale < 1.5 && !is_mobile_skin && document.body.clientWidth > 600) {
            bannerScale = 1.5
        } 
    }
    
    if(bannerScale > 2.5) bannerScale = 2.5;
}

function isMobileStyle()
{
	/*check if is mobile style*/
    metas = document.getElementsByTagName( "meta" );
    var is_mobile_style = false
    for(i = 0; i < metas.length; i++)
    {
    	if(metas[i].name && metas[i].name.toLowerCase() == 'viewport' )
    	{
    		meta_content = metas[i].content;
    		re = /width\s?=\s?device\-width/i;
    		if((re.test(meta_content)))
    		{
    			is_mobile_style = true;
    		}
    	}
    }
    if(document.body.clientWidth < 600)
    {
    	is_mobile_style = true;
    }
    return is_mobile_style;
}

function openOrInstall()
{
	iframe = document.createElement("iframe");	
	iframe.id = 'open_in_app';
	document.body.insertBefore(iframe, bodyItem.firstChild);
	iframe.style.display = "none";		
	iframe.src =  banner_location_url;
	setTimeout(function(){
		window.location.href = app_install_url;
	},1);	
}

function resetBannerTop()
{
    if (getComputedStyle(bodyItem, null).position !== 'static' || document.getElementById('google_translate_element'))
        appBanner.style.top = '-'+bannerTop
}

function closeBanner()
{
	bannerDiv = document.getElementById("banner_div_empty");
    bodyItem.removeChild( appBanner );
    bodyItem.removeChild( bannerDiv );
    setBannerCookies('banner-closed', 'true', 90);
}

function setBannerCookies(name, value, exdays)
{
    var exdate = new Date();
    exdate.setDate(exdate.getDate()+exdays);
    value=escape(value)+((exdays==null)?'':'; expires='+exdate.toUTCString());
    document.cookie=name+'='+value+'; path=/;';
}

add_app_event(gestureChangeListener);

function gestureChangeListener()
{
	appBanner = document.getElementById("tt_mobile_banner");
	if(appBanner == undefined)
	{
		return;
	}
	document.addEventListener("touchmove", touchMove, false);
	document.addEventListener("touchend", touchEnd, false);
	touchEnd();
}

function touchMove()
{
	touchEnd();
}

function touchEnd()
{
	resetBannerStyle();
}

function resetBannerStyle()
{
	appBanner = document.getElementById("tt_mobile_banner");
	if(appBanner == undefined)
	{
		return;
	}
	Scale = window.innerWidth / document.body.clientWidth ;
	if(Scale > 1)
	{
		Scale = 1;
	} 
	
	setFontSize(Scale);
	newBannerHeight = bannerHeight * Scale; 
	bannerDiv = document.getElementById("banner_div_empty");
	bannerLogo = document.getElementById("mobile_banner_logo_img");
	bannerLogo.style.height = tapatalk_logo_height * Scale + 'px';
	if(bannerDiv == undefined)
	{
		bannerDiv = document.createElement("div");  
		bannerDiv.style.margin = 0;
	    bannerDiv.style.padding = 0;
	    bannerDiv.id = "banner_div_empty";
	    document.body.insertBefore(bannerDiv, bodyItem.firstChild);
	}
	
    bannerDiv.style.height = newBannerHeight + "px";
}
