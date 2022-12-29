{TITLE}

<table class="columned-table wide-table results-table autosized-table responsive-table">
	<thead>
		<tr>
			<th>{!TITLE}</th>
			<th>{!CURRENT}</th>
			<th>{!TARGET}</th>
			<th>{!ACTIONS}</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,KPIS}
			<tr>
				<td>
					<a href="#graph_{GRAPH_NAME*}">{TITLE*}</a>
				</td>
				<td>
					{+START,IF_PASSED,CURRENT}
						{CURRENT*}
					{+END}
					{+START,IF_NON_PASSED,CURRENT}
						{!NA_EM}
					{+END}
				</td>
				<td {+START,IF,{$NOT,{HITS_TARGET}}} class="red-alert"{+END}>
					{+START,IF_PASSED,TARGET}
						{TARGET*}
					{+END}
					{+START,IF_NON_PASSED,TARGET}
						{!NONE_EM}
					{+END}
				</td>
				<td>
					<a href="{KPI_EDIT_URL*}">{!EDIT}</a>
				</td>
			</tr>
		{+END}
	</tbody>
</table>

{+START,LOOP,GRAPHS}
	{+START,INCLUDE,STATS_GRAPH}{+END}
{+END}
