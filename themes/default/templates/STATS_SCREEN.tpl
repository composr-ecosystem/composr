{TITLE}

{+START,IF,{$NOT,{$ISSET,SVG_ONCE}}}
	<div class="box svg-exp"><p class="box-inner">{!SVG_EXPLANATION}</p></div>
	{$SET,SVG_ONCE,1}
{+END}

{GRAPH}
<div class="link-exempt-wrap">
	{STATS}
</div>

{+START,IF_NON_PASSED_OR_FALSE,NO_SPREADSHEET}
	<ul class="actions-list force-margin">
		<li>{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$EXTEND_URL*,{$SELF_URL},spreadsheet=1}" class="xls-link">{!EXPORT_STATS_TO_SPREADSHEET}</a></li>
	</ul>
{+END}
