{$,Add CSS class .with-white-navbar for a white navbar, .with-seed-navbar for seed-colored navbar}
<header itemscope="itemscope" itemtype="http://schema.org/WPHeader" class="header header-side with-white-navbar is-touch-interface {+START,IF,{$THEME_OPTION,sticky_header}}is-sticky{+END}" data-view="Header">
	{$,This allows screen-reader users (e.g. blind users) to jump past the panels etc to the main content}
	<a accesskey="s" class="accessibility-hidden" href="#maincontent">{!SKIP_NAVIGATION}</a>

	<div class="header-inner">
		<a class="btn btn-outline-primary btn-side-menu-toggler desktop-only" href="#!">{+START,INCLUDE,ICON}NAME=menus/mobile_menu{+END}</a>

		{$,Main menu}
		<div class="global-navigation js-side-menu-toggleable" {+START,IF,{$DESKTOP}}style="display: none;"{+END} id="global-navigation">
			{$,The main logo}
			<h1 class="logo">
				<a class="logo-link" target="_self" href="{$PAGE_LINK*,:}" rel="home" title="{!HOME}" accesskey="r">{+START,TRIM}
					{+START,IF,{$NOT,{$THEME_OPTION,use_site_name_text_as_logo}}}
						<img class="logo-image logo-image-color" src="{$IMG*,logo/small_logo}" alt="{$SITE_NAME*}" />
					{+END}
					{+START,IF,{$THEME_OPTION,use_site_name_text_as_logo}}
						<span class="logo-text">{$SITE_NAME*}</span>
					{+END}
				{+END}</a>
			</h1>

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
{+START,IF,{$THEME_OPTION,sticky_header}}
	<div class="header-sticky-placeholder mobile-only"></div>
{+END}
