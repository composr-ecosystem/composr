{TITLE}

<p>
	{!ECOM_PRODUCTS_LOG_TEXT}
</p>

<div class="clearfix">
	{+START,IF_PASSED,FILTERS_ROW_A}{+START,IF_PASSED,URL}
		{+START,INCLUDE,FILTER_BOX}{+END}
	{+END}{+END}

	{CONTENT}

	{+START,IF_NON_EMPTY,{PAGINATION}}
		<div class="pagination-spacing clearfix ajax-block-wrapper-links">
			{PAGINATION}
		</div>
	{+END}
</div>
