<section class="box box___block_side_personal_stats"><div class="box_inner">
	<h3>{$DISPLAYED_USERNAME*,{USERNAME}}</h3>

	{+START,IF_NON_EMPTY,{AVATAR_URL}}
		<div class="personal_stats_avatar"><img src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" title="{!AVATAR}" alt="{!AVATAR}" /></div>
	{+END}

	{+START,IF_NON_EMPTY,{DETAILS}}
		<ul class="compact_list">
			{DETAILS}
		</ul>
	{+END}

	{+START,IF_NON_EMPTY,{LINKS}}
		<ul class="associated_links_block_group">
			{LINKS}
		</ul>
	{+END}

	{+START,IF,{$CNS}}{+START,IF,{$NEQ,{$CPF_VALUE,m_password_compat_scheme},facebook}}
		{+START,IF_NON_EMPTY,{$CONFIG_OPTION,facebook_appid}}{+START,IF,{$CONFIG_OPTION,facebook_allow_signups}}
			<div class="fb-login-button" data-scope="email{$,Asking for this stuff is now a big hassle as it needs a screencast(s) making: user_gender,user_birthday,user_location}"></div>
		{+END}{+END}
	{+END}{+END}
</div></section>
