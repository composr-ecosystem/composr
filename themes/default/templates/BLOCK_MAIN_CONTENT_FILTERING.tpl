{$REQUIRE_JAVASCRIPT,checking}

{+START,IF_NON_EMPTY,{FIELDS}}
	<form title="{!PRIMARY_PAGE_FORM}" method="get" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}" class="js-submission-flow" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}{+END}">
		{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,0,0,active_filter=<null>}}

		<div>
			<input type="hidden" name="active_filter" value="{ACTIVE_FILTER*}" />

			<div class="wide-table-wrap"><table class="map-table form-table wide-table">
				{+START,IF,{$DESKTOP}}
					<colgroup>
						<col class="field-name-column" />
						<col class="field-input-column" />
					</colgroup>
				{+END}

				<tbody>
					{FIELDS}
				</tbody>
			</table></div>

			{+START,INCLUDE,FORM_STANDARD_END}SUBMIT_NAME={!FILTER}{+END}
		</div>
	</form>
{+END}

{+START,IF_NON_EMPTY,{LINKS}}
	<ul>
		{+START,LOOP,LINKS}
			<li>
				{+START,IF,{ACTIVE}}
					{TITLE*}
				{+END}
				{+START,IF,{$NOT,{ACTIVE}}}
					<a href="{URL*}">{TITLE*}</a>
				{+END}
			</li>
		{+END}
	</ul>
{+END}
