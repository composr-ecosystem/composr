<div data-view="PostingForm" data-view-params="{+START,PARAMS_JSON,MODSECURITY_WORKAROUND}{_*}{+END}">
	{+START,IF,{$IN_STR,{SPECIALISATION}{SPECIALISATION2},-required}}
		{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}
	{+END}

	<form title="{!PRIMARY_PAGE_FORM}" id="posting-form" method="post" enctype="multipart/form-data" action="{URL*}" class="js-submission-flow"{+START,IF_PASSED_AND_TRUE,MODSECURITY_WORKAROUND} data-submit-modsecurity-workaround="1"{+END} data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}FORM_NAME=posting-form{+END}">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			<div class="wide-table-wrap"><table class="map-table form-table wide-table">
				{+START,IF,{$DESKTOP}}
					<colgroup>
						<col class="field-name-column" />
						<col class="field-input-column" />
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
				EXTRA_BUTTONS={$GET^,extra_buttons}
			{+END}

			<input type="hidden" name="comcode__post" value="1" />
			<input type="hidden" name="posting_ref_id" value="{$RAND%}" />
		</div>
	</form>

	{+START,IF_PASSED,EXTRA}
		{EXTRA}
	{+END}
</div>
