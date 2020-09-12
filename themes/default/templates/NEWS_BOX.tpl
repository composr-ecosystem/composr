<div class="box box---news-box"><div class="box-inner">
	{+START,IF,{$HAS_DELETE_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_news}}
		{+START,INCLUDE,MASS_SELECT_MARKER}
			TYPE=news
			ID={ID}
		{+END}

		{$INC,has_mass_select}
	{+END}

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
		<h3>{+START,IF_NON_EMPTY,{FULL_URL}}<a class="subtle-link" href="{FULL_URL*}">{+END}{$TRIM,{$GET,content_box_title}}{+START,IF_NON_EMPTY,{FULL_URL}}</a>{+END}</h3>
	{+END}

	{+START,IF,{$AND,{$NOT,{BLOG}},{$IS_NON_EMPTY,{REP_IMAGE_URL}}}}{+START,IF,{$DESKTOP}}
		<div class="newscat-img newscat-img-author block-desktop">
			{+START,IF_NON_EMPTY,{CATEGORY}}
				<a href="{FULL_URL*}"><img width="100" height="100" src="{$THUMBNAIL*,{REP_IMAGE_URL},100x100}" alt="" /></a>
			{+END}
		</div>
	{+END}{+END}

	{+START,IF,{$NAND,{$NOT,{BLOG}},{$IS_NON_EMPTY,{REP_IMAGE_URL}}}}{+START,IF,{$DESKTOP}}
		<div class="newscat-img newscat-img-member block-desktop">
			{+START,IF_NON_EMPTY,{$AVATAR,{SUBMITTER}}}
				<a href="{FULL_URL*}"><img src="{$ENSURE_PROTOCOL_SUITABILITY*,{$AVATAR,{SUBMITTER}}}" title="{!AVATAR}" alt="{!AVATAR}" /></a>
			{+END}

			{+START,IF,{$CNS}}{+START,IF_NON_EMPTY,{$CNS_RANK_IMAGE,{SUBMITTER}}}
				<p>{$CNS_RANK_IMAGE,{SUBMITTER}}</p>
			{+END}{+END}
		</div>
	{+END}{+END}

	<div class="meta-details" role="note">
		<ul class="meta-details-list">
			<li>{!POSTED_TIME_SIMPLE,{DATE*}}</li>
			{+START,SET,author_details}
				{+START,IF,{$IS_NON_EMPTY,{AUTHOR_URL}}}
					{!BY_SIMPLE,<a href="{AUTHOR_URL*}" title="{!AUTHOR}: {AUTHOR*}">{AUTHOR*}</a>}
					{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={$AUTHOR_MEMBER,{AUTHOR}}{+END}
				{+END}

				{+START,IF,{$IS_EMPTY,{AUTHOR_URL}}}
					{+START,IF_NON_EMPTY,{$USERNAME*,{SUBMITTER},1}}
						{!BY_SIMPLE,<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}">{$USERNAME*,{SUBMITTER},1}</a>}
						{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
					{+END}
				{+END}
			{+END}
			{+START,IF_NON_EMPTY,{$GET,author_details}}
				<li>
					{$GET,author_details}
				</li>
			{+END}
		</ul>
	</div>

	<div>
		{+START,IF_NON_EMPTY,{NEWS}}
			{+START,IF,{$AND,{$NOT,{$IN_STR,{NEWS},<p><div>}},{$NOT,{$IN_STR,{NEWS},<h}}}}<p class="news-summary-p">{+END}
			{+START,IF,{TRUNCATE}}{$TRUNCATE_LEFT,{NEWS},400,0,1,0,0.4}{+END}
			{$SET,large_news_posts,{$NOT,{TRUNCATE}}}
			{+START,IF,{$NOT,{TRUNCATE}}}{NEWS}{+END}
			{+START,IF,{$AND,{$NOT,{$IN_STR,{NEWS},<p><div>}},{$NOT,{$IN_STR,{NEWS},<h}}}}</p>{+END}
		{+END}
	</div>

	{+START,IF_PASSED,TAGS}
		{+START,IF,{$THEME_OPTION,show_content_tagging_inline}}{TAGS}{+END}
	{+END}

	<ul class="horizontal-links associated-links-block-group">
		<li><a title="{!READ_MORE}: {NEWS_TITLE}" href="{FULL_URL*}">{!READ_MORE}</a>{+START,IF,{$NOT,{$MATCH_KEY_MATCH,forum:topicview,forum:forumview}}} {+START,IF_PASSED_AND_TRUE,COMMENT_COUNT} <span class="comment-count">{$COMMENT_COUNT,news,{ID}}</span>{+END}{+END}</li>
	</ul>
</div></div>
