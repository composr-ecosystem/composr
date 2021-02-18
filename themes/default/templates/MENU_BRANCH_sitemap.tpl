{$SET-,js_menu,{$NOT,{$BROWSER_MATCHES,bot}}}
{$SET-,img,{$?,{$AND,{$IS_EMPTY,{IMG}},{$LT,{THE_LEVEL},3},{$THEME_OPTION,menu_icons}},{$IMG,icons/content_types/page},{IMG}}}

{+START,IF,{$NOT,{$GET,js_menu}}}
	<li class="menu-sitemap-item {$?,{CURRENT},current,non-current} {$?,{$IS_EMPTY,{$GET,img}},has-no-img,has-img}">
		<span>
			{+START,IF_NON_EMPTY,{$GET,img}}<img alt="" width="24" height="24" src="{$GET*,img}" />{+END}
			{+START,IF_NON_EMPTY,{URL}}
				<a class="menu-sitemap-item-a" {+START,IF_NON_EMPTY,{TOOLTIP}} title="{$STRIP_TAGS,{CAPTION}}{+START,IF_NON_EMPTY,{TOOLTIP}}: {TOOLTIP*}{+END}"{+END} href="{URL*}">{CAPTION}</a>
			{+END}
			{+START,IF_EMPTY,{URL}}
				<span>{CAPTION}</span>
			{+END}
		</span>
		{+START,IF_NON_EMPTY,{CHILDREN}}
			<ul class="toggleable-tray">
				{CHILDREN}
			</ul>
		{+END}
	</li>
{+END}
{+START,IF,{$GET,js_menu}}{$?,{FIRST},[}{"caption": {$JSON_ENCODE,{CAPTION}}, "tooltip": {$JSON_ENCODE,{TOOLTIP}}, "url": {$JSON_ENCODE,{URL}}, "img": {$JSON_ENCODE,{$GET,img}}, "current": {$?,{CURRENT},true,false}, "children": {$?,{$IS_EMPTY,{CHILDREN}},[],{$TRIM,{CHILDREN}}}}{$?,{LAST},],\,}{+END}
