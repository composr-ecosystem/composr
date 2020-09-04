{+START,SET,login_supplemental}
	<h2>{!hybridauth:LOGIN_PROVIDER_HEADER}</h2>

	<div class="hybridauth-login-screen-buttons">
		{$HYBRIDAUTH_BUTTONS}
	</div>

	<h2>{!hybridauth:LOGIN_NATIVE_HEADER,{$SITE_NAME*}}</h2>
{+END}

{+START,INCLUDE,LOGIN_SCREEN}{+END}
