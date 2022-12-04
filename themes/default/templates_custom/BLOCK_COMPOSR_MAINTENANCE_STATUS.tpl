<table class="columned-table wide-table results-table autosized-table">
	<thead>
		<tr>
			{+START,LOOP,HEADER_ROW}
				<th>{_loop_var*}</th>
			{+END}
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,ROWS}
			<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}">
				{+START,LOOP,DATA}
					{+START,IF,{$EQ,{_loop_key},0}}
						<th>
							<a id="feature-{CODENAME*}"></a>

							{_loop_var*}
						</th>
					{+END}

					{+START,IF,{$NEQ,{_loop_key},0}}
						<td>
							{_loop_var*}

							{+START,IF_EMPTY,{_loop_var}}
								{!NONE_EM}
							{+END}
						</td>
					{+END}
				{+END}
			</tr>
		{+END}
	</tbody>
</table>
