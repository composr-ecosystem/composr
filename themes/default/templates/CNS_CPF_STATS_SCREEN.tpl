{TITLE}

<table class="columned-table autosized-table wide-table results-table spaced-table">
	<thead>
		<tr>
			<th>{!CHOICE}</th>
			<th>{!COUNT_MEMBERS}</th>
		</tr>
	</thead>

	<tbody>
		{+START,LOOP,STATS}
			<tr>
				<td>
					{+START,IF_EMPTY,{VAL}}{!BLANK_EM}{+END}

					{+START,IF_NON_EMPTY,{VAL}}{VAL*}{+END}
				</td>
				<td>
					{CNT*}
				</td>
			</tr>
		{+END}
	</tbody>
</table>
