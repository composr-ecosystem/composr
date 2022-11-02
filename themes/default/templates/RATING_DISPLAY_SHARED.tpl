{$,Semantics to show results}
{+START,IF,{$GET,has_schema_reviews}}
	<meta itemprop="worstRating" content="1" />
	<meta itemprop="bestRating" content="5" />
	<meta itemprop="ratingValue" content="{$DIV_FLOAT*,{RATING},2}" />
	<meta itemprop="ratingCount" content="{_NUM_RATINGS*}" />
{+END}

{$,Shows only if no rating form [which build in result display] or if likes enabled [shows separate stars results and form]}
{+START,IF,{$OR,{LIKES},{$IS_EMPTY,{$TRIM,{RATING_FORM}}}}}
	{+START,IF_NON_EMPTY,{TITLE}}
		<strong>{TITLE*}:</strong>
	{+END}

	{$,Visually show results}
	{$SET,rating_loop,0}
	{+START,SET,rating_stars}{$ROUND,{$DIV_FLOAT,{RATING},2}}{+END}
	{+START,WHILE,{$LT,{$GET,rating_loop},{$GET,rating_stars}}}
		{+START,INCLUDE,ICON}
			NAME=feedback/rating
			ICON_SIZE=18
			ICON_CLASS=rating-display-star
			ICON_TITLE={$?,{$EQ,{$GET,rating_loop},0},{!HAS_RATING,{$GET,rating_stars}}}
		{+END}
		{$INC,rating_loop}
	{+END}
	{+START,IF_NON_PASSED_OR_FALSE,NO_PEOPLE_SHOWN}{+START,IF,{LIKES}}{+START,IF_PASSED,LIKED_BY}{+START,IF_NON_EMPTY,{LIKED_BY}}
		{$SET,done_one_liker,0}
		{+START,LOOP,LIKED_BY}{+START,IF_NON_EMPTY,{$AVATAR,{MEMBER_ID}}}{+START,IF,{$NOT,{$GET,done_one_liker,0}}}({+END}<a href="{$MEMBER_PROFILE_URL*,{MEMBER_ID}}"><img width="10" height="10" style="width: 10px; height: 10px"{$,LEGACY- fixes display issues on Mac OS Mail app} src="{$ENSURE_PROTOCOL_SUITABILITY*,{$AVATAR,{MEMBER_ID}}}" title="{!LIKED_BY} {USERNAME*}" alt="{!LIKED_BY} {$DISPLAYED_USERNAME*,{USERNAME}}" /></a>{$SET,done_one_liker,1}{+END}{+END}{+START,IF,{$GET,done_one_liker,0}}){+END}
	{+END}{+END}{+END}{+END}
{+END}
