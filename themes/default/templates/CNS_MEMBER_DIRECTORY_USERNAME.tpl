<span class="vertical-alignment">
	{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={ID}{+END}

	<a href="{URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>

	{+START,IF,{$NOT,{VALIDATED}}}
		<span>{!MEMBER_IS_NOT_VALIDATED}</span>
	{+END}

	{+START,IF,{$NOT,{CONFIRMED}}}
		<span>{!MEMBER_IS_UNCONFIRMED}</span>
	{+END}
</span>
