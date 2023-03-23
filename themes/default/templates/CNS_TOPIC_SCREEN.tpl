{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$REQUIRE_JAVASCRIPT,cns_forum}
<div data-tpl="cnsTopicScreen" data-tpl-params="{+START,PARAMS_JSON,SERIALIZED_OPTIONS,HASH}{_*}{+END}">
	{TITLE}

	{WARNING_DETAILS}

	{$SET,topic_title,{_TITLE}}

	<div class="cns-topic-{THREADED*}">
		{+START,IF,{$CONFIG_OPTION,enable_forum_dupe_buttons}}{+START,IF_NON_EMPTY,{BUTTON_SCREENS}{ID}}
			<div class="non-accessibility-redundancy cns-topic-buttons">
				<div class="clearfix">
					<div class="buttons-group cns-buttons-screen">
						<div class="buttons-group-inner">
							{+START,IF,{$NOT,{TICKET_FORUM}}}
								{+START,INCLUDE,NOTIFICATION_BUTTONS}
									NOTIFICATIONS_TYPE=cns_topic
									NOTIFICATIONS_ID={ID}
									NOTIFICATIONS_PAGE_LINK=forum:topics:toggle_notifications_topic:{ID}
								{+END}
							{+END}
							{BUTTON_SCREENS}
						</div>
					</div>
				</div>
			</div>
		{+END}{+END}

		{POLL}

		{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,topic,{ID}}}
		{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

		{+START,IF,{$CONFIG_OPTION,enable_forum_dupe_buttons}}
			{+START,IF_NON_EMPTY,{PAGINATION}}
				<div class="non-accessibility-redundancy">
					<div class="pagination-spacing clearfix">
						{$REPLACE, id="blp-, id="blp2-,{$REPLACE, for="blp-, for="blp2-,{$REPLACE, id="r-, id="r2-,{$REPLACE, for="r-, for="r2-,{PAGINATION}}}}}
					</div>
				</div>
			{+END}
		{+END}

		{+START,IF,{THREADED}}
			<div class="comments-wrapper">
				<div class="boxless-space">
					{POSTS}
				</div>
			</div>
		{+END}
		{+START,IF,{$NOT,{THREADED}}}
			{+START,IF_NON_EMPTY,{POSTS}}
				<div class="wide-table cns-topic autosized-table">
					{POSTS}
				</div>
			{+END}
		{+END}

		{+START,IF_NON_EMPTY,{ID}}
			<div class="box box---cns-topic-screen"><div class="box-inner">
				{+START,IF_NON_EMPTY,{MEMBERS_VIEWING}}
					{+START,IF,{$NEQ,{NUM_MEMBERS},0}}
						{!MEMBERS_VIEWING,{NUM_GUESTS*},{NUM_MEMBERS*},{MEMBERS_VIEWING}}
					{+END}
					{+START,IF,{$EQ,{NUM_MEMBERS},0}}
						{!_MEMBERS_VIEWING,{NUM_GUESTS*},{NUM_MEMBERS*},{MEMBERS_VIEWING}}
					{+END}
				{+END}
				{+START,IF_EMPTY,{MEMBERS_VIEWING}}
					{!TOO_MANY_USERS_ONLINE}
				{+END}
			</div></div>
		{+END}
		{+START,IF_EMPTY,{POSTS}}
			<p class="nothing-here">
				{!NO_ENTRIES,post}
			</p>
		{+END}

		{+START,IF,{$OR,{$IS_NON_EMPTY,{MODERATOR_ACTIONS}},{$IS_NON_EMPTY,{MARKED_POST_ACTIONS}},{THREADED}}}
			<div class="box cns-topic-control-functions{+START,IF,{$NOR,{$IS_NON_EMPTY,{MARKED_POST_ACTIONS}},{THREADED}}} block-desktop{+END}"><div class="box-inner">
				{+START,IF_NON_EMPTY,{MODERATOR_ACTIONS}}
					<form class="form-moderator-actions"  title="{!TOPIC_ACTIONS}" action="{$URL_FOR_GET_FORM*,{ACTION_URL}}" method="get">
						{$HIDDENS_FOR_GET_FORM,{ACTION_URL}}
						<label for="tma-type">{!TOPIC_ACTIONS}:</label>
						<select class="form-control form-control-sm dropdown-actions js-topic-moderator-action-submit-form" id="tma-type" name="type">
							<option value="browse">-</option>
							{MODERATOR_ACTIONS}
						</select>
					</form>
				{+END}

				{+START,IF,{$DESKTOP}}
					{+START,IF_NON_EMPTY,{MARKED_POST_ACTIONS}}
						<form class="desktop-only" title="{!MARKED_POST_ACTIONS}" action="{$URL_FOR_GET_FORM*,{ACTION_URL}}" method="get">
							{$HIDDENS_FOR_GET_FORM,{ACTION_URL}}
							<label for="mpa-type">{!_MARKED_POST_ACTIONS}:</label>
							<select id="mpa-type" name="type" class="form-control form-control-sm js-moderator-action-submit-form">
								{+START,IF,{$GT,{$SUBSTR_COUNT,{MARKED_POST_ACTIONS},<option},1}}
									<option value="browse">-</option>
								{+END}
								{MARKED_POST_ACTIONS}
							</select>
						</form>
					{+END}
				{+END}

				{+START,IF,{THREADED}}
					<form class="form-comments-sort" action="{$SELF_URL*}" method="post">
						{$INSERT_FORM_POST_SECURITY}

						<label for="comments_sort">{!SORT}:</label>
						<select id="comments_sort" name="comments_sort" class="form-control form-control-sm js-order-change-submit-form">
							<option {+START,IF,{$EQ,{$_POST,comments_sort,oldest},relevance}} selected="selected"{+END} value="relevance">{!RELEVANCE}</option>
							<option {+START,IF,{$EQ,{$_POST,comments_sort,oldest},newest}} selected="selected"{+END} value="newest">{!NEWEST_FIRST}</option>
							<option {+START,IF,{$EQ,{$_POST,comments_sort,oldest},oldest}} selected="selected"{+END} value="oldest">{!OLDEST_FIRST}</option>
							<option {+START,IF,{$EQ,{$_POST,comments_sort,oldest},average_rating}} selected="selected"{+END} value="average_rating">{!RATING}</option>
							<option {+START,IF,{$EQ,{$_POST,comments_sort,oldest},compound_rating}} selected="selected"{+END} value="compound_rating">{!POPULARITY}</option>
						</select>
					</form>
				{+END}
			</div></div>
		{+END}

		{+START,IF_NON_EMPTY,{PAGINATION}}
			<div class="clearfix pagination-spacing">
				{PAGINATION}
			</div>
		{+END}

		{+START,IF_NON_EMPTY,{POSTS}}
			<div class="clearfix cns-topic-buttons">
				{+START,IF_NON_EMPTY,{BUTTON_SCREENS}{ID}}
					<div class="buttons-group cns-buttons-screen">
						<div class="buttons-group-inner">
							{+START,IF,{$NOT,{TICKET_FORUM}}}
								{+START,INCLUDE,NOTIFICATION_BUTTONS}
									NOTIFICATIONS_TYPE=cns_topic
									NOTIFICATIONS_ID={ID}
									NOTIFICATIONS_PAGE_LINK=forum:topics:toggle_notifications_topic:{ID}
								{+END}
							{+END}
							{BUTTON_SCREENS}
						</div>
					</div>
				{+END}

				{+START,IF,{$CONFIG_OPTION,enable_forum_dupe_buttons}}
					<div class="non-accessibility-redundancy left"><nav class="breadcrumbs" itemprop="breadcrumb">
						<p class="breadcrumbs">
							{+START,INCLUDE,ICON}
								NAME=breadcrumbs
								ICON_TITLE={!YOU_ARE_HERE}
								ICON_SIZE=20
								ICON_CLASS=breadcrumbs-img
							{+END}
							{BREADCRUMBS}
						</p>
					</nav></div>
				{+END}
			</div>
		{+END}

		{+START,IF_NON_EMPTY,{FORUM_ID}}
			{+START,IF,{$THEME_OPTION,show_screen_actions}}{+START,IF_PASSED,_TITLE}{$BLOCK,failsafe=1,block=main_screen_actions,title={_TITLE}}{+END}{+END}
		{+END}

		{$REVIEW_STATUS,topic,{ID}}

		{+START,SET,double_post_message}
			{+START,IF_EMPTY,{QUICK_REPLY}}{+START,IF,{$EQ,{LAST_POSTER},{$MEMBER}}}{+START,IF,{$NOT,{$IS_GUEST}}}{+START,IF,{$NOT,{MAY_DOUBLE_POST}}}
				<div class="box box---members-viewing"><div class="box-inner">
					{!NO_DOUBLE_POST}
				</div></div>
			{+END}{+END}{+END}{+END}
		{+END}
		{+START,IF,{$OR,{$IS_NON_EMPTY,{QUICK_REPLY}},{$IS_NON_EMPTY,{$TRIM,{$GET,double_post_message}}}}}
			<div class="cns-quick-reply">
				{QUICK_REPLY}

				{$GET,double_post_message}
			</div>
		{+END}
	</div>
</div>
