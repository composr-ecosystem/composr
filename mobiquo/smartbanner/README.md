# App Banner & Twitter Card Integration


## Normal Usage ##

First you need to upload the 'smartbanner' directory onto your server in tapatalk plugin directory. Normally it should be at 'mobiquo/smartbanner/'

### Include Twitter App Card support

To get your site support twitter card with your App, you just need to include some meta data in your page source head like below.
What you need to change is the parameters in content section. For more details, [check here](https://dev.twitter.com/docs/cards/types/app-card)

    <html>
        <head>
        ...
        <!-- twitter app card start-->
        <!-- https://dev.twitter.com/docs/cards/types/app-card -->
        <meta name="twitter:card"               content="app">
        <meta name="twitter:app:id:iphone"      content="307880732">
        <meta name="twitter:app:url:iphone"     content="tapatalk://support.tapatalk.com/?user_id=169&location=index">
        <meta name="twitter:app:id:ipad"        content="307880732">
        <meta name="twitter:app:url:ipad"       content="tapatalk://support.tapatalk.com/?user_id=169&location=index">
        <meta name="twitter:app:id:googleplay"  content="com.quoord.tapatalkpro.activity">
        <meta name="twitter:app:url:googleplay" content="tapatalk://support.tapatalk.com/?user_id=169&location=index">
        <!-- twitter app card -->
        ...
        </head>
        ...
    </html>
    
The url parameter above for iphone/ipad/android should be the same, and the format follow the [App Scheme Rules](#app-scheme-rules)


### Include App Banner

The App banner is a brief prompt to users on mobile browser that the site has a native App to work with. 
Also it provides a buttion named 'Open In App' to users who already installed the app to open current page inside the app, and another button named 'Install' to redirect users to download the app in store.
Currently the app banner will work on iOS/Android/Windows Phone/Kindle devices.
Simply add two pieces of blow html code in head and body will get everything done. For the banner body part, it's better to add it as earlier as in the body section.

    <html>
        <head>
        ...
        <!-- Tapatalk Banner head start -->
        <link href="mobiquo/smartbanner/appbanner.css" rel="stylesheet" type="text/css" media="screen" />
        <script type="text/javascript">
            var is_mobile_skin     = 0;
            var app_ios_id         = "307880732";
            var app_android_id     = "com.quoord.tapatalkpro.activity";
            var app_kindle_url     = "http://www.amazon.com/gp/mas/dl/android?p=com.quoord.tapatalkpro.activity";
            var app_banner_message = "Follow {your_forum_name} <br /> with {app_name} for [os_platform]";
            var app_forum_name     = "Tapatalk Support";
            var app_location_url   = "tapatalk://support.tapatalk.com/?user_id=169&location=index";
            var functionCallAfterWindowLoad = 0;
            
            var app_referer        = "https://support.tapatalk.com/index.php";
            var tapatalk_dir_name  = "mobiquo";
            var mobiquo_extension  = "php";
            var app_welcome_enable = 1;
        </script>
        <script src="mobiquo/smartbanner/appbanner.js" type="text/javascript"></script>
        <!-- Tapatalk Banner head end-->
        ...
        </head>
        
        <body>
        ...
        <!-- Tapatalk Banner body start -->
            <script type="text/javascript">if (typeof(tapatalkDetect) == "function") tapatalkDetect()</script>
        <!-- Tapatalk Banner body end -->
        ...
        </body>
    </html>

**Parameter Specification**

* `is_mobile_skin`: Specify if it's on a mobile skin. App Banner has a little size adjustment for mobile skin
* `app_ios_id`: Your iOS app id in Apple Store. Set '-1' if your site don't have App for iOS device
* `app_android_id`: Your Android app id in Google Play. Set '-1' if your site don't have App for Android device
* `app_kindle_url`: Your Kindle app url in Amazon store. Set '-1' if your site don't have App for Kindle device
* `app_banner_message`: The message displayed on App Banner. Do not change the [os_platform] tag as it is displayed dynamically based on user's device platform.
* `app_forum_name`: Your forum name
* `app_location_url`: deep-link url associate with 'Open In App' button, check [App Scheme Rules](#app-scheme-rules)
* `functionCallAfterWindowLoad`: For any reason, it's hard to include the body code in page source, set this to '1', and you don't need to add the banner body code any more, the **tapatalkDetect** function will be called after page load, so the banner will be displayed at last. We don't recommend this usage if it's able to add the banner body code.
* `tapatalk_dir_name`: optional, the directory where mobiquo files are, if not included, default as 'mobiquo'
* `mobiquo_extension`: optional, the extension of the mobiquo file, if not included, default as 'php'



## For PHP site only ##

This package provides a simple way to generate banner head html code for php.
Here is the php code samle

    $functionCallAfterWindowLoad = 0;
    
    $app_forum_name = {forum name};
    $board_url = {forum url to root};
    $tapatalk_dir_url = $board_url.'/'.$tapatalk_dir;
    $mobiquo_extension = 'php';
    $api_key = {md5 string of tapatalk api key};
    $is_mobile_skin = {this is on a mobile skin};
    $app_location_url = {page location url with tapatalk scheme rules};
    
    // below fours variables are all optional, defaultly it will use tapatalk app information
    // it's better to add them as plugin options/settins in forum Admin CP, for those forums with BYO app, they can custom the banner with their own app
    $app_banner_message = {banner message};
    $app_ios_id = {ios app id};
    $app_android_id = {android app id};
    $app_kindle_url = {kindle app url};
    $twitterfacebook_card_enabled = {twitter card option status} //optional to enable/disable twitter card, enabled by default
    $twc_title = {page title}
    $twc_description = {page description} // optional
    $twc_image = {page preview image}  // optional

    // for welcome screen
    $app_ads_enable = {welcome screen option status}; // optional, default to be enable
    $app_banner_enable = {smartbanner option status}; // optional, default to be enable
    
    // for google app-indexing
    $page_type = {current page type}; // valid data: home, forum, topic, post, pm, search, profile, online, other
    
    if (file_exists($tapatalk_dir . '/smartbanner/head.inc.php'))
        include($tapatalk_dir . '/smartbanner/head.inc.php');
    
    // you'll get $app_head_include set here and you need add it into html head


In additional, you need add below code at the head of mobiquo/mobiquo.php to get it work:
    define('IN_MOBIQUO', true);
    
    if (isset($_GET['welcome']))
    {
        include('./smartbanner/app.php');
        exit;
    }


## App Scheme Rules

**Format:**  
`scheme`://`url-to-forum-root`/?`user_id`={user-id}&`location`={location}&`fid`={fid}&`tid`={tid}&`pid`={pid}&`uid`={uid}&`mid`={mid}

**URL:**  
* **scheme**: app scheme name, default as 'tapatalk'  
* **url-to-forum-root**: used to search if the forum was in app account/history list. If not, search it in tapatalk/byo App network.

**Params: all params are optional**  
* **user_id**: Indicate app should open the content with which account. When there is no account for this forum, open content as guest.  When the user_id was not in one of the accounts for this forum, app side decide open with which account.  
* **location**: Valid value: `index` `forum` `topic` `post` `profile` `message` `online` `search` `login`. Default as `index`.  
* **fid**: Forum board id. **Required** if location is `forum` `topic` `post`  
* **tid**: Topic id. **Required** if location is `topic` `post`  
* **pid**: Post id. **Required** if location is `post`  
* **uid**: User id. **Required** if location is `profile`  
* **mid**: PM id or Conversation id. **Required** if location is `message`  
* **page**: Page number. **Required** if location is `forum``topic``post`  
* **perpage**: Topic/Post number per-page. **Required** if location is `forum``topic`  
