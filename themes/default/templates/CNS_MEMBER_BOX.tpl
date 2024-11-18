{+START,IF,{GIVE_CONTEXT}}
<div class="box"><div class="box-inner">
{+END}
{+START,IF,{$NOT,{GIVE_CONTEXT}}}
<div class="cns-member-box">
{+END}
	{+START,IF,{GIVE_CONTEXT}}
		<h3>{!CONTENT_IS_OF_TYPE,{!MEMBER},{$DISPLAYED_USERNAME*,{USERNAME}}}</h3>
	{+END}

	<div class="inline-lined-up">
		{+START,IF,{$DESKTOP}}
			{+START,IF_NON_EMPTY,{AVATAR_URL}}
				<img class="cns-member-box-avatar inline-desktop" src="{$ENSURE_PROTOCOL_SUITABILITY*,{AVATAR_URL}}" alt="{!SPECIFIC_AVATAR,{USERNAME*}}" title="{!SPECIFIC_AVATAR,{USERNAME*}}" />
			{+END}
		{+END}

		<div>{$,div will be set as inline block}<table class="map-table tooltip-fields autosized-table">
			<tbody>
				<tr><th class="de-th">{!USERNAME}:</th><td><a href="{$MEMBER_PROFILE_URL*,{MEMBER_ID}}">{USERNAME*}</a></td></tr>
				<tr><th class="de-th">{!cns:SPECIFIC_FORUM_POSTS}:</th><td>{POSTS*}</td></tr>
				{+START,IF_NON_EMPTY,{POINTS}}
					<tr><th class="de-th"><abbr title="{!RANK_POINTS,{$INTEGER_FORMAT*,{$POINTS_BALANCE,{MEMBER_ID}},0}}">{!POINTS}</abbr>:</th><td>{POINTS*}</td></tr>
				{+END}
				<tr><th class="de-th">{!JOIN_DATE}:</th><td>{JOIN_DATE*}</td></tr>
				{+START,IF,{$NOT,{$MATCH_KEY_MATCH,_WILD:galleries}}}
					{+START,IF_PASSED,IP_ADDRESS}
						<tr><th class="de-th">{!IP_ADDRESS}:</th><td>{$TRUNCATE_LEFT,{IP_ADDRESS},15,1}</td></tr>
					{+END}
					{+START,IF_PASSED,NUM_WARNINGS}
						<tr><th class="de-th">{!cns_warnings:MODULE_TRANS_NAME_warnings}:</th><td>{NUM_WARNINGS*}</td></tr>
					{+END}
					{+START,IF_PASSED,GALLERIES}
						<tr><th class="de-th">{!galleries:GALLERIES}:</th><td>{GALLERIES*}</td></tr>
					{+END}
					{+START,IF_NON_EMPTY,{DOB}}
						<tr>
							<th class="de-th">{DOB_LABEL*}:</th>
							<td>{DOB*}</td>
						</tr>
					{+END}
				{+END}
				<tr>
					<th class="de-th">{!USERGROUPS}:</th>
					<td>{+START,LOOP,SECONDARY_GROUPS}{+START,IF,{$NEQ,{_loop_key},0}}, {+END}{_loop_var*}{+END}</td>
				</tr>
				<tr><th class="de-th">{!ONLINE_NOW}:</th><td>{$?*,{ONLINE},{!YES},{!NO}}</td></tr>
				{CUSTOM_FIELDS}
			</tbody>
		</table></div>
	</div>
{+START,IF,{$NOT,{GIVE_CONTEXT}}}
</div>
{+END}
{+START,IF,{GIVE_CONTEXT}}
</div></div>
{+END}
