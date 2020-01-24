{TITLE}

<div class="wide-table-wrap"><table class="columned-table autosized-table wide-table results-table responsive-table">
	<thead>
		<tr>
			<th>
				{!USERNAME}
			</th>
			<th>
				{!LAST_ACTIVITY}
			</th>
			{+START,IF,{$DESKTOP}}
				{+START,IF,{$HAS_PRIVILEGE,show_user_browsing}}
					<th class="cell-desktop">
						{!LOCATION}
					</th>
				{+END}
			{+END}
			{+START,IF,{$ADDON_INSTALLED,securitylogging}}
				{+START,IF,{$HAS_PRIVILEGE,see_ip}}
					<th>
						{!IP_ADDRESS}
					</th>
				{+END}
			{+END}
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,ROWS}
			{+START,SET,location}
				{+START,SET,_location}
					{+START,IF_NON_EMPTY,{LOCATION}}
						{LOCATION}
					{+END}
					{+START,IF_EMPTY,{LOCATION}}
						<em>{!OTHER}</em>
					{+END}
				{+END}

				{+START,IF_EMPTY,{AT_URL}}
					{$GET,_location}
				{+END}
				{+START,IF_NON_EMPTY,{AT_URL}}
					<a href="{AT_URL*}">{$TRIM,{$GET,_location}}</a>
				{+END}
			{+END}

			<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}">
				<td>
					{MEMBER}
				</td>
				<td>
					{!_AGO,{!MINUTES,{TIME*}}}

					{+START,IF,{$HAS_PRIVILEGE,show_user_browsing}}
						<p class="associated-details block-mobile">
							{$GET,location}
						</p>
					{+END}
				</td>
				{+START,IF,{$DESKTOP}}
					{+START,IF,{$HAS_PRIVILEGE,show_user_browsing}}
						<td class="cell-desktop">
							{$GET,location}
						</td>
					{+END}
				{+END}
				{+START,IF,{$ADDON_INSTALLED,securitylogging}}
					{+START,IF,{$HAS_PRIVILEGE,see_ip}}
						<td>
							<a href="{$PAGE_LINK*,adminzone:admin_lookup:results:param={IP&}}">{IP*}</a>
						</td>
					{+END}
				{+END}
			</tr>
		{+END}
	</tbody>
</table></div>
