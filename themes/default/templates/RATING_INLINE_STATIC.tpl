{$,This is not a usual template to use, as it's for read-only ratings only. Usually we use RATING_INLINE_DYNAMIC}

{$SET,has_schema_reviews,{$AND,{$GET,supports_schema_ratings_and_reviews},{HAS_RATINGS}}}

{$,Show the current result (nothing shows if nobody voted yet)}
{+START,IF,{HAS_RATINGS}}
	<div class="rating-inline rating-inline-static"{+START,IF,{$GET,has_schema_reviews}} itemprop="aggregateRating" itemscope="itemscope" itemtype="http://schema.org/AggregateRating"{+END}>
		{$SET,i,0}
		{+START,LOOP,ALL_RATING_CRITERIA}
			<div {+START,IF,{$NEQ,{$GET,i},0}} class="horiz-field-sep"{+END}>
				{$,We set RATING_FORM as blank so that it doesn't stop showing current rating due to expected showing of current rating within the rating form stars, which of course we do not show due to read-only status}
				{+START,INCLUDE,RATING_DISPLAY_SHARED}
					NO_PEOPLE_SHOWN=1
					RATING_FORM=
				{+END}
			</div>
			{$INC,i}
		{+END}
	</div>
{+END}
