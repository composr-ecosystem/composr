{$REQUIRE_CSS,menu__dropdown}

{+START,IF_NON_EMPTY,{CONTENT}}
	{$SET,menu_id,r-{$RAND}-d}
	{+START,IF_PASSED,MENU}{+START,IF_NON_EMPTY,{MENU}}{$SET,menu_id,r-{MENU|}-d}{+END}{+END}

	<div class="menu-dropdown {+START,IF,{$MOBILE}}is-touch-interface{+END} {+START,IF,{$DESKTOP}}is-hover-interface{+END}" data-view="DropdownMenu" data-view-params="{+START,PARAMS_JSON,MENU,JAVASCRIPT_HIGHLIGHTING,menu_id}{_*}{+END}">
		<a href="{$PAGE_LINK*,:sitemap}" class="menu-dropdown-toggle-btn">{+START,INCLUDE,ICON}NAME=menus/mobile_menu{+END} <span class="text">{!MENU}</span></a>

		<nav class="menu-dropdown-content">
			{$,NB: .top-buttons element is moved here for touch interface}

			<ul class="menu-dropdown-items menu-dropdown-items-main" id="{$GET*,menu_id}">
				{CONTENT}
			</ul>
		</nav>
	</div>
{+END}
