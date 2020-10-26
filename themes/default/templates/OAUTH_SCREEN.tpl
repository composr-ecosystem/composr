{TITLE}

{+START,IF_NON_EMPTY,{SERVICES}}
	<div class="wide-table-wrap"><table class="columned-table wide-table results-table autosized-table zebra">
		<thead>
			<tr>
				<th>
					{!NAME}
				</th>
				<th>
					{!AVAILABLE}
				</th>
				<th>
					{!CONFIGURED}
				</th>
				<th>
					{!CONNECTED}
				</th>
			</tr>
		</thead>

		<tbody>
			{+START,LOOP,SERVICES}
				<tr class="zebra-{$CYCLE*,oauth_rows,0,1}">
					<td>
						{LABEL*}
					</td>
					<td>
						{$?,{AVAILABLE},{!YES},{!NO}}
					</td>
					<td>
						{+START,IF_PASSED,CONFIG_URL}
							<a href="{CONFIG_URL*}">{$?,{CONFIGURED},{!YES},{!NO}}</a>
						{+END}
						{+START,IF_NON_PASSED,CONFIG_URL}
							{$?,{CONFIGURED},{!YES},{!NO}}
						{+END}
					</td>
					<td>
						{+START,IF_PASSED,CONNECT_URL}
							<a href="{CONNECT_URL*}">{$?,{CONNECTED},{!YES},{!NO}}</a>
						{+END}
						{+START,IF_NON_PASSED,CONNECT_URL}
							{$?,{CONNECTED},{!YES},{!NO}}
						{+END}
					</td>
				</tr>
			{+END}
		</tbody>
	</table></div>
{+END}

{+START,IF_EMPTY,{SERVICES}}
	<p class="nothing-here">{!NO_ENTRIES}</p>
{+END}
