<!DOCTYPE html>

{$SET,page_link_privacy,{$PAGE_LINK,:privacy}}

{$,We deploy as HTML5 but code and conform strictly to XHTML5}
<html lang="{$LCASE*,{$METADATA,lang}}"{$ATTR_DEFAULTED,dir,{!dir},ltr} data-view="Global" data-view-params="{+START,PARAMS_JSON,page_link_privacy}{_*}{+END}">
<head>
	{+START,INCLUDE,HTML_HEAD}{+END}
</head>

{$,You can use main-website-inner to help you create fixed width designs; never put fixed-width stuff directly on ".website-body" or "body" because it will affects things like the preview or banner frames or popups/overlays}
<body class="website-body zone-running-{$REPLACE*,_,-,{$ZONE}} page-running-{$REPLACE*,_,-,{$PAGE}}" id="main-website" itemscope="itemscope" itemtype="http://schema.org/WebPage" data-tpl="globalHtmlWrap">
	<div id="main-website-inner">
		{+START,IF,{$SHOW_HEADER}}
			<header itemscope="itemscope" itemtype="http://schema.org/WPHeader">
				{$,This allows screen-reader users (e.g. blind users) to jump past the panels etc to the main content}
				<a accesskey="s" class="accessibility-hidden" href="#maincontent">{!SKIP_NAVIGATION}</a>

				{$,The banner}
				{+START,IF,{$DESKTOP}}
					{$SET-,BANNER,{$BANNER}} {$,This is to avoid evaluating the banner twice}
					{+START,IF_NON_EMPTY,{$GET,BANNER}}
						<div class="global-banner block-desktop">{$GET,BANNER}</div>
					{+END}
				{+END}

				{$,The main logo}
				<h1 class="logo-outer">
					<a target="_self" href="{$PAGE_LINK*,:}" rel="home" title="{!HOME}">
						{+START,IF,{$NOT,{$THEME_OPTION,use_site_name_text_as_logo}}}
						<img class="logo" src="{$LOGO_URL*}" alt="{$SITE_NAME*}" />
						{+END}
						{+START,IF,{$THEME_OPTION,use_site_name_text_as_logo}}
						<span class="logo">{$SITE_NAME*}</span>
						{+END}
					</a>
				</h1>

				{$,Main menu}
				<div class="global-navigation">
					{$BLOCK,block=menu,param={$CONFIG_OPTION,header_menu_call_string},type=dropdown}

					<div class="global-navigation-inner">
						{$,Login form for guests}
						{+START,IF,{$IS_GUEST}}{+START,IF,{$CONFIG_OPTION,block_top_login}}
							<div class="top-form top-login">
								{$BLOCK,block=top_login}
							</div>
						{+END}{+END}

						{$,Search box for logged in users [could show to guests, except space is lacking]}
						{+START,IF,{$AND,{$ADDON_INSTALLED,search},{$DESKTOP},{$NOT,{$IS_GUEST}}}}{+START,IF,{$CONFIG_OPTION,block_top_search,1}}
							<div class="top-form top-search block-desktop">
								{$BLOCK,block=top_search,block_id=desktop,failsafe=1,limit_to={$?,{$MATCH_KEY_MATCH,forum:_WILD},cns_posts,all_defaults}}
							</div>
						{+END}{+END}

						{+START,IF,{$OR,{$AND,{$NOT,{$IS_GUEST}},{$OR,{$CONFIG_OPTION,block_top_notifications},{$CONFIG_OPTION,block_top_personal_stats}}},{$CONFIG_OPTION,block_top_language,1}}}
							<div class="top-buttons">
								{+START,IF,{$CONFIG_OPTION,block_top_language,1}}{$BLOCK,block=top_language}{+END}

								{+START,IF,{$CONFIG_OPTION,block_top_notifications}}{$BLOCK,block=top_notifications}{+END}

								{+START,IF,{$CONFIG_OPTION,block_top_personal_stats}}{$BLOCK,block=top_personal_stats}{+END}
							</div>
						{+END}
					</div>
				</div>
			</header>
		{+END}

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

		{$,The main panels and content; float-surrounder contains the layout into a rendering box so that the footer etc can sit underneath}
		<div class="global-middle-outer">
			{$SET,has_left_panel,{$IS_NON_EMPTY,{$TRIM,{$LOAD_PANEL,left}}}}
			{$SET,has_right_panel,{$IS_NON_EMPTY,{$TRIM,{$LOAD_PANEL,right}}}}

			<article class="global-middle {$?,{$GET,has_left_panel},has-left-panel,has-no-left-panel} {$?,{$GET,has_right_panel},has-right-panel,has-no-right-panel}" role="main">
				{$,Breadcrumbs}
				{+START,IF,{$IN_STR,{$BREADCRUMBS},<a }}{+START,IF,{$SHOW_HEADER}}
					<nav class="global-breadcrumbs breadcrumbs" itemprop="breadcrumb" id="global-breadcrumbs">
						{+START,INCLUDE,ICON}
							NAME=breadcrumbs
							ICON_TITLE={!YOU_ARE_HERE}
							ICON_DESCRIPTION={!YOU_ARE_HERE}
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

			{+START,IF,{$GET,has_left_panel}}
				<div id="panel-left" class="global-side-panel{+START,IF,{$GET,has_right_panel}} with-both-panels{+END}" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
					<div class="stuck-nav" data-stuck-nav>{$LOAD_PANEL,left}</div>
				</div>
			{+END}

			{+START,IF,{$GET,has_right_panel}}
				<div id="panel-right" class="global-side-panel{+START,IF,{$GET,has_left_panel}} with-both-panels{+END}" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
					<div class="stuck-nav" data-stuck-nav>{$LOAD_PANEL,right}</div>
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

		<noscript>
			{!JAVASCRIPT_REQUIRED}
		</noscript>

		{$,This is the main site footer}
		{+START,IF,{$SHOW_FOOTER}}
			<footer class="float-surrounder" itemscope="itemscope" itemtype="http://schema.org/WPFooter" role="contentinfo">
				<div class="global-footer-left block-desktop">
					{+START,SET,FOOTER_BUTTONS}
						{+START,IF,{$CONFIG_OPTION,bottom_show_top_button}}
							<li>
								<a rel="back_to_top" accesskey="g" href="#" title="{!BACK_TO_TOP}">
									{+START,INCLUDE,ICON}
										NAME=tool_buttons/top
										SIZE=24
									{+END}
								</a>
							</li>
						{+END}
						{+START,IF,{$ADDON_INSTALLED,realtime_rain}}{+START,IF,{$CONFIG_OPTION,bottom_show_realtime_rain_button,1}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_realtime_rain}}{+START,IF,{$NEQ,{$ZONE}:{$PAGE},adminzone:admin_realtime_rain}}
							<li>
								<a id="realtime-rain-button" data-btn-load-realtime-rain="{}" title="{!realtime_rain:REALTIME_RAIN}" href="{$PAGE_LINK*,adminzone:admin_realtime_rain}">
									{+START,INCLUDE,ICON}
										NAME=tool_buttons/realtime_rain_on
										ICON_ID=realtime-rain-img
										ICON_SIZE=24
									{+END}
								</a>
							</li>
						{+END}{+END}{+END}{+END}
						{+START,IF,{$HAS_ZONE_ACCESS,adminzone}}
							{+START,IF,{$ADDON_INSTALLED,commandr}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_commandr}}{+START,IF,{$CONFIG_OPTION,bottom_show_commandr_button,1}}{+START,IF,{$NEQ,{$ZONE}:{$PAGE},adminzone:admin_commandr}}
								<li>
									<a id="commandr-button" accesskey="o"{+START,IF,{$DESKTOP}} data-btn-load-commandr="{}" {+END} href="{$PAGE_LINK*,adminzone:admin_commandr}">
										{+START,INCLUDE,ICON}
											NAME=tool_buttons/commandr_on
											ICON_CLASS=commandr-img
											ICON_TITLE={!commandr:COMMANDR_DESCRIPTIVE_TITLE}
											ICON_DESCRIPTION={!commandr:COMMANDR_DESCRIPTIVE_TITLE}
											ICON_SIZE=24
										{+END}
									</a>
								</li>
							{+END}{+END}{+END}{+END}
							<li>
								<a href="{$PAGE_LINK*,adminzone:,,,,keep_theme}">
									{+START,INCLUDE,ICON}
										NAME=menu/adminzone/adminzone
										ICON_TITLE={!ADMIN_ZONE}
										ICON_SIZE=24
									{+END}
								</a>
							</li>
							{+START,IF,{$DESKTOP}}{+START,IF,{$EQ,{$BRAND_NAME},Composr}}
								<li>
									<a id="software-chat-button" accesskey="-" href="#!" class="js-global-click-load-software-chat">
										{+START,INCLUDE,ICON}
											NAME=tool_buttons/software_chat
											ICON_CLASS=software-chat-img
											ICON_TITLE={!SOFTWARE_CHAT}
											ICON_DESCRIPTION={!SOFTWARE_CHAT}
											ICON_SIZE=24
										{+END}
									</a>
								</li>
							{+END}{+END}
						{+END}
					{+END}
					{+START,IF_NON_EMPTY,{$TRIM,{$GET,FOOTER_BUTTONS}}}{+START,IF,{$DESKTOP}}
						<ul class="horizontal-buttons">
							{$GET,FOOTER_BUTTONS}
						</ul>
					{+END}{+END}

					{+START,IF,{$HAS_SU}}
						<form title="{!SU} {!LINK_NEW_WINDOW}" class="inline su-form" method="get" action="{$URL_FOR_GET_FORM*,{$SELF_URL,0,1}}" target="_blank" autocomplete="off">
							{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,1},keep_su}

							<div class="inline">
								<div class="accessibility-hidden"><label for="su">{!SU}</label></div>
								<input title="{!SU_2}" class="js-global-input-su-keypress-enter-submit-form" accesskey="w" size="10" type="text"{+START,IF_NON_EMPTY,{$_GET,keep_su}} placeholder="{$USERNAME*}"{+END} value="{+START,IF_NON_EMPTY,{$_GET,keep_su}}{$USERNAME*}{+END}" id="su" name="keep_su" />
								<button data-disable-on-click="1" class="button-micro menu--site-meta--user-actions--login" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/login{+END} {!SU}</button>
							</div>
						</form>
					{+END}

					{+START,IF,{$DESKTOP}}{+START,IF_NON_EMPTY,{$STAFF_ACTIONS}}{+START,IF,{$CONFIG_OPTION,show_staff_page_actions}}
						<form title="{!SCREEN_DEV_TOOLS} {!LINK_NEW_WINDOW}" class="inline special-page-type-form js-global-submit-staff-actions-select" action="{$URL_FOR_GET_FORM*,{$SELF_URL,0,1}}" method="get" target="_blank" autocomplete="off">
							{$HIDDENS_FOR_GET_FORM,{$SELF_URL,0,1,0,cache_blocks=0,cache_comcode_pages=0,keep_minify=0,special_page_type=<null>,keep_template_magic_markers=<null>}}

							<div class="inline">
								<p class="accessibility-hidden"><label for="special-page-type">{!SCREEN_DEV_TOOLS}</label></p>
								<select id="special-page-type" name="special_page_type">{$STAFF_ACTIONS}</select>
								<button class="button-micro buttons--proceed" type="submit">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!PROCEED_SHORT}</button>
							</div>
						</form>
					{+END}{+END}{+END}
				</div>

				<div class="global-footer-right">
					<nav class="global-minilinks">
						<ul class="footer-links">
							{+START,IF,{$CONFIG_OPTION,bottom_show_sitemap_button}}
								<li><a accesskey="3" rel="site_map" href="{$PAGE_LINK*,_SEARCH:sitemap}">{!SITEMAP}</a></li>
							{+END}
							{+START,IF,{$CONFIG_OPTION,bottom_show_rules_link}}
								<li><a data-open-as-overlay="{}" rel="site_rules" accesskey="7" href="{$PAGE_LINK*,:rules}">{!RULES}</a></li>
							{+END}
							{+START,IF,{$CONFIG_OPTION,bottom_show_privacy_link}}
								<li><a data-open-as-overlay="{}" rel="site_privacy" accesskey="8" href="{$PAGE_LINK*,_SEARCH:privacy}">{!PRIVACY}</a></li>
							{+END}
							{+START,IF,{$CONFIG_OPTION,bottom_show_feedback_link}}
								<li><a rel="site_contact" accesskey="9" href="{$PAGE_LINK*,_SEARCH:feedback:redirect={$SELF_URL&,1}}">{!_FEEDBACK}</a></li>
							{+END}
							{+START,IF,{$NOR,{$IS_HTTPAUTH_LOGIN},{$IS_GUEST}}}
								<li><form title="{!LOGOUT}" class="inline" method="post" action="{$PAGE_LINK*,_SELF:login:logout}" autocomplete="off"><button class="button-hyperlink" type="submit" title="{!_LOGOUT,{$USERNAME*}}">{!LOGOUT}</button></form></li>
							{+END}
							{+START,IF,{$OR,{$IS_HTTPAUTH_LOGIN},{$IS_GUEST}}}
								<li><a data-open-as-overlay="{}" href="{$PAGE_LINK*,_SELF:login:{$?,{$NOR,{$GET,login_screen},{$?,{$NOR,{$GET,login_screen},{$_POSTED},{$EQ,{$PAGE},login,join}},redirect={$SELF_URL&*,1}}}}}">{!_LOGIN}</a></li>
							{+END}
							{+START,IF,{$THEME_OPTION,mobile_support}}
								{+START,IF,{$MOBILE}}
									<li><a href="{$SELF_URL*,1,0,0,keep_mobile=0}">{!NONMOBILE_VERSION}</a>
								{+END}
								{+START,IF,{$DESKTOP}}
									<li><a href="{$SELF_URL*,1,0,0,keep_mobile=1}">{!MOBILE_VERSION}</a></li>
								{+END}
							{+END}
							{+START,IF,{$HAS_ZONE_ACCESS,adminzone}}
								{+START,IF,{$ADDON_INSTALLED,commandr}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_commandr}}{+START,IF,{$CONFIG_OPTION,bottom_show_commandr_button,1}}{+START,IF,{$NEQ,{$ZONE}:{$PAGE},adminzone:admin_commandr}}
									<li class="inlineblock-mobile"><a accesskey="o" href="{$PAGE_LINK*,adminzone:admin_commandr}">{!commandr:COMMANDR}</a></li>
								{+END}{+END}{+END}{+END}
								<li class="inlineblock-mobile"><a href="{$PAGE_LINK*,adminzone:}">{!ADMIN_ZONE}</a></li>
							{+END}
							{+START,IF,{$CONFIG_OPTION,bottom_show_top_button}}
								<li class="inlineblock-mobile"><a rel="back_to_top" accesskey="g" href="#">{!_BACK_TO_TOP}</a></li>
							{+END}
							{+START,IF_NON_EMPTY,{$HONEYPOT_LINK}}
								<li class="accessibility-hidden">{$HONEYPOT_LINK}</li>
							{+END}
							<li class="accessibility-hidden"><a accesskey="1" href="{$PAGE_LINK*,:}">{$SITE_NAME*}</a></li>
							<li class="accessibility-hidden"><a accesskey="0" href="{$PAGE_LINK*,:keymap}">{!KEYBOARD_MAP}</a></li>
						</ul>
					</nav>

					<div class="global-copyright">
						{$,Uncomment to show user's time {$DATE} {$TIME}}

						{$COPYRIGHT`}
					</div>
				</div>
			</footer>
		{+END}

		{$EXTRA_FOOT}

		{$JS_TEMPCODE}
	</div>
</body>
</html>

<!-- Powered by {$BRAND_NAME*}, (c) ocProducts Ltd - {$BRAND_BASE_URL*} -->
