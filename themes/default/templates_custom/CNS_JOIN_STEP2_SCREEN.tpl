{+START,SET,join_supplemental}
	<h2>{!hybridauth:LOGIN_PROVIDER_HEADER}</h2>

	<div class="hybridauth-login-screen-buttons">
		{$HYBRIDAUTH_BUTTONS}
	</div>

	<h2>{!hybridauth:JOIN_NATIVE_HEADER,{$SITE_NAME*}}</h2>
{+END}

{+START,INCLUDE,CNS_JOIN_STEP2_SCREEN}{+END}
