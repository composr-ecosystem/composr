{TITLE}

<h2>{!DETAILS}</h2>

<div class="wide_table_wrap"><table class="map_table results_table wide_table spaced_table">
	<colgroup>
		<col class="field_name_column" />
		<col class="field_value_column" />
	</colgroup>

	<tbody>
		<tr>
			<th>{!USERNAME}</th>
			<td>{NAME*}</td>
		</tr>

		{+START,IF,{$NOT,{$IS_GUEST,{ID}}}}
			<tr>
				<th>{!MEMBER_ID}</th>
				<td>
					#<strong>{ID*}</strong>

					<div class="mini_indent">
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

					<div class="mini_indent">
						<div><em>{!BANNED}, {$LCASE,{IP_BANNED*}}</em>{+START,IF_PASSED,IP_BAN_LINK} {IP_BAN_LINK}{+END}</div>

						{+START,IF_NON_EMPTY,{$CONFIG_OPTION,stopforumspam_api_key}{$CONFIG_OPTION,tornevall_api_username}}
							<div><span class="associated_link"><a href="{$PAGE_LINK*,_SEARCH:admin_ip_ban:syndicate_ip_ban:ip={IP}:member_id={ID}:reason={!MANUAL}:redirect={$SELF_URL&}}">{!SYNDICATE_TO_STOPFORUMSPAM}</a></span></div>
						{+END}
					</div>
				</td>
			</tr>
		{+END}

		<tr>
			<th>{!RELATED_SCREENS}</th>
			<td>
				<nav>
					<ul class="actions_list">
						{+START,IF_PASSED,PROFILE_URL}
							<li><a href="{PROFILE_URL*}">{!VIEW_PROFILE}</a></li>
						{+END}
						{+START,IF_PASSED,ACTIONLOG_URL}
							<li><a href="{ACTIONLOG_URL*}">{!actionlog:VIEW_ACTIONLOGS}</a></li>
						{+END}
						{+START,IF_PASSED,POINTS_URL}
							<li><a href="{POINTS_URL*}">{!POINTS}</a></li>
						{+END}
						{+START,IF_PASSED,AUTHOR_URL}
							<li><a href="{AUTHOR_URL*}">{!VIEW_AUTHOR}</a></li>
						{+END}
						{+START,IF_PASSED,SEARCH_URL}
							<li><a rel="search" href="{SEARCH_URL*}">{!SEARCH}</a></li>
						{+END}
					</ul>
				</nav>
			</td>
		</tr>

		{+START,IF_NON_EMPTY,{IP}}
			<tr>
				<th>{!ACTIONS}</th>
				<td>
					<!-- If you like new windows, add this... title="{!LINK_NEW_WINDOW}" target="_blank"  -->
					<nav>
						<ul class="actions_list">
							<li><a rel="external" href="https://whatismyipaddress.com/ip/{IP*}">Reverse-DNS/WHOIS/Geo-Lookup</a></li>
							<li><a rel="external" href="https://ping.eu/ping/?host={IP*}">Ping</a></li>
							<li><a rel="external" href="https://ping.eu/traceroute/?host={IP*}">Tracert</a></li>
						</ul>
					</nav>
				</td>
			</tr>
		{+END}
	</tbody>
</table></div>

<h2>{!BANNED_ADDRESSES}</h2>

{+START,IF_NON_EMPTY,{IP_LIST}}
	<form title="{!PRIMARY_PAGE_FORM}" action="{$SELF_URL*}" method="post" autocomplete="off">
		{$INSERT_SPAMMER_BLACKHOLE}

		<p class="lonely_label">
			{!IP_LIST}
		</p>
		<ul>
			{IP_LIST}
		</ul>

		<input onclick="disable_button_just_clicked(this);" class="button_screen buttons__save" type="submit" value="{!SET}" />
	</form>
{+END}
{+START,IF_EMPTY,{IP_LIST}}
	<p class="nothing_here">
		{!NONE}
	</p>
{+END}

<h2>{!VIEWS}{+START,IF,{$IS_GUEST,{ID}}} ({!IP_ADDRESS}){+END}</h2>

{STATS}

{+START,IF_NON_EMPTY,{ALERTS}}
	<h2>{!SECURITY_ALERTS}</h2>

	{ALERTS}
{+END}

