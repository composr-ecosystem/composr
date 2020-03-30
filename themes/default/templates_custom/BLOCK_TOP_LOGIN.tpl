{+START,IF,{$NOR,{$GET,login_screen},{$MATCH_KEY_MATCH,_WILD:login}}}
	{+START,INCLUDE,BLOCK_TOP_LOGIN}{+END}

	{+START,COMMENT}
		Commented out due to lack of space in default design

		{+START,IF_NON_EMPTY,{$CONFIG_OPTION,facebook_appid}}{+START,IF,{$CONFIG_OPTION,facebook_allow_signups}}
			{+START,IF_EMPTY,{$FB_CONNECT_UID}}
				<div class="fb-login-button" data-scope="email{$,Asking for this stuff is now a big hassle as it needs a screencast(s) making: user_birthday,user_about_me,user_hometown,user_location,user_website}{+START,IF,{$CONFIG_OPTION,facebook_auto_syndicate}},publish_pages{+END}"></div>
			{+END}
		{+END}{+END}
	{+END}
{+END}
