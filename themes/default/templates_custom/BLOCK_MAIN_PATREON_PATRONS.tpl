{+START,IF_NON_EMPTY,{PATREON_PATRONS}}
	<p class="lonely-label">A big thank you to the following <a rel="noopener" href="https://www.patreon.com/composr" target="_blank" title="Patreon {!LINK_NEW_WINDOW}">Patreons</a>:</p>
	<ul>
		{+START,LOOP,PATREON_PATRONS}
			<li>{NAME*}</li>
		{+END}
	</ul>
{+END}
