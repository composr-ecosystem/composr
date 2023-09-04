{$,The line below is just a fudge - we need CHILDREN to evaluate first, so it doesn't interfere with our 'RAND' variable}
{$SET,HAS_CHILDREN,{$IS_NON_EMPTY,{CHILDREN}}}

{$SET,RAND,{$RAND}}

<li class="{$?,{CURRENT},current,non-current} {$?,{$IS_EMPTY,{IMG}},has-no-img,has-img}">
	{+START,IF_NON_EMPTY,{IMG}}<img alt="" width="24" height="24" src="{IMG*}" />{+END}
	{+START,IF,{$NOT,{$GET,HAS_CHILDREN}}}
		<a {+START,INCLUDE,MENU_LINK_PROPERTIES}{+END}>{CAPTION}</a>
	{+END}
	{+START,IF,{$GET,HAS_CHILDREN}}
		{+START,IF_NON_EMPTY,{URL}}
			<a class="drawer"{+START,INCLUDE,MENU_LINK_PROPERTIES}{+END}>{CAPTION}</a>
		{+END}
		{+START,IF_EMPTY,{URL}}
			<a href="#!" class="drawer" data-menu-tree-toggle="{MENU|*}_{$GET*,RAND}">{CAPTION}</a>
		{+END}
	{+END}
	{+START,IF,{$GET,HAS_CHILDREN}}
		<ul aria-haspopup="true" id="{MENU|;*}-{$GET*,RAND}"{+START,IF_EMPTY,{URL}} style="display: {DISPLAY*}"{+END}>
			{CHILDREN}
		</ul>
	{+END}
</li>
