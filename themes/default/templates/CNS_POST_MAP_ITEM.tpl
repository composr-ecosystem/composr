<li>
	{+START,LOOP,LEVEL_HAS_ADJACENT_SIBLING}
		{+START,IF,{$NEQ,{_loop_key},0}} {$,Skip first level}
				{+START,IF,{$NEQ,{_loop_key},{POST_LEVEL}}}
					{$?,{_loop_var},<img alt="" width="20" src="{$IMG*,cns_post_map/middle_mesg_level}" />,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;}
				{+END}

				{+START,IF,{$EQ,{_loop_key},{POST_LEVEL}}}
					<img alt="" width="20" src="{$IMG*,cns_post_map/{$?,{_loop_var},mesg_level,last_mesg_level}}" />
				{+END}
		{+END}
	{+END}

	<span>
		<a href="{URL*}" class="{$?,{IS_UNREAD},cns-post-map-item-unread,cns-post-map-item-read}">#{POST_NUMBER*} &ndash; {!POST_MAP_RE,{TITLE*}}</a>

		{+START,IF,{POSTER_IS_GUEST}}
			{!BY_SIMPLE_LOWER,<span>{POSTER_NAME*}</span>},
		{+END}

		{+START,IF,{$NOT,{POSTER_IS_GUEST}}}
			{!BY_SIMPLE_LOWER,<a href="{POSTER_URL*}">{POSTER_NAME*}</a>},
		{+END}

		{DATE*}
	</span>
</li>
