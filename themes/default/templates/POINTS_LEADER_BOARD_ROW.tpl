<tr>
	<th>
		<a href="{PROFILE_URL*}">{$DISPLAYED_USERNAME*,{USERNAME}}</a>
	</th>
	<td>
		{+START,IF_NON_EMPTY,{VOTING_POWER}{VOTING_CONTROL}}
			<ul>
				<li>
					{+START,IF_PASSED,POINTS_URL}
						{!POINTS}: <a href="{POINTS_URL*}" title="{!POINTS}: {USERNAME*}">{POINTS*}</a>
					{+END}
					{+START,IF_NON_PASSED,POINTS_URL}
						{!POINTS}: {POINTS*}
					{+END}
				</li>
				{+START,IF_NON_EMPTY,{VOTING_POWER}}
					<li>
						{!VOTING_POWER}: {VOTING_POWER*}
					</li>
				{+END}
				{+START,IF_NON_EMPTY,{VOTING_CONTROL}}
					<li>
						{!VOTING_POWER_CONTROL_PERCENTAGE}: {VOTING_CONTROL*}%
					</li>
				{+END}
			</ul>
		{+END}
		{+START,IF_EMPTY,{VOTING_POWER}{VOTING_CONTROL}}
			{+START,IF_PASSED,POINTS_URL}
				{!POINTS}: <a href="{POINTS_URL*}" title="{!POINTS}: {USERNAME*}">{POINTS*}</a>
			{+END}
			{+START,IF_NON_PASSED,POINTS_URL}
				{!POINTS}: {POINTS*}
			{+END}
		{+END}
	</td>
	{+START,IF,{HAS_RANK_IMAGES}}
		<td class="leader-board-rank">
			{$CNS_RANK_IMAGE,{ID}}
		</td>
	{+END}
</tr>
