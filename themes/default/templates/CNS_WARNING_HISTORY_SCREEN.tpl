{TITLE}

{RESULTS_TABLE}

<p class="lonely-label">{!ACTIONS}:</p>
<nav>
	<ul class="actions-list">
		{+START,IF_NON_EMPTY,{ADD_WARNING_URL}}
			<li class="actions-list-strong">
		{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{ADD_WARNING_URL*}">{!ADD_WARNING}</a>
			</li>
		{+END}
		{+START,IF_NON_EMPTY,{VIEW_PROFILE_URL}}
			<li class="actions-list-strong">
				{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{VIEW_PROFILE_URL*}">{!VIEW_PROFILE}</a>
			</li>
		{+END}
		<li class="actions-list-strong">
			{+START,INCLUDE,ICON}NAME=buttons/proceed2{+END} <a href="{$PAGE_LINK*,_SEARCH:rules}">{!RULES}</a>
		</li>
	</ul>
</nav>
