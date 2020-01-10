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
	<a id="graph_{GRAPH_NAME*}"></a>

	<h2>{GRAPH_LABEL*}</h2>

	{+START,IF_NON_EMPTY,{GRAPH_RENDERED}}
		{GRAPH_RENDERED}

		{+START,IF_NON_EMPTY,{GRAPH_FORM}}
			{GRAPH_FORM}
		{+END}
	{+END}

	{+START,IF_NON_EMPTY,{RESULTS_TABLE}}
		{RESULTS_TABLE}

		<ul class="actions-list force-margin">
			<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,_SEARCH:admin_stats:spreadsheet:{GRAPH_NAME}}" class="xls-link">{!EXPORT_STATS_TO_SPREADSHEET}</a></li>
		</ul>
	{+END}

	{+START,IF_EMPTY,{GRAPH_RENDERED}{RESULTS_TABLE}}
		<p class="nothing-here">
			{!NO_DATA}
		</p>
	{+END}
{+END}
