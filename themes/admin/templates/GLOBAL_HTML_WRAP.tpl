<!DOCTYPE html>

{$REQUIRE_CSS,adminzone}
{$REQUIRE_CSS,helper_panel}

{$SET,page_link_privacy,{$PAGE_LINK,:privacy}}

{$,We deploy as HTML5 but code and conform strictly to XHTML5}
<html lang="{$LCASE*,{$METADATA,lang}}"{$ATTR_DEFAULTED,dir,{!dir},ltr} data-view="Global" data-view-params="{+START,PARAMS_JSON,page_link_privacy}{_*}{+END}">
<head>
	{+START,INCLUDE,HTML_HEAD}{+END}
</head>

{$,You can use main-website-inner to help you create fixed width designs; never put fixed-width stuff directly on ".website-body" or "body" because it will affects things like the preview or banner frames or popups/overlays}
<body class="website-body zone-running-{$REPLACE*,_,-,{$ZONE}} page-running-{$REPLACE*,_,-,{$PAGE}}" id="main-website" itemscope="itemscope" itemtype="http://schema.org/WebPage">
	{$,This is the main site header}
	{+START,IF,{$SHOW_HEADER}}
		<header class="header header-admin" itemscope="itemscope" itemtype="http://schema.org/WPHeader" data-view="Header">
			{$,The main logo}
			<h1 class="logo">
				<a class="logo-link" target="_self" href="{$PAGE_LINK*,{$?,{$HAS_ZONE_ACCESS,site},site,}:}" rel="home">{+START,TRIM}
					{+START,IF,{$NOT,{$THEME_OPTION,use_site_name_text_as_logo}}}
						<img class="logo-image" src="{$IMG*,logo/small_white_logo}" width="{$IMG_WIDTH*,{$IMG,logo/small_white_logo}}" height="{$IMG_HEIGHT*,{$IMG,logo/small_white_logo}}" alt="{$SITE_NAME*}" />
					{+END}
					{+START,IF,{$THEME_OPTION,use_site_name_text_as_logo}}
						<span class="logo-text">{$SITE_NAME*}</span>
					{+END}
				{+END}</a>
			</h1>

			{$,This allows screen-reader users (e.g. blind users) to jump past the panels etc to the main content}
			<a accesskey="s" class="accessibility-hidden" href="#maincontent">{!SKIP_NAVIGATION}</a>

			<div class="admin-navigation">
				{+START,IF,{$HAS_ZONE_ACCESS,adminzone}}
					{$SET,admin_menu_string,adminzone:{$DEFAULT_ZONE_PAGE_NAME}\,include=node\,title={!menus:DASHBOARD}\,icon=menu/adminzone/{$DEFAULT_ZONE_PAGE_NAME} + adminzone:\,include=children\,max_recurse_depth=4\,use_page_groupings=1 + cms:\,include=node\,max_recurse_depth=3\,use_page_groupings=1,type={$?,{$MOBILE},mobile,dropdown}}
				{+END}
				{+START,IF,{$NOT,{$HAS_ZONE_ACCESS,adminzone}}}
					{$SET,admin_menu_string,{$?,{$HAS_ZONE_ACCESS,site},site,}:{$DEFAULT_ZONE_PAGE_NAME}\,include=node\,title={!HOME}\,icon=buttons/close + cms:\,include=node\,max_recurse_depth=3\,use_page_groupings=1,type={$?,{$MOBILE},mobile,dropdown}}
				{+END}

				{$BLOCK-,block=menu,param={$GET,admin_menu_string},type=dropdown}

				{+START,IF,{$MOBILE}}
					<div class="admin-navigation-inner">
						<span>{$?,{$EQ,{$ZONE},adminzone},{!ADMIN_ZONE},{!CMS}}</span>
					</div>
				{+END}
			</div>
		</header>
	{+END}

	<div id="main-website-inner" class="container-fluid">
		{$,By default the top panel contains the admin menu, community menu, member bar, etc}
		{+START,IF_NON_EMPTY,{$TRIM,{$LOAD_PANEL,top}}}
			<div id="panel-top">
				{$LOAD_PANEL,top}
			</div>
		{+END}

		{$,Composr may show little messages for you as it runs relating to what you are doing or the state the site is in}
		<div class="global-messages" id="global-messages">
			{$MESSAGES_TOP}
		</div>

		{$,The main panels and content; .clearfix contains the layout into a rendering box so that the footer etc can sit underneath}
		<div class="global-middle-outer clearfix">
			<article class="global-middle" role="main">
				{$,Breadcrumbs}
				{+START,IF,{$IN_STR,{$BREADCRUMBS},<a }}{+START,IF,{$SHOW_HEADER}}
					<nav class="global-breadcrumbs breadcrumbs" itemprop="breadcrumb" id="global-breadcrumbs">
						{+START,INCLUDE,ICON}
							NAME=breadcrumbs
							ICON_TITLE={!YOU_ARE_HERE}
							ICON_SIZE=24
							ICON_CLASS=breadcrumbs-img
						{+END}
						{$BREADCRUMBS}
					</nav>
				{+END}{+END}

				{$,Associated with the SKIP_NAVIGATION link defined further up}
				<a id="maincontent"></a>

				{$,The main site, whatever 'page' is being loaded}
				{MIDDLE}
			</article>

			{+START,IF_NON_EMPTY,{$TRIM,{$LOAD_PANEL,left}}}
				<div id="panel-left" class="global-side-panel" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
					<div class="stuck-nav" data-stuck-nav="data-stuck-nav">{$LOAD_PANEL,left}</div>
				</div>
			{+END}

			{$,Deciding whether/how to show the right panel requires some complex logic}
			{$SET,HELPER_PANEL_TUTORIAL,{$?,{$HAS_PRIVILEGE,see_software_docs},{$HELPER_PANEL_TUTORIAL}}}
			{$SET,helper_panel,{$OR,{$IS_NON_EMPTY,{$GET,HELPER_PANEL_TUTORIAL}},{$IS_NON_EMPTY,{$HELPER_PANEL_TEXT}}}}
			{+START,IF,{$OR,{$GET,helper_panel},{$IS_NON_EMPTY,{$TRIM,{$LOAD_PANEL,right}}}}}
				<div id="panel-right" class="global-side-panel{+START,IF_EMPTY,{$TRIM,{$LOAD_PANEL,right}}} helper-panel {$?,{$HIDE_HELP_PANEL},helper-panel-hidden,helper-panel-visible}{+END}" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
					{+START,IF_NON_EMPTY,{$TRIM,{$LOAD_PANEL,right}}}
						<div class="stuck-nav" data-stuck-nav="data-stuck-nav">{$LOAD_PANEL,right}</div>
					{+END}

					{+START,IF_EMPTY,{$TRIM,{$LOAD_PANEL,right}}}
						{+START,INCLUDE,GLOBAL_HELPER_PANEL}{+END}
					{+END}
				</div>
			{+END}
		</div>

		{+START,IF_NON_EMPTY,{$TRIM,{$LOAD_PANEL,bottom}}}
			<div id="panel-bottom" role="complementary">
				{$LOAD_PANEL,bottom}
			</div>
		{+END}

		{+START,IF_NON_EMPTY,{$MESSAGES_BOTTOM}}
			<div class="global-messages">
				{$MESSAGES_BOTTOM}
			</div>
		{+END}

		{+START,IF,{$SHOW_FOOTER}}
			{+START,IF,{$EQ,{$CONFIG_OPTION,sitewide_im,1},1}}{$CHAT_IM}{+END}
		{+END}

		{$,Late messages happen if something went wrong during outputting everything (i.e. too late in the process to show the error in the normal place)}
		{+START,IF_NON_EMPTY,{$LATE_MESSAGES}}
			<div class="global-messages" id="global-messages-2">
				{$LATE_MESSAGES}
			</div>
		{+END}
	</div>

	{$,This is the main site footer}
	{+START,IF,{$SHOW_FOOTER}}
		<footer class="clearfix" itemscope="itemscope" itemtype="http://schema.org/WPFooter">
			<div class="footer-inner">
				<div class="global-footer-left block-desktop">
					{+START,SET,FOOTER_BUTTONS}
						{+START,IF,{$CONFIG_OPTION,bottom_show_top_button}}
							<li>
								<a rel="back_to_top" accesskey="g" href="#" title="{!BACK_TO_TOP}">{+START,INCLUDE,ICON}
									NAME=tool_buttons/top
									SIZE=24
								{+END}</a>
							</li>
						{+END}
						{+START,IF,{$ADDON_INSTALLED,realtime_rain}}{+START,IF,{$CONFIG_OPTION,bottom_show_realtime_rain_button,1}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_realtime_rain}}{+START,IF,{$NEQ,{$ZONE}:{$PAGE},adminzone:admin_realtime_rain}}
							<li>
								<a id="realtime-rain-button" aria-haspopup="dialog" data-btn-load-realtime-rain="{}" title="{!realtime_rain:REALTIME_RAIN}" href="{$PAGE_LINK*,adminzone:admin_realtime_rain}">{+START,INCLUDE,ICON}
									NAME=tool_buttons/realtime_rain_on
									ICON_ID=realtime-rain-img
									ICON_SIZE=24
								{+END}</a>
							</li>
						{+END}{+END}{+END}{+END}
						{+START,IF,{$HAS_ZONE_ACCESS,adminzone}}
							{+START,IF,{$ADDON_INSTALLED,commandr}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_commandr}}{+START,IF,{$CONFIG_OPTION,bottom_show_commandr_button,1}}{+START,IF,{$NEQ,{$ZONE}:{$PAGE},adminzone:admin_commandr}}
								<li>
									<a id="commandr-button" aria-haspopup="dialog" title="{!commandr:COMMANDR_DESCRIPTIVE_TITLE*}" accesskey="o"{+START,IF,{$DESKTOP}} data-btn-load-commandr="{}" {+END} href="{$PAGE_LINK*,adminzone:admin_commandr}">{+START,INCLUDE,ICON}
										NAME=tool_buttons/commandr_on
										ICON_CLASS=commandr-img
										ICON_SIZE=24
									{+END}</a>
								</li>
							{+END}{+END}{+END}{+END}
							{+START,IF,{$DESKTOP}}{+START,IF,{$EQ,{$BRAND_NAME},Composr}}
								<li>
									<a id="software-chat-button" aria-haspopup="dialog" title="{!SOFTWARE_CHAT}" accesskey="-" href="#!" class="js-global-click-load-software-chat">{+START,INCLUDE,ICON}
										NAME=tool_buttons/software_chat
										ICON_CLASS=software-chat-img
										ICON_SIZE=24
									{+END}</a>
								</li>
							{+END}{+END}
						{+END}
						{+START,IF,{$CONFIG_OPTION,block_top_language}}<li>{$BLOCK,block=top_language}</li>{+END}
					{+END}
					{+START,IF_NON_EMPTY,{$TRIM,{$GET,FOOTER_BUTTONS}}}{+START,IF,{$DESKTOP}}
						<ul class="horizontal-buttons">
							{$GET,FOOTER_BUTTONS}
						</ul>
					{+END}{+END}

					{+START,IF,{$DESKTOP}}{+START,IF_NON_EMPTY,{$STAFF_ACTIONS}}{+START,IF,{$CONFIG_OPTION,show_staff_page_actions}}
						<form title="{!SCREEN_DEV_TOOLS} {!LINK_NEW_WINDOW}" class="inline special-page-type-form js-global-submit-staff-actions-select" action="{$URL_FOR_GET_FORM*,{$SELF_URL}}" method="get" target="_blank">
							{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,0,0,cache_blocks=0,cache_comcode_pages=0,keep_minify=0,special_page_type=<null>,keep_template_magic_markers=<null>}}

							<div class="inline">
								<p class="accessibility-hidden"><label for="special-page-type">{!SCREEN_DEV_TOOLS}</label></p>
								<div class="input-group">
									<select id="special-page-type" name="special_page_type" class="form-control form-control-sm">{$STAFF_ACTIONS}</select>
									<div class="input-group-append">
										<button class="btn btn-primary btn-sm buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} <span>{!PROCEED_SHORT}</span></button>
									</div>
								</div>
							</div>
						</form>
					{+END}{+END}{+END}
				</div>

				<div class="global-footer-right">
					<div class="global-copyright">
						{$,Uncomment to show user's time {$DATE} {$TIME}}
					</div>

					<nav class="global-minilinks">
						<ul class="footer-links">
							<li><a href="{$PAGE_LINK*,:}">{!HOME}</a></li>
							{+START,IF,{$CONFIG_OPTION,bottom_show_sitemap_button}}
								<li><a accesskey="z" rel="site_map" href="{$PAGE_LINK*,_SEARCH:sitemap}">{!SITEMAP}</a></li>
							{+END}
							{+START,IF,{$CONFIG_OPTION,bottom_show_rules_link}}
								<li><a data-open-as-overlay="{}" rel="site_rules" accesskey="," href="{$PAGE_LINK*,:rules}">{!RULES}</a></li>
							{+END}
							{+START,IF,{$CONFIG_OPTION,bottom_show_privacy_link}}
								<li><a data-open-as-overlay="{}" rel="site_privacy" accesskey="." href="{$PAGE_LINK*,_SEARCH:privacy}">{!PRIVACY}</a></li>
							{+END}
							{+START,IF,{$CONFIG_OPTION,bottom_show_feedback_link}}
								<li><a rel="site_contact" accesskey="'" href="{$PAGE_LINK*,_SEARCH:feedback:redirect={$SELF_URL&,1}}">{!_FEEDBACK}</a></li>
							{+END}
							{+START,IF,{$NOR,{$IS_HTTPAUTH_LOGIN},{$IS_GUEST}}}
								<li><form title="{!LOGOUT}" class="inline" method="post" action="{$PAGE_LINK*,_SELF:login:logout}"><button accesskey="l" class="button-hyperlink" type="submit" title="{!_LOGOUT,{$USERNAME*}}">{!LOGOUT}</button></form></li>
							{+END}
							{+START,IF,{$OR,{$IS_HTTPAUTH_LOGIN},{$IS_GUEST}}}
								<li><a data-open-as-overlay="{}" href="{$PAGE_LINK*,_SELF:login:{$?,{$NOR,{$GET,login_screen},{$?,{$NOR,{$GET,login_screen},{$_POSTED},{$EQ,{$PAGE},login,join}},redirect={$SELF_URL&*,1}}}}}">{!_LOGIN}</a></li>
							{+END}
							{+START,IF,{$THEME_OPTION,mobile_support}}
								{+START,IF,{$MOBILE}}
									<li><a accesskey="[" href="{$SELF_URL*,1,0,0,keep_mobile=0}">{!NONMOBILE_VERSION}</a>
								{+END}
								{+START,IF,{$DESKTOP}}
									<li><a accesskey="]" href="{$SELF_URL*,1,0,0,keep_mobile=1}">{!MOBILE_VERSION}</a></li>
								{+END}
							{+END}
							{+START,IF,{$HAS_ZONE_ACCESS,adminzone}}
								{+START,IF,{$ADDON_INSTALLED,commandr}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_commandr}}{+START,IF,{$CONFIG_OPTION,bottom_show_commandr_button}}{+START,IF,{$NEQ,{$ZONE}:{$PAGE},adminzone:admin_commandr}}
									<li class="inlineblock-mobile"><a href="{$PAGE_LINK*,adminzone:admin_commandr}">{!commandr:COMMANDR}</a></li>
								{+END}{+END}{+END}{+END}
								<li class="inlineblock-mobile"><a accesskey="i" href="{$PAGE_LINK*,adminzone:}">{!ADMIN_ZONE}</a></li>
							{+END}
							{+START,IF,{$CONFIG_OPTION,bottom_show_top_button}}
								<li class="inlineblock-mobile"><a rel="back_to_top" accesskey="g" href="#">{!_BACK_TO_TOP}</a></li>
							{+END}
							{+START,IF_NON_EMPTY,{$HONEYPOT_LINK}}
								<li class="accessibility-hidden">{$HONEYPOT_LINK}</li>
							{+END}
							<li class="accessibility-hidden"><a accesskey="/" href="{$PAGE_LINK*,:keymap}">{!KEYBOARD_MAP}</a></li>
							<li>{!POWERED_BY,<a target="_blank" title="{$BRAND_NAME*} {!LINK_NEW_WINDOW}" href="{$BRAND_BASE_URL*}">{$BRAND_NAME*}}</a></li>
						</ul>
					</nav>
				</div>
			</div>
		</footer>
	{+END}

	{$EXTRA_FOOT}

	{$JS_TEMPCODE}
</body>
</html>

<!-- Powered by {$BRAND_NAME*} (admin theme), (c) Christopher Graham - {$BRAND_BASE_URL*} -->
