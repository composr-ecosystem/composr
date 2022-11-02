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

		<form title="{!_LOGIN}" action="{LOGIN_URL*}" method="post" target="{TARGET*}" autocomplete="on">
			<div>
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

					{+START,IF,{$OR,{$CONFIG_OPTION,is_on_invisibility},{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_off,default_on}}}
						<div class="login-page-options">
							{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_off,default_on}}
								<p>
									<label for="remember">
										<input type="checkbox" id="remember" name="remember" value="1"{+START,IF,{$OR,{$EQ,{$_POST,remember},1},{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_on}}} checked="checked"{+END} class="{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_off}}js-click-confirm-remember-me{+END}" />
										<span class="field-name">{!REMEMBER_ME}</span>
									</label>
									<span class="associated-details">{!REMEMBER_ME_TEXT}</span>
								</p>
							{+END}

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
					{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},always_on}}
						<input type="hidden" name="remember" value="1" />
					{+END}
				</div>

				<p class="proceed-button">
					<button class="btn btn-primary btn-scr menu--site-meta--user-actions--login js-check-login-username-field" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/login{+END} <span>{!_LOGIN}</span></button>
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
