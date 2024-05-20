{$REQUIRE_JAVASCRIPT,checking}

{+START,IF,{$NOR,{$GET,login_screen},{$MATCH_KEY_MATCH,_WILD:login}}}
	<div data-tpl="blockTopLogin">
		<form title="{!_LOGIN}" action="{LOGIN_URL*}" method="post" class="form-inline top-login" autocomplete="on">
			<input type="hidden" name="_active_login" value="1" />

			{+START,IF,{$DESKTOP}}{+START,IF,{$NOT,{$CONFIG_OPTION,single_public_zone}}}{$,Hide login form when having single public zone to make space for navigation menu}
				<div class="top-login-controls desktop-only">
					<div class="accessibility-hidden"><label for="s-login-username">{$LOGIN_LABEL}</label></div>
					<input maxlength="80" size="10" accesskey="l" type="text" placeholder="{!USERNAME}" id="s-login-username" name="username" autocomplete="username" class="form-control" />
					<div class="accessibility-hidden"><label for="s-password">{!PASSWORD}</label></div>
					<input maxlength="255" size="10" type="password" placeholder="{!PASSWORD}" name="password" autocomplete="current-password" id="s-password" class="form-control" />

					{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_off,default_on}}
						<label class="accessibility-hidden" for="s-remember">{!REMEMBER_ME}</label>
						<input type="checkbox" id="s-remember" name="remember" value="1" title="{!REMEMBER_ME}"{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_on}} checked="checked"{+END} class="{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_off}}js-click-confirm-remember-me{+END}" />
					{+END}
					{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},always_on}}
						<input type="hidden" name="remember" value="1" />
					{+END}

					<button class="btn btn-primary menu--site-meta--user-actions--login js-top-login" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/login{+END} <span>{!_LOGIN}</span></button>
				</div>
			{+END}{+END}

			<ul class="horizontal-links with-icons block-top-login-links">
				{+START,IF_NON_EMPTY,{JOIN_URL}}<li class="li-join"><a href="{JOIN_URL*}">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/join{+END}<span class="li-join-text">{!_JOIN}</span></a></li>{+END}
				<li class="li-login"><a data-open-as-overlay="{}" rel="nofollow" href="{FULL_LOGIN_URL*}" title="{!MORE}: {!_LOGIN}">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/login{+END} {+START,IF,{$DESKTOP}}<span class="desktop-only">{$?,{$CONFIG_OPTION,single_public_zone},{!_LOGIN},{!OPTIONS}}</span>{+END}<span class="li-login-text mobile-only">{!_LOGIN}</span></a></li>
			</ul>
		</form>
	</div>
{+END}
