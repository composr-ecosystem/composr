{TITLE}

{+START,IF_NON_EMPTY,{CONTRIBUTORS}}
	<p class="lonely-label">{!CONTRIBUTORS_LABEL}</p>
	<ul>
		{+START,LOOP,CONTRIBUTORS}
			<li>
				{_loop_key*}:
				<ul>
					{+START,LOOP,AREAS}
						<li>{_loop_var*}</li>
					{+END}
				</ul>
			</li>
		{+END}
	</ul>
{+END}

{+START,IF_NON_EMPTY,{PATREON_PATRONS}}
	<p class="lonely-label">{!PATREON_PATRONS_LABEL}</p>
	<ul>
		{+START,LOOP,PATREON_PATRONS}
			<li>{NAME*}</li>
		{+END}
	</ul>
{+END}

<h2>{!CONTRIBUTE_HEADING}</h2>

<p>{!CONTRIBUTE_PARAGRAPH,{$BRAND_BASE_URL*}/contributions.htm}</p>
