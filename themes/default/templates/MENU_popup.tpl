{$REQUIRE_CSS,menu__popup}

{+START,IF_NON_EMPTY,{CONTENT}}
	{$SET,menu_id,r-{MENU|}-p}
	<nav class="menu-type--popup" data-view="PopupMenu" data-view-params="{+START,PARAMS_JSON,MENU,JAVASCRIPT_HIGHLIGHTING,menu_id}{_*}{+END}">
		<ul class="nl js-mouseout-unset-active-menu" id="{$GET*,menu_id}">
			{CONTENT}
		</ul>
	</nav>
{+END}
