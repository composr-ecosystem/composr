{$REQUIRE_CSS,menu__sitemap}
{$REQUIRE_JAVASCRIPT,core_menus}

{$SET,js_menu,{$NOT,{$BROWSER_MATCHES,bot}}}

{+START,IF,{$NOT,{$GET,js_menu}}}
	<nav class="menu-type--sitemap">
		<ul itemprop="significantLinks">
			{CONTENT}
		</ul>
	</nav>
{+END}

{+START,IF,{$GET,js_menu}}
	{$SET,menu_sitemap_id,menu-sitemap-{$RAND}}

	<nav id="{$GET*,menu_sitemap_id}" class="menu-type--sitemap" data-tpl="menuSitemap" data-tpl-params="{+START,PARAMS_JSON,menu_sitemap_id}{_*}{+END}" data-tp-menu-content="{$TRIM*,{CONTENT}}">
		<div aria-busy="true" class="spaced">
			<div class="ajax-loading vertical-alignment">
				<img width="20" height="20" src="{$IMG*,loading}" title="{!LOADING}" alt="{!LOADING}" />
				<span>{!LOADING}</span>
			</div>
		</div>
	</nav>
{+END}
