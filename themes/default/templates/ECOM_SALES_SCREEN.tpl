{TITLE}

<p>
	{!ECOM_PRODUCTS_LOG_TEXT}
</p>

<div class="clearfix">
	{+START,IF_PASSED,FILTERCODE_BOX}
		{+START,INCLUDE,FILTER_BOX}{+END}
	{+END}

	{CONTENT}

	{+START,IF_NON_EMPTY,{PAGINATION}}
		<div class="pagination-spacing clearfix ajax-block-wrapper-links">
			{PAGINATION}
		</div>
	{+END}
</div>
