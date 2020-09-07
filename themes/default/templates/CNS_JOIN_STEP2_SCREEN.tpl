{TITLE}

{$GET,join_supplemental}

<p>
	{!ENTER_PROFILE_DETAILS}
</p>

{+START,IF,{$CONFIG_OPTION,is_on_invites}}
	<p>
		{!INVITE_ONLY}
	</p>
{+END}

{FORM}
