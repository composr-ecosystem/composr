{$,Infinite scrolling hides the pagination when it comes into view, and auto-loads the next link, appending below the current results}
{+START,IF,{$AND,{$NEQ,{ALLOW_INFINITE_SCROLL},2},{$NEQ,{$_GET,keep_infinite_scroll},0}}}
{+START,IF,{$OR,{$EQ,{ALLOW_INFINITE_SCROLL},2},{$THEME_OPTION,infinite_scrolling}}}
	{$SET,infinite_scroll_call_url,{$FACILITATE_AJAX_BLOCK_CALL,{BLOCK_PARAMS}}{+START,IF_PASSED,EXTRA_GET_PARAMS}{EXTRA_GET_PARAMS}{+END}&page={$PAGE&}&zone={$ZONE&}}
{+END}
{+END}

<div class="tpl-placeholder" hidden="hidden" data-tpl="ajaxPagination" data-tpl-params="{+START,PARAMS_JSON,WRAPPER_ID,infinite_scroll_call_url}{_*}{+END}"></div>
