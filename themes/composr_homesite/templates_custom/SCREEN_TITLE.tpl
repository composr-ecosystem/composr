{+START,IF_PASSED,ID}
<a id="title__{ID*}"></a>
{+END}

<h1 class="screen_title"{+START,IF,{$NOT,{$GET,name_set_elsewhere}}} itemprop="name"{+END}>
	{+START,IF_NON_PASSED,AWARDS}
		{TITLE}
	{+END}

	{+START,IF_PASSED,AWARDS}
		{+START,IF,{$NOT,{$MATCH_KEY_MATCH,forum:topicview}}}
			{TITLE}
		{+END}
		{+START,IF,{$MATCH_KEY_MATCH,forum:topicview}}
			{$PROVIDE_WITH_TRANSLATION,{TITLE},{$TRANS_TEXT_CONTEXT_html_inline}}
		{+END}

		{+START,IF_NON_EMPTY,{AWARDS}}
			{+START,SET,AWARDS_TEXT}
				<h2>{!AWARD_WINNER}</h2>
				<p class="lonely_label">{!AWARDS_WON,{AWARDS}}</p>
				<ul>
					{+START,LOOP,AWARDS}
						<li>
							<strong>{AWARD_TYPE*}</strong>
							<span>{!AWARD_ON,{$DATE*,1,1,1,{AWARD_TIMESTAMP}}}</span>
						</li>
					{+END}
				</ul>
			{+END}
			<a href="{$PAGE_LINK*,_SEARCH:awards}"><img onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'{$GET;^*,AWARDS_TEXT}','auto',null,null,false,true);" title="" alt="{!AWARD_WINNER}" src="{$IMG*,awarded}" /></a>
		{+END}
	{+END}
</h1>

{+START,IF_PASSED,SUB}
	<div class="title_tagline">
		{SUB}
	</div>
{+END}
