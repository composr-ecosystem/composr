{$REQUIRE_JAVASCRIPT,facebook_support}

{+START,IF_EMPTY,{$CONFIG_OPTION,facebook_appid}}
	<div id="fb-root"></div>
{+END}

{+START,IF,{$ADDON_INSTALLED,facebook_support}}{+START,IF_NON_EMPTY,{$CONFIG_OPTION,facebook_appid}}
	{$SET,facebook_appid,{$CONFIG_OPTION,facebook_appid}}
	{$SET,fb_connect_finishing_profile,{$FB_CONNECT_FINISHING_PROFILE}}
	{$SET,fb_connect_logged_out,{$FB_CONNECT_LOGGED_OUT}}
	{$SET,fb_connect_uid,{$FB_CONNECT_UID}}
	{$SET,page_link_home,{$PAGE_LINK,:}}
	{$SET,page_link_logout,{$PAGE_LINK,:login:logout}}

	<div id="fb-root" data-tpl="facebookFooter" data-tpl-params="{+START,PARAMS_JSON,facebook_appid,fb_connect_finishing_profile,fb_connect_logged_out,fb_connect_uid,page_link_home,page_link_logout}{_*}{+END}"></div>
{+END}{+END}
