{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{+START,SET,details}
	{+START,IF,{$NEQ,{!UNKNOWN},{USERNAME}}}
		<tr>
			<th>{!USERNAME}</th>
			<td>{USERNAME*}</td>
		</tr>
	{+END}

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
					{+START,IF_NON_EMPTY,{RISK_SCORE}}
						<div><em>{!security:RISK*}, {RISK_SCORE*}</em></div>
					{+END}

					{+START,IF,{$OR,{$IS_NON_EMPTY,{$CONFIG_OPTION,stopforumspam_api_key}},{$EQ,{$CONFIG_OPTION,spam_use_tornevall},1}}}
						<div><span class="associated-link"><a href="{$PAGE_LINK*,_SEARCH:admin_ip_ban:syndicate_ip_ban:ip={IP}:member_id={MEMBER_ID}:reason={!MANUAL}:redirect={$SELF_URL&}}">{!SYNDICATE_TO_STOPFORUMSPAM}</a></span></div>
					{+END}
				</div>
			</td>
		</tr>
	{+END}

	{+START,IF_NON_EMPTY,{EMAIL_ADDRESS}}
		<tr>
			<th>{!EMAIL_ADDRESS}</th>
			<td>
				<strong>{EMAIL_ADDRESS*}</strong>
			</td>
		</tr>
	{+END}

	{+START,SET,related_screens}
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
	{+END}
	{+START,IF_NON_EMPTY,{$TRIM,{$GET,related_screens}}}
		<tr>
			<th>{!RELATED_SCREENS}</th>
			<td>
				<nav>
					<ul class="actions-list">
						{$GET,related_screens}
					</ul>
				</nav>
			</td>
		</tr>
	{+END}

	{+START,IF_NON_EMPTY,{IP}}
		<tr>
			<th>{!ACTIONS}</th>
			<td>
				<!-- If you like new windows, add this... title="{!LINK_NEW_WINDOW}" target="_blank" -->
				<nav>
					<ul class="actions-list">
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="external noopener" target="_blank" href="https://ip.me/ip/{IP*}" title="Reverse-DNS/WHOIS/Geo-Lookup {!LINK_NEW_WINDOW}">Reverse-DNS/WHOIS/Geo-Lookup</a></li>
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="external noopener" target="_blank" href="https://ping.eu/ping/?host={IP*}" title="Ping {!LINK_NEW_WINDOW}">Ping</a></li>
						<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="external noopener" target="_blank" href="https://ping.eu/traceroute/?host={IP*}" title="Tracert {!LINK_NEW_WINDOW}">Tracert</a></li>
					</ul>
				</nav>
			</td>
		</tr>
	{+END}
{+END}

{+START,IF_NON_EMPTY,{$TRIM,{$GET,details}}}
	<h2>{!DETAILS}</h2>

	<table class="map-table results-table wide-table spaced-table responsive-blocked-table">
		<colgroup>
			<col class="field-name-column" />
			<col class="field-value-column" />
		</colgroup>

		<tbody>
			{$GET,details}
		</tbody>
	</table>
{+END}

<h2>{!BANNED_ADDRESSES}</h2>

{+START,IF_NON_EMPTY,{IP_LIST}}
	<form title="{!PRIMARY_PAGE_FORM}" action="{$SELF_URL*}" method="post">
		{$INSERT_FORM_POST_SECURITY}

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
