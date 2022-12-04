{TITLE}

<p>
	{!WARNING_MODULES}
</p>

<table class="columned-table wide-table results-table autosized-table responsive-table">
	<thead>
		<tr>
			<th>
				{!NAME}
			</th>
			<th>
				{!AUTHOR}
			</th>
			<th>
				{!ORGANISATION}
			</th>
			<th>
				{!VERSION}
			</th>
			<th>
				{!HACKED_BY}
			</th>
			<th>
				{!HACK_VERSION}
			</th>
			<th>
				{!STATUS}
			</th>
			<th>
				{!ACTIONS}
			</th>
		</tr>
	</thead>

	<tbody>
		{+START,LOOP,MODULES}
			<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}">
				<td class="addon-name">
					{NAME*}
				</td>
				<td>
					{AUTHOR*}
				</td>
				<td>
					{ORGANISATION*}
				</td>
				<td>
					{VERSION*}
				</td>
				<td>
					{HACKED_BY*}
				</td>
				<td>
					{HACK_VERSION*}
				</td>
				<td>
					{STATUS*}
				</td>
				<td>
					{ACTIONS}
				</td>
			</tr>
		{+END}
	</tbody>
</table>
