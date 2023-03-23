<header itemscope="itemscope" itemtype="http://schema.org/WPHeader" class="header header-classic with-seed-navbar{+START,IF,{$MOBILE}} is-touch-interface{+END}{+START,IF,{$DESKTOP}} is-hover-interface{+END}" data-view="Header">
	{$,This allows screen-reader users (e.g. blind users) to jump past the panels etc to the main content}
	<a accesskey="s" class="accessibility-hidden" href="#maincontent">{!SKIP_NAVIGATION}</a>

	{$,The main logo}
	<h1 class="logo container">
		<a class="logo-link" target="_self" href="{$PAGE_LINK*,{$?,{$HAS_ZONE_ACCESS,site},site,}:}" rel="home" title="{!HOME}" accesskey="r">{+START,TRIM}
			{+START,IF,{$NOT,{$THEME_OPTION,use_site_name_text_as_logo}}}
				<img class="logo-image logo-image-large" {+START,IF,{$MOBILE}}style="display: none;"{+END} src="{$LOGO_URL*}" width="{$IMG_WIDTH*,{$LOGO_URL}}" height="{$IMG_HEIGHT*,{$LOGO_URL}}" alt="{$SITE_NAME*}" />
				<img class="logo-image logo-image-color" {+START,IF,{$DESKTOP}}style="display: none;"{+END} src="{$IMG*,logo/small_logo}" width="{$IMG_WIDTH*,{$IMG,logo/small_logo}}" height="{$IMG_HEIGHT*,{$IMG,logo/small_logo}}" alt="{$SITE_NAME*}" />
			{+END}
			{+START,IF,{$THEME_OPTION,use_site_name_text_as_logo}}
				<span class="logo-text">{$SITE_NAME*}</span>
			{+END}
		{+END}</a>
	</h1>

	{$,Main menu}
	<div class="global-navigation" id="global-navigation">
		<div class="container">
			<div class="global-navigation-items">
				{$BLOCK,block=menu,param={$CONFIG_OPTION,header_menu_call_string},type=dropdown}

				{$,Login form for guests}
				{+START,IF,{$IS_GUEST}}{+START,IF,{$CONFIG_OPTION,block_top_login}}
					<div class="top-form top-login">
						{$BLOCK,block=top_login}
					</div>
				{+END}{+END}

				{+START,IF,{$NOT,{$IS_GUEST}}}{+START,IF,{$OR,{$CONFIG_OPTION,block_top_notifications},{$CONFIG_OPTION,block_top_notifications},{$CONFIG_OPTION,block_top_personal_stats}}}
					<div class="top-buttons">
						{+START,IF,{$CONFIG_OPTION,block_top_language,1}}{$BLOCK,block=top_language}{+END}

						{+START,IF,{$CONFIG_OPTION,block_top_notifications}}{$BLOCK,block=top_notifications}{+END}

						{+START,IF,{$CONFIG_OPTION,block_top_personal_stats}}{$BLOCK,block=top_personal_stats}{+END}

						{$,Search box for logged in users [could show to guests, except space is lacking]}
						{+START,IF,{$AND,{$ADDON_INSTALLED,search},{$DESKTOP},{$NOT,{$IS_GUEST}}}}{+START,IF,{$CONFIG_OPTION,block_top_search,1}}
							{$BLOCK,block=top_search,block_id=desktop,failsafe=1,limit_to={$?,{$MATCH_KEY_MATCH,forum:_WILD},cns_posts,all_defaults}}
						{+END}{+END}
					</div>
				{+END}{+END}
			</div>
		</div>
	</div>
</header>
