{$REQUIRE_JAVASCRIPT,core_rich_media}
{$REQUIRE_CSS,big_tabs}

{+START,LOOP,TAB_CONTENTS}
	{+START,IF,{$EQ,{_loop_key},0}}
		{$SET,first_tab_id,ga-{TITLE|}}
	{+END}
{+END}

<div class="float-surrounder" data-tpl="googleAnalyticsTabs" data-tpl-params="{+START,PARAMS_JSON,first_tab_id}{_*}{+END}">
	<div class="comcode-big-tab-controller" role="tablist" data-view="ComcodeBigTabsController" data-view-params="{+START,PARAMS_JSON,SWITCH_TIME,PASS_ID,big_tab_sets,TABS}{_*}{+END}">
		{+START,LOOP,TAB_CONTENTS}
			<div class="{$?,{$EQ,{_loop_key},0},big-tab-active big-tab-first,big-tab-inactive}" id="{PASS_ID|*}-btgoto-{TITLE|*}">
				<a aria-controls="{PASS_ID|*}-section-{TITLE|*}" role="tab" href="#!" class="js-onclick-flip-page js-onclick-trigger-tab-ga-init" data-vw-flip-to="{TITLE|*}" data-tp-tab-id="ga-{TITLE|*}"><span>{TITLE*}</span></a>
			</div>
		{+END}
	</div>

	{+START,LOOP,TAB_CONTENTS}
		<div aria-labeledby="{PASS_ID|*}-btgoto-{TITLE|*}" role="tabpanel" class="comcode-big-tab" id="{PASS_ID|*}-section-{TITLE|*}" style="display: {$?,{$EQ,{_loop_key},0},block,none}; background-color: white">
			{CONTENT}
		</div>
	{+END}
</div>
