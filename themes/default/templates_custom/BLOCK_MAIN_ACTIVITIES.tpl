{$REQUIRE_JAVASCRIPT,activity_feed}
{$SET,is_block_raw,{$EQ,{$COMMA_LIST_GET,{BLOCK_PARAMS},raw},1}}

{+START,IF,{$NOT,{$GET,is_block_raw}}}
	{$SET,ajax_block_main_activities_wrapper,ajax-block-main-activities-wrapper-{$RAND%}}
	{$SET,block_call_url,{$FACILITATE_AJAX_BLOCK_CALL,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS}{+END}&page={$PAGE&}}
	<div id="{$GET*,ajax_block_main_activities_wrapper}" data-tpl="blockMainActivities" data-tpl-params="{+START,PARAMS_JSON,is_block_raw,MODE,MEMBER_IDS,START,GROW,MAX,REFRESH_TIME}{_*}{+END}" data-ajaxify="{ callUrl: '{$GET;*,block_call_url}', callParamsFromTarget: ['^[^_]*_start$', '^[^_]*_max$'], targetsSelector: '.ajax-block-wrapper-links a, .ajax-block-wrapper-links form' }">
		<div class="clearfix">
			<div id="activities-feed">
				<div id="activities-general-notify"></div>
				<ul class="clearfix activities-holder raw-ajax-grow-spot" id="activities-holder">
					{+START,IF_EMPTY,{CONTENT}}
						<li id="activity--1"><p class="nothing-here">{!NO_ACTIVITIES}</p></li>
					{+END}

					{+START,LOOP,CONTENT}
						<li id="activity-{LIID*}" class="activities-box box">
							{+START,INCLUDE,ACTIVITY}{+END}
						</li>
					{+END}
				</ul>
			</div>

			{+START,IF_NON_EMPTY,{PAGINATION}}
				<div class="pagination-spacing clearfix ajax-block-wrapper-links">
					{PAGINATION}
				</div>
			{+END}
		</div>

		{+START,IF_NON_EMPTY,{PAGINATION}}
			{+START,INCLUDE,AJAX_PAGINATION}
				WRAPPER_ID={$GET,ajax_block_main_activities_wrapper}
				ALLOW_INFINITE_SCROLL={$AND,{GROW},{$NAND,{$MATCH_KEY_MATCH,_WILD:members:view},{$EQ,{$MEMBER},{MEMBER_IDS}}}}
			{+END}
		{+END}
	</div>
{+END}

{+START,IF,{$GET,is_block_raw}}
	{+START,LOOP,CONTENT}
		<li id="{LIID*}" class="activities-box box">
			{+START,INCLUDE,ACTIVITY}{+END}
		</li>
	{+END}

	{+START,IF_PASSED,PAGINATION}
		{PAGINATION}
	{+END}
{+END}
