{$REQUIRE_CSS,menu__mobile}

{+START,IF_NON_EMPTY,{CONTENT}}
	{$SET,menu_id,r-{MENU|}-mobile-d}
	<div data-view="MobileMenu" data-view-params="{+START,PARAMS_JSON,MENU,JAVASCRIPT_HIGHLIGHTING,menu_id}{_*}{+END}">
		<a href="{$PAGE_LINK*,:sitemap}" class="mobile-menu-button js-click-toggle-content">{+START,INCLUDE,ICON}NAME=menus/mobile_menu{+END} <span>{!MENU}</span></a>

		<nav class="menu-type--mobile js-el-menu-content" style="display: none" aria-expanded="false">
			<div class="mobile-search">
				{$BLOCK,block=top_search,failsafe=1,block_id=mobile}
			</div>

			<ul class="nl" id="{$GET,menu_id}">
				{CONTENT}
			</ul>
		</nav>
	</div>
{+END}
