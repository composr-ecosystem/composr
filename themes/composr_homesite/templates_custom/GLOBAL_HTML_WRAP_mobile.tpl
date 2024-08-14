<body itemscope="itemscope" itemtype="http://schema.org/WebPage" class="website_body_actual">
<div class="website_body zone_running_{$ZONE*} page_running_{$PAGE*}" id="main_website">
	{+START,IF,{$SHOW_HEADER}}
		<header itemscope="itemscope" itemtype="http://schema.org/WPHeader">
			{$,This allows screen-reader users (e.g. blind users) to jump past the panels etc to the main content}
			<a accesskey="s" class="accessibility_hidden" href="#maincontent">{!SKIP_NAVIGATION}</a>

			<div class="row">
				<figure class="logo"><a href="{$PAGE_LINK*,:}"><img src="{$IMG*,composr_homesite/mobile/logo-composr}" alt="Composr logo"></a></figure>

				<div class="headRCol">
					<ul>
						<li>
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
						</li>

						<li>
							{$BLOCK,block=menu,type=mobile,param=composr_homesite_header}
						</li>
					</ul>

					{+START,INCLUDE,_SEARCH}{+END}

					<!--
					<div class="headEmail">
						Briefr: <a class="link_exempt" href="{$PAGE_LINK*,:contact}" title="Bring questions or projects">Contact ocProducts</a>{+START,IF,{$EQ,{$PAGE},support,tickets,professional_support,contact}}. {$BLOCK,block=show_credits}{+END}
					</div>
					-->
				</div>
			</div>
		</header>
	{+END}

	{$,Associated with the SKIP_NAVIGATION link defined further up}
	<a id="maincontent"></a>

	{$,Composr may show little messages for you as it runs relating to what you are doing or the state the site is in}
	<div class="global_messages" id="global_messages">
		{$MESSAGES_TOP}
	</div>

	{+START,IF,{$SHOW_HEADER}}{+START,IF_NON_EMPTY,{$BREADCRUMBS}}
		<nav class="brCrumb breadcrumbs" itemprop="breadcrumb">
			{$BREADCRUMBS}
		</nav>
	{+END}{+END}

	{$LOAD_PANEL,top}

	<div class="screenInner">
		{MIDDLE}
	</div>

	{$LOAD_PANEL,bottom}

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
			<p>
				Copyright &copy; <a href="{$PAGE_LINK*,ocproducts:}">ocProducts Ltd</a>, {$FROM_TIMESTAMP*,%Y}. All Rights Reserved.
			</p>

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
		</footer>
	{+END}

	{$EXTRA_FOOT}

	{$JS_TEMPCODE,footer}
	<script>// <![CDATA[
		script_load_stuff();

		{+START,IF,{$EQ,{$_GET,wide_print},1}}try { window.print(); } catch (e) {}{+END}
	//]]></script>
</div>

<input type="hidden" id="g-post-tkn" value="{$CSRF_TOKEN}">
</body>
