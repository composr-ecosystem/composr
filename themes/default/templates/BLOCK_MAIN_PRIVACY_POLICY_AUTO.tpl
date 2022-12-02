{+START,LOOP,SECTIONS}
	<h2>{HEADING*}</h2>

	{+START,IF,{$EQ,{HEADING},{!COOKIES}}}
		<p>{!FOLLOWING_COOKIES}</p>

		{+START,IF_NON_EMPTY,{COOKIES}}
			<div class="wide-table-wrap"><table class="columned-table results-table wide-table responsive-table">
				<thead>
					<tr>
						<th>
							{!NAME}
						</th>

						<th>
							{!REASON}
						</th>
					</tr>
				</thead>

				<tbody>
					{+START,LOOP,COOKIES}
						<tr>
							<td>
								<kbd>{NAME*}</kbd>
							</td>

							<td>
								{REASON*}
							</td>
						</tr>
					{+END}
				</tbody>
			</table></div>
		{+END}
	{+END}

	{+START,IF_NON_EMPTY,{POSITIVE}}
		<ul>
			{+START,LOOP,POSITIVE}
				<li>{EXPLANATION*}</li>
			{+END}
		</ul>
	{+END}

	{+START,IF_NON_EMPTY,{GENERAL}}
		<div class="wide-table-wrap"><table class="columned-table results-table wide-table responsive-table">
			<thead>
				<tr>
					<th>
						{!ACTION}
					</th>

					<th>
						{!REASON}
					</th>
				</tr>
			</thead>

			<tbody>
				{+START,LOOP,GENERAL}
					<tr>
						<td>
							{ACTION*}
						</td>

						<td>
							{REASON*}
						</td>
					</tr>
				{+END}
			</tbody>
		</table></div>
	{+END}
{+END}

<h2>{!CONTACT_US}</h2>

<p>
	{!PRIVACY_YOU_MAY}
	<ul>
		<li><a href="{$MAILTO}{$STAFF_ADDRESS}">{!EMAIL_US}</a></li>
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			<li><a href="{$PAGE_LINK*,_SEARCH:tickets:ticket}">{!tickets:ADD_TICKET}</a>
		{+END}
		{+START,IF_NON_EMPTY,{$CONFIG_OPTION,privacy_fax}}
			<li>{!FAX_US}: {$CONFIG_OPTION*,privacy_fax}</li>
		{+END}
		{+START,IF_NON_EMPTY,{$CONFIG_OPTION,privacy_postal_address}}
			<li>{!MAIL_US}:<br /><address>{$CONFIG_OPTION*,privacy_postal_address}</address></li>
		{+END}
	</ul>
</p>
