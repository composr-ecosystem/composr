<div data-view="CatalogueAddingScreen">
	{TITLE}

	{$PARAGRAPH,{TEXT}}

	{+START,INCLUDE,FORM_SCREEN_ARE_REQUIRED}{+END}

	<form title="{!PRIMARY_PAGE_FORM}" method="post" action="{URL*}" target="_top" id="catalogue-form" class="js-form-catalogue-add">
		{$INSERT_FORM_POST_SECURITY}

		<div>
			{HIDDEN}

			{+START,IF_NON_EMPTY,{FIELDS}}
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
			{+END}

			<h2>{!FIELDS_NEW}</h2>

			<p>{!FIELDS_NEW_HELP}</p>
			{FIELDS_NEW}

			{+START,INCLUDE,FORM_STANDARD_END}
				FORM_NAME=catalogue-form
				SUPPORT_AUTOSAVE=1
			{+END}
		</div>
	</form>
</div>
