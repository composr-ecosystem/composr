{$SET,has_image,0}
{+START,IF,{$AND,{$NOT,{BLOG}},{$IS_NON_EMPTY,{REP_IMAGE_URL}}}}
	{$SET,has_image,1}
{+END}
{+START,IF,{$AND,{BLOG},{$IS_EMPTY,{REP_IMAGE_URL}}}}{+START,IF_NON_EMPTY,{$AVATAR,{SUBMITTER}}}
	{$SET,has_image,1}
{+END}{+END}

<div class="news-grid-item{+START,IF,{$GET,has_image}} has-image{+END}">
	{+START,IF,{$HAS_DELETE_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_news}}
		{+START,INCLUDE,MASS_SELECT_MARKER}
			TYPE=news
			ID={ID}
		{+END}

		{$INC,has_mass_select}
	{+END}

	{+START,IF,{$NOT,{BLOG}}}
		<a class="news-grid-item-image is-associated" href="{FULL_URL*}"><img src="{$ENSURE_PROTOCOL_SUITABILITY*,{REP_IMAGE_URL}}" alt="" /></a>
	{+END}

	{+START,IF,{$AND,{BLOG},{$IS_EMPTY,{REP_IMAGE_URL}}}}{+START,IF_NON_EMPTY,{$AVATAR,{SUBMITTER}}}
		<a class="news-grid-item-image is-avatar" href="{FULL_URL*}"><img src="{$ENSURE_PROTOCOL_SUITABILITY*,{$AVATAR,{SUBMITTER}}}" title="{!AVATAR}" alt="{!AVATAR}" /></a>
	{+END}{+END}

	{+START,IF_NON_EMPTY,{CATEGORY}}{+START,IF_NON_EMPTY,{CATEGORY_URL}}
		<a href="{CATEGORY_URL*}" class="news-grid-item-category btn btn-secondary">{CATEGORY*}</a>
	{+END}{+END}

	{+START,SET,content_box_title}
		{+START,IF,{GIVE_CONTEXT}}
			{!CONTENT_IS_OF_TYPE,{!NEWS},{NEWS_TITLE}}
		{+END}
		{+START,IF,{$NOT,{GIVE_CONTEXT}}}
			{+START,IF_PASSED,ID}
				{+START,FRACTIONAL_EDITABLE,{NEWS_TITLE_PLAIN},title,_SEARCH:cms_news:__edit:{ID},1}{$TRUNCATE_LEFT,{NEWS_TITLE},70,1,1}{+END}
			{+END}
			{+START,IF_NON_PASSED,ID}
				{$TRUNCATE_LEFT,{NEWS_TITLE},70,1,1}
			{+END}
		{+END}
	{+END}
	{+START,IF,{$NOT,{$GET,skip_content_box_title}}}
		<h3 class="news-grid-item-heading">{+START,IF_NON_EMPTY,{FULL_URL}}<a class="subtle-link" href="{FULL_URL*}">{+END}{$TRIM,{$GET,content_box_title}}{+START,IF_NON_EMPTY,{FULL_URL}}</a>{+END}</h3>
	{+END}

	<div class="news-grid-item-details" role="note">
		<ul class="horizontal-links">
			{+START,SET,author_details}
				{+START,IF,{$IS_NON_EMPTY,{AUTHOR_URL}}}
					{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={$AUTHOR_MEMBER,{AUTHOR}}{+END}
					<a href="{AUTHOR_URL*}" title="{!AUTHOR}: {AUTHOR*}">{AUTHOR*}</a>
				{+END}

				{+START,IF,{$IS_EMPTY,{AUTHOR_URL}}}{+START,IF_NON_EMPTY,{$USERNAME*,{SUBMITTER},1}}
					{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
					<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}">{$USERNAME*,{SUBMITTER},1}</a>
				{+END}{+END}
			{+END}
			{+START,IF_NON_EMPTY,{$TRIM,{$GET,author_details}}}
				<li>
					{$GET,author_details}
				</li>
			{+END}
			<li><a href="{FULL_URL*}" title="{DATE*}" class="subtle-link">{$FROM_TIMESTAMP*,j M Y,{DATE_RAW*}}</a></li>
			{+START,IF,{$NOT,{$MATCH_KEY_MATCH,forum:topicview,forum:forumview}}}{+START,IF_PASSED_AND_TRUE,COMMENT_COUNT}
				<li><a href="{FULL_URL*}#comments-wrapper" class="subtle-link comments-link">{$COMMENT_COUNT,news,{ID}}</a></li>
			{+END}{+END}
		</ul>
	</div>

	<div class="news-grid-item-summary">
		{+START,IF_NON_EMPTY,{NEWS}}
			{+START,IF,{$AND,{$NOT,{$IN_STR,{NEWS},<p>}},{$NOT,{$IN_STR,{NEWS},<h}}}}<p class="news-summary-p">{+END}
			{+START,IF,{TRUNCATE}}
				{$TRUNCATE_LEFT,{NEWS},400,0,1,0,0.4}
			{+END}
			{$SET,large_news_posts,{$NOT,{TRUNCATE}}}
			{+START,IF,{$NOT,{TRUNCATE}}}
				{NEWS}
			{+END}
			{+START,IF,{$AND,{$NOT,{$IN_STR,{NEWS},<p>}},{$NOT,{$IN_STR,{NEWS},<h}}}}</p>{+END}
		{+END}
	</div>

	{+START,IF_PASSED,TAGS}{+START,IF,{$THEME_OPTION,show_content_tagging_inline}}
		{TAGS}
	{+END}{+END}
</div>
