<form title="{!SORT}" action="{$URL_FOR_GET_FORM*,{URL}}{+START,IF_PASSED,HASH}#{HASH*}{+END}" method="get" class="pagination-sort inline-block">
	{$HIDDENS_FOR_GET_FORM,{URL},{SORT}}

	{$SET,RAND_PAGINATION,{$RAND}}

	{+START,SET,sort_button}
		{+START,IF,{$GET,show_sort_button}}
			{+START,IF_NON_PASSED,FILTER}
				<button data-disable-on-click="1" class="btn btn-primary btn-sm buttons--sort" type="submit" title="{!SORT}{+START,IF_NON_EMPTY,{$GET,TEXT_ID}}: {$GET*,TEXT_ID}{+END}">{+START,INCLUDE,ICON}NAME=buttons/sort{+END} {!SORT}</button>
			{+END}
		{+END}
		{+START,IF_PASSED,FILTER}
			<button data-disable-on-click="1" class="btn btn-primary btn-sm buttons--filter" type="submit" title="{!PROCEED}{+START,IF_NON_EMPTY,{$GET,TEXT_ID}}: {$GET*,TEXT_ID}{+END}">{+START,INCLUDE,ICON}NAME=buttons/filter{+END} {!PROCEED}</button>
		{+END}
	{+END}

	{+START,IF_PASSED,HIDDEN}
		{HIDDEN}
	{+END}

	{+START,IF_PASSED,FILTER}
		<label for="filter"><span class="field-name">{!SEARCH}:</span> <input value="{FILTER*}" name="filter" id="filter" size="10" /></label>
	{+END}

	<label for="r-{$GET*,RAND_PAGINATION}">{!SORT_BY} <span class="accessibility-hidden">{$GET*,TEXT_ID}</span></label>
	<select {+START,IF,{$NOT,{$GET,show_sort_button}}} data-change-submit-form="1"{+END} id="r-{$GET*,RAND_PAGINATION}" name="{SORT*}" class="form-control form-control-sm">
		{SELECTORS}
	</select>{$GET,sort_button}
</form>
