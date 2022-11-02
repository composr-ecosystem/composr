{+START,IF,{$NOR,{$GET,login_screen},{$MATCH_KEY_MATCH,_WILD:login}}}
	<section class="box box---block-side-personal-stats-no" data-tpl="blockSidePersonalStatsNo"><div class="box-inner">
		{+START,IF_NON_EMPTY,{TITLE}}<h3>{TITLE}</h3>{+END}

		<form title="{!_LOGIN}" action="{LOGIN_URL*}" method="post" autocomplete="on">
			<input type="hidden" name="_active_login" value="1" />

			<div>
				<div>
					<div class="accessibility-hidden"><label for="ps-login-username">{$LOGIN_LABEL}</label></div>
					<input maxlength="80" class="form-control form-control-wide login-block-username" type="text" placeholder="{!USERNAME}" id="ps-login-username" name="username" autocomplete="username" />
				</div>
				<div>
					<div class="accessibility-hidden"><label for="ps-password">{!PASSWORD}</label></div>
					<input maxlength="255" class="form-control form-control-wide" type="password" placeholder="{!PASSWORD}" name="password" autocomplete="current-password" id="ps-password" />
				</div>

				{+START,IF,{$OR,{$CONFIG_OPTION,is_on_invisibility},{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_off,default_on}}}
					<div class="login-block-cookies">
						{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_off,default_on}}
							<div class="clearfix">
								<label for="ps-remember">{!REMEMBER_ME}</label>
								<input type="checkbox" id="ps-remember" name="remember" value="1"{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_on}} checked="checked"{+END} class="{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},default_off}}js-click-checkbox-remember-me-confirm{+END}" />
							</div>
						{+END}

						{+START,IF,{$CONFIG_OPTION,is_on_invisibility}}
							<div class="clearfix">
								<label for="login_invisible">{!INVISIBLE}</label>
								<input type="checkbox" value="1" id="login_invisible" name="login_invisible" />
							</div>
						{+END}
					</div>
				{+END}
				{+START,IF,{$EQ,{$CONFIG_OPTION,remember_me_behaviour},always_on}}
					<input type="hidden" name="remember" value="1" />
				{+END}

				<p class="proceed-button">
					<button class="btn btn-primary btn-scri menu--site-meta--user-actions--login js-check-login-username-field" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/login{+END} <span>{!_LOGIN}</span></button>
				</p>
			</div>
		</form>

		<ul class="horizontal-links associated-links-block-group force-margin">
			{+START,IF_NON_EMPTY,{JOIN_URL}}<li><a href="{JOIN_URL*}">{!_JOIN}</a></li>{+END}
			<li><a data-open-as-overlay="{}" rel="nofollow" href="{FULL_LOGIN_URL*}" title="{!MORE}: {!_LOGIN}">{!MORE}</a></li>
		</ul>

		{$GET,side_personal_stats_supplemental}
	</div></section>
{+END}
