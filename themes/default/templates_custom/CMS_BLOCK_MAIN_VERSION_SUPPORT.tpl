<table class="columned_table autosized_table results_table">
	<thead>
		<tr>
			<th>Branch</th>
			<th>Git branch</th>
			<th>Status</th>
			<th><abbr title="End of Life">EOL</abbr> date</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,BRANCHES}
			<tr>
				<td>{BRANCH*}</td>
				<td>{GIT_BRANCH*}</td>
				<td>{STATUS*}</td>
				<td>
                    {+START,IF_NON_EMPTY,{EOL}}
                        {EOL*}
                    {+END}
                    {+START,IF_EMPTY,{EOL}}
                        <em>Unknown</em>
                    {+END}
                </td>
			</tr>
		{+END}
	</tbody>
</table>
