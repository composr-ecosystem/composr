{+START,IF_EMPTY,{CONTENT}}
	<p class="nothing-here">{!NO_ENTRIES,{CONTENT_TYPE}}</p>
{+END}

{+START,IF_PASSED,PAGINATION}
	{+START,IF_NON_EMPTY,{PAGINATION}}
		<div class="pagination-spacing clearfix ajax-block-wrapper-links">
			{PAGINATION}
		</div>

		{+START,INCLUDE,AJAX_PAGINATION}
			WRAPPER_ID={$GET,ajax_block_main_multi_content_wrapper}
			ALLOW_INFINITE_SCROLL=2
		{+END}
	{+END}
{+END}

{+START,IF_NON_EMPTY,{SUBMIT_URL}{ARCHIVE_URL}}
	<ul class="horizontal-links associated-links-block-group">
		{+START,IF_NON_EMPTY,{SUBMIT_URL}}
			<li><a rel="add" href="{SUBMIT_URL*}">{ADD_STRING*}</a></li>
		{+END}
		{+START,IF_NON_EMPTY,{CONTENT}}
			{+START,IF_NON_EMPTY,{ARCHIVE_URL}}
				<li><a href="{ARCHIVE_URL*}" title="{!ARCHIVES}: {TYPE*}">{!ARCHIVES}</a></li>
			{+END}
		{+END}
	</ul>
{+END}
