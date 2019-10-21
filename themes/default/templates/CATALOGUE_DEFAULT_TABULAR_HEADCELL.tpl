{$,Read the catalogue tutorial for information on custom catalogue layouts}

<th>
	{$PREG_REPLACE,^.*: ,,{FIELD*}}

	<span class="table-header-sorting">
		{+START,IF,{$NEQ,{$PAGE},search}}
			{+START,IF_NON_EMPTY,{SORT_URL_ASC}}
				<a target="_self" href="{SORT_URL_ASC*}" title="{!SORT_BY} {FIELD*}, {!ASCENDING}" class="table-header-sorting-link {$?,{SORT_ASC_SELECTED},selected}">
					{+START,INCLUDE,ICON}
						NAME=results/sortablefield_asc
						ICON_SIZE=12
					{+END}
				</a>
			{+END}
		{+END}
		{+START,IF,{$NEQ,{$PAGE},search}}
			{+START,IF_NON_EMPTY,{SORT_URL_DESC}}
				<a target="_self" href="{SORT_URL_DESC*}" title="{!SORT_BY} {FIELD*}, {!DESCENDING}" class="table-header-sorting-link {$?,{SORT_DESC_SELECTED},selected}">
					{+START,INCLUDE,ICON}
						NAME=results/sortablefield_desc
						ICON_SIZE=12
					{+END}
				</a>
			{+END}
		{+END}
	</span>
</th>
