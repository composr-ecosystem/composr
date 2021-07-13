<p>{!SPAM_URLS}:</p>
<div class="wide-table-wrap"><table class="columned-table wide-table results-table autosized-table responsive-table">
	<thead>
		<tr>
			<th>{!DOMAIN}</th>
			<th>{!URL}</th>
			<th>{!IP_ADDRESS}</th>
			<th>{!ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{+START,LOOP,SPAM_URLS}
			<tr>
				<td>
					<a rel="noopener" href="https://whois.domaintools.com/{DOMAIN*}" target="_blank" title="WHOIS {DOMAIN*} {!LINK_NEW_WINDOW}">{DOMAIN*}</a>
				</td>
				<td>
					{+START,LOOP,URLS}
						{+START,IF,{$NEQ,{I},0}}<br />{+END}
						<a rel="noopener" href="{URL*}" target="_blank" title="{URL*} {!LINK_NEW_WINDOW}">{URL*}</a>
					{+END}
				</td>
				<td>
					{+START,IF,{$ADDON_INSTALLED,securitylogging}}
						<a rel="noopener" href="{$PAGE_LINK*,_SEARCH:admin_lookup:view:{IP*}}" target="_blank" title="{IP*} {!LINK_NEW_WINDOW}">{IP*}</a>
					{+END}
					{+START,IF,{$NOT,{$ADDON_INSTALLED,securitylogging}}}
						{IP*}
					{+END}
				</td>
				<td>
					{+START,SET,posts}{+START,LOOP,POSTS}{+START,IF,{$NEQ,{I},0}}

	{+END}{POST}{+END}{+END}
					<a data-cms-tooltip="{ contents: '{!PREPARE_EMAIL_DESCRIPTION;^*}', width: '700px' }" href="mailto:?subject={!PREPARE_EMAIL_SUBJECT.*,{$SITE_NAME},{USERNAME},{DOMAIN}}&amp;body={!PREPARE_EMAIL_BODY.*,{USERNAME},{$GET,posts},{$SITE_NAME},{DOMAIN}}">{!PREPARE_EMAIL}</a>
				</td>
			</tr>
		{+END}
	</tbody>
</table></div>
