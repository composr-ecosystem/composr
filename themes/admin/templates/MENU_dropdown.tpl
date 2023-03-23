{$REQUIRE_CSS,menu__dropdown}

{+START,IF_NON_EMPTY,{CONTENT}}

{$SET,menu_id,r-{$RAND}-d}
{+START,IF_NON_EMPTY,{MENU}}{$SET,menu_id,r-{MENU|}-d}{+END}
	{$SET,HAS_CHILDREN,{$HAS_ACTUAL_PAGE_ACCESS,admin,adminzone}}

	<div class="menu-dropdown menu-dropdown-admin" data-view="DropdownMenu" data-view-params="{+START,PARAMS_JSON,MENU,JAVASCRIPT_HIGHLIGHTING,menu_id}{_*}{+END}">
		<a href="#!" class="menu-dropdown-toggle-btn">{+START,INCLUDE,ICON}NAME=menus/mobile_menu{+END} <span class="text">{!MENU}</span></a>

		<nav class="menu-dropdown-content">
			<ul class="menu-dropdown-items menu-dropdown-items-main" id="{$GET*,menu_id}">
				{CONTENT}

				<li class="menu-dropdown-item toplevel non-current {$?,{$GET,HAS_CHILDREN},has-children} last">
					<a href="{$TUTORIAL_URL*,tutorials}" class="menu-dropdown-item-a toplevel-link" title="{!menus:MM_TOOLTIP_DOCS}"><span class="menu-dropdown-item-icon">{+START,INCLUDE,ICON}NAME=help{+END}</span><span class="menu-dropdown-item-caption">{!HELP}</span></a>
					{+START,IF,{$GET,HAS_CHILDREN}}
						<div aria-haspopup="true" class="menu-dropdown-items menu-dropdown-item-popup nlevel menu-help-section" style="display: none">
							{+START,INCLUDE,ADMIN_ZONE_SEARCH}{+END}
						</div>
					{+END}
				</li>
			</ul>
		</nav>
	</div>
{+END}
