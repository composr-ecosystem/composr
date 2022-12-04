{+START,IF_NON_EMPTY,{TAGS}}
	<table class="columned-table autosized-table results-table wide-table">
		<thead>
			<tr>
				<th>{!TITLE}</th>
				<th>{!DESCRIPTION}</th>
				<th>{!EXAMPLE}</th>
			</tr>
		</thead>

		<tbody>
			{+START,LOOP,TAGS}
				<tr>
					<td>{TITLE*}</td>
					<td>{DESCRIPTION*}</td>
					<td><kbd>{EXAMPLE*}</kbd></td>
				</tr>
			{+END}
		</tbody>
	</table>
{+END}
{+START,IF_EMPTY,{TAGS}}
	<p class="nothing-here">{!NONE}</p>
{+END}
