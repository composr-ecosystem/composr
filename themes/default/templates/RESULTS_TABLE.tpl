{+START,IF_PASSED_AND_TRUE,INTERACTIVE}
	{$REQUIRE_CSS,sortable_tables}
	{$REQUIRE_JAVASCRIPT,sortable_tables}
{+END}

{+START,IF_PASSED,TEXT_ID}{$SET,TEXT_ID,{TEXT_ID}}{+END}

{+START,IF_EMPTY,{RESULT_ENTRIES}}
	<p class="nothing-here">
		{!NO_ENTRIES}
	</p>
{+END}

{+START,IF_NON_EMPTY,{RESULT_ENTRIES}}
	{$PARAGRAPH,{MESSAGE}}

	<div class="wide-table-wrap"><table class="columned-table results-table wide-table{+START,IF_EMPTY,{WIDTHS}} autosized-table{+END}{+START,IF,{$NOT,{NONRESPONSIVE}}} responsive-table{+END}{+START,IF_PASSED_AND_TRUE,INTERACTIVE} sortable_table table-autosort:2 table-autofilter{+END}" itemprop="significantLinks">
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
		{+START,IF_PASSED,FOOTER_ROW}
			<tfoot>
				<tr>
					{FOOTER_ROW}
				</tr>
			</tfoot>
		{+END}
	</table></div>

	{+START,SET,RESULTS_TABLE_PAGINATION}
		{+START,IF_NON_EMPTY,{SORT}{PAGINATION}}
			<div class="box results-table-under"><div class="box-inner clearfix">
				{+START,IF_NON_EMPTY,{SORT}}
					<div class="results-table-sorter">
						{SORT}
					</div>
				{+END}

				{PAGINATION}
			</div></div>
		{+END}
	{+END}
	{+START,IF,{$NOT,{$GET,DEFER_RESULTS_TABLE_PAGINATION}}}
		{$GET,RESULTS_TABLE_PAGINATION}
	{+END}
{+END}
