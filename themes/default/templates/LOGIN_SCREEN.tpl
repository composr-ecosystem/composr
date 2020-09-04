{$REQUIRE_JAVASCRIPT,checking}
<div data-tpl="loginScreen">
	{TITLE}

	{$SET,login_screen,1}

	<div class="login-page">
		{$GET,login_supplemental}

		{+START,IF,{$HAS_FORUM,1}}
			<div class="box box---login-screen"><div class="box-inner">
				{!LOGIN_TEXT,<a href="{JOIN_URL*}"><strong>{!JOIN_HERE}</strong></a>}
			</div></div>
		{+END}

		<form title="{!_LOGIN}" class="js-submit-check-login-username-field" action="{LOGIN_URL*}" method="post" target="_top" autocomplete="on">
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
									<input id="remember" type="checkbox" value="1" name="remember"{+START,IF,{$OR,{$EQ,{$_POST,remember},1},{$CONFIG_OPTION,remember_me_by_default}}} checked="checked"{+END} class="{+START,IF,{$NOT,{$CONFIG_OPTION,remember_me_by_default}}}js-click-confirm-remember-me{+END}" />
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
