{$REQUIRE_JAVASCRIPT,core_feedback_features}
{$,If may rate}
{+START,IF,{ALLOW_RATING}}
	<div data-tpl="ratingForm" data-tpl-params="{+START,PARAMS_JSON,ERROR,ALL_RATING_CRITERIA,FEEDBACK_TYPE,CONTENT_TITLE,ID}{_*}{+END}">
		{+START,LOOP,ALL_RATING_CRITERIA}
			{$SET-,identifier,{FEEDBACK_TYPE*}--{TYPE*}--{ID*}}

			<div class="rating-outer">
				<div class="rating-type-title">
					<a id="rating--{$GET,identifier}-jump"></a>

					{+START,IF_NON_EMPTY,{TITLE}}<strong>{TITLE*}:</strong>{+END}
				</div>

				<div class="rating-inner">
					{$,Like/dislike}
					{+START,IF,{LIKES}}
						{+START,INCLUDE,ICON}
							NAME=feedback/dislike2
							ICON_ID=rating-bar-1--{$GET,identifier}
							ICON_SIZE=18
						{+END}
						{+START,INCLUDE,ICON}
							NAME=feedback/like2
							ICON_ID=rating-bar-10--{$GET,identifier}
							ICON_SIZE=18
						{+END}
					{+END}

					{$,Star ratings}
					{+START,IF,{$NOT,{LIKES}}}
						{+START,INCLUDE,ICON}
							NAME=feedback/rating
							ICON_ID=rating-bar-2--{$GET,identifier}
							ICON_SIZE=18
						{+END}
						{+START,INCLUDE,ICON}
							NAME=feedback/rating
							ICON_ID=rating-bar-4--{$GET,identifier}
							ICON_SIZE=18
						{+END}
						{+START,INCLUDE,ICON}
							NAME=feedback/rating
							ICON_ID=rating-bar-6--{$GET,identifier}
							ICON_SIZE=18
						{+END}
						{+START,INCLUDE,ICON}
							NAME=feedback/rating
							ICON_ID=rating-bar-8--{$GET,identifier}
							ICON_SIZE=18
						{+END}
						{+START,INCLUDE,ICON}
							NAME=feedback/rating
							ICON_ID=rating-bar-10--{$GET,identifier}
							ICON_SIZE=18
						{+END}
					{+END}
				</div>
			</div>
		{+END}
	</div>
{+END}
