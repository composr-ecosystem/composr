{TITLE}

{+START,IF_NON_EMPTY,{SERVICES}}
	<table class="columned-table wide-table results-table autosized-table responsive-table zebra">
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

					<td title="{!AVAILABLE}: {$?,{AVAILABLE},{!YES},{!NO}}">
						{$?,{AVAILABLE},{!YES},{!NO}}
					</td>

					<td title="{!CONFIGURED}: {$?,{CONFIGURED},{!YES},{!NO}}">
						{+START,IF_PASSED,CONFIG_URL}
							<a href="{CONFIG_URL*}">{$?,{CONFIGURED},{!YES},{!NO}}</a>
						{+END}
						{+START,IF_NON_PASSED,CONFIG_URL}
							{$?,{CONFIGURED},{!YES},{!NO}}
						{+END}
					</td>

					<td title="{!CONNECTED}: {$?,{CONNECTED},{!YES},{!NO}}">
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
	</table>
{+END}

{+START,IF_EMPTY,{SERVICES}}
	<p class="nothing-here">{!NO_ENTRIES}</p>
{+END}
