{TITLE}

<p class="lonely-label">{!TABLE_OF_CONTENTS}:</p>
<ul>
	{+START,LOOP,GRAPHS}
		<li>
			<a href="#graph_{GRAPH_NAME*}">{GRAPH_LABEL*}</a>
		</li>
	{+END}
</ul>

{+START,LOOP,GRAPHS}
	{+START,INCLUDE,STATS_GRAPH}{+END}
{+END}
