{+START,SET,user}
	{+START,IF,{$NEQ,{_GUID},b2d355ff45f4b4170b937ef0753e6a78}}
		{+START,INCLUDE,CNS_USER_MEMBER}{+END}
	{+END}

	{+START,IF,{$EQ,{_GUID},b2d355ff45f4b4170b937ef0753e6a78}}
		{+START,INCLUDE,CNS_USER_MEMBER}
			SUP=&nbsp;<a href="{$PAGE_LINK*,_SEARCH:purchase:browse:category=giftr:username={USERNAME}}" title="{!giftr:GIFT_GIFT}"><img alt="{!giftr:GIFT_GIFT}" width="14" height="14" src="{$IMG*,icons/birthday}" /></a>
		{+END}
	{+END}
{+END}{$TRIM,{$GET,user}}
