{+START,IF_NON_EMPTY,{SUMMARY}}
	<p class="accessibility-hidden">
		{SUMMARY*}
	</p>
{+END}
<div class="fake-table{+START,IF_PASSED,CLASS} {CLASS*}{+END}{+START,IF,{IS_COLUMNED_TABLE}} columned-table responsive-table{+END}{+START,IF,{IS_WIDE}} wide-table{+END}"{+START,IF_PASSED,ID} id="{ID*}"{+END}>
	{+START,IF_NON_EMPTY,{COLUMN_SIZES}}
		<div class="fake-colgroup">
			{+START,LOOP,COLUMN_SIZES}
				<div class="fake-col" style="width: {_loop_var*}"></div>
			{+END}
		</div>
	{+END}

	{+START,LOOP,ROWS}
		{+START,IF,{IS_HEADER_ROW}}<div class="fake-thead">{+END}
		{+START,IF,{FIRST_NON_HEADER_ROW}}<div class="fake-tbody">{+END}
		<div class="fake-tr">
			{+START,LOOP,CELLS}
				<div class="{+START,IF,{IS_HEADER}}fake-th {+END}fake-td">
					{VALUE}
				</div>
			{+END}
		</div>
		{+START,IF,{LAST_NON_HEADER_ROW}}</div>{+END}
		{+START,IF,{IS_HEADER_ROW}}</div>{+END}
	{+END}
</div>
