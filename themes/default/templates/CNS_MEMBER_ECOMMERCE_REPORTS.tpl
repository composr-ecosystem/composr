<div class="cns-member-profile-ecommerce-logs clearfix">
	{+START,IF_EMPTY,{CONTENT}}
		<p class="nothing-here">{!NO_ENTRIES}</p>
	{+END}

	{+START,IF_NON_EMPTY,{CONTENT}}
		{CONTENT}
	{+END}

	{+START,IF_NON_EMPTY,{PAGINATION}}
		<div class="pagination-spacing clearfix ajax-block-wrapper-links">
			{PAGINATION}
		</div>
	{+END}
</div>
