{+START,IF_NON_EMPTY,{SUMMARY}}
	<p class="accessibility-hidden">
		{SUMMARY*}
	</p>
{+END}
{+START,IF,{IS_WIDE}}<div class="wide-table-wrap">{+END}<table class="{+START,IF_PASSED,CLASS} {CLASS*}{+END}{+START,IF,{IS_COLUMNED_TABLE}} columned-table responsive-table{+END}{+START,IF,{IS_WIDE}} wide-table{+END}"{+START,IF_PASSED,ID} id="{ID*}"{+END}>
	{+START,IF_NON_EMPTY,{COLUMN_SIZES}}
		<colgroup>
			{+START,LOOP,COLUMN_SIZES}
				<col style="width: {_loop_var*}" />
			{+END}
		</colgroup>
	{+END}

	{+START,LOOP,ROWS}
		{+START,IF,{IS_HEADER_ROW}}<thead>{+END}
		{+START,IF,{FIRST_NON_HEADER_ROW}}<tbody>{+END}
		<tr>
			{+START,LOOP,CELLS}
				<{$?,{IS_HEADER},th,td}>
					{VALUE}
				</{$?,{IS_HEADER},th,td}>
			{+END}
		</tr>
		{+START,IF,{LAST_NON_HEADER_ROW}}</tbody>{+END}
		{+START,IF,{IS_HEADER_ROW}}</thead>{+END}
	{+END}
</table>{+START,IF,{IS_WIDE}}</div>{+END}
