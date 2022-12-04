<table class="map-table wide-table results-table spaced-table autosized-table">
	<tbody>
		<tr>
			<th>{!PATH}</th>
			<td>{FULL_PATH*}</td>
		</tr>

		{+START,IF_PASSED,LAST_EDITING_USERNAME}
			<tr>
				<th>{!EDITED_BY}</th>
				<td>{LAST_EDITING_USERNAME*}</td>
			</tr>
		{+END}

		{+START,IF_PASSED,LAST_EDITING_DATE}
			<tr>
				<th>{!EDITED_AT}</th>
				<td>{LAST_EDITING_DATE*}</td>
			</tr>
		{+END}

		<tr>
			<th>{!FILE_SIZE}</th>
			<td>{FILE_SIZE*}</td>
		</tr>

		{+START,IF_PASSED,ADDON}
			<tr>
				<th>{!addons:ADDON}</th>
				<td><kbd>{ADDON*}</kbd></td>
			</tr>
		{+END}
	</tbody>
</table>
