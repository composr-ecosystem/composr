<?php

/*CQC: No API check*/
/*CQC: No check*/

$app_head_include = '';

// don't include it when the request was from inside app
$in_app = tt_getenv('HTTP_IN_APP');
$referer = tt_getenv('HTTP_REFERER');
if ($in_app || preg_match('#^https?://link.tapatalk.com#i', $referer))
    return;


if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['REQUEST_URI']))
{
    $app_referer = (tt_is_https() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
else
    $app_referer = $board_url;


// for those forum system which can not add js in html body, please set $functionCallAfterWindowLoad as 1
$functionCallAfterWindowLoad = isset($functionCallAfterWindowLoad) && $functionCallAfterWindowLoad ? 1 : 0;

$app_ios_id_default = '307880732';      // Tapatalk 1, 585178888 for Tapatalk 2
$app_ios_hd_id_default = '307880732';   // Tapatalk 1, 481579541 for Tapatalk HD
$app_android_id_default = 'com.quoord.tapatalkpro.activity';

$app_location_url = isset($app_location_url) && preg_match('#^tapatalk://#i', $app_location_url) ? $app_location_url : 'tapatalk://';
$app_location_url_byo = str_replace('tapatalk://', 'tapatalk-byo://', $app_location_url);
$tapatalk_dir_url = isset($tapatalk_dir_url) && $tapatalk_dir_url ? $tapatalk_dir_url : './mobiquo';
$mobiquo_extension = isset($mobiquo_extension) && $mobiquo_extension ? $mobiquo_extension : 'php';
$app_forum_name = isset($app_forum_name) && $app_forum_name ? html_entity_decode($app_forum_name) : 'this forum';
$board_url = isset($board_url) ? preg_replace('#/$#', '', trim($board_url)) : '';

$app_ios_id = isset($app_ios_id) && intval($app_ios_id) ? intval($app_ios_id) : '';
$app_android_id = isset($app_android_id) && $app_android_id ? preg_replace('/^.*?details\?id=([^\s,&]+).*?$/si', '$1', $app_android_id) : '';
$app_kindle_url = isset($app_kindle_url) ? $app_kindle_url : '';
$app_banner_message = isset($app_banner_message) && $app_banner_message && ($app_ios_id || $app_android_id) ? preg_replace('/\r\n|\n|\r/si', '<br />', $app_banner_message) : '';
$is_mobile_skin = isset($is_mobile_skin) && $is_mobile_skin ? 1 : 0;

$twitterfacebook_card_enabled = !isset($twitterfacebook_card_enabled) ? true : $twitterfacebook_card_enabled;
$twc_title = isset($twc_title) && $twc_title ? $twc_title : '';
$twc_description = isset($twc_description) && $twc_description ? $twc_description : $app_forum_name;
$twc_image = isset($twc_image) && $twc_image ? $twc_image : '';

// valid page_type: index, forum, topic, post, pm, search, profile, online, other
$page_type = isset($page_type) && $page_type ? $page_type : 'other';

$is_byo = $app_ios_id && $app_ios_id != -1 || $app_android_id && $app_android_id != -1 || $app_kindle_url && $app_kindle_url != -1 ? 1 : 0;

// add google/facebook/twitter meta
$host_path = preg_replace('#tapatalk://#si', '', $app_location_url);
if (in_array($page_type, array('topic', 'post', 'home')) && $host_path && !$is_byo)
{
    // display google app indexing meta
    $app_head_include .= '
    <!-- App Indexing for Google Search -->
    <link href="android-app://com.quoord.tapatalkpro.activity/tapatalk/'.tt_html_escape($host_path).'" rel="alternate" />
    <link href="ios-app://307880732/tapatalk/'.tt_html_escape($host_path).'" rel="alternate" />
    ';

    if ($twitterfacebook_card_enabled)
    {
        // display facebook deeping link
        $app_head_include .= '
        <meta property="al:android:package" content="'.tt_html_escape($app_android_id_default).'" />
        <meta property="al:android:url" content="'.tt_html_escape($app_location_url).'" />
        <meta property="al:android:app_name" content="Tapatalk" />
        <meta property="al:ios:url" content="'.tt_html_escape($app_location_url).'" />
        <meta property="al:ios:app_store_id" content="'.tt_html_escape($app_ios_id_default).'" />
        <meta property="al:ios:app_name" content="Tapatalk" />
        ';

        // display twitter card
        $app_head_include .= '
        <!-- twitter app card start-->
        <!-- https://dev.twitter.com/docs/cards/types/app-card -->
        <meta name="twitter:card" content="summary" />
        <meta name="twitter:site" content="@tapatalk" />
        <meta name="twitter:title" content="'.tt_html_escape($twc_title,true).'" />
        <meta name="twitter:description" content="'.tt_html_escape($twc_description,true).'" />
        ';

        if ($twc_image)
            $app_head_include .= '<meta name="twitter:image" content="'.tt_html_escape($twc_image,true).'" />';

        $app_head_include .= '
        <meta name="twitter:app:id:iphone" content="'.tt_html_escape($app_ios_id ? $app_ios_id : $app_ios_id_default).'" />
        <meta name="twitter:app:url:iphone" content="'.tt_html_escape($app_ios_id ? $app_location_url_byo : $app_location_url).'" />
        <meta name="twitter:app:id:ipad" content="'.tt_html_escape($app_ios_id ? $app_ios_id : $app_ios_hd_id_default).'" />
        <meta name="twitter:app:url:ipad" content="'.tt_html_escape($app_ios_id ? $app_location_url_byo : $app_location_url).'" />
        <meta name="twitter:app:id:googleplay" content="'.tt_html_escape($app_android_id ? $app_android_id : $app_android_id_default).'" />
        <meta name="twitter:app:url:googleplay" content="'.tt_html_escape($app_android_id ? $app_location_url_byo : $app_location_url).'" />
        <!-- twitter app card -->
        ';
    }
}

// don't include it when the request was not from mobile device
$useragent = tt_getenv('HTTP_USER_AGENT');
if (!preg_match('/iPhone|iPod|iPad|Silk|Android|IEMobile|Windows Phone|Windows RT.*?ARM/i', $useragent))
    return;

// don't show welcome page and banner for googlebot or twitterbot
if (preg_match('/googlebot|twitterbot/i', $useragent))
    return;

// display smart banner and welcome page
$app_banner_head = '';
$tapatalk_dir_name = isset($tapatalk_dir_name) ? $tapatalk_dir_name : basename(dirname(__DIR__));
if (file_exists(__DIR__ . '/appbanner.js') &&
    file_exists(__DIR__ . '/app.php') &&
    file_exists(__DIR__ . '/appbanner.css'))
{
    if(!$is_byo)
    {
        //$app_banner_css_link = $tapatalk_dir_url . '/' . tt_html_escape($tapatalk_dir_name) . '/smartbanner/appbanner.css';
        $app_banner_js_link = $tapatalk_dir_url . '/' . tt_html_escape($tapatalk_dir_name) .'/smartbanner/appbanner.js';
    }
    else
    {
        $app_banner_css_link = 'https://welcome-screen.tapatalk-cdn.com/appbanner.min.css';
        $app_banner_js_link = 'https://welcome-screen.tapatalk-cdn.com/appbanner.min.js';
    }

    if (isset($_GET['display_banner']) && $_GET['display_banner'])
    {
        $app_banner_enable = 1;
        $app_ads_enable = 0;
    }

    $app_banner_head = '
        <!-- Tapatalk Banner&Welcome head start -->
        ' . ($is_byo ? '<link href="'.tt_html_escape($app_banner_css_link).'" rel="stylesheet" type="text/css" media="screen" />' : '') . '
        <script ' . csp_nonce_html() . '>
            var is_byo             = '.$is_byo.';
            var is_mobile_skin     = '.$is_mobile_skin.';
            var app_ios_id         = "'.$app_ios_id.'";
            var app_android_id     = "'.tt_html_escape($app_android_id).'";
            var app_kindle_url     = "'.tt_html_escape(urlencode($app_kindle_url),true).'";
            var app_banner_message = "'.tt_html_escape($app_banner_message,true).'";
            var app_forum_name     = "'.tt_html_escape($app_forum_name,true).'";
            var app_location_url   = "'.tt_html_escape($app_location_url,true).'";
            var app_board_url      = "'.tt_html_escape($board_url,true).'";
            var mobiquo_extension  = "'.tt_html_escape($mobiquo_extension,true).'";
            var functionCallAfterWindowLoad = '.$functionCallAfterWindowLoad.';

            var app_api_key        = "'.(trim($api_key) ? md5(trim($api_key)) : '').'";
            var app_referer        = "'.tt_html_escape($app_referer,true).'";
            var tapatalk_dir_name  = "'.tt_html_escape($tapatalk_dir_name,true).'";
            var app_welcome_enable = '.(!isset($app_ads_enable) || $app_ads_enable ? 1 : 0).';
            var app_banner_enable  = '.(!isset($app_banner_enable) || $app_banner_enable ? 1 : 0).';
        </script>
        <script src="' . tt_html_escape($app_banner_js_link) . '"></script>
        <!-- Tapatalk Banner head end-->
    ';
}

$app_head_include .= $app_banner_head;

function tt_getenv($key)
{
    $return = '';

    if ( is_array( $_SERVER ) && isset( $_SERVER[$key] ) && $_SERVER[$key])
    {
        $return = $_SERVER[$key];
    }
    else
    {
        $return = getenv($key);
    }

    return $return;
}

function tt_is_https()
{
    return (isset($_SERVER['HTTPS']) && trim($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
}

function tt_html_escape($str, $special = false)
{
    $str = addslashes(htmlspecialchars($str, ENT_NOQUOTES, "UTF-8"));
    if($special)
    {
        $str = str_replace('&amp;', '&', $str);
    }
    return $str;
}
