<div class="news-entry-screen" itemscope="itemscope" itemtype="http://schema.org/{$?,{BLOG},BlogPosting,NewsArticle}">
	{TITLE}

	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<div class="news-entry-details">
		<ul class="news-entry-details-col-start horizontal-links vertical-alignment-normalise-line-height">
			<li class="news-entry-date">
				{+START,INCLUDE,ICON}NAME=menu/rich_content/calendar{+END}
				<time class="news-entry-date" datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{ADD_DATE_RAW}}" itemprop="datePublished">{DATE*}</time>
			</li>
			<li class="news-entry-author">
				{+START,INCLUDE,ICON}NAME=content_types/member{+END}
				{+START,IF_NON_EMPTY,{AUTHOR_URL}}
					<a rel="author" itemprop="author" href="{AUTHOR_URL*}" title="{!AUTHOR}: {AUTHOR*}">{AUTHOR*}</a>
					{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={$AUTHOR_MEMBER,{AUTHOR}}{+END}
				{+END}
				{+START,IF_EMPTY,{AUTHOR_URL}}{+START,IF_NON_EMPTY,{$USERNAME,{SUBMITTER},1}}
					<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}" itemprop="author">{$USERNAME*,{SUBMITTER},1}</a>
					{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
				{+END}{+END}
			</li>
		</ul>
		<ul class="news-entry-details-col-end horizontal-links vertical-alignment-normalise-line-height">
			{+START,IF,{$INLINE_STATS}}<li class="news-entry-views">{+START,INCLUDE,ICON}NAME=cns_topic_modifiers/hot{+END} <span>{!VIEWS_SIMPLE,{VIEWS*}}</span></li>{+END}
			{+START,IF_PASSED,COMMENT_COUNT}<li class="news-entry-comments">{+START,INCLUDE,ICON}NAME=feedback/comment{+END} <span>{$COMMENT_COUNT,news,{ID}}</span></li>{+END}
		</ul>
	</div>

	<div class="news-entry-image right float-separation">
		<img width="100" height="100" src="{$THUMBNAIL*,{REP_IMAGE_URL},100x100}" alt="" />
	</div>

	<div itemprop="articleBody" class="clearfix">
		<p>
			{NEWS_FULL}
		</p>
		{$METADATA_IMAGE_EXTRACT,{NEWS_FULL}}
	</div>

	{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,news,{ID}}}
	{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

	{+START,IF_NON_EMPTY,{EDIT_DATE_RAW}}
		<div class="edited" role="note">
			<img alt="" width="9" height="6" src="{$IMG*,edited}" />
			<span>{!EDITED}</span>
			<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{EDIT_DATE_RAW}}">{$DATE*,,,,{EDIT_DATE_RAW}}</time>
		</div>
	{+END}

	{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,INCLUDE,STAFF_ACTIONS}
		1_URL={SUBMIT_URL*}
		1_TITLE={$?,{BLOG},{!ADD_NEWS_BLOG},{!ADD_NEWS}}
		1_REL=add
		1_NOREDIRECT=1
		1_ICON=admin/add
		2_URL={EDIT_URL*}
		2_ACCESSKEY=q
		2_TITLE={!EDIT_LINK}
		2_ICON=admin/edit_this
		2_REL=edit
		3_URL={NEWSLETTER_URL*}
		3_TITLE={+START,IF_NON_EMPTY,{NEWSLETTER_URL}}{!newsletter:NEWSLETTER_SEND}{+END}
		3_ICON=menu/site_meta/newsletters
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			4_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=news:content_id={ID}:redirect={$SELF_URL&}}
			4_TITLE={!report_content:REPORT_THIS}
			4_ICON=buttons/report
			4_REL=report
		{+END}
	{+END}

	{+START,SET,next_and_prev}
		{+START,IF_PASSED,PREV_ARTICLE_URL}{+START,IF_PASSED,PREV_ARTICLE_TITLE}
			<div class="news-entry-prev">
				<p class="news-entry-prev-label">&larr; {!PREVIOUS_ARTICLE}</p>
				<h4 class="news-entry-prev-title"><a href="{PREV_ARTICLE_URL*}">{PREV_ARTICLE_TITLE*}</a></h4>
			</div>
		{+END}{+END}
		{+START,IF_PASSED,NEXT_ARTICLE_URL}{+START,IF_PASSED,NEXT_ARTICLE_TITLE}
			<div class="news-entry-next">
				<p class="news-entry-next-label">{!NEXT_ARTICLE} &rarr;</p>
				<h4 class="news-entry-next-title"><a href="{NEXT_ARTICLE_URL*}">{NEXT_ARTICLE_TITLE*}</a></h4>
			</div>
		{+END}{+END}
	{+END}

	{+START,IF_NON_EMPTY,{$TRIM,{$GET,next_and_prev}}}
		<div class="news-entry-next-and-prev">
			{$GET,next_and_prev}
		</div>
	{+END}

	<div class="clearfix">
		{+START,IF_NON_EMPTY,{RATING_DETAILS}}
			<div class="box box---news-entry-screen">
				<nav class="box-inner">
					<p class="lonely-label">
						{$?,{BLOG},{!BLOG_NEWS_UNDER_THESE},{!NEWS_UNDER_THESE}}
					</p>
					<ul>
						{+START,LOOP,CATEGORIES}
							<li><a href="{$PAGE_LINK*,_SELF:_SELF:browse:{_loop_key}{$?,{BLOG},:blog=1,}}">{_loop_var*}</a></li>
						{+END}
					</ul>

					{+START,IF,{$NOT,{$_GET,blog}}}
						{$,Actually breadcrumbs will do fine!,<div>
							<a class="btn btn-primary btn-scr buttons--all2" rel="archives" href="\{ARCHIVE_URL*\}">{+START,INCLUDE,ICON}NAME=buttons/all2{+END} <span>\{!VIEW_ARCHIVE\}</span></a>
						</div>}
					{+END}
				</nav>
			</div>
			<div class="ratings right force-normal-margin">
				{RATING_DETAILS}
			</div>
		{+END}
	</div>

	<div class="clearfix lined-up-boxes">
	</div>

	{$REVIEW_STATUS,news,{ID}}

	<div class="content-screen-comments">
		{COMMENT_DETAILS}
	</div>

	{+START,IF_NON_EMPTY,{TRACKBACK_DETAILS}}
		<div class="clearfix">
			<div class="trackbacks">
				{TRACKBACK_DETAILS}
			</div>
		</div>
	{+END}

	{+START,IF,{$THEME_OPTION,show_screen_actions}}{+START,IF_PASSED,_TITLE}{$BLOCK,failsafe=1,block=main_screen_actions,title={_TITLE}}{+END}{+END}
</div>
