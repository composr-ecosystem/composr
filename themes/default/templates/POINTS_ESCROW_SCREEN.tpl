{TITLE}

<table class="map-table wide-table results-table spaced-table autosized-table{+START,IF_PASSED_AND_TRUE,RESPONSIVE} responsive-blocked-table{+END}">
	<tbody>
		{FIELDS}
	</tbody>
</table>

{+START,IF_PASSED,BUTTONS}
	<div class="buttons-group">
		<div class="buttons-group-inner">
			{BUTTONS}
		</div>
	</div>
{+END}

<h2>{!ESCROW_LOGS}</h2>

{ESCROW_LOGS}
