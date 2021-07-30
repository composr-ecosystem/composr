{TITLE}

<p>
	{!LEADER_BOARD_PAGE_TEXT,{LEADER_BOARD_TYPE*},{LEADER_BOARD_TITLE*}}
</p>

{SETS}

{+START,IF_PASSED,PAGINATION}
	{+START,IF_NON_EMPTY,{PAGINATION}}
		<div class="pagination-spacing clearfix">
			{PAGINATION}
		</div>
	{+END}
{+END}
