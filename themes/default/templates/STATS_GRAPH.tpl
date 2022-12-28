<a id="graph_{GRAPH_NAME*}"></a>

<h2>{GRAPH_LABEL*}</h2>

{+START,IF_EMPTY,{GRAPH_RENDERED}{RESULTS_TABLE}}
	<p class="nothing-here">
		{!NO_DATA}
	</p>
{+END}

{+START,IF_NON_EMPTY,{GRAPH_RENDERED}}
	{GRAPH_RENDERED}
{+END}

{+START,IF_NON_EMPTY,{GRAPH_FORM}}
	{GRAPH_FORM}
{+END}

{+START,IF_NON_EMPTY,{RESULTS_TABLE}}
	{RESULTS_TABLE}
{+END}

{+START,SET,graph_actions}
	{+START,IF_PASSED,EXISTING_KPIS}
		{+START,LOOP,EXISTING_KPIS}
			<li>{+START,INCLUDE,ICON}NAME=admin/edit{+END} <a href="{KPI_EDIT_URL*}">{!_EDIT_KPI,{KPI_TITLE*}}</a></li>
		{+END}
	{+END}
	{+START,IF_PASSED,KPI_ADD_URL}
		<li>{+START,INCLUDE,ICON}NAME=admin/add{+END} <a href="{KPI_ADD_URL*}">{!ADD_KPI}</a></li>
	{+END}
	{+START,IF_PASSED,SPREADSHEET_URL}
		<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{SPREADSHEET_URL*}" class="xls-link">{!EXPORT_STATS_TO_SPREADSHEET}</a></li>
	{+END}
{+END}
{+START,IF_NON_EMPTY,{$TRIM,{$GET,graph_actions}}}
	<ul class="actions-list force-margin">
		{$GET,graph_actions}
	</ul>
{+END}
