<h1 class="screen-title{+START,IF_PASSED,CLASS} {CLASS*}{+END}"{+START,IF,{$NOT,{$GET,name_set_elsewhere}}} itemprop="name"{+END}>
	{+START,IF_PASSED,ID}
		<a id="title--{ID*}"></a>
	{+END}

	{TITLE}

	{+START,IF_PASSED,AWARDS}
		{+START,IF_NON_EMPTY,{AWARDS}}
			{+START,SET,AWARDS_TEXT}
				<h2>{!AWARD_WINNER}</h2>
				<p class="lonely-label">{!AWARDS_WON,{AWARDS}}</p>
				<ul>
					{+START,LOOP,AWARDS}
						<li>
							<strong>{AWARD_TYPE*}</strong>
							<span>{!AWARD_ON,{$DATE*,{AWARD_TIMESTAMP},1,1}}</span>
						</li>
					{+END}
				</ul>
			{+END}
			<a href="{$PAGE_LINK*,_SEARCH:awards}"><img data-cms-tooltip="{ contents: '{$GET;^*,AWARDS_TEXT}', delay: 0 }" title="" alt="{!AWARD_WINNER}" width="19" height="25" src="{$IMG*,awarded}" /></a>
		{+END}
	{+END}
</h1>

{+START,IF_PASSED,SUB}
	<div class="title-tagline">
		{SUB}
	</div>
{+END}
