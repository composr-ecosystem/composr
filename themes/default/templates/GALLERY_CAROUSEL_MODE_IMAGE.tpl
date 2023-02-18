<div class="gallery-carousel-mode-entry is-image" itemscope="itemscope" itemtype="http://schema.org/ImageObject">
	<div class="media-box">
		<img src="{$THUMBNAIL*,{IMAGE_URL}}" {+START,IF_EMPTY,{_TITLE}}alt="{!IMAGE}"{+END} {+START,IF_NON_EMPTY,{_TITLE}}alt="{_TITLE*}"{+END} itemprop="contentURL" />
	</div>

	{+START,IF_NON_EMPTY,{_TITLE}}
		<h2 class="entry-title">{+START,FRACTIONAL_EDITABLE,{_TITLE},title,_SEARCH:cms_galleries:__edit:{ID},1,0,{$HAS_EDIT_PERMISSION,mid,{SUBMITTER},{$MEMBER},cms_galleries,galleries,{CAT}}}{_TITLE*}{+END}</h2>
	{+END}

	{+START,IF_PASSED,DESCRIPTION}{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<div class="entry-description" itemprop="caption">
			{$PARAGRAPH,{DESCRIPTION}}
		</div>
	{+END}{+END}

	<div class="entry-boxes lined-up-boxes">
		<div class="box gallery-entry-meta-details left" role="note">
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
							<a href="{VIEW_URL*}">{$COMMENT_COUNT,images,{ID}}</a>
						</li>
					{+END}

					{$,{+START,IF,{$ADDON_INSTALLED,recommend}}
					<li>
						{+START,INCLUDE,ICON}NAME=file_types/email_link{+END}
						<a href="{$PAGE_LINK*,:recommend:browse:subject={!ECARD_FOR_YOU_SUBJECT}:page_title={!SEND_AS_ECARD}:s_message={!ECARD_FOR_YOU,{$SELF_URL},{IMAGE_URL},{$SITE_NAME}}}">{!SEND_AS_ECARD}</a>
					</li>
					{+END}}

					{+START,IF_NON_EMPTY,{EDIT_URL}}
						<li>
							{+START,INCLUDE,ICON}NAME=admin/edit_this{+END}
							<a href="{EDIT_URL*}">{!EDIT_THIS_IMAGE}</a>
						</li>
					{+END}
				</ul>
			</div>
		</div>

		{+START,IF_NON_EMPTY,{RATING_DETAILS}}
			<div class="ratings right">
				{RATING_DETAILS}
			</div>
		{+END}
	</div>
</div>
