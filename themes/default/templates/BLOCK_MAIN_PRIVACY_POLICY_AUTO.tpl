{+START,LOOP,SECTIONS}
	<h2>{HEADING*}</h2>

	{+START,IF,{$EQ,{HEADING},{!COOKIES}}}
		<p>{!FOLLOWING_COOKIES}</p>

		{+START,IF_NON_EMPTY,{COOKIES}}
			<table class="columned-table results-table wide-table responsive-table">
				<thead>
					<tr>
						<th>
							{!NAME}
						</th>

						<th>
							{!CATEGORY}
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
								{CATEGORY*}
							</td>

							<td>
								{REASON*}
							</td>
						</tr>
					{+END}
				</tbody>
			</table>
			<p class="buttons-group">
				<span class="buttons-group-inner">
					<button class="btn btn-primary btn-scr" data-cc="show-preferencesModal"><span>{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!COOKIE_CONSENT_MANAGE_SETTINGS_TITLE}</span></button>
				</span>
			</p>
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
		<table class="columned-table results-table wide-table responsive-table">
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
		</table>
	{+END}
{+END}

<h2>{!CONTACT_US}</h2>

<p>
	{!PRIVACY_YOU_MAY}
	<ul>
		<li><a href="{$MAILTO}{$STAFF_ADDRESS}">{!EMAIL_US}</a></li>
		{+START,IF,{$ADDON_INSTALLED,tickets}}
			<li><a href="{$PAGE_LINK*,_SEARCH:tickets:ticket}">{!tickets:ADD_TICKET}</a></li>
		{+END}
		{+START,IF_NON_EMPTY,{$CONFIG_OPTION,privacy_fax}}
			<li>{!FAX_US}: {$CONFIG_OPTION*,privacy_fax}</li>
		{+END}
		{+START,IF_NON_EMPTY,{$CONFIG_OPTION,privacy_postal_address}}
			<li>{!MAIL_US}:<br /><address>{$CONFIG_OPTION*,privacy_postal_address}</address></li>
		{+END}
	</ul>
</p>
