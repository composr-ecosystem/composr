<table class="columned-table autosized-table results-table">
	<thead>
		<tr>
			<th>Branch</th>
			<th>Git branch</th>
			<th>Latest version</th>
			<th>Released On</th>
			<th>Branch Status</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,BRANCHES}
			{$SET,cycle,{$CYCLE,results_table_zebra,zebra-0,zebra-1}}

			<tr class="{$GET,cycle} {ROW_CLASS*}">
				<td>{BRANCH*}</td>
				<td>{GIT_BRANCH*}</td>
				<td>{VERSION*}</td>
				<td>{VERSION_TIME*}</td>
				<td>{STATUS*}</td>
			</tr>
		{+END}
	</tbody>
</table>
