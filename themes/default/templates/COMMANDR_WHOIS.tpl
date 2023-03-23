<table class="map-table autosized-table wide-table results-table">
	<tbody>
		<tr>
			<th>{!USERNAME}</th>
			<td>{USERNAME*}</td>
		</tr>
		<tr>
			<th>{!MEMBER_ID}</th>
			<td>{MEMBER_ID*}</td>
		</tr>
		<tr>
			<th>{!IP_ADDRESS}</th>
			<td>
				{+START,IF_EMPTY,{IP}{IP_LIST}}
					{!UNKNOWN}
				{+END}
				{+START,IF_NON_EMPTY,{IP}{IP_LIST}}
					<ul>
						<li class="whois-ip">{IP*}</li>
						{IP_LIST}
					</ul>
				{+END}
			</td>
		</tr>
		<tr>
			<th>{!EMAIL_ADDRESS}</th>
			<td>{EMAIL_ADDRESS*}</td>
		</tr>
		<tr>
			<th>{!ACTIONS}</th>
			<td>
				<ul class="actions-list">
					<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="noopener" href="https://ip.me/ip/{IP*}">Reverse-DNS/WHOIS/Geo-Lookup</a></li>
					<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="noopener" href="https://ping.eu/ping/?host={IP*}">Ping</a></li>
					<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a rel="noopener" href="https://ping.eu/traceroute/?host={IP*}">Tracert</a></li>
				</ul>
			</td>
		</tr>
	</tbody>
</table>
