{$SET-,has_schema_reviews,{$AND,{$GET,supports_schema_ratings_and_reviews},{$EQ,{INDIVIDUAL_REVIEW_RATINGS},1}}}

{+START,IF,{IS_SPACER_POST}}
	{+START,IF,{$NOT,{$IN_STR,{POST},<div}}}
		<div class="box box___post"><div class="box_inner">
			{POST}
		</div></div>
	{+END}

	{+START,IF,{$IN_STR,{POST},<div}}
		{POST}
	{+END}
{+END}

{+START,IF,{$NOT,{IS_SPACER_POST}}}
	<div class="box box___post{+START,IF,{$OR,{IS_UNREAD},{HIGHLIGHT}}} highlighted_post{+END}"><div class="box_inner">
		<div id="post_wrap_{ID*}" class="post time_{TIME_RAW*}"{+START,IF,{$GET,has_schema_reviews}} itemprop="review" itemscope="itemscope" itemtype="http://schema.org/Review"{+END}>
			{+START,IF_NON_EMPTY,{ID}}<a id="post_{ID*}"></a>{+END}

			<div class="float_surrounder">
				{+START,IF_NON_EMPTY,{TITLE}}{+START,IF,{$NEQ,{TITLE},{$GET,topic_title}}}<h3 class="post_title"{+START,IF,{$GET,has_schema_reviews}} itemprop="name"{+END}>{TITLE*}</h3>{+END}{+END}

				<div class="post_subline">
					<span{+START,IF,{$GET,has_schema_reviews}} itemprop="author" itemscope="itemscope" itemtype="https://schema.org/Person"{+END}>
						{+START,IF_NON_EMPTY,{POSTER_URL}}{!BY_SIMPLE,<a{+START,IF,{$GET,has_schema_reviews}} itemprop="name"{+END} class="post_poster" href="{POSTER_URL*}">{$DISPLAYED_USERNAME*,{POSTER_NAME}}</a>} {+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={POSTER_ID}{+END}{+END}
						{+START,IF_EMPTY,{POSTER_URL}}<span{+START,IF,{$GET,has_schema_reviews}} itemprop="name"{+END}>{!BY_SIMPLE,{POSTER_NAME*}},</span>{+END}
					</span>

					<span class="post_time">
						{!POSTED_TIME_SIMPLE_LOWER,<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{TIME_RAW}}"{+START,IF,{$GET,has_schema_reviews}} itemprop="datePublished"{+END}>{TIME*}</time>}
					</span>

					{+START,IF_NON_EMPTY,{EMPHASIS}}
						<span class="post_action_link">({EMPHASIS})</span>
					{+END}

					{+START,IF_NON_EMPTY,{UNVALIDATED}}
						<span class="post_action_link">({UNVALIDATED})</span>
					{+END}

					{+START,LOOP,INDIVIDUAL_REVIEW_RATINGS}
						{+START,IF_PASSED,REVIEW_RATING}
							{+START,SET,REVIEWS}
								{+START,IF_NON_EMPTY,{REVIEW_TITLE}}
									<span class="field_title">{REVIEW_TITLE*}:</span>
								{+END}

								{$SET,rating_loop,0}
								{+START,WHILE,{$LT,{$GET,rating_loop},{$ROUND,{$DIV_FLOAT,{REVIEW_RATING},2}}}}
									<img src="{$IMG*,icons/14x14/rating}" srcset="{$IMG*,icons/28x28/rating} 2x" alt="{$ROUND,{$DIV_FLOAT,{REVIEW_RATING},2}}" />
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

							<span class="post_action_link">
								({$GET,REVIEWS})
							</span>
						{+END}
					{+END}

					{+START,IF_PASSED,RATING}
						<span class="post_action_link">{RATING}</span>
					{+END}

					{+START,IF,{$NOT,{$MOBILE}}}
						{+START,IF,{$JS_ON}}{+START,IF_NON_EMPTY,{ID}}{+START,IF_NON_PASSED_OR_FALSE,PREVIEWING}{+START,IF,{$MATCH_KEY_MATCH,_SEARCH:topicview}}
							<div id="cell_mark_{ID*}" class="cns_off post_action_link inline_block">
								<form class="webstandards_checker_off" title="{!MARKER} #{ID*}" method="post" action="index.php" id="form_mark_{ID*}" autocomplete="off">
									{$INSERT_SPAMMER_BLACKHOLE}

									<div>
										{+START,IF,{$NOT,{$IS_GUEST}}}<div class="accessibility_hidden"><label for="mark_{ID*}">{!MARKER} #{ID*}</label></div>{+END}{$,Guests don't see this so search engines don't; hopefully people with screen-readers are logged in}
										<input{+START,IF,{$NOT,{$IS_GUEST}}} title="{!MARKER} #{ID*}"{+END} value="1" type="checkbox" id="mark_{ID*}" name="mark_{ID*}" />
									</div>
								</form>
							</div>
						{+END}{+END}{+END}{+END}
					{+END}
				</div>

				<div{+START,IF,{$GET,has_schema_reviews}} itemprop="reviewBody"{+END}>
					{POST}
				</div>

				{LAST_EDITED}
			</div>

			{+START,IF_NON_EMPTY,{BUTTONS}}
				<div class="post_buttons buttons_group">
					{BUTTONS}
				</div>
			{+END}

			{+START,IF_PASSED,CHILDREN}
				<div id="post_children_{ID*}" class="post_thread_children">
					{CHILDREN}
				</div>
			{+END}
			{+START,INCLUDE,POST_CHILD_LOAD_LINK}{+END}
		</div>
	</div></div>
{+END}
