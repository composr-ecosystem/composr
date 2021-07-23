{$SET,hybridauth_buttons,{$HYBRIDAUTH_BUTTONS}}
{+START,IF_NON_EMPTY,{$GET,hybridauth_buttons}}
	{+START,SET,login_supplemental}
		<h2>{!hybridauth:LOGIN_PROVIDER_HEADER}</h2>

		<div class="hybridauth-login-screen-buttons">
			{$GET,hybridauth_buttons}
		</div>

		<h2>{!hybridauth:LOGIN_NATIVE_HEADER,{$SITE_NAME*}}</h2>
	{+END}
{+END}

{+START,INCLUDE,LOGIN_SCREEN}{+END}
