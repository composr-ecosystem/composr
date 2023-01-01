{$REQUIRE_JAVASCRIPT,core_rich_media}

{$SET,block_call_url}

<div aria-labelledby="t-{$GET|*,tab_sets}-{TITLE|*}" role="tabpanel" id="g-{$GET|*,tab_sets}-{TITLE|*}" data-tpl="comcodeTabBody" data-tpl-params="{+START,PARAMS_JSON,block_call_url,tab_sets,TITLE}{_*}{+END}" style="display: {$?,{DEFAULT},block,none}">
	{+START,IF_PASSED,PAGE_LINK}
		{$SET,block_call_url,{$FACILITATE_AJAX_BLOCK_CALL,block=main_include_module\,param={PAGE_LINK},raw=.*}}
		<div class="spaced"><div class="ajax-tree-list-loading vertical-alignment"></div></div>
	{+END}

	{+START,IF_PASSED,CONTENT}
		{CONTENT}
	{+END}
</div>
