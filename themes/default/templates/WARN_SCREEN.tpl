<div data-tpl="warnScreen">
	{TITLE}

	{$REQUIRE_CSS,messages}

	{+START,IF,{$NEQ,{TEXT},{!MISSING_RESOURCE}}}
		{+START,IF_PASSED,WEBSERVICE_RESULT}
			<div class="box box---warn-screen"><div class="box-inner">
				{TEXT*}
			</div></div>

			<div class="ssm-warn expanded-advice">
				<h2>Expanded advice</h2>

				{WEBSERVICE_RESULT}
			</div>
		{+END}
		{+START,IF_NON_PASSED,WEBSERVICE_RESULT}
			<div class="site-special-message ssm-warn" role="alert"{+START,IF_PASSED,IMAGE_URL} style="background-image: url('{IMAGE_URL;*}');"{+END}>
				<div class="site-special-message-inner">
					<div class="box box---warn-screen"><div class="box-inner">
						{TEXT*}
					</div></div>
				</div>
			</div>
		{+END}
	{+END}
	{+START,IF,{$EQ,{TEXT},{!MISSING_RESOURCE}}}
		{+START,INCLUDE,RED_ALERT}
			ROLE=error
			TEXT={!MISSING_RESOURCE}
		{+END}

		<h2>{!SITEMAP}</h2>

		{$REQUIRE_CSS,menu__sitemap}
		{$REQUIRE_JAVASCRIPT,core_menus}
		{$BLOCK-,block=menu,param=\,use_page_groupings=1,type=sitemap,quick_cache=1}

		{+START,IF,{$ADDON_INSTALLED,search}}
			<h2>{!SEARCH}</h2>

			{$BLOCK,block=main_search,failsafe=1}
		{+END}
	{+END}

	{+START,IF,{PROVIDE_BACK}}{+START,IF,{$NOT,{$RUNNING_SCRIPT,preview}}}
		<p class="back-button">
			<a href="#!" data-cms-btn-go-back="1" title="{!NEXT_ITEM_BACK}">{+START,INCLUDE,ICON}
				NAME=admin/back
				ICON_SIZE=48
			{+END}</a>
		</p>
	{+END}{+END}
</div>
