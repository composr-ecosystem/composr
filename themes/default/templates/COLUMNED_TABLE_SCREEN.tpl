<div data-tpl="columnedTableScreen" data-tpl-params="{+START,PARAMS_JSON,JS_FUNCTION_CALLS}{_*}{+END}">
	{TITLE}

	{$SET,DEFER_RESULTS_TABLE_PAGINATION,1}

	{+START,IF_PASSED,TEXT}
		{$PARAGRAPH,{TEXT}}
	{+END}

	{+START,IF_PASSED,FILTERCODE_BOX}
		{+START,INCLUDE,FILTER_BOX}{+END}
	{+END}

	{+START,IF_PASSED,POST_URL}{+START,IF_PASSED,SUBMIT_NAME}
		<form title="{!PRIMARY_PAGE_FORM}"{+START,IF_PASSED,FORM_ID} id="{FORM_ID}"{+END}{+START,IF_NON_PASSED_OR_FALSE,GET} method="post"{+END}{+START,IF_PASSED_AND_TRUE,GET} method="get"{+END} action="{POST_URL*}">
			{+START,IF_NON_PASSED_OR_FALSE,GET}{$INSERT_FORM_POST_SECURITY}{+END}

			{$SET,DEFER_RESULTS_TABLE_BROWSER,1}
			{TABLE}
			{$SET,DEFER_RESULTS_TABLE_BROWSER,0}

			{+START,IF_PASSED,FIELDS}
				<table class="map-table wide-table">
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
			{+END}

			{+START,IF_PASSED,HIDDEN}
				<div>
					{HIDDEN}
				</div>
			{+END}

			{+START,IF_PASSED,SUBMIT_NAME}
				<p class="proceed-button" id="selection-submit">
					<button class="btn btn-primary btn-scr" type="submit">{+START,INCLUDE,ICON}NAME={SUBMIT_ICON}{+END} {SUBMIT_NAME*}</button>
				</p>
			{+END}
		</form>

		{$GET,RESULTS_TABLE_BROWSER}
	{+END}{+END}

	{+START,IF_NON_PASSED,SUBMIT_NAME}
		{TABLE}
	{+END}

	{$GET,RESULTS_TABLE_PAGINATION}

	{$SET,DEFER_RESULTS_TABLE_PAGINATION,0}

	{+START,IF_PASSED,EXTRA}
		{+START,IF_PASSED,SUB_TITLE}<h2 class="force-margin">{SUB_TITLE*}</h2>{+END}

		{EXTRA}
	{+END}
</div>
