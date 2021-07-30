<tr>
	<th>
		<a href="{PROFILE_URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>
	</th>
	<td>
		{+START,IF_PASSED,POINTS_URL}
			<a href="{POINTS_URL*}" title="{!POINTS}: {USERNAME*}">{POINTS*}</a>
		{+END}
		{+START,IF_NON_PASSED,POINTS_URL}
			{POINTS*}
		{+END}
	</td>
	{+START,IF,{HAS_RANK_IMAGES}}
		<td class="leader-board-rank">
			{$CNS_RANK_IMAGE,{ID}}
		</td>
	{+END}
</tr>
