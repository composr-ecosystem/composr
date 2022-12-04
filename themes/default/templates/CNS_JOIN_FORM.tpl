{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$REQUIRE_JAVASCRIPT,core_cns}
{$REQUIRE_JAVASCRIPT,checking}
{$SET,form_name,form-{$RAND}}

<div data-tpl="joinForm" data-tpl-params="{+START,PARAMS_JSON,USERNAME_CHECK_SCRIPT,SNIPPET_SCRIPT,INVITES_ENABLED,ONE_PER_EMAIL_ADDRESS,USE_CAPTCHA}{_*}{+END}">
	{+START,IF_NON_EMPTY,{TEXT}}
		{$PARAGRAPH,{TEXT}}
	{+END}

	{+START,IF_NON_PASSED_OR_FALSE,SKIP_REQUIRED}
		{+START,IF,{$IN_STR,{FIELDS},required-star}}
			{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
		{+END}
	{+END}

	<form title="{!PRIMARY_PAGE_FORM}" class="form-join"{+START,IF_PASSED_AND_TRUE,MODSECURITY_WORKAROUND} data-submit-modsecurity-workaround="1"{+END} method="post" action="{URL*}"{+START,IF,{$IN_STR,{FIELDS},"file"}} enctype="multipart/form-data"{+END} id="{$GET*,form_name}" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}FORM_NAME={$GET,form_name}{+END}">
		{$INSERT_FORM_POST_SECURITY}

		{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}

		<div>
			{HIDDEN}

			<table class="map-table form-table wide-table">
				{+START,IF,{$DESKTOP}}
					<colgroup>
						<col class="field-name-column" />
						<col class="field-input-column" />
					</colgroup>
				{+END}

				<tbody>
					{FIELDS}
				</tbody>
			</table>

			{+START,IF_NON_EMPTY,{SUBMIT_NAME}}
				{+START,INCLUDE,FORM_STANDARD_END}{+END}
			{+END}
		</div>
	</form>
</div>
