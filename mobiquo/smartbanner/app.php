<?php

/*CQC: No API check*/
/*CQC: No check*/

defined('IN_MOBIQUO') or exit;
error_reporting(0);

if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SCRIPT_NAME']))
    $default_board_url = (tt_is_https() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . str_replace(DIRECTORY_SEPARATOR, '/', dirname($_SERVER['SCRIPT_NAME'], 2));
else
    $default_board_url = '';

$title = isset($_GET['name']) ? $_GET['name'] : 'Stay in touch with us via Tapatalk app';
$name = isset($_GET['name']) ? $_GET['name'] : 'online forums';
$code = isset($_GET['code']) ? $_GET['code'] : '';

$board_url = isset($_GET['board_url']) && trim($_GET['board_url']) ? trim($_GET['board_url']) : '';
if (($board_url && $default_board_url && parse_url($board_url, PHP_URL_HOST) != $_SERVER['HTTP_HOST']) || empty($board_url))
{
    $board_url = $default_board_url;
}

$redirect_url = isset($_GET['referer']) && trim($_GET['referer']) ? trim($_GET['referer']) : '';
if ($redirect_url && $default_board_url && parse_url($redirect_url, PHP_URL_HOST) != $_SERVER['HTTP_HOST'])
{
    $redirect_url = $default_board_url;
}

$deeplink = isset($_GET['deeplink']) ? $_GET['deeplink'] : $board_url;

if (!preg_match('#^https?://#si', $redirect_url)) $redirect_url = '/';

$banner_image_path = 'smartbanner/images/';
$image_list = array(
    'wrt-v-bg.jpg', 'wrt-h-bg.jpg',
    'wp-v-bg.jpg', 'wp-h-bg.jpg',
    'pad-v-bg.jpg', 'pad-h-bg.jpg',
    'iphone-v-bg.jpg', 'iphone-h-bg.jpg',
    'ipad-v-bg.jpg', 'ipad-h-bg.jpg',
    'android-v-bg.jpg', 'android-h-bg.jpg',
    'close.png', 'logo.png',
    'tapatalk-banner-logo.png', 'star.png'
);

foreach($image_list as $image)
{
    if (!file_exists('smartbanner/images/'.$image))
    {
        $banner_image_path = 'http://dw0bg18yn71wc.cloudfront.net/images/';
    }
}

function tt_is_https()
{
    return (isset($_SERVER['HTTPS']) && trim($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?= htmlspecialchars($title, ENT_QUOTES, "UTF-8") ?></title>
<meta name="format-detection" content="telephone=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="white" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://welcome-screen.tapatalk-cdn.com/welcome_screen.css"/>
<script>
    var banner_image_path = '<?= addslashes($banner_image_path) ?>';
    var forum_name = '<?= addslashes(htmlspecialchars($name, ENT_QUOTES, "UTF-8")) ?>';
    var app_api_key = '<?= addslashes(htmlspecialchars($code, ENT_QUOTES, "UTF-8")) ?>';
    var app_deep_link = '<?= addslashes(htmlspecialchars($deeplink, ENT_QUOTES, "UTF-8")) ?>';
    var banner_redirect_url = '<?= addslashes(htmlspecialchars($redirect_url, ENT_QUOTES, "UTF-8")) ?>';
    
    // ---- tapstream track ----
    var _tsq = _tsq || [];
    _tsq.push(["setAccountName", "tapatalk"]);
    _tsq.push(["setPageUrl", document.location.protocol + "//" + document.location.hostname]);
    // "key" and "value" will appear as custom_parameters in the JSON that the app receives
    // from the getConversionData callback. Set it to something like "forum-id", "123456".
    _tsq.push(["addCustomParameter", "key", app_api_key]);
    _tsq.push(["addCustomParameter", "referer", app_deep_link]);
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
</script>
<script src="https://welcome-screen.tapatalk-cdn.com/welcome2.js" charset="UTF-8"></script>
<script>
    $(document).ready(function()
    {
        $("body").html(body);
        check_device();
        // Detect whether device supports orientationchange event, otherwise fall back to
        // the resize event.
        var supportsOrientationChange = "onorientationchange" in window,
            orientationEvent = supportsOrientationChange ? "orientationchange" : "resize";
          
        window.addEventListener(orientationEvent, function() {
            check_device();
            $("#close_icon img").click(function() {
                localStorage.hide = true;
                window.location.href='<?= addslashes(htmlspecialchars($redirect_url, ENT_QUOTES, "UTF-8")) ?>';
            });
        }, false);

        $("#web_bg img").css("max-height",$(window).height() + 'px');
        //$("body").height(($(window).height()*2- $(document).height() )+ 'px');
        $("#close_icon img").click(function() {
            localStorage.hide = true;
            window.location.href='<?= addslashes(htmlspecialchars($redirect_url, ENT_QUOTES, "UTF-8")) ?>';
        });
        
        $("#button a").attr("href", 'http://tapstream.tapatalk.com/lzzq-1/?__tsid=$TSID&__tsid_override=1&referer='+encodeURIComponent(app_deep_link));
    })
</script>
</head>
<body scroll="no">
</body>
</html>
