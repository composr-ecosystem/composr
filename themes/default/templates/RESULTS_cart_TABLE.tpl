{+START,IF_EMPTY,{RESULT_ENTRIES}}
	<p class="nothing-here">
		{!NO_ENTRIES}
	</p>
{+END}
{+START,IF_NON_EMPTY,{RESULT_ENTRIES}}
	{MESSAGE}
	<div class="wide-table-wrap">
		<table class="columned-table cart-table results-table{+START,IF,{$NOT,{NONRESPONSIVE}}} responsive-table{+END}">
			{+START,IF,{$DESKTOP}}{+START,IF,{$EQ,{$LANG},EN}}{+START,IF_NON_EMPTY,{WIDTHS}}
				<colgroup>
					{+START,LOOP,WIDTHS}
						<col style="width: {_loop_var}{+START,IF,{$NOT,{$IN_STR,{_loop_var},px,%}}}px{+END}" />
					{+END}
				</colgroup>
			{+END}{+END}{+END}

			<thead>
				<tr>
					{HEADER_ROW}
				</tr>
			</thead>
			<tbody>
				{RESULT_ENTRIES}
			</tbody>
		</table>
	</div>
{+END}
