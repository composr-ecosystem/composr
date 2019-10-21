{+START,IF_PASSED,TEXT_ID}{$SET,TEXT_ID,{TEXT_ID}}{+END}

<div class="pagination">
	<span class="pagination-per-page">{PER_PAGE}</span>

	<nav class="pagination-nav">
		{FIRST}{PREVIOUS}{CONTINUES_LEFT}{PARTS}{CONTINUES_RIGHT}{NEXT}{LAST}
	</nav>

	<span class="pagination-pages-list">{PAGES_LIST}</span>
</div>
