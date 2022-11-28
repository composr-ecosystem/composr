<table class="columned-table autosized-table results-table">
	<thead>
		<tr>
			<th>Branch</th>
			<th>Git branch</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,BRANCHES}
			<tr>
				<td>{BRANCH*}</td>
				<td>{GIT_BRANCH*}</td>
				<td>{STATUS*}</td>
			</tr>
		{+END}
	</tbody>
</table>
