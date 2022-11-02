{$REQUIRE_JAVASCRIPT,ecommerce}

{+START,IF_PASSED,TEXT}
	{$PARAGRAPH,{TEXT}}
{+END}

{+START,IF_PASSED,FIELDS}
	<div class="wide-table-wrap"><table class="map-table form-table wide-table">
		{+START,IF,{$DESKTOP}}
			<colgroup>
				<col class="purchase-field-name-column" />
				<col class="purchase-field-input-column" />
			</colgroup>
		{+END}

		<tbody>
			{FIELDS}
		</tbody>
	</table></div>
{+END}
