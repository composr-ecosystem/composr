{TITLE}

{$SET,login_screen,1}

<div class="login_page">
	{+START,IF,{$HAS_FORUM,1}}
		<div class="box box___login_screen"><div class="box_inner">
			{!LOGIN_TEXT,<a href="{JOIN_URL*}"><strong>{!JOIN_HERE}</strong></a>}
		</div></div>
	{+END}

	<form title="{!_LOGIN}" onsubmit="if (check_field_for_blankness(this.elements['login_username'],event)) { disable_button_just_clicked(this); return true; } return false;" action="{LOGIN_URL*}" method="post" target="_top" autocomplete="on">
		<div>
			{PASSION}

			<div class="float_surrounder">
				{+START,IF,{$MOBILE}}
					<div class="login_page_form">
						<p class="constrain_field">
							<label for="login_username">{$LOGIN_LABEL}</label>
							<input maxlength="80" class="wide_field" type="text" value="{USERNAME*}" id="login_username" name="login_username" size="25" />
						</p>

						<p class="constrain_field">
							<label for="password">{!PASSWORD}</label>
							<input maxlength="255" class="wide_field" type="password" id="password" name="password" size="25" />
						</p>
					</div>
				{+END}

				{+START,IF,{$NOT,{$MOBILE}}}
					<table class="map_table autosized_table login_page_form">
						<tbody>
							<tr>
								<th class="de_th"><label for="login_username">{$LOGIN_LABEL}</label>:</th>
								<td>
									<input maxlength="80" type="text" value="{USERNAME*}" id="login_username" name="login_username" size="25" />
								</td>
							</tr>
							<tr>
								<th class="de_th"><label for="password">{!PASSWORD}</label>:</th>
								<td>
									<input maxlength="255" type="password" id="password" name="password" size="25" />
								</td>
							</tr>
						</tbody>
					</table>
				{+END}

				{+START,IF,{$CONFIG_OPTION,password_cookies}}
					<div class="login_page_options">
						<p>
							<label for="remember">
							  <input id="remember" type="checkbox" value="1" name="remember"{+START,IF,{$OR,{$EQ,{$_POST,remember},1},{$CONFIG_OPTION,remember_me_by_default}}} checked="checked"{+END}{+START,IF,{$NOT,{$CONFIG_OPTION,remember_me_by_default}}} onclick="if (this.checked) { var t=this; window.fauxmodal_confirm('{!REMEMBER_ME_COOKIE;}',function(answer) { if (!answer) t.checked=false; }); }"{+END} />
							  <span class="field_name">{!REMEMBER_ME}</span>
							</label>
							<span class="associated_details">{!REMEMBER_ME_TEXT}</span>
						</p>

						{+START,IF,{$CONFIG_OPTION,is_on_invisibility}}
							<p>
								<label for="login_invisible">
									<input id="login_invisible" type="checkbox" value="1" name="login_invisible" />
									<span class="field_name">{!INVISIBLE}</span>
								</label>
								<span class="associated_details">{!INVISIBLE_TEXT}</span>
							</p>
						{+END}
					</div>
				{+END}
			</div>

			<p class="proceed_button">
				<input class="button_screen menu__site_meta__user_actions__login" type="submit" value="{!_LOGIN}" />
			</p>
		</div>
	</form>

	{+START,IF_NON_EMPTY,{EXTRA}}
		<p class="login_note">
			{EXTRA}
		</p>
	{+END}
</div>

<script>// <![CDATA[
	add_event_listener_abstract(window,'real_load',function() {
		if ((typeof document.activeElement=='undefined') || (document.activeElement!=document.getElementById('password')))
		{
			try
			{
				document.getElementById('login_username').focus();
			}
			catch (e){};
		}
	});
//]]></script>
