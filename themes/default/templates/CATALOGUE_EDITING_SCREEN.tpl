<div data-view="CatalogueEditingScreen">
	{TITLE}

	{$PARAGRAPH,{TEXT}}

	<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}" target="_top" id="catalogue-form" data-submit-modsecurity-workaround="1" data-view="SubmissionFlow" data-view-params="{+START,INCLUDE,FORM_STANDARD_START}
		FORM_NAME=catalogue-form
		SUPPORT_AUTOSAVE=1
	{+END}">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			{HIDDEN}

			<div class="wide-table-wrap"><table class="map-table form-table wide-table scrollable-inside">
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

			<h2>{!FIELDS_EXISTING}</h2>
			{FIELDS_EXISTING}

			<h2>{!FIELDS_NEW}</h2>
			<p>{!FIELDS_NEW_HELP}</p>
			{FIELDS_NEW}

			{+START,INCLUDE,FORM_STANDARD_END}{+END}
		</div>
	</form>
</div>
