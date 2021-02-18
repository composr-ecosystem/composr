{$,The line below is just a fudge - we need CHILDREN to evaluate first, so it doesn't interfere with our 'RAND' variable}
{$SET,HAS_CHILDREN,{$IS_NON_EMPTY,{CHILDREN}}}

{$SET,RAND,{$RAND}}

{$SET,img,{$?,{$AND,{$IS_EMPTY,{IMG}},{$LT,{THE_LEVEL},3},{$THEME_OPTION,menu_icons}},{$IMG,icons/content_types/page},{IMG}}}

<li class="{$?,{CURRENT},current,non-current} {$?,{$IS_EMPTY,{$GET,img}},has-no-img,has-img}">
	<a class="{+START,IF_EMPTY,{URL}}non-link {+END}{+START,IF,{$GET,HAS_CHILDREN}} drawer js-click-toggle-sub-menu{+END}"{+START,IF,{$GET,HAS_CHILDREN}} data-vw-sub-menu-id="{MENU|*}-pexpand-{$GET*,RAND}"{+END} href="{$?*,{$IS_EMPTY,{URL}},#!,{URL}}">{+START,IF_NON_EMPTY,{$GET,img}}<img alt="" width="24" height="24" src="{$GET*,img}" /> {+END}{CAPTION}</a>
	{+START,IF,{$GET,HAS_CHILDREN}}
		<ul aria-haspopup="true" class="nlevel" id="{MENU|*}-pexpand-{$GET*,RAND}" style="display: none">
			{CHILDREN}
		</ul>
	{+END}
</li>
