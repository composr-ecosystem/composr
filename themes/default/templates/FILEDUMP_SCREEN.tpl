{$REQUIRE_JAVASCRIPT,filedump}

{$SET,file_link,}
{+START,IF_NON_EMPTY,{$_GET,filename}}
	{$SET,file_link,{$PAGE_LINK;,_SELF:_SELF:embed:subpath={$_GET&,subpath}:file={$_GET&,filename}:wide_high=1}}
{+END}

<div data-tpl="filedumpScreen" data-tpl-params="{+START,PARAMS_JSON,file_link}{_*}{+END}">
	{TITLE}

	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<div class="filedump-screen">
		<div class="clearfix"><div class="tabs" role="tablist">
			<a aria-controls="g-thumbnails" role="tab" href="#!" id="t-thumbnails" class="tab tab-active tab-first js-click-select-tab-g" data-tp-tab="thumbnails"><span>{!VIEW_THUMBNAILS}</span></a>

			<a aria-controls="g-listing" role="tab" href="#!" id="t-listing" class="tab{+START,IF_EMPTY,{CREATE_FOLDER_FORM}{UPLOAD_FORM}} tab-last{+END} js-click-select-tab-g" data-tp-tab="listing"><span>{!VIEW_LISTING}</span></a>

			{+START,IF_NON_EMPTY,{CREATE_FOLDER_FORM}}
				<a aria-controls="g-create-folder" role="tab" href="#!" id="t-create-folder" class="tab{+START,IF_EMPTY,{UPLOAD_FORM}} tab-last{+END} js-click-select-tab-g" data-tp-tab="create-folder"><span>{!FILEDUMP_CREATE_FOLDER}</span></a>
			{+END}

			{+START,IF_NON_EMPTY,{UPLOAD_FORM}}
				<a aria-controls="g-upload" role="tab" href="#!" id="t-upload" class="tab tab-last js-click-select-tab-g" data-tp-tab="upload"><span>{!UPLOAD}</span></a>
			{+END}
		</div></div>
		<div class="tab-surround">
			<div aria-labelledby="t-thumbnails" role="tabpanel" id="g-thumbnails" style="display: block">
				{+START,INCLUDE,FILEDUMP_SEARCH}
					I=1
					TAB=thumbnails
				{+END}

				<form title="{!ACTION}" action="{POST_URL*}#g-thumbnails" method="post">
					{$INSERT_FORM_POST_SECURITY}

					{+START,IF_NON_EMPTY,{THUMBNAILS}}
						<div class="clearfix filedump-thumbnails">
							{+START,LOOP,THUMBNAILS}
								<div class="box"><div class="box-inner">
									{+START,IF,{CHOOSABLE}}
										<span class="filedump-select">{ACTIONS}</span>
									{+END}

									{+START,IF_PASSED,EMBED_URL}
										<p class="filedump-embed"><a id="embed-link-{FILENAME|*}" href="{EMBED_URL*}" data-open-as-overlay='{"width": 950, "height": 680}' class="link-exempt">{!_FILEDUMP_EMBED}</a></p>
									{+END}

									<p><a {+START,IF,{IS_IMAGE}} rel="lightbox"{+END} href="{URL*}"><img class="img-thumb" alt="{DESCRIPTION*}" src="{$THUMBNAIL*,{IMAGE_URL}}" /></a></p>

									<p class="meta associated-details">
										<strong>{FILENAME*}</strong><br />
										<span class="associated-details">({+START,IF_NON_EMPTY,{_SIZE}}{SIZE*}{+END}{+START,IF_NON_EMPTY,{DATE}}{+START,IF_NON_EMPTY,{_SIZE}}, {+END}{DATE*}{+END}{+START,IF_NON_EMPTY,{WIDTH}}, {WIDTH*}&times;{HEIGHT*}{+END})</span>
									</p>
								</div></div>
							{+END}
						</div>
					{+END}
					{+START,IF_EMPTY,{THUMBNAILS}}
						<p class="nothing-here">{!NO_ENTRIES}</p>
					{+END}

					{+START,IF,{SOMETHING_EDITABLE}}
						{+START,INCLUDE,FILEDUMP_FOOTER}I=1{+END}
					{+END}
				</form>

				{+START,INCLUDE,NOTIFICATION_BUTTONS}
					NOTIFICATIONS_TYPE=filedump
					NOTIFICATIONS_ID={SUBPATH}
					RIGHT=1
				{+END}

				{+START,IF_NON_EMPTY,{PAGINATION_THUMBNAILS}}
					<div class="clearfix force-margin">
						{PAGINATION_THUMBNAILS}
					</div>
				{+END}
			</div>

			<div aria-labelledby="t-listing" role="tabpanel" id="g-listing" style="display: none">
				{+START,INCLUDE,FILEDUMP_SEARCH}
					I=2
					TAB=listing
				{+END}

				<form title="{!ACTION}" action="{POST_URL*}#g-listing" method="post">
					{$INSERT_FORM_POST_SECURITY}

					{+START,IF_NON_EMPTY,{LISTING}}
						{LISTING}
					{+END}
					{+START,IF_EMPTY,{LISTING}}
						<p class="nothing-here">{!NO_ENTRIES}</p>
					{+END}

					{+START,IF,{SOMETHING_EDITABLE}}
						{+START,INCLUDE,FILEDUMP_FOOTER}I=2{+END}
					{+END}
				</form>

				{+START,INCLUDE,NOTIFICATION_BUTTONS}
					NOTIFICATIONS_TYPE=filedump
					NOTIFICATIONS_ID={SUBPATH}
					RIGHT=1
				{+END}

				{+START,IF_NON_EMPTY,{PAGINATION_LISTING}}
					<div class="clearfix force-margin">
						{PAGINATION_LISTING}
					</div>
				{+END}
			</div>

			{+START,IF_NON_EMPTY,{CREATE_FOLDER_FORM}}
				<div aria-labelledby="t-create-folder" role="tabpanel" id="g-create-folder" style="display: none">
					{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}

					{CREATE_FOLDER_FORM}
				</div>
			{+END}

			{+START,IF_NON_EMPTY,{UPLOAD_FORM}}
				<div aria-labelledby="t-upload" role="tabpanel" id="g-upload" style="display: none">
					{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}

					{UPLOAD_FORM}
				</div>
			{+END}
		</div>
	</div>

	{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
	{+START,IF,{$AND,{$SHOW_DOCS},{$HAS_PRIVILEGE,see_software_docs}}}
		{+START,INCLUDE,STAFF_ACTIONS}
			STAFF_ACTIONS_TITLE={!STAFF_ACTIONS}

			1_URL={$TUTORIAL_URL*,tut_collaboration}
			1_TITLE={!HELP}
			1_REL=help
			1_ICON=help

			{+START,IF,{$IS_ADMIN}}
				2_URL={$PAGE_LINK*,_SELF:_SELF:broken}
				2_TITLE={!FIND_BROKEN_FILEDUMP_LINKS}
				2_ICON=menu/adminzone/tools/cleanup
			{+END}
		{+END}
	{+END}
</div>
