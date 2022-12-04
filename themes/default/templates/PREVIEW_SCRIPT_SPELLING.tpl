<h2>{FIELD*}</h2>

<table class="columned-table autosized-table results-table wide-table">
	<thead>
		<tr>
			<th>{!WORD}</th>
			<th>{!POSSIBLE_CORRECTIONS}</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,MISSPELLINGS}
			<tr>
				<td>{WORD*}</td>
				<td>{CORRECTIONS*}</td>
			</tr>
		{+END}
	</tbody>
</table>
