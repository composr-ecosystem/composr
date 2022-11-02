{$SET-,has_schema_reviews,{$AND,{$GET,supports_schema_ratings_and_reviews},{$EQ,{REVIEW_RATING_CRITERIA},1}}}

<div id="comments-wrapper" class="comments-wrapper" role="complementary" data-tpl="commentsWrapper" data-tpl-params="{+START,PARAMS_JSON,SERIALIZED_OPTIONS,HASH}{_*}{+END}">
	{+START,SET,REVIEWS_TITLE}
		<span class="field-title">{!_REVIEWS,{$METADATA*,numcomments}}:</span>

		{$SET,rating_loop,0}
		{+START,LOOP,REVIEW_RATING_CRITERIA}
			{+START,IF_NON_EMPTY,{REVIEW_RATING}}
				{+START,IF,{$GET,has_schema_reviews}}
					<span itemprop="aggregateRating" itemscope="itemscope" itemtype="http://schema.org/AggregateRating">
						<meta itemprop="worstRating" content="1" />
						<meta itemprop="bestRating" content="5" />
						<meta itemprop="ratingValue" content="{$DIV_FLOAT*,{REVIEW_RATING},2}" />
						<meta itemprop="reviewCount" content="{$METADATA*,numcomments}" />
					</span>
				{+END}

				{+START,IF_EMPTY,{REVIEW_TITLE}}
					{+START,WHILE,{$LT,{$GET,rating_loop},{$ROUND,{$DIV_FLOAT,{REVIEW_RATING},2}}}}
						{+START,INCLUDE,ICON}
							NAME=feedback/rating
							ICON_SIZE=18
							ICON_DESCRIPTION={$ROUND*,{$DIV_FLOAT,{REVIEW_RATING},2}}
						{+END}
						{$INC,rating_loop}
					{+END}
				{+END}
			{+END}
		{+END}

		{+START,IF,{$NEQ,{$GET,rating_loop},0}}
			<span class="reviews-average horiz-field-sep">({!AVERAGED})</span>
		{+END}

		{+START,IF,{$EQ,{$GET,rating_loop},0}}
			{!UNRATED}
		{+END}
	{+END}

	<div class="boxless-space">
		<div class="box box---comments-wrapper"><div class="box-inner">
			<h2 class="comments-header">{$?,{$IS_NON_EMPTY,{REVIEW_RATING_CRITERIA}},{$GET,REVIEWS_TITLE},{!COMMENTS}}</h2>

			<div class="clearfix">
				<div class="comments-notification-buttons">
					{+START,INCLUDE,NOTIFICATION_BUTTONS}
						NOTIFICATIONS_TYPE=comment_posted
						NOTIFICATIONS_ID={TYPE}_{ID}
						BUTTON_TYPE=button-scri
						BUTTON_LABEL_ENABLE={!ENABLE_COMMENT_NOTIFICATIONS}
						BUTTON_LABEL_DISABLE={!DISABLE_COMMENT_NOTIFICATIONS}
					{+END}
				</div>

				<div class="comments-sorting-box">
					<form title="{!SORT}" class="inline" action="{$SELF_URL*}" method="post">
						{$INSERT_FORM_POST_SECURITY}

						<label for="comments_sort">{!SORT_BY}</label>
						<select class="form-control js-change-select-submit-form" id="comments_sort" name="comments_sort">
							<option {+START,IF,{$EQ,{SORT},relevance}} selected="selected"{+END} value="relevance">{!RELEVANCE}</option>
							<option {+START,IF,{$EQ,{SORT},newest}} selected="selected"{+END} value="newest">{!NEWEST_FIRST}</option>
							<option {+START,IF,{$EQ,{SORT},oldest}} selected="selected"{+END} value="oldest">{!OLDEST_FIRST}</option>
							<option {+START,IF,{$EQ,{SORT},average_rating}} selected="selected"{+END} value="average_rating">{!RATING}</option>
							<option {+START,IF,{$EQ,{SORT},compound_rating}} selected="selected"{+END} value="compound_rating">{!POPULARITY}</option>
						</select>
					</form>
				</div>
			</div>

			{+START,LOOP,REVIEW_RATING_CRITERIA}
				{+START,IF_NON_EMPTY,{REVIEW_RATING}}
					{+START,IF_NON_EMPTY,{REVIEW_TITLE}}
						<p>
							<strong>{REVIEW_TITLE*}:</strong>
							{$SET,rating_loop,0}
							{+START,WHILE,{$LT,{$GET,rating_loop},{$ROUND,{$DIV_FLOAT,{REVIEW_RATING},2}}}}
								{+START,INCLUDE,ICON}
									NAME=feedback/rating
									ICON_SIZE=18
									ICON_DESCRIPTION={$ROUND,{$DIV_FLOAT,{REVIEW_RATING},2}}
								{+END}
								{$INC,rating_loop}
							{+END}
						</p>
					{+END}
				{+END}
			{+END}

			<div class="comment-wrapper">
				<meta itemprop="interactionCount" content="UserComments:{$METADATA*,numcomments}" />

				{COMMENTS}

				{+START,IF_EMPTY,{$TRIM,{COMMENTS}}}
					<p class="nothing-here">{!NO_COMMENTS}</p>
				{+END}
			</div>

			{+START,IF_PASSED,PAGINATION}
				<div class="clearfix">
					{PAGINATION}
				</div>
			{+END}
		</div></div>

		{$,If has commenting permission}
		{+START,IF_NON_EMPTY,{FORM}}
			{+START,IF_PASSED,COMMENTS}<a id="last-comment" rel="docomment"></a>{+END}

			{FORM}
		{+END}
	</div>

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,IF_NON_EMPTY,{AUTHORISED_FORUM_URL}}
		{+START,INCLUDE,STAFF_ACTIONS}
			STAFF_ACTIONS_TITLE={!COMMENTS}
			1_URL={AUTHORISED_FORUM_URL*}
			1_TITLE={!VIEW_COMMENT_TOPIC}
			1_ICON=feedback/comments_topic
		{+END}
	{+END}
</div>
