{+START,IF,{$NOR,{$GET,login_screen},{$MATCH_KEY_MATCH,_WILD:login}}}
	{+START,INCLUDE,BLOCK_TOP_LOGIN}{+END}

	<div class="hybridauth-block-top-login">
		{$HYBRIDAUTH_BUTTONS,1,3}
	</div>
{+END}
