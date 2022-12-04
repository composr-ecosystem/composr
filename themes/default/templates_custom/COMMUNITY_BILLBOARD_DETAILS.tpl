<div class="box box---community-billboard-details"><div class="box-inner">
	<h2>{!DETAILS}</h2>

	<table class="map-table wide-table results-table">
		{+START,IF,{$DESKTOP}}
			<colgroup>
				<col class="field-name-column" />
				<col class="field-value-column" />
			</colgroup>
		{+END}

		<tbody>
			{+START,IF_PASSED,MESSAGE}
				<tr>
					<th>
						{!MESSAGE}
					</th>
					<td>
						{MESSAGE}
					</td>
				</tr>
			{+END}

			{+START,IF_PASSED,DAYS}
				<tr>
					<th>{!_UP_FOR}</th>
					<td>{!DAYS,{DAYS*}}</td>
				</tr>
			{+END}

			<tr>
				<th>{!SUBMITTER}</th>
				<td>{USERNAME}</td>
			</tr>

			<tr>
				<th>{!DAYS_ORDERED}</th>
				<td>{DAYS_ORDERED*}</td>
			</tr>

			<tr>
				<th>{!ORDER_DATE}</th>
				<td>{DATE*}</td>
			</tr>
		</tbody>
	</table>
</div></div>
