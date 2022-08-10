{$,The line below is just a fudge - we need CHILDREN to evaluate first, so it doesn't interfere with our 'RAND' variable}
{$SET,HAS_CHILDREN,{$IS_NON_EMPTY,{CHILDREN}}}

{$SET,RAND,{$RAND}}

<li class="{$?,{CURRENT},current,non_current} {$?,{$IS_EMPTY,{IMG}},has_no_img,has_img}">
	{+START,IF_NON_EMPTY,{IMG}}<img alt="" src="{IMG*}" srcset="{IMG_2X*} 2x" />{+END}
	{+START,IF,{$NOT,{$GET,HAS_CHILDREN}}}
		<a{+START,INCLUDE,MENU_LINK_PROPERTIES}{+END}>{CAPTION}</a>
	{+END}
	{+START,IF,{$GET,HAS_CHILDREN}}
		{+START,IF_NON_EMPTY,{URL}}
			<a class="drawer"{+START,INCLUDE,MENU_LINK_PROPERTIES}{+END}>{CAPTION}</a>
		{+END}
		{+START,IF_EMPTY,{URL}}
			<a href="#" class="drawer" onclick="return toggleable_tray('{MENU|;*}_{$GET;*,RAND}');">{CAPTION}</a>
		{+END}
	{+END}
	{+START,IF,{$GET,HAS_CHILDREN}}
		<ul aria-haspopup="true" id="{MENU|;*}_{$GET*,RAND}"{+START,IF_EMPTY,{URL}} style="display: {DISPLAY*}"{+END}>
			{CHILDREN}
		</ul>
	{+END}
</li>
