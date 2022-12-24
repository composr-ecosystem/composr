{$SET,num_mobile_columns,2}
{+START,LOOP,{NUM_COLUMNS}\,{$GET,num_mobile_columns}}
	{$SET,num_columns,{_loop_var}}
	<div class="{$?,{$EQ,{_loop_key},0},block-desktop,block-mobile}">
		<table class="columned-table autosized-table results-table wide-table">
			<thead>
				{$SET,i,0}
				<tr>
					{+START,WHILE,{$LT,{$GET,i},{$GET,num_columns}}}
						<th>{!CODE}</th>
						<th>{!IMAGE}</th>
						{$INC,i}
					{+END}
				</tr>
			</thead>

			<tbody>
				{$SET,i,0}
				<tr class="zebra-{$CYCLE*,emoticon_rows,0,1}">
				{+START,LOOP,EMOTICONS}
					{$SET,needs_new_row,{$AND,{$NEQ,{$GET,i},0},{$EQ,{$REM,{$GET,i},{$GET,num_columns}},0}}}
					{+START,IF,{$GET,needs_new_row}}
					</tr>
					<tr class="zebra-{$CYCLE*,emoticon_rows,0,1}">
					{+END}
						<td>{CODE*}</td>
						<td>{TPL}</td>
					{$INC,i}
				{+END}
				{+START,WHILE,{$NEQ,{$REM,{$GET,i},{$GET,num_columns}},0}}
					<td></td>
					<td></td>
					{$INC,i}
				{+END}
				</tr>
			</tbody>
		</table>
	</div>
{+END}
