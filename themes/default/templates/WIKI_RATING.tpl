<div class="left">
	{+START,IF,{HAS_RATINGS}}
		{+START,LOOP,ALL_RATING_CRITERIA}
			{+START,IF_NON_EMPTY,{TITLE}}
				<strong>{TITLE*}:</strong>
			{+END}
			{$SET,rating_loop,0}
			{+START,WHILE,{$LT,{$GET,rating_loop},{$ROUND,{$DIV_FLOAT,{RATING},2}}}}
				{+START,INCLUDE,ICON}
					NAME=feedback/rating
					ICON_SIZE=18
					ICON_DESCRIPTION={$ROUND,{$DIV_FLOAT,{RATING},2}}
				{+END}
				{$INC,rating_loop}
			{+END}
		{+END}
	{+END}
	{+START,IF,{$NOT,{HAS_RATINGS}}}
		<em>{!UNRATED}</em>
	{+END}

	<span class="associated-details"><em>{!VOTES,{OVERALL_NUM_RATINGS*}}</em></span>
	<span class="wiki-rating-inside">{RATING_FORM}</span>
</div>
