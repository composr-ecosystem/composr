{$REQUIRE_JAVASCRIPT,core_cns}
{$REQUIRE_CSS,modern_tabs}
{$SET,name_set_elsewhere,1}

<div class="vcard member-profile-screen" itemscope="itemscope" itemtype="http://schema.org/ProfilePage" data-view="CnsMemberProfileScreen" data-view-params="{+START,PARAMS_JSON,TABS,TAB_CODE,TAB_CONTENT,MEMBER_ID}{_*}{+END}">
	{TITLE}

	<!-- Member: #{MEMBER_ID%} -->

	{+START,IF,{$GT,{TABS},1}}
		<div class="modern-tabs">
			<div class="modern-tab-headers" role="tablist">
				{+START,LOOP,TABS}
					<div id="t-{TAB_CODE*}"{+START,IF,{TAB_FIRST}} class="tab-active"{+END}>
						<a aria-controls="g-{TAB_CODE*}" role="tab" href="#!" class="js-click-select-tab-g" data-vw-tab="{TAB_CODE*}">{+START,IF_NON_EMPTY,{TAB_ICON}}{+START,INCLUDE,ICON}
							NAME={TAB_ICON}
							ICON_SIZE=24
						{+END}{+END}
						<span>{TAB_TITLE*}</span></a>
					</div>
				{+END}
			</div>
			<div class="modern-tab-bodies">
				{+START,LOOP,TABS}
					<div aria-labelledby="t-{TAB_CODE*}" role="tabpanel" id="g-{TAB_CODE*}" class="modern-tab-body modern-tab-body-{TAB_CODE*}" style="display: {$?,{TAB_FIRST},block,none}">
						{+START,IF_PASSED,TAB_CONTENT}
							{TAB_CONTENT}
						{+END}

						{+START,IF_NON_PASSED,TAB_CONTENT}
							<p class="ajax-loading"><img alt="{!LOADING}" class="vertical-alignment" src="{$IMG*,loading}" /></p>
						{+END}
					</div>
				{+END}
			</div>
		</div>
	{+END}

	{+START,IF,{$EQ,{TABS},1}}
		{+START,LOOP,TABS}
			{+START,IF_PASSED,TAB_CONTENT}
				{TAB_CONTENT}
			{+END}
		{+END}
	{+END}
</div>
