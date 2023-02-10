{+START,SET,branch}
{$,The line below is just a fudge - we need CHILDREN to evaluate first, so it doesn't interfere with our 'RAND' variable}
{+START,SET,HAS_CHILDREN}{$IS_NON_EMPTY,{CHILDREN}}{+END}

{+START,SET,img_html}{+END}
{+START,IF_PASSED,ICON}
	{+START,SET,img_html}{+START,INCLUDE,ICON}NAME={ICON}{+END}{+END}
{+END}
{+START,IF_NON_PASSED,ICON}
	{+START,IF,{$AND,{$IS_EMPTY,{IMG}},{$LT,{THE_LEVEL},3}}}
		{+START,SET,img_html}{+START,INCLUDE,ICON}NAME=content_types/page{+END}{+END}
	{+END}
	{+START,IF_NON_EMPTY,{IMG}}
		{+START,SET,img_html}<img class="icon" alt="" src="{IMG*}" />{+END}
	{+END}
{+END}

{+START,IF,{TOP_LEVEL}}
	<li class="menu-dropdown-item toplevel {$?,{CURRENT},current,non-current}{+START,IF,{$GET,HAS_CHILDREN}} has-children{+END}{$?,{FIRST}, first}{+START,IF_PASSED,SIBLINGS} siblings-{SIBLINGS*}{+END}">
		<a {+START,INCLUDE,MENU_LINK_PROPERTIES}{+END} class="menu-dropdown-item-a toplevel-link">{+START,IF_NON_EMPTY,{$GET,img_html}}<span class="menu-dropdown-item-icon">{$GET,img_html}</span>{+END}<span class="menu-dropdown-item-caption">{CAPTION}</span></a>
		{+START,IF,{$GET,HAS_CHILDREN}}
			<ul aria-haspopup="true" class="menu-dropdown-items nlevel" style="display: none">
				{CHILDREN}
			</ul>
		{+END}
	</li>
	{+END}

	{+START,IF,{$NOT,{TOP_LEVEL}}}
		<li class="menu-dropdown-item nlevel {$?,{CURRENT},current,non-current}{+START,IF,{$GET,HAS_CHILDREN}} has-children{+END}{$?,{FIRST}, first}{$?,{LAST}, last} {+START,IF_PASSED,SIBLINGS}siblings-{SIBLINGS*}{+END}">
			<a {+START,IF_NON_EMPTY,{URL}}{+START,INCLUDE,MENU_LINK_PROPERTIES}{+END}{+END} {+START,IF_EMPTY,{URL}}href="#!"{+END} class="menu-dropdown-item-a nlevel-link">{+START,IF_NON_EMPTY,{$GET,img_html}}<span class="menu-dropdown-item-icon">{$GET,img_html}</span>{+END}<span class="menu-dropdown-item-caption">{CAPTION}</span></a>

			{+START,IF,{$GET,HAS_CHILDREN}}
				<ul aria-haspopup="true" class="menu-dropdown-items nlevel" style="display: none">
					{+START,IF_NON_EMPTY,{URL}}{$,Add duplicate parent link in child items that can be opened when using the hamburger menu (parent itself will toggle the child items)}
						<li class="block-mobile menu-dropdown-item nlevel {$?,{CURRENT},current,non-current} has-img">
							<a {+START,INCLUDE,MENU_LINK_PROPERTIES}{+END} class="menu-dropdown-item-a nlevel-link">{+START,IF_NON_EMPTY,{$GET,img_html}}<span class="menu-dropdown-item-icon">{$GET,img_html}</span>{+END}<span class="menu-dropdown-item-caption">{CAPTION}</span></a>
						</li>
					{+END}
					{CHILDREN}
				</ul>
			{+END}
		</li>
	{+END}
{+END}{$REPLACE,	,,{$REPLACE,
+,,{$GET,branch}}}
