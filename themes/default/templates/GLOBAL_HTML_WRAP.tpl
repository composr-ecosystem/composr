<!DOCTYPE html>

{$SET,page_link_privacy,{$PAGE_LINK,:privacy}}

{+START,SET,hero_slider}
	{+START,IF,{$NEQ,{$CONFIG_OPTION,site_closed},2}}
		{+START,IF,{$AND,{$ADDON_INSTALLED,galleries},{$MATCH_KEY_MATCH,:home,site:home}}}{+START,IF,{$THEME_OPTION,homepage_slider_enabled}}
			{$BLOCK,block=main_hero_slider,block_id=homepage-hero,gallery_name={$THEME_OPTION,homepage_slider_gallery},blank_if_empty=1,effect={$THEME_OPTION,homepage_slider_effect},interval={$THEME_OPTION,homepage_slider_cycle_duration},fullscreen={$THEME_OPTION,homepage_slider_fullscreen},show_scroll_down=1,check=0}
		{+END}{+END}
	{+END}
{+END}

{$SET,has_left_panel,{$IS_NON_EMPTY,{$TRIM,{$LOAD_PANEL,left}}}}
{$SET,has_right_panel,{$IS_NON_EMPTY,{$TRIM,{$LOAD_PANEL,right}}}}

{$,We deploy as HTML5 but code and conform strictly to XHTML5}
<html lang="{$LCASE*,{$METADATA,lang}}"{$ATTR_DEFAULTED,dir,{!dir},ltr} data-view="Global" data-view-params="{+START,PARAMS_JSON,page_link_privacy}{_*}{+END}" class="{+START,IF,{$SHOW_HEADER}}has-header-{$THEME_OPTION*,header_type}{+END} {+START,IF,{$IS_NON_EMPTY,{$TRIM,{$GET,hero_slider}}}}has-homepage-slider {$?,{$THEME_OPTION,homepage_slider_fullscreen},has-homepage-slider-fullscreen}{+END} {$?,{$GET,has_left_panel},has-left-panel} {$?,{$GET,has_right_panel},has-right-panel}">
<head>
	{+START,INCLUDE,HTML_HEAD}{+END}
</head>

