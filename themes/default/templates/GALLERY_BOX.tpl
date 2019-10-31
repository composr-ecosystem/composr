<div class="gallery-box">
	{+START,IF_NON_EMPTY,{THUMB}}
		<div class="gallery-box-thumb">
			<a href="{URL*}">{$TRIM,{THUMB}}</a>
		</div>
	{+END}

	<div class="gallery-box-details">
		{+START,SET,content_box_title}
			{+START,IF,{GIVE_CONTEXT}}
				{!CONTENT_IS_OF_TYPE,{!GALLERY},{TITLE*}}
			{+END}

			{+START,IF,{$NOT,{GIVE_CONTEXT}}}
				{+START,FRACTIONAL_EDITABLE,{TITLE},fullname,_SEARCH:cms_galleries:__edit_category:{ID}}{TITLE*}{+END}
			{+END}
		{+END}
		{+START,IF,{$NOT,{$GET,skip_content_box_title}}}
			<h3 class="gallery-box-title">{+START,IF_NON_EMPTY,{URL}}<a class="subtle-link" href="{URL*}">{+END}{$TRIM,{$GET,content_box_title}}{+START,IF_NON_EMPTY,{URL}}</a>{+END}</h3>
		{+END}

		<div class="gallery-box-meta">
			<ul class="horizontal-links">
				<li>{+START,INCLUDE,ICON}NAME=menu/rich_content/calendar{+END} <span class="desktop-only">{!ADDED}</span> <a href="{URL*}"><time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{ADD_DATE_RAW}}" itemprop="datePublished">{$DATE_TIME,{ADD_DATE_RAW}}</time></a></li>

				{+START,IF_PASSED,OWNER}
					<li>
						{+START,INCLUDE,ICON}NAME=content_types/member{+END}
						{!BY}
						<a rel="author" href="{$MEMBER_PROFILE_URL*,{OWNER}}" itemprop="author">{$USERNAME*,{OWNER},1}</a>
						{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={OWNER}{+END}
					</li>
				{+END}

				{+START,IF_PASSED_AND_TRUE,COMMENT_COUNT}
					<li>
						{+START,INCLUDE,ICON}NAME=feedback/comment{+END}
						<a href="{URL*}#comments-wrapper">{$COMMENT_COUNT,galleries,{ID}}</a>
					</li>
				{+END}
			</ul>
		</div>

		{+START,IF_PASSED,DESCRIPTION}{+START,IF_NON_EMPTY,{DESCRIPTION}}
			<div class="gallery-box-description">{$PARAGRAPH,{$TRUNCATE_LEFT,{DESCRIPTION},300,0,1}}</div>
		{+END}{+END}

		<ul class="gallery-box-contents-list">
			{$,Displays summary of gallery contents}
			{+START,IF,{$NEQ,{NUM_CHILDREN},0}}<li>{!SUBGALLERY_BITS,{NUM_CHILDREN}}</li>{+END}
			{+START,IF,{$NEQ,{NUM_IMAGES},0}}<li>{!_SUBGALLERY_BITS_IMAGES,{NUM_IMAGES}}</li>{+END}
			{+START,IF,{$NEQ,{NUM_VIDEOS},0}}<li>{!_SUBGALLERY_BITS_VIDEOS,,{NUM_VIDEOS}}</li>{+END}
			{+START,IF_PASSED,VIEWS}<li><strong>{VIEWS*}</strong> {!VIEWS}</li>{+END}
		</ul>

		{+START,IF_PASSED,BREADCRUMBS}{+START,IF_NON_EMPTY,{BREADCRUMBS}}
			<nav class="breadcrumbs" itemprop="breadcrumb"><p>
				{!LOCATED_IN,{BREADCRUMBS}}
			</p></nav>
		{+END}{+END}

		<div class="buttons-group">
			<div class="buttons-group-inner">
				<a class="btn btn-primary buttons--more" href="{URL*}"><span>{+START,INCLUDE,ICON}NAME=buttons/more{+END} {!VIEW}</span></a>
				{+START,IF_PASSED,SLIDESHOW_URL}{+START,IF_NON_EMPTY,{SLIDESHOW_URL}}
					<a class="btn btn-secondary buttons--slideshow" data-link-start-slideshow="{}" rel="nofollow" {+START,IF,{$DESKTOP}}title="{!LINK_NEW_WINDOW*}" target="_blank"{+END} href="{SLIDESHOW_URL*}"><span>{+START,INCLUDE,ICON}NAME=buttons/slideshow{+END} {!_SLIDESHOW}</span></a>
				{+END}{+END}
			</div>
		</div>
	</div>
</div>
