{$SET-,has_schema_reviews,{$AND,{$GET,supports_schema_ratings_and_reviews},{$EQ,{INDIVIDUAL_REVIEW_RATINGS},1}}}

{+START,IF,{IS_SPACER_POST}}
	{+START,IF,{$NOT,{$IN_STR,{POST},<div}}}
		<div id="box-post-{ID*}" class="box box---post"><div class="box-inner">
			{POST`}
		</div></div>
	{+END}

	{+START,IF,{$IN_STR,{POST},<div}}
		{POST`}
	{+END}
{+END}

{+START,IF,{$NOT,{IS_SPACER_POST}}}
	<div id="box-post-{ID*}" class="box box---post{+START,IF,{$OR,{IS_UNREAD},{HIGHLIGHT}}} highlighted-post{+END}"><div class="box-inner">
		<div id="post-wrap-{ID*}" class="post time-{TIME_RAW*}" itemprop="reviews" itemscope="itemscope" itemtype="http://schema.org/Review">
			{+START,IF_NON_EMPTY,{ID}}<a id="post-{ID*}"></a>{+END}

			<div class="clearfix">
				{+START,IF_NON_EMPTY,{TITLE}}{+START,IF,{$NEQ,{TITLE},{$GET,topic_title}}}<h3 class="post-title" itemprop="name">{TITLE*}</h3>{+END}{+END}

				<div class="post-subline">
					{+START,IF_EMPTY,{POSTER_URL}}{!BY_SIMPLE,{POSTER_NAME*}},{+END}
					<span {+START,IF,{$GET,has_schema_reviews}} itemprop="author" itemscope="itemscope" itemtype="https://schema.org/Person"{+END}>
						{+START,IF_NON_EMPTY,{POSTER_URL}}{!BY_SIMPLE,<a {+START,IF,{$GET,has_schema_reviews}} itemprop="name"{+END} class="post-poster" href="{POSTER_URL*}">{$DISPLAYED_USERNAME*,{POSTER_NAME}}</a>} {+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={POSTER_ID}{+END}{+END}
						{+START,IF_EMPTY,{POSTER_URL}}<span {+START,IF,{$GET,has_schema_reviews}} itemprop="name"{+END}>{!BY_SIMPLE,{POSTER_NAME*}},</span>{+END}
					</span>

					<span class="post-time">
						{!POSTED_TIME_SIMPLE_LOWER,<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{TIME_RAW}}" itemprop="datePublished">{TIME*}</time>}
					</span>

					{+START,IF_NON_EMPTY,{EMPHASIS}}
						<span class="post-action-link">({EMPHASIS})</span>
					{+END}

					{+START,IF_NON_EMPTY,{NOT_VALIDATED}}
						<span class="post-action-link">({NOT_VALIDATED})</span>
					{+END}

					{+START,LOOP,INDIVIDUAL_REVIEW_RATINGS}
						{+START,IF_PASSED,REVIEW_RATING}
							{+START,SET,REVIEWS}
								{+START,IF_NON_EMPTY,{REVIEW_TITLE}}
									<span class="field-title">{REVIEW_TITLE*}:</span>
								{+END}

								{$SET,rating_loop,0}
								{+START,WHILE,{$LT,{$GET,rating_loop},{$ROUND,{$DIV_FLOAT,{REVIEW_RATING},2}}}}
									{+START,INCLUDE,ICON}
										NAME=feedback/rating
										ICON_SIZE=18
										ICON_DESCRIPTION={$ROUND,{$DIV_FLOAT,{REVIEW_RATING},2}}
									{+END}
									{$INC,rating_loop}
								{+END}

								{+START,IF,{$GET,has_schema_reviews}}
									{+START,IF_NON_EMPTY,{REVIEW_RATING}}
										<span itemprop="reviewRating" itemscope="itemscope" itemtype="http://schema.org/Rating">
											<meta itemprop="worstRating" content="1" />
											<meta itemprop="bestRating" content="5" />
											<meta itemprop="ratingValue" content="{$DIV_FLOAT*,{REVIEW_RATING},2}" />
										</span>
									{+END}
								{+END}
							{+END}

							<span class="post-action-link">
								({$GET,REVIEWS})
							</span>
						{+END}
					{+END}

					{+START,IF_PASSED,RATING}
						<span class="post-action-link">{RATING}</span>
					{+END}

					{+START,IF,{$DESKTOP}}
						{+START,IF_NON_EMPTY,{ID}}{+START,IF_NON_PASSED_OR_FALSE,PREVIEWING}{+START,IF,{$MATCH_KEY_MATCH,_SEARCH:topicview}}
							<div id="cell-mark-{ID*}" class="cns-off post-action-link inline-block mobile-inline">
								<form class="webstandards-checker-off" title="{!MARKER} #{ID*}" method="post" action="#" id="form-mark-{ID*}">
									{$INSERT_FORM_POST_SECURITY}

									<div>
										{+START,IF,{$NOT,{$IS_GUEST}}}<div class="accessibility-hidden"><label for="mark_{ID*}">{!MARKER} #{ID*}</label></div>{+END}{$,Guests don't see this so search engines don't; hopefully people with screen-readers are logged in}
										<input {+START,IF,{$NOT,{$IS_GUEST}}} title="{!MARKER} #{ID*}"{+END} value="1" type="checkbox" id="mark_{ID*}" name="mark_{ID*}" />
									</div>
								</form>
							</div>
						{+END}{+END}{+END}
					{+END}
				</div>

				<div {+START,IF,{$GET,has_schema_reviews}} itemprop="reviewBody"{+END}>
					{POST`}
					{$METADATA_IMAGE_EXTRACT,{POST}}
				</div>

				{LAST_EDITED}
			</div>

			{+START,IF_NON_EMPTY,{BUTTONS}}
				<div class="post-buttons buttons-group">
					<div class="buttons-group-inner">
						{BUTTONS}
					</div>
				</div>
			{+END}

			{+START,IF_PASSED,CHILDREN}{+START,IF_NON_EMPTY,{CHILDREN}}
				<div id="post-children-{ID*}" class="post-thread-children">
					{CHILDREN}
				</div>
			{+END}{+END}
			{+START,INCLUDE,POST_CHILD_LOAD_LINK}{+END}
		</div>
	</div></div>
{+END}
