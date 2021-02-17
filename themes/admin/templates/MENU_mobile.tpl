{$REQUIRE_CSS,menu__mobile}

{$SET,menu_id,r-{MENU|}-mobile-d}

<a data-tpl="menuMobile" data-tpl-params="{+START,PARAMS_JSON,menu_id}{_*}{+END}" href="{$PAGE_LINK*,:sitemap}" class="mobile-menu-button js-click-toggle-{$GET,menu_id}-content">{+START,INCLUDE,ICON}NAME=menus/mobile_menu{+END}	<span>{!MENU}</span></a>

{+START,IF_NON_EMPTY,{CONTENT}}
	<nav class="menu-type--mobile" style="display: none" aria-expanded="false" data-view="MobileMenu" data-view-params="{+START,PARAMS_JSON,MENU,JAVASCRIPT_HIGHLIGHTING,menu_id}{_*}{+END}">
		<div class="mobile-search">
			{+START,INCLUDE,ADMIN_ZONE_SEARCH}{+END}
		</div>

		<ul class="nl" id="{$GET,menu_id}">
			{CONTENT}
		</ul>
	</nav>
{+END}
