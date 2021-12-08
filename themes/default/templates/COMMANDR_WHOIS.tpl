<div class="wide_table_wrap"><table class="map_table autosized_table wide_table results_table">
	<tbody>
		<tr>
			<th>{!USERNAME}</th>
			<td>{NAME*}</td>
		</tr>
		<tr>
			<th>{!MEMBER_ID}</th>
			<td>{ID*}</td>
		</tr>
		<tr>
			<th>{!IP_ADDRESS}</th>
			<td>
				{+START,IF_EMPTY,{IP}{IP_LIST}}
					{!UNKNOWN}
				{+END}
				{+START,IF_NON_EMPTY,{IP}{IP_LIST}}
					<ul>
						<li class="whois_ip">{IP*}</li>
						{IP_LIST}
					</ul>
				{+END}
			</td>
		</tr>
		<tr>
			<th>{!ACTIONS}</th>
			<td>
				<ul class="actions_list">
					<li><a href="https://ip.me/?ip={IP*}">Reverse-DNS/WHOIS/Geo-Lookup</a></li>
					<li><a href="https://ping.eu/ping/?host={IP*}">Ping</a></li>
					<li><a href="https://ping.eu/traceroute/?host={IP*}">Tracert</a></li>
				</ul>
			</td>
		</tr>
	</tbody>
</table></div>
