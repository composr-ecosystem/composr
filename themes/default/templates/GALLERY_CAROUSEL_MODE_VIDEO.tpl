<div class="gallery-carousel-mode-entry is-video" itemscope="itemscope" itemtype="http://schema.org/VideoObject">
	<div class="media-box">
		{VIDEO_PLAYER}
	</div>

	{+START,IF_NON_EMPTY,{_TITLE}}
		<h2 class="entry-title">{+START,FRACTIONAL_EDITABLE,{_TITLE},title,_SEARCH:cms_galleries:__edit_other:{ID},1,0,{$HAS_EDIT_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries,galleries,{CAT}}}{_TITLE*}{+END}</h2>
	{+END}

	{+START,IF_PASSED,DESCRIPTION}
		<div class="entry-description" itemprop="caption">
			{$PARAGRAPH,{DESCRIPTION}}
		</div>
	{+END}

	<div class="entry-boxes lined-up-boxes">
		<div class="box gallery-entry-meta-details" role="note">
			<div class="box-inner">
				<ul class="horizontal-links">
					<li>
						{+START,INCLUDE,ICON}NAME=menu/rich_content/calendar{+END}
						{!ADDED}
						<a href="{VIEW_URL*}"><time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{ADD_DATE_RAW}}" itemprop="datePublished">{$DATE_TIME*,{ADD_DATE_RAW}}</time></a>
					</li>

					<li>
						{+START,INCLUDE,ICON}NAME=content_types/member{+END}
						{!BY}
						<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}" itemprop="author">{$USERNAME*,{SUBMITTER},1}</a>
						{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
					</li>

					{+START,IF_NON_EMPTY,{EDIT_DATE_RAW}}
						<li>{+START,INCLUDE,ICON}NAME=admin/edit{+END} {!EDITED} {$DATE_TIME*,{EDIT_DATE_RAW}}</li>
					{+END}

					{+START,IF,{$INLINE_STATS}}
						<li>{+START,INCLUDE,ICON}NAME=cns_topic_modifiers/hot{+END} {VIEWS*} {!COUNT_VIEWS}</li>
					{+END}

					{+START,IF_PASSED_AND_TRUE,COMMENT_COUNT}
						<li>
							{+START,INCLUDE,ICON}NAME=feedback/comment{+END}
							<a href="{VIEW_URL*}">{$COMMENT_COUNT,videos,{ID}}</a>
						</li>
					{+END}

					{+START,IF_NON_EMPTY,{EDIT_URL}}
						<li>
							{+START,INCLUDE,ICON}NAME=admin/edit_this{+END}
							<a href="{EDIT_URL*}">{!EDIT_THIS_VIDEO}</a>
						</li>
					{+END}
				</ul>
			</div>
		</div>

		<div class="video-details right">
			{VIDEO_DETAILS}
		</div>

		{+START,IF_NON_EMPTY,{RATING_DETAILS}}
			<div class="clearfix">
				<div class="ratings left">
					{RATING_DETAILS}
				</div>
			</div>
		{+END}
	</div>
</div>
