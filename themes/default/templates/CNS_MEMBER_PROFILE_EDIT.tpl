{+START,IF,{$NOT,{$WIDE_HIGH}}}
	{+START,IF_NON_EMPTY,{$MESSAGES_TOP}}
		<div class="global-messages">
			{$MESSAGES_TOP}
		</div>
	{+END}
{+END}

{$REQUIRE_JAVASCRIPT,core_cns}
{$REQUIRE_JAVASCRIPT,checking}

<form data-tpl="cnsMemberProfileEdit" data-tpl-params="{+START,PARAMS_JSON,TABS}{_*}{+END}" class="cns-member-profile-edit form-table " title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}" enctype="multipart/form-data" id="main-form" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}
	FORM_NAME=main-form
	SUPPORT_AUTOSAVE=1
{+END}">
	{$INSERT_FORM_POST_SECURITY}

	{HIDDEN}

	{+START,IF,{$GT,{TABS},1}}
		<div class="modern-subtabs">
			<div class="modern-subtab-headers" role="tablist">
				{+START,LOOP,TABS}
					<div id="t-edit--{$LCASE,{TAB_CODE|*}}" class="{+START,IF,{TAB_FIRST}}tab-first tab-active{+END}{+START,IF,{TAB_LAST}} tab-last{+END}">
						<a class="js-click-select-edit-tab" data-tp-tab-code="{TAB_CODE*}" aria-controls="g-edit--{$LCASE,{TAB_CODE|*}}" role="tab" href="#!">{+START,IF_NON_EMPTY,{TAB_ICON}}
							{+START,INCLUDE,ICON}
								NAME={TAB_ICON}
								ICON_SIZE=24
							{+END}
						{+END}<span>{TAB_TITLE*}</span></a>
					</div>
				{+END}
			</div>
			<div class="modern-subtab-bodies">
	{+END}
				{+START,LOOP,TABS}
					{+START,IF,{$GT,{TABS},1}}
					<div aria-labelledby="t-edit--{$LCASE,{TAB_CODE|*}}" role="tabpanel" id="g-edit--{$LCASE*,{TAB_CODE|}}" style="display: {$?,{TAB_FIRST},block,none}">
					{+END}

						{+START,IF_NON_EMPTY,{TAB_TEXT}}
							{$PARAGRAPH,{TAB_TEXT}}
						{+END}

						{+START,IF_NON_PASSED_OR_FALSE,SKIP_REQUIRED}
							{+START,IF,{$IN_STR,{TAB_FIELDS},-required}}
								{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
							{+END}
						{+END}

						{+START,IF,{TAB_SINGLE_FIELD}}
							{TAB_FIELDS}
						{+END}

						{+START,IF,{$NOT,{TAB_SINGLE_FIELD}}}{+START,IF_NON_EMPTY,{TAB_FIELDS}}
							<table class="map-table form-table wide-table">
								{+START,IF,{$DESKTOP}}
									<colgroup>
										<col class="field-name-column-shorter" />
										<col class="field-input-column" />
									</colgroup>
								{+END}

								<tbody>
									{TAB_FIELDS}
								</tbody>
							</table>
						{+END}{+END}

					{+START,IF,{$GT,{TABS},1}}
					</div>
					{+END}
				{+END}
	{+START,IF,{$GT,{TABS},1}}
			</div>
		</div>
	{+END}

	{+START,IF_NON_EMPTY,{SUBMIT_NAME}}
		{+START,INCLUDE,FORM_STANDARD_END}
			SECONDARY_FORM=1
			BUTTON_ID=account-submit-button
		{+END}
	{+END}
</form>
