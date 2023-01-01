<table class="map-table wide-table results-table spaced-table autosized-table{+START,IF_PASSED_AND_TRUE,RESPONSIVE} responsive-blocked-table{+END}">
	{+START,IF,{$DESKTOP}}{+START,IF,{$EQ,{$LANG},EN}}{+START,IF_PASSED,WIDTHS}
		<colgroup>
			{+START,LOOP,WIDTHS}
				<col style="width: {_loop_var}{+START,IF,{$NOT,{$IN_STR,{_loop_var},px,%}}}px{+END}" />
			{+END}
		</colgroup>
	{+END}{+END}{+END}

	<tbody>
		{FIELDS}
	</tbody>
</table>
