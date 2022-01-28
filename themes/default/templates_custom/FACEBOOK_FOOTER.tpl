<div id="fb-root"></div>
{+START,IF_NON_EMPTY,{$CONFIG_OPTION,facebook_appid}}
	<script>// <![CDATA[
		facebook_init('{$CONFIG_OPTION;/,facebook_appid}','{$BASE_URL;/}/facebook_connect.php',{$?,{$OR,{$FB_CONNECT_FINISHING_PROFILE},{$FB_CONNECT_LOGGED_OUT}},true,false},{$?,{$IS_EMPTY,{$FB_CONNECT_UID}},null,{$FB_CONNECT_UID}},'{$PAGE_LINK;/,:}','{$PAGE_LINK;/,:login:logout}');
	//]]></script>
	<script async="async" defer="defer" crossorigin="anonymous" src="https://connect.facebook.net/en_US/all.js"></script>
{+END}
