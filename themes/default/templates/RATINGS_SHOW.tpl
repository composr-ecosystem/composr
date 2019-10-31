{+START,IF_NON_EMPTY,{RATINGS}}
	{+START,LOOP,RATINGS}
		<div class="vertical-alignment">
			<span class="vertical-alignment" style="min-width: 55px; display: inline-block">
				{$,Visually show}
				{$SET,rating_loop,0}
				{+START,SET,rating_stars}{$ROUND,{$DIV_FLOAT,{RATING},2}}{+END}
				{+START,WHILE,{$LT,{$GET,rating_loop},{$GET,rating_stars}}}
					<a {$?,{$EQ,{$GET,rating_loop},0},title="{$GET*,rating_stars}/5"}>{+START,INCLUDE,ICON}
						NAME=feedback/rating
						ICON_SIZE=14
					{+END}</a>
					{$INC,rating_loop}
				{+END}
			</span>

			<span>{RATING_TIME_FORMATTED*},</span>

			<span>
				{+START,IF_NON_EMPTY,{RATING_USERNAME}}
					{!BY_SIMPLE_LOWER,<a class="link-exempt" target="_blank" title="{RATING_USERNAME*} {!LINK_NEW_WINDOW}" href="{$MEMBER_PROFILE_URL*,{RATING_MEMBER}}">{RATING_USERNAME*}</a>}
					{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={RATING_MEMBER}{+END}
				{+END}

				{+START,IF_EMPTY,{RATING_USERNAME}}
					{!BY_SIMPLE_LOWER,{!GUEST}}
					{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_lookup}}
						<span class="associated-details">(<a class="link-exempt" target="_blank" title="{RATING_IP*} {!LINK_NEW_WINDOW}" href="{$PAGE_LINK*,adminzone:admin_lookup:param={RATING_IP&}}">{RATING_IP*}</a>)</span>
					{+END}
				{+END}
			</span>
		</div>
	{+END}

	{+START,IF,{HAS_MORE}}
		<p>&hellip;</p>
	{+END}
{+END}
