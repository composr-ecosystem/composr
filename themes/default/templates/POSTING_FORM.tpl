{+START,IF,{$IN_STR,{SPECIALISATION}{SPECIALISATION2},_required}}
	{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
{+END}

<form title="{!PRIMARY_PAGE_FORM}" id="posting_form" method="post" enctype="multipart/form-data" action="{URL*}" autocomplete="off"{+START,IF_PASSED_AND_TRUE,MODSECURITY_WORKAROUND} onsubmit="return modsecurity_workaround(this);" data-modsecurity-workaround="1"{+END}>
	{$INSERT_SPAMMER_BLACKHOLE}

	<div>
		<div class="wide_table_wrap"><table class="map_table form_table wide_table">
			{+START,IF,{$NOT,{$MOBILE}}}
				<colgroup>
					<col class="field_name_column" />
					<col class="field_input_column" />
				</colgroup>
			{+END}

			<tbody>
				{SPECIALISATION}

				{+START,INCLUDE,POSTING_FIELD}
					NAME=post
					WORD_COUNTER=1
					URL={COMCODE_URL}
				{+END}

				{+START,IF,{$AND,{$IS_NON_EMPTY,{SPECIALISATION2}},{$OR,{$NOT,{$IN_STR,{SPECIALISATION2},<th colspan="2"}},{$LT,{$STRPOS,{SPECIALISATION2},<td},{$STRPOS,{SPECIALISATION2},<th colspan="2"}}}}}
					{+START,INCLUDE,FORM_SCREEN_FIELD_SPACER}
						TITLE={!OTHER_DETAILS}
						{+START,IF_PASSED_AND_TRUE,SPECIALISATION2_HIDDEN}SECTION_HIDDEN=1{+END}
					{+END}
				{+END}

				{SPECIALISATION2}
			</tbody>
		</table></div>

		{+START,INCLUDE,FORM_STANDARD_END}
			FORM_NAME=posting_form
			SUPPORT_AUTOSAVE={SUPPORT_AUTOSAVE}
			EXTRA_BUTTONS={$GET,extra_buttons}
		{+END}

		<input type="hidden" name="comcode__post" value="1" />
		<input type="hidden" name="posting_ref_id" value="{$RAND%}" />
	</div>
</form>

{+START,IF_PASSED,EXTRA}
	{EXTRA}
{+END}
