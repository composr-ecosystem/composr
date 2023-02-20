<body class="website_body zone_running_{$ZONE*} page_running_{$PAGE*} type_running_{$_GET*,type,browse}{+START,IF,{$_GET,overlay}} overlay lightbox{+END}" id="main_website" itemscope="itemscope" itemtype="http://schema.org/WebPage">
	{+START,IF,{$SHOW_HEADER}}
		<header itemscope="itemscope" itemtype="http://schema.org/WPHeader">
			{$,This allows screen-reader users (e.g. blind users) to jump past the panels etc to the main content}
			<a accesskey="s" class="accessibility_hidden" href="#maincontent">{!SKIP_NAVIGATION}</a>

			<div class="row">
				<figure class="logo">
					<a href="{$PAGE_LINK*,:}"><img width="316" height="119" alt="Composr logo" src="{$IMG*,composr_homesite/composr_full_logo}" /></a>
				</figure>

				<div class="headRCol">
					<ul>
						<li class="textLabel"><a href="{$COMPOSR_REPOS_URL*}">GitLab</a></li>

						<li class="textLabel"><a href="{$PAGE_LINK*,forum:}">Community Forum</a></li>

						{$,Login form for guests}
						{+START,IF,{$IS_GUEST}}
							{$BLOCK,block=top_login}
						{+END}

						{+START,IF,{$NOT,{$IS_GUEST}}}
							<li>
								<div class="top_buttons">
									{$BLOCK,block=top_notifications}

									{$BLOCK,block=top_personal_stats}
								</div>
							</li>
						{+END}
					</ul>

					<!--
					<div class="headEmail">
						Briefr: <a class="link_exempt" href="{$PAGE_LINK*,:contact}" title="Bring questions or projects">Contact ocProducts</a>{+START,IF,{$EQ,{$PAGE},support,tickets,professional_support,contact}}. {$BLOCK,block=show_credits}{+END}
					</div>
					-->

					{+START,INCLUDE,_SEARCH}{+END}
				</div>
			</div>
		</header>

		{$BLOCK,block=menu,type=dropdown_new,param=composr_homesite_header}
	{+END}

	{+START,IF,{$AND,{$NOT,{$WIDE_HIGH}},{$OR,{$HAS_PRIVILEGE,access_closed_site},{$NOT,{$CONFIG_OPTION,site_closed}}},{$MATCH_KEY_MATCH,:start}}}
		<div class="bnrHolder">
			<div class="headBnr">
				<div class="banner">
					<h1 class="bnrText">
						The versatile <span class="orange">Content Management System</span> for next<br>
						generation websites
					</h1>

					<div class="bannerImg"><img width="600" height="403" alt="Small Composr screenshot" src="{$IMG*,composr_homesite/start/banner-img1}" /></div>

					<div class="textTop">
						Need a website? Tired of primitive systems that don't meet your requirements?
					</div>

					<div class="textBtm">
						Composr is Open Source so our community download runs without limits. Our services will give you that critical 'pro edge'.
					</div>

					<div class="topBtn">
						<a href="{$PAGE_LINK*,site:demo}" title="see how it works">SEE HOW IT WORKS</a>
					</div>
				</div>
			</div>
		</div>
	{+END}

	<div class="cntMainWrapper pdLess">
		<div class="cntMain" role="main">
			{$,Composr may show little messages for you as it runs relating to what you are doing or the state the site is in}
			<div class="global_messages" id="global_messages">
				{$MESSAGES_TOP}
			</div>

			{+START,IF,{$SHOW_HEADER}}{+START,IF_NON_EMPTY,{$BREADCRUMBS}}
				<nav class="brCrumb breadcrumbs" itemprop="breadcrumb">
					{$BREADCRUMBS}
				</nav>
			{+END}{+END}

			{$,Associated with the SKIP_NAVIGATION link defined further up}
			<a id="maincontent"></a>

			{$LOAD_PANEL,top}

			<div class="screenInner">
				{+START,IF,{$IN_STR,{$ZONE},docs}}
					<!-- Disabled affiliate
					<div style="text-align: center; margin-bottom: 5px;">
						<div id="amzn-assoc-ad-b42f4d41-65e0-4af7-b4a8-d8352681f987"></div><script async src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId=b42f4d41-65e0-4af7-b4a8-d8352681f987"></script>
					</div>
					-->
				{+END}

				{MIDDLE}

				{+START,IF,{$IN_STR,{$ZONE},docs}}
					<!-- Disabled affiliate
					<div style="text-align: center; margin-top: 5px;">
						<div id="amzn-assoc-ad-b42f4d41-65e0-4af7-b4a8-d8352681f987"></div><script async src="//z-na.amazon-adsystem.com/widgets/onejs?MarketPlace=US&adInstanceId=b42f4d41-65e0-4af7-b4a8-d8352681f987"></script>
					</div>
					-->
				{+END}
			</div>

			{$LOAD_PANEL,bottom}

			<a class="back-to-top" href="#">Back to Top</a>
		</div>
	</div>

	{$,Late messages happen if something went wrong during outputting everything (i.e. too late in the process to show the error in the normal place)}
	{+START,IF_NON_EMPTY,{$MESSAGES_BOTTOM}}
		<div class="global_messages">
			{$MESSAGES_BOTTOM}
		</div>
	{+END}
	{+START,IF_NON_EMPTY,{$LATE_MESSAGES}}
		<div class="global_messages" id="global_messages_2">
			{$LATE_MESSAGES}
		</div>
		<script>merge_global_messages();</script>
	{+END}

	{$,This is the main site footer}
	{+START,IF,{$SHOW_FOOTER}}
		<footer>
			<div class="ftrMain">
				<div class="ftrLeft">
					<ul class="ftr">
						<li class="accessibility_hidden"><a accesskey="1" href="{$PAGE_LINK*,:}">{$SITE_NAME*}</a></li>
						<li class="accessibility_hidden"><a accesskey="0" href="{$PAGE_LINK*,:keymap}">{!KEYBOARD_MAP}</a></li>
						{+START,IF_NON_EMPTY,{$HONEYPOT_LINK}}
							<li class="accessibility_hidden">{$HONEYPOT_LINK}</li>
						{+END}
						<li><a href="{$PAGE_LINK*,site:support}">Support</a></li>
						<li><a onclick="return open_link_as_overlay(this);" rel="site_rules" accesskey="7" href="{$PAGE_LINK*,_SEARCH:rules}">{!RULES}</a></li>
						<li><a onclick="return open_link_as_overlay(this);" rel="site_privacy" accesskey="8" href="{$PAGE_LINK*,_SEARCH:privacy}">{!PRIVACY}</a></li>
						<!--
						<li><a target="_blank" href="http://www.arvixe.com/5256-223-3-122.html">Preinstalled Composr Hosting</a></li>
						-->
						{+START,IF,{$NOR,{$IS_HTTPAUTH_LOGIN},{$IS_GUEST}}}
							<li><form title="{!LOGOUT}" class="inline" method="post" action="{$PAGE_LINK*,_SELF:login:logout}"><input class="button_hyperlink" type="submit" title="{!_LOGOUT,{$USERNAME*}}" value="{!LOGOUT}" /></form></li>
						{+END}
						{+START,IF,{$OR,{$IS_HTTPAUTH_LOGIN},{$IS_GUEST}}}
							<li><a onclick="return open_link_as_overlay(this);" href="{$PAGE_LINK*,_SELF:login:{$?,{$NOR,{$GET,login_screen},{$EQ,{$PAGE},login}},redirect={$SELF_URL&*,1}}}">{!_LOGIN}</a></li>
						{+END}
						{+START,IF,{$CONFIG_OPTION,mobile_support}}
							{+START,IF,{$MOBILE}}
								<li><a href="{$SELF_URL*,1,0,0,keep_mobile=0}">{!NONMOBILE_VERSION}</a>
							{+END}
							{+START,IF,{$NOT,{$MOBILE}}}
								<li><a href="{$SELF_URL*,1,0,0,keep_mobile=1}">{!MOBILE_VERSION}</a></li>
							{+END}
						{+END}
					</ul>

					<div class="ftrBtm">
						Copyright &copy; <a href="{$PAGE_LINK*,ocproducts:}">ocProducts Ltd</a>, {$FROM_TIMESTAMP*,%Y}. All rights reserved. Composr was formerly known as ocPortal.
					</div>

					<div class="ftrBtm">
						<strong>Did you know?</strong> {$BLOCK,block=main_quotes}
					</div>
				</div>

				<div class="ftrRight">
					<div class="ftrImg">
						<div class="one">
							<a class="ttGeekCred" onfocus="this.onmouseover();" onblur="this.onmouseout();" onmouseover="if (typeof window.activate_tooltip!='undefined') activate_tooltip(this,event,'&lt;div class=&quot;geekCred&quot;&gt;&lt;h2&gt;Geek stats&lt;br /&gt;(standard Composr)&lt;/h2&gt;OSS License: CPAL&lt;br /&gt;&lt;br /&gt;Template files: &lt;strong&gt;1087&lt;/strong&gt;&lt;br /&gt;PHP files: &lt;strong&gt;2092&lt;/strong&gt;&lt;br /&gt;Image files: &lt;strong&gt;1377&lt;/strong&gt;&lt;br /&gt;CSS files: &lt;strong&gt;81&lt;/strong&gt;&lt;br /&gt;&lt;br /&gt;KLOC: &lt;strong&gt;428&lt;/strong&gt;&lt;br /&gt;&lt;br /&gt;Downloads: &lt;strong&gt;over 1.5 million&lt;/strong&gt;&lt;br /&gt;&lt;br /&gt;&lt;em&gt;(As of Jan 2015)&lt;/em&gt;&lt;/div&gt;','auto');" href="{$PAGE_LINK*,site:licence}"><img alt="Composr is Open Source" src="{$IMG*,composr_homesite/img-open-source}" width="{$IMG_WIDTH*,{$IMG,composr_homesite/img-open-source}}" height="{$IMG_HEIGHT*,{$IMG,composr_homesite/img-open-source}}" title="" /></a>
						</div>

						<div class="two">
							<a href="{$PAGE_LINK*,docs:tutorials:Web standards & Accessibility}"><img alt="Composr uses HTML5" src="{$IMG*,composr_homesite/img-html5}" width="{$IMG_WIDTH*,{$IMG,composr_homesite/img-html5}}" height="{$IMG_HEIGHT*,{$IMG,composr_homesite/img-html5}}" /></a>
						</div>
					</div>
				</div>
			</div>
		</footer>
	{+END}

	{$EXTRA_FOOT}

	{$JS_TEMPCODE,footer}
	<script>// <![CDATA[
		script_load_stuff();

		{+START,IF,{$EQ,{$_GET,wide_print},1}}try { window.print(); } catch (e) {}{+END}
	//]]></script>
</body>
