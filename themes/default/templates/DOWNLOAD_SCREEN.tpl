<div itemscope="itemscope" itemtype="http://schema.org/ItemPage">
	{TITLE}

	{+START,IF_NON_EMPTY,{OUTMODE_URL}}
		{+START,INCLUDE,RED_ALERT}TEXT=<a href="{OUTMODE_URL*}">{!OUTMODED}</a>{+END}
	{+END}

	{WARNING_DETAILS}

	<div class="clearfix">
		<div class="download-meta-details" role="note">
			<div class="download-now-wrapper">
				<div class="box box---download-screen" data-toggleable-tray="{}">
					<div class="box-inner">
						{+START,IF_PASSED,LICENCE_HYPERLINK}
						<p class="download-licence">
							{!D_BEFORE_PROCEED_AGREE,{LICENCE_HYPERLINK}}
						</p>

						<div class="toggleable-tray-title tt-title-licence-agreement">
							<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!EXPAND}">{+START,INCLUDE,ICON}
								NAME=trays/expand
								ICON_SIZE=24
							{+END}</a>
							<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!">{!I_AGREE}</a>
						</div>

						<div class="toggleable-tray js-tray-content" style="display: none">
						{+END}
							<div class="download-now" itemprop="significantLinks">
								{+START,INCLUDE,ICON}
									NAME=menu/rich_content/downloads
									ICON_SIZE=24
								{+END}
								{+START,IF,{MAY_DOWNLOAD}}
									<p class="download-link associated-link suggested-link"><a {+START,IF,{$NOT,{$INLINE_STATS}}} data-click-stats-event-track="{ category: '{!DOWNLOAD;^*}', action: '{ORIGINAL_FILENAME;^*}', nativeTracking: false }"{+END} rel="nofollow" href="{DOWNLOAD_URL*}"><strong>{!DOWNLOAD_NOW}</strong></a></p>
								{+END}
								{+START,IF,{$NOT,{MAY_DOWNLOAD}}}
									<p>{!NO_DOWNLOAD_ACCESS}</p>
								{+END}
								<p class="download-filesize">({FILE_SIZE*})</p>
							</div>
						{+START,IF_PASSED,LICENCE_HYPERLINK}
						</div>
						{+END}
					</div>
				</div>
			</div>

			<div class="download-stats-wrapper">
				<table class="map-table download-stats results-table wide-table" role="note">
					{+START,IF,{$DESKTOP}}
						<colgroup>
							<col class="download-field-name-column" />
							<col class="download-field-value-column" />
						</colgroup>
					{+END}

					<tbody>
						<tr>
							<th class="de-th metadata-title">{!ADDED}</th>
							<td>
								<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{DATE_RAW}}" itemprop="datePublished">{DATE*}</time>
							</td>
						</tr>

						<tr>
							{+START,IF_NON_EMPTY,{AUTHOR_URL}}
								<th class="de-th metadata-title">{!BY}</th>
								<td>
									<a rel="author" href="{AUTHOR_URL*}" title="{!AUTHOR}: {AUTHOR*}">{AUTHOR*}</a>
									{+START,INCLUDE,MEMBER_TOOLTIP}SUBMITTER={$AUTHOR_MEMBER,{AUTHOR}}{+END}
								</td>
							{+END}

							{+START,IF_EMPTY,{AUTHOR_URL}}{+START,IF_NON_EMPTY,{$USERNAME,{SUBMITTER},1}}
								<th class="de-th metadata-title">{!BY}</th>
								<td>
									<a rel="author" href="{$MEMBER_PROFILE_URL*,{SUBMITTER}}">{$USERNAME*,{SUBMITTER},1}</a>
									{+START,INCLUDE,MEMBER_TOOLTIP}{+END}
								</td>
							{+END}{+END}
						</tr>

						{+START,IF_NON_EMPTY,{EDIT_DATE}}
							<tr>
								<th class="de-th metadata-title">{!EDITED}</th>
								<td>
									<time datetime="{$FROM_TIMESTAMP*,Y-m-d\TH:i:s\Z,{EDIT_DATE_RAW}}">{EDIT_DATE*}</time>
								</td>
							</tr>
						{+END}

						{+START,IF,{$INLINE_STATS}}
							<tr>
								<th class="de-th metadata-title">{!COUNT_VIEWS}</th>
								<td>{VIEWS*}</td>
							</tr>
						{+END}

						<tr>
							<th class="de-th metadata-title">{!COUNT_DOWNLOADS}</th>
							<td>
								<meta itemprop="interactionCount" content="UserDownloads:{$PREG_REPLACE*,[^\d],,{NUM_DOWNLOADS}}"/>
								{NUM_DOWNLOADS*}
							</td>
						</tr>

						{$SET,review_status,{$REVIEW_STATUS,download,{ID}}}
						{+START,IF_NON_EMPTY,{$GET,review_status}}
							<tr>
								<td colspan="2">
									{$GET,review_status}
								</td>
							</tr>
						{+END}
					</tbody>
				</table>
			</div>
		</div>

		<div class="download-description" itemprop="description">
			{+START,IF_NON_EMPTY,{DESCRIPTION}}
				{$PARAGRAPH,{DESCRIPTION}}
			{+END}

			{+START,IF_NON_EMPTY,{ADDITIONAL_DETAILS}}
				<h2>{!ADDITIONAL_INFO}</h2>

				{ADDITIONAL_DETAILS}
			{+END}

			{$SET,bound_catalogue_entry,{$CATALOGUE_ENTRY_FOR,download,{ID}}}
			{+START,IF_NON_EMPTY,{$GET,bound_catalogue_entry}}{$CATALOGUE_ENTRY_ALL_FIELD_VALUES,{$GET,bound_catalogue_entry}}{+END}
		</div>
	</div>

	{+START,IF_NON_EMPTY,{IMAGES_DETAILS}}
		<div class="box box---download-screen"><div class="box-inner">
			<h2>{!IMAGES}</h2>

			{$REQUIRE_JAVASCRIPT,core_rich_media}
			{$REQUIRE_CSS,carousels}

			{$SET-,carousel_id,{$RAND}}

			<div id="carousel-{$GET*,carousel_id}" class="carousel" style="display: none" data-view="Carousel" data-view-params="{+START,PARAMS_JSON,carousel_id}{_*}{+END}">
				<div class="move-left js-btn-car-move" data-move-amount="-100">{+START,INCLUDE,ICON}NAME=carousel/button_left{+END}</div>
				<div class="main"></div>
				<div class="move-right js-btn-car-move" data-move-amount="+100">{+START,INCLUDE,ICON}NAME=carousel/button_right{+END}</div>
			</div>

			<div class="carousel-temp" id="carousel-ns-{$GET*,carousel_id}">
				{IMAGES_DETAILS}
			</div>

			{$,<p class="download-start-slideshow"><span class="associated-link"><a target="_blank" title="\{!galleries:_SLIDESHOW\}: \{!LINK_NEW_WINDOW\}" href="\{$PAGE_LINK*,_SEARCH:galleries:image:\{$GET*,FIRST_IMAGE_ID\}:slideshow=1:wide_high=1\}">\{!galleries:_SLIDESHOW\}</a></span></p>}
		</div></div>
	{+END}

	{+START,IF,{$THEME_OPTION,show_content_tagging}}{TAGS}{+END}

	{+START,IF,{$THEME_OPTION,show_screen_actions}}{$BLOCK,failsafe=1,block=main_screen_actions,title={NAME}}{+END}

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,INCLUDE,STAFF_ACTIONS}
		1_URL={EDIT_URL*}
		1_TITLE={!EDIT}
		1_ACCESSKEY=q
		1_REL=edit
		1_ICON=admin/edit_this
		{+START,IF,{$ADDON_INSTALLED,galleries}}
			2_URL={ADD_IMAGE_URL*}
			2_TITLE={!ADD_IMAGE}
			2_ICON=admin/add
		{+END}
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			3_URL={$PAGE_LINK*,_SEARCH:report_content:content_type=download:content_id={ID}:redirect={$SELF_URL&}}
			3_TITLE={!report_content:REPORT_THIS}
			3_ICON=buttons/report
			3_REL=report
		{+END}
	{+END}

	{+START,IF_NON_EMPTY,{RATING_DETAILS}}
		<div class="clearfix">
			<div class="ratings">
				{RATING_DETAILS}
			</div>
		</div>
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
</div>
