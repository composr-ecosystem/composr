{+START,IF_NON_EMPTY,{TEXT}}
	{$PARAGRAPH,{TEXT}}
{+END}

{+START,IF_NON_PASSED_OR_FALSE,SKIP_REQUIRED}
	{+START,IF,{$IN_STR,{FIELDS},required_star}}
		{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
	{+END}
{+END}

{$SET,form_name,form_{$RAND}}

{$REQUIRE_JAVASCRIPT,checking}
<form title="{!PRIMARY_PAGE_FORM}"{+START,IF_PASSED,TARGET} target="{TARGET*}"{+END}{+START,IF_NON_PASSED_OR_FALSE,GET} method="post" action="{URL*}"{+START,IF,{$IN_STR,{FIELDS},"file"}} enctype="multipart/form-data"{+END}{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get" action="{$URL_FOR_GET_FORM*,{URL}}"{+END}{+START,IF_NON_PASSED,TARGET} target="_top"{+END} id="{$GET*,form_name}" autocomplete="off"{+START,IF_PASSED_AND_TRUE,MODSECURITY_WORKAROUND} onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1"{+END}>
	{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_SPAMMER_BLACKHOLE}{+END}

	{+START,IF_PASSED_AND_TRUE,GET}{$HIDDENS_FOR_GET_FORM,{URL}}{+END}

	{+START,IF_PASSED,SKIPPABLE}
		{+START,IF,{$JS_ON}}
			<div class="skip_step_button_wrap">
				<input type="hidden" id="{SKIPPABLE*}" name="{SKIPPABLE*}" value="0" />
				<div>
					<input onclick="document.getElementById('{SKIPPABLE;*}').value='1'; disable_button_just_clicked(this);" tabindex="151" class="button_screen_item buttons__skip" type="submit" value="{!SKIP}" />
				</div>
			</div>
		{+END}
	{+END}

	<div>
		{HIDDEN}

		<div class="wide_table_wrap"><table class="map_table form_table wide_table">
			{+START,IF_NON_PASSED,NO_SIZING}
				{+START,IF,{$NOT,{$MOBILE}}}
					<colgroup>
						<col class="field_name_column" />
						<col class="field_input_column" />
					</colgroup>
				{+END}
			{+END}

			<tbody>
				{FIELDS}
			</tbody>
		</table></div>

		{+START,IF_NON_EMPTY,{SUBMIT_NAME}}
			{+START,INCLUDE,FORM_STANDARD_END}FORM_NAME={$GET,form_name}{+END}
		{+END}
	</div>
</form>

