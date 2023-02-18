{$REQUIRE_JAVASCRIPT,jquery}

<div class="gallery-entry-screen" id="gallery-entry-screen" itemscope="itemscope" itemtype="http://schema.org/{+START,IF_PASSED,VIDEO}Video{+END}{+START,IF_NON_PASSED,VIDEO}Image{+END}Object">
	{TITLE}

	{WARNING_DETAILS}

	{NAV}

	{+START,SET,boxes}
		<div class="box gallery-entry-meta-details left" role="note">
			<div class="box-inner">
				<ul class="horizontal-links vertical-alignment-normalise-line-height">
					<li>
						{+START,INCLUDE,ICON}NAME=menu/rich_content/calendar{+END} 
						<span>{!ADDED} <time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{ADD_DATE_RAW}}" itemprop="datePublished">{ADD_DATE*}</time></span>
					</li>

					<li>
						{+START,INCLUDE,ICON}NAME=content_types/member{+END}
						<span>
							{!BY}
							<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}" itemprop="author">{$USERNAME*,{SUBMITTER},1}</a>
							{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
						</span>
					</li>

					{+START,IF_NON_EMPTY,{EDIT_DATE}}
						<li>{+START,INCLUDE,ICON}NAME=admin/edit{+END} <span>{!EDITED} {EDIT_DATE*}</span></li>
					{+END}

					{+START,IF,{$INLINE_STATS}}
						<li>{+START,INCLUDE,ICON}NAME=cns_topic_modifiers/hot{+END} <span>{VIEWS*} {!COUNT_VIEWS}</span></li>
					{+END}

					{+START,IF_NON_EMPTY,{RATING_DETAILS}}
						{$SET-,rating,{$RATING,{MEDIA_TYPE}s,{ID},{SUBMITTER},,,RATING_INLINE_DYNAMIC}}
						{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
							<li><span>{!RATING} {$GET,rating}</span></li>
						{+END}
					{+END}

					{+START,IF_NON_EMPTY,{$REVIEW_STATUS,{MEDIA_TYPE},{ID}}}
						<li>{$REVIEW_STATUS,{MEDIA_TYPE},{ID}}</li>
					{+END}

					{+START,IF,{$ADDON_INSTALLED,recommend}}{+START,IF,{$CONFIG_OPTION,enable_ecards}}{+START,IF_NON_PASSED,VIDEO}
						<li>
							{+START,INCLUDE,ICON}NAME=file_types/email_link{+END}
							<a href="{$PAGE_LINK*,:recommend:browse:subject={!ECARD_FOR_YOU_SUBJECT}:page_title={!SEND_AS_ECARD}:s_message={!ECARD_FOR_YOU,{$SELF_URL},{URL*},{$SITE_NAME}}:ecard=1}">{!SEND_AS_ECARD}</a>
						</li>
					{+END}{+END}{+END}
				</ul>
			</div>
		</div>
	{+END}

	<div class="media-box">
		{+START,IF_NON_PASSED,VIDEO}
			<img {+START,IF_EMPTY,{E_TITLE}} alt="{!IMAGE}"{+END} {+START,IF_NON_EMPTY,{E_TITLE}}alt="{E_TITLE*}"{+END} src="{$ENSURE_PROTOCOL_SUITABILITY*,{URL}}" itemprop="contentURL" />
		{+END}
		{+START,IF_PASSED,VIDEO}
			{VIDEO}
			<!-- <p><a href="{URL*}">{!TO_DOWNLOAD_VIDEO}</a></p> -->
		{+END}
	</div>

	{+START,IF_NON_EMPTY,{DESCRIPTION}}
		<div class="entry-description" itemprop="caption">
			{$PARAGRAPH,{DESCRIPTION}}
		</div>
	{+END}

	<div class="clearfix lined-up-boxes">
		{$GET,boxes}
	</div>

	{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,{MEDIA_TYPE},{ID}}}
	{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}

	{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

	{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={$METADATA,title}}{+END}

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,INCLUDE,STAFF_ACTIONS}
		1_URL={EDIT_URL*}
		1_TITLE={!EDIT}
		1_REL=edit
		1_ICON=admin/edit_this
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			2_URL={$PAGE_LINK*,_SEARCH:report_content:content_type={MEDIA_TYPE}:content_id={ID}:redirect={$SELF_URL&}}
			2_TITLE={!report_content:REPORT_THIS}
			2_ICON=buttons/report
			2_REL=report
		{+END}
	{+END}

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
<!--DO_NOT_REMOVE_THIS_COMMENT--></div>