{$,You can use main-website-inner to help you create fixed width designs; never put fixed-width stuff directly on ".website-body" or "body" because it will affects things like the preview or banner frames or popups/overlays}
<body class="website-body zone-running-{$REPLACE*,_,-,{$ZONE}} page-running-{$REPLACE*,_,-,{$PAGE}}" id="main-website" itemscope="itemscope" itemtype="http://schema.org/WebPage" data-tpl="globalHtmlWrap">
	<div id="main-website-inner">
		{+START,IF,{$SHOW_HEADER}}
			{+START,IF,{$EQ,{$THEME_OPTION,header_type},modern}}
				{+START,INCLUDE,HEADER_MODERN}{+END}
			{+END}
			{+START,IF,{$EQ,{$THEME_OPTION,header_type},side}}
				{+START,INCLUDE,HEADER_SIDE}{+END}
			{+END}
			{+START,IF,{$EQ,{$THEME_OPTION,header_type},classic}}
				{+START,INCLUDE,HEADER_CLASSIC}{+END}
			{+END}
		{+END}

		{+START,IF,{$IS_NON_EMPTY,{$TRIM,{$GET,hero_slider}}}}
			{$GET,hero_slider}
		{+END}

		{$,The banner}
		{+START,IF,{$DESKTOP}}
			{+START,IF,{$SHOW_HEADER}}
				{$SET-,BANNER,{$BANNER}} {$,This is to avoid evaluating the banner twice}
				{+START,IF_NON_EMPTY,{$GET,BANNER}}
					<div class="global-banner block-desktop container">{$GET,BANNER}</div>
				{+END}
			{+END}
		{+END}

		<div class="container">
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
			<div class="global-middle-outer">
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

				{+START,IF,{$GET,has_left_panel}}
					<div id="panel-left" class="global-side-panel{+START,IF,{$GET,has_right_panel}} with-both-panels{+END}" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
						<div {+START,IF,{$THEME_OPTION,stuck_nav}} class="stuck-nav" data-stuck-nav="data-stuck-nav"{+END}>{$LOAD_PANEL,left}</div>
					</div>
				{+END}

				{+START,IF,{$GET,has_right_panel}}
					<div id="panel-right" class="global-side-panel{+START,IF,{$GET,has_left_panel}} with-both-panels{+END}" role="complementary" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
						<div {+START,IF,{$THEME_OPTION,stuck_nav}} class="stuck-nav" data-stuck-nav="data-stuck-nav"{+END}>{$LOAD_PANEL,right}</div>
					</div>
				{+END}
			</div>
		</div>

		{+START,IF_NON_EMPTY,{$TRIM,{$LOAD_PANEL,bottom}}}
			<div id="panel-bottom" role="complementary">
				<div class="panel-bottom-inner container">
					{$LOAD_PANEL,bottom}
				</div>
			</div>
		{+END}

		{+START,IF_NON_EMPTY,{$MESSAGES_BOTTOM}}
			<div class="container">
				<div class="global-messages">
					{$MESSAGES_BOTTOM}
				</div>
			</div>
		{+END}

		{+START,IF,{$SHOW_FOOTER}}{+START,IF,{$EQ,{$CONFIG_OPTION,sitewide_im,1},1}}
			<div class="container">
				{$CHAT_IM}
			</div>
		{+END}{+END}

		{$,Late messages happen if something went wrong during outputting everything (i.e. too late in the process to show the error in the normal place)}
		{+START,IF_NON_EMPTY,{$LATE_MESSAGES}}
			<div class="container">
				<div class="global-messages" id="global-messages-2">
					{$LATE_MESSAGES}
				</div>
			</div>
		{+END}

		<noscript>
			{!JAVASCRIPT_REQUIRED}
		</noscript>

		{$,This is the main site footer}
		{+START,IF,{$SHOW_FOOTER}}
			<footer class="footer clearfix" itemscope="itemscope" itemtype="http://schema.org/WPFooter">
				<div class="footer-inner container">
					{+START,SET,footer_left}
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
								<li>
									<a href="{$PAGE_LINK*,adminzone:,,,,keep_theme}" title="{!ADMIN_ZONE}">{+START,INCLUDE,ICON}
										NAME=menu/adminzone/adminzone
										ICON_SIZE=24
									{+END}</a>
								</li>
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
						{+END}
						{+START,IF_NON_EMPTY,{$TRIM,{$GET,FOOTER_BUTTONS}}}{+START,IF,{$DESKTOP}}
							<ul class="horizontal-buttons">
								{$GET,FOOTER_BUTTONS}
							</ul>
						{+END}{+END}

						{+START,IF,{$HAS_SU}}
							<form title="{!SU} {!LINK_NEW_WINDOW}" class="inline su-form" method="get" action="{$URL_FOR_GET_FORM*,{$SELF_URL,1}}" target="_blank">
								{$HIDDENS_FOR_GET_FORM,{$SELF_URL,1},keep_su}

								<div class="inline">
									<div class="accessibility-hidden"><label for="su">{!SU}</label></div>
									<div class="input-group">
										<input title="{!SU_2}" class="form-control form-control-sm" data-submit-on-enter="1" accesskey="w" size="10" type="text"{+START,IF_NON_EMPTY,{$_GET,keep_su}} placeholder="{$USERNAME*}"{+END} value="{+START,IF_NON_EMPTY,{$_GET,keep_su}}{$USERNAME*}{+END}" id="su" name="keep_su" />
										<div class="input-group-append">
											<button data-disable-on-click="1" class="btn btn-primary btn-sm menu--site-meta--user-actions--login" type="submit">{+START,INCLUDE,ICON}NAME=menu/site_meta/user_actions/login{+END} {!SU}</button>
										</div>
									</div>
								</div>
							</form>
						{+END}

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
						{$,extra_footer_left_goes_here}
					{+END}
					{+START,IF_NON_EMPTY,{$TRIM,{$GET,footer_left}}}
						<div class="global-footer-left block-desktop">
							{$GET,footer_left}
						</div>
					{+END}

					<div class="global-footer-right">
						<nav class="global-minilinks">
							<ul class="footer-links">
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
									{+START,IF,{$ADDON_INSTALLED,commandr}}{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_commandr}}{+START,IF,{$CONFIG_OPTION,bottom_show_commandr_button,1}}{+START,IF,{$NEQ,{$ZONE}:{$PAGE},adminzone:admin_commandr}}
										<li class="inlineblock-mobile"><a accesskey="o" href="{$PAGE_LINK*,adminzone:admin_commandr}">{!commandr:COMMANDR}</a></li>
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
							</ul>
						</nav>

						{+START,IF_NON_EMPTY,{$COPYRIGHT}}
							<div class="global-copyright">
								{$,Uncomment to show user's time {$DATE} {$TIME}}

								{$COPYRIGHT`}
							</div>
						{+END}
						{$,extra_footer_right_goes_here}
					</div>
				</div>
			</footer>
		{+END}

		{$EXTRA_FOOT}

		{$JS_TEMPCODE}
	</div>
</body>
</html>

<!-- Powered by {$BRAND_NAME*}, (c) Christopher Graham - {$BRAND_BASE_URL*} -->
