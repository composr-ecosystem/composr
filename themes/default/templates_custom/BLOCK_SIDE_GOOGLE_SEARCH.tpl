{$REQUIRE_JAVASCRIPT,google_search}
{$REQUIRE_CSS,google_search}
<section class="box box---block-side-google-search" data-tpl="blockSideGoogleSearch" data-tpl-params="{+START,PARAMS_JSON,ID}{_*}{+END}">
	<div class="box-inner">
	{+START,IF_NON_EMPTY,{TITLE}}<h3>{TITLE*}</h3>{+END}

	<div id="cse-search-form">
		<div class="gcse-searchbox-only" data-resultsUrl="{$PAGE_LINK*,_SELF:{PAGE_NAME}}" data-newWindow="false" data-queryParameterName="q" data-autoSearchOnLoad="false"></div>
	</div>
</div>
</section>
