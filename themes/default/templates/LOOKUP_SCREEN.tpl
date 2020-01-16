{TITLE}

<h2>{!DETAILS}</h2>

<div class="wide-table-wrap"><table class="map-table results-table wide-table spaced-table responsive-blocked-table">
	<colgroup>
		<col class="field-name-column" />
		<col class="field-value-column" />
	</colgroup>

	<tbody>
		<tr>
			<th>{!USERNAME}</th>
			<td>{USERNAME*}</td>
		</tr>

		{+START,IF,{$NOT,{$IS_GUEST,{MEMBER_ID}}}}
			<tr>
				<th>{!MEMBER_ID}</th>
				<td>
					#<strong>{MEMBER_ID*}</strong>

					<div class="mini-indent">
						<div><em>{!MEMBER_BANNED}, {$LCASE,{MEMBER_BANNED*}}</em>{+START,IF_PASSED,MEMBER_BAN_LINK} {MEMBER_BAN_LINK}{+END}</div>
						<div><em>{!SUBMITTER_BANNED}, {$LCASE,{SUBMITTER_BANNED*}}</em>{+START,IF_PASSED,SUBMITTER_BAN_LINK} {SUBMITTER_BAN_LINK}{+END}</div>
					</div>
				</td>
			</tr>
		{+END}

		{+START,IF_NON_EMPTY,{IP}}
			<tr>
				<th>{!IP_ADDRESS}</th>
				<td>
					<strong>{IP*}</strong>

					<div class="mini-indent">
						<div><em>{!BANNED}, {$LCASE,{IP_BANNED*}}</em>{+START,IF_PASSED,IP_BAN_LINK} {IP_BAN_LINK}{+END}</div>

						{+START,IF_NON_EMPTY,{$CONFIG_OPTION,stopforumspam_api_key}{$CONFIG_OPTION,tornevall_api_username}}
							<div><span class="associated-link"><a href="{$PAGE_LINK*,_SEARCH:admin_ip_ban:syndicate_ip_ban:ip={IP}:member_id={MEMBER_ID}:reason={!MANUAL}:redirect={$SELF_URL&}}">{!SYNDICATE_TO_STOPFORUMSPAM}</a></span></div>
						{+END}
					</div>
				</td>
			</tr>
		{+END}

		<tr>
			<th>{!RELATED_SCREENS}</th>
			<td>
				<nav>
					<ul class="actions-list">
						{+START,IF_PASSED,PROFILE_URL}
							<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{PROFILE_URL*}">{!VIEW_PROFILE}</a></li>
						{+END}
						{+START,IF_PASSED,ACTIONLOG_URL}
							<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{ACTIONLOG_URL*}">{!actionlog:VIEW_ACTIONLOGS}</a></li>
						{+END}
						{+START,IF_PASSED,POINTS_URL}
							<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{POINTS_URL*}">{!POINTS}</a></li>
						{+END}
						{+START,IF_PASSED,AUTHOR_URL}
							<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{AUTHOR_URL*}">{!VIEW_AUTHOR}</a></li>
						{+END}
						{+START,IF_PASSED,SEARCH_URL}
							<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="search" href="{SEARCH_URL*}">{!SEARCH}</a></li>
						{+END}
					</ul>
				</nav>
			</td>
		</tr>

		{+START,IF_NON_EMPTY,{IP}}
			<tr>
				<th>{!ACTIONS}</th>
				<td>
					<!-- If you like new windows, add this... title="{!LINK_NEW_WINDOW}" target="_blank" -->
					<nav>
						<ul class="actions-list">
							<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="external" href="http://whatismyipaddress.com/ip/{IP*}">Reverse-DNS/WHOIS/Geo-Lookup</a></li>
							<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="external" href="https://ping.eu/ping/?host={IP*}">Ping</a></li>
							<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="external" href="https://ping.eu/traceroute/?host={IP*}">Tracert</a></li>
						</ul>
					</nav>
				</td>
			</tr>
		{+END}
	</tbody>
</table></div>

<h2>{!BANNED_ADDRESSES}</h2>

{+START,IF_NON_EMPTY,{IP_LIST}}
	<form title="{!PRIMARY_PAGE_FORM}" action="{$SELF_URL*}" method="post">
		{$INSERT_SPAMMER_BLACKHOLE}

		<p class="lonely-label">
			{!IP_LIST}
		</p>
		<ul>
			{IP_LIST}
		</ul>

		<button data-disable-on-click="1" class="btn btn-primary btn-scr buttons--save" type="submit">{+START,INCLUDE,ICON}NAME=buttons/save{+END} {!SET}</button>
	</form>
{+END}
{+START,IF_EMPTY,{IP_LIST}}
	<p class="nothing-here">
		{!NONE}
	</p>
{+END}

<h2>{!VIEWS}{+START,IF,{$IS_GUEST,{MEMBER_ID}}} ({!IP_ADDRESS}){+END}</h2>

{STATS}

{+START,IF_NON_EMPTY,{ALERTS}}
	<h2>{!SECURITY_ALERTS}</h2>

	{ALERTS}
{+END}
