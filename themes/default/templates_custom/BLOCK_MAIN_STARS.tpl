<table class="wide-table results-table spaced-table autosized-table columned-table responsive-table">
	<thead>
		<tr>
			<th>{!AVATAR}</th>
			<th>{!DETAILS}</th>
			<th>{!SIGNATURE}</th>
		</tr>
	</thead>

	<tbody>
		{+START,LOOP,STARS}
			<tr>
				<td>
					{+START,IF_NON_EMPTY,{AVATAR_URL}}
						<img style="max-width: 100%" alt="" src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" />
					{+END}
				</td>
				<td>
					{!MEMBER}: <a href="{URL*}">{USERNAME*}</a><br /><br />
					Role points: {POINTS*}<br /><br />
					{!RANK}: {RANK*}
				</td>
				<td style="font-size: 0.8em;">
					{$SMART_LINK_STRIP,{SIGNATURE},{MEMBER_ID}}
				</td>
			</tr>
		{+END}

		{+START,IF_EMPTY,{STARS}}
			<tr>
				<td class="responsive-table-no-prefix-no-indent" colspan="3" style="font-weight: bold; padding: 10px">Nobody yet &ndash; could you be here?</td>
			</tr>
		{+END}
	</tbody>
</table>
