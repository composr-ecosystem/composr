<div class="gallery-video-info">
	<table class="columned-table results-table">
		{+START,IF,{$DESKTOP}}
			<colgroup>
				<col class="gallery-entry-field-name-column" />
				<col class="gallery-entry-field-value-column" />
			</colgroup>
		{+END}

		<tbody>
			<tr>
				<th class="de-th metadata-title">{!RESOLUTION}</th>
				<td class="de-th metadata-title">{WIDTH*} &times; {HEIGHT*}</td>
			</tr>
			<tr>
				<th class="de-th metadata-title">{!VIDEO_LENGTH}</th>
				<td class="de-th metadata-title">{$TIME_PERIOD*,{LENGTH}}</td>
			</tr>
		</tbody>
	</table>
</div>
