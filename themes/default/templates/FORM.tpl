{$REQUIRE_JAVASCRIPT,core_form_interfaces}
{$REQUIRE_JAVASCRIPT,checking}
{$SET,form_name,form-{$RAND}}

<div data-tpl="form" data-tpl-params="{+START,PARAMS_JSON,SKIPPABLE}{_*}{+END}">
	{+START,IF_NON_EMPTY,{TEXT}}
		{$PARAGRAPH,{TEXT}}
	{+END}

	{+START,IF_NON_PASSED_OR_FALSE,SKIP_REQUIRED}
		{+START,IF,{$IN_STR,{FIELDS},required-star}}
			{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
		{+END}
	{+END}

	{$SET,might_have_file_input,{$IN_STR,{FIELDS},"file"}}
	<form title="{!PRIMARY_PAGE_FORM}"{+START,IF_PASSED_AND_TRUE,MODSECURITY_WORKAROUND} data-submit-modsecurity-workaround="1"{+END}{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END}{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{URL*}"{+START,IF,{$GET,might_have_file_input}} enctype="multipart/form-data"{+END}{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END}{+START,IF_NON_PASSED,TARGET} target="_top"{+END} id="{$GET*,form_name}" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}FORM_NAME={$GET,form_name}{+END}">
		{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_FORM_POST_SECURITY}{+END}

		{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}

		{+START,IF_PASSED,SKIPPABLE}
			<div class="skip-step-button-wrap">
				<input type="hidden" id="{SKIPPABLE*}" name="{SKIPPABLE*}" value="0" />
				<div>
					<button data-disable-on-click="1" tabindex="151" class="btn btn-primary btn-scri buttons--skip js-btn-skip-step" type="submit">{+START,INCLUDE,ICON}NAME=buttons/skip{+END} {!SKIP}</button>
				</div>
			</div>
		{+END}

		<div>
			{HIDDEN}

			{+START,IF_NON_EMPTY,{FIELDS}}
				<table class="map-table form-table wide-table">
					{+START,IF_NON_PASSED,NO_SIZING}
						{+START,IF,{$DESKTOP}}
							<colgroup>
								<col class="field-name-column" />
								<col class="field-input-column" />
							</colgroup>
						{+END}
					{+END}

					<tbody>
						{FIELDS}
					</tbody>
				</table>
			{+END}

			{+START,IF_NON_EMPTY,{SUBMIT_NAME}}
				{+START,INCLUDE,FORM_STANDARD_END}{+END}
			{+END}
		</div>
	</form>
</div>
