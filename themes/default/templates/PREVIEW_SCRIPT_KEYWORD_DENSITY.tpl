<h2>{FIELD*}</h2>

<table class="columned-table autosized-table results-table spaced-table responsive-table">
	<thead>
		<tr>
			<th>{!KEYWORD}</th>
			<th>{!DENSITY}</th>
			<th>{!IDEAL_DENSITY}</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,KEYWORDS}
			<tr>
				<td>{KEYWORD*}</td>
				<td>{DENSITY*}%</td>
				<td>{IDEAL_DENSITY*}%</td>
			</tr>
		{+END}
	</tbody>
</table>
