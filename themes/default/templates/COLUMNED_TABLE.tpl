<table class="columned-table wide-table results-table autosized-table{+START,IF,{$NOT,{NONRESPONSIVE}}} responsive-table{+END}">
	{+START,IF_PASSED,HEADER_ROW}
		<thead>
			{HEADER_ROW}
		</thead>
	{+END}

	<tbody>
		{ROWS}
	</tbody>
</table>
