{+START,IF_NON_EMPTY,{PATREON_PATRONS}}
	<p class="lonely-label">A big thank you to the following <a rel="noopener" href="https://www.patreon.com/composr" target="_blank" title="Patreon {!LINK_NEW_WINDOW}">Patreons</a>:</p>
	<ul>
		{+START,LOOP,PATREON_PATRONS}
			<li><a href="{$MEMBER_PROFILE_URL*,{MEMBER_ID}}">{USERNAME*}</a></li>
		{+END}
	</ul>
{+END}

<p>Accounts are matched by e-mail address. If you aren't shown check your e-mail address matches your {$SITE_NAME*} account and wait 24 hours.</p>
