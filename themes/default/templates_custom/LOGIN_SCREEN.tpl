{$REQUIRE_JAVASCRIPT,checking}

<div data-tpl="loginScreen">
	{TITLE}

	{$SET,login_screen,1}

	<div class="login-page">
		{+START,IF_NON_EMPTY,{$CONFIG_OPTION,facebook_appid}}{+START,IF,{$CONFIG_OPTION,facebook_allow_signups}}
			{+START,IF_EMPTY,{$FB_CONNECT_UID}}
				<h2>{!facebook:LOGIN_FACEBOOK_HEADER}</h2>

				<p>{!facebook:LOGIN_USING_FACEBOOK}</p>

				<div class="fb-login-button" data-width="200" data-max-rows="1" data-scope="email{$,Asking for this stuff is now a big hassle as it needs a screencast(s) making: user_birthday,user_about_me,user_hometown,user_location,user_website}{+START,IF,{$CONFIG_OPTION,facebook_auto_syndicate}},publish_pages{+END}"></div>

				<p>{!facebook:IF_NO_BUTTON_LOGIN_FIRST}</p>
			{+END}

			<h2>{!facebook:LOGIN_NATIVE_HEADER,{$SITE_NAME*}}</h2>
		{+END}{+END}

		<div class="box box---login-screen"><div class="box-inner">
			{!LOGIN_TEXT,<a href="{JOIN_URL*}"><strong>{!JOIN_HERE}</strong></a>}
		</div></div>

		<form title="{!_LOGIN}" class="js-submit-check-username-for-blankness" action="{LOGIN_URL*}" method="post" autocomplete="on">
			<div>
				{$INSERT_SPAMMER_BLACKHOLE}

				<input type="hidden" name="_active_login" value="1" />

				{PASSION}

				<div class="clearfix">
					<table class="map-table autosized-table login-page-form">
						<tbody>
							<tr>
								<th class="de-th"><label for="login_username">{$LOGIN_LABEL}:</label></th>
								<td>
									<input maxlength="80" type="text" value="{USERNAME*}" id="login_username" class="form-control" name="username" autocomplete="username" size="25" />
								</td>
							</tr>
							<tr>
								<th class="de-th"><label for="password">{!PASSWORD}:</label></th>
								<td>
									<input maxlength="255" type="password" id="password" class="form-control" name="password" autocomplete="current-password" size="25" />
								</td>
							</tr>
						</tbody>
					</table>

					{+START,IF,{$CONFIG_OPTION,password_cookies}}
						<div class="login-page-options">
							<p>
								<label for="remember">
									<input class="{+START,IF,{$NOT,{$CONFIG_OPTION,remember_me_by_default}}}js-click-confirm-remember-me{+END}" id="remember" type="checkbox" value="1" name="remember"{+START,IF,{$CONFIG_OPTION,remember_me_by_default}} checked="checked"{+END} />
									<span class="field-name">{!REMEMBER_ME}</span>
								</label>
								<span class="associated-details">{!REMEMBER_ME_TEXT}</span>
							</p>

							{+START,IF,{$CONFIG_OPTION,is_on_invisibility}}
								<p>
									<label for="login_invisible">
										<input id="login_invisible" type="checkbox" value="1" name="login_invisible" />
										<span class="field-name">{!INVISIBLE}</span>
									</label>
									<span class="associated-details">{!INVISIBLE_TEXT}</span>
								</p>
							{+END}
						</div>
					{+END}
				</div>

				<p class="proceed-button">
					<button class="btn btn-primary btn-scr menu--site-meta--user-actions--login" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/login{+END} {!_LOGIN}</button>
				</p>
			</div>
		</form>

		{+START,IF_NON_EMPTY,{EXTRA}}
			<p class="login-note">
				{EXTRA}
			</p>
		{+END}
	</div>
</div>
