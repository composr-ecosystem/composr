{$REQUIRE_JAVASCRIPT,checking}
{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$REQUIRE_JAVASCRIPT,core_themeing}

<div data-tpl="themeImageEditScreen">
	{TITLE}

	{+START,IF_PASSED,PING_URL}
		{+START,IF_PASSED,PING_URL}
	{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}
	{+END}
	{+START,IF_PASSED,WARNING_DETAILS}
		{WARNING_DETAILS}
	{+END}

	<div class="box box---theme-image-preview"><div class="box-inner">
		<h2>{!CURRENT}</h2>

		<div class="clearfix">
			<img class="{$?,{$GT,{WIDTH},300},theme-image-preview-wide,theme-image-preview}" src="{IMAGE_URL*}" alt="{!THEME_IMAGE}" />

			{+START,IF,{$NEQ,{$SUBSTR,{IMAGE_URL},-4},.svg}}
				<p>{!THEME_IMAGE_CURRENTLY_LIKE,{!THEME_IMAGE_CURRENTLY_LIKE_DIMENSIONS,{WIDTH*},{HEIGHT*}}}</p>
			{+END}
			{+START,IF,{$EQ,{$SUBSTR,{IMAGE_URL},-4},.svg}}
				<p>{!THEME_IMAGE_CURRENTLY_LIKE,{!THEME_IMAGE_CURRENTLY_LIKE_VECTOR}}</p>
			{+END}

			{+START,IF,{UNMODIFIED}}
				<p>{!THEME_IMAGE_CURRENTLY_UNMODIFIED}</p>
			{+END}

			<p>{!RIGHT_CLICK_SAVE_AS}</p>
		</div>
	</div></div>

	{+START,IF_PASSED,URL_THEMEWIZARD}{+START,IF_PASSED,HIDDEN_THEMEWIZARD}{+START,IF_PASSED,FIELDS_THEMEWIZARD}
	<div class="clearfix"><div class="tabs" role="tablist">
		<a aria-controls="g-file" role="tab" href="#!" id="t-file" class="tab tab-active tab-first js-click-select-tab-g" data-tp-tab="file"><span>{!FILE}</span></a>

		<a aria-controls="g-themewizard" role="tab" href="#!" id="t-themewizard" class="tab tab-last js-click-select-tab-g" data-tp-tab="themewizard"><span>{!THEMEWIZARD}</span></a>
	</div></div>
	<div class="tab-surround">
		<div aria-labelledby="t-file" role="tabpanel" id="g-file" style="display: block">
	{+END}{+END}{+END}

			{+START,IF_NON_EMPTY,{TEXT_EDIT_FILE}}
				<div class="form-text">{$PARAGRAPH,{TEXT_EDIT_FILE}}</div>
			{+END}

			{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}

			<form title="{!PRIMARY_PAGE_FORM}" id="main-form" method="post" action="{URL_EDIT_FILE*}" enctype="multipart/form-data" target="_top" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}FORM_NAME=main-form{+END}">
				{$INSERT_FORM_POST_SECURITY}

				<div>
					{HIDDEN_EDIT_FILE}

					<table class="map-table form-table wide-table scrollable-inside">
						{+START,IF,{$DESKTOP}}
							<colgroup>
								<col class="field-name-column" />
								<col class="field-input-column" />
							</colgroup>
						{+END}

						<tbody>
							{FIELDS_EDIT_FILE}
						</tbody>
					</table>

					{+START,INCLUDE,FORM_STANDARD_END}
						SUBMIT_ICON=admin/edit_this
						SUBMIT_NAME={!SAVE}
					{+END}
				</div>
			</form>
	{+START,IF_PASSED,URL_THEMEWIZARD}{+START,IF_PASSED,HIDDEN_THEMEWIZARD}{+START,IF_PASSED,FIELDS_THEMEWIZARD}
		</div>

		<div aria-labelledby="t-themewizard" role="tabpanel" id="g-themewizard" style="display: none">
			<p>{!THEME_IMAGE_RECOLOUR_DESCRIPTION}</p>

			<form id="themewizard-form" title="{!THEMEWIZARD}" class="float-surrounder" method="post" action="{URL_THEMEWIZARD*}" enctype="multipart/form-data" target="_top" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}
				FORM_NAME=themewizard-form
				PREVIEW=1
			{+END}">
				{$INSERT_FORM_POST_SECURITY}

				<div>
					{HIDDEN_THEMEWIZARD}

					<table class="map-table form-table wide-table scrollable-inside">
						{+START,IF,{$DESKTOP}}
							<colgroup>
								<col class="field-name-column" />
								<col class="field-input-column" />
							</colgroup>
						{+END}

						<tbody>
							{FIELDS_THEMEWIZARD}
						</tbody>
					</table>

					{+START,INCLUDE,FORM_STANDARD_END}
						SUBMIT_ICON=menu/adminzone/style/themes/themewizard
						SUBMIT_NAME={!SAVE}
						PREVIEW=1
						SECONDARY_FORM=1
					{+END}
				</div>
			</form>
		</div>
	</div>
	{+END}{+END}{+END}
</div>
