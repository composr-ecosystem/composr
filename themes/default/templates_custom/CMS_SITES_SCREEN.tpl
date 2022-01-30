{TITLE}

{+START,IF_NON_EMPTY,{ROWS}}
	<div class="wide-table-wrap"><table class="columned-table wide-table results-table responsive-table">
		<thead>
			<tr>
				<th><a href="{$PAGE_LINK*,_SELF:_SELF:sort=website_name {WEBSITE_NAME_DIR}}">{!CMS_WEBSITE_NAME}</a></th>
				<th><a href="{$PAGE_LINK*,_SELF:_SELF:sort=hittime {HITTIME_DIR}}">{!CMS_LAST_ADMIN_ACCESS}</a></th>
				<th>{!CMS_STILL_INSTALLED}</th>
				<th><a href="{$PAGE_LINK*,_SELF:_SELF:sort=l_version {L_VERSION_DIR}}">{!VERSION}</a></th>
				<th>{!CMS_PRIVACY}</th>
				<th><a href="{$PAGE_LINK*,_SELF:_SELF:sort=num_members {NUM_MEMBERS_DIR}}">{!COUNT_MEMBERS}</a></th>
				<th><a href="{$PAGE_LINK*,_SELF:_SELF:sort=num_hits_per_day {NUM_HITS_PER_DAY_DIR}}">{!CMS_HITS_24_HRS}</a></th>
			</tr>
		</thead>

		<tbody>
			{+START,LOOP,ROWS}
				<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}">
					<td><a href="{WEBSITE_URL*}" target="_blank" title="{WEBSITE_NAME*} {!LINK_NEW_WINDOW}">{WEBSITE_NAME*}</a></td>
					<td><abbr title="{!_AGO,{!DAYS,{HITTIME_2*}}}">{!_AGO,{!HOURS,{HITTIME*}}}</abbr></td>
					<td>{CMS_ACTIVE*}</td>
					<td>{L_VERSION*}</td>
					<td>{NOTE*}</td>
					<td>{NUM_MEMBERS*}</td>
					<td>{NUM_HITS_PER_DAY*}</td>
				</tr>
			{+END}
		</tbody>
	</table></div>
{+END}

{+START,IF_EMPTY,{ROWS}}
	<p class="nothing-here">
		{!NO_ENTRIES}
	</p>
{+END}
