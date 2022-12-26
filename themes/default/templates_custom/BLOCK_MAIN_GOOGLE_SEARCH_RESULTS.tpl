<div class="clearfix" id="cse">
	{+START,IF_EMPTY,{$_GET,q}}
		<p id="no-search-entered" class="nothing-here">{!NO_SEARCH_ENTERED}</p>
	{+END}

	<div class="gcse-searchresults-only"></div>
</div>
