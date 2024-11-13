{$,Read the catalogue tutorial for information on custom catalogue layouts}

{$SET,EDIT_URL,{EDIT_URL}}

<tr class="{$CYCLE,results_table_zebra,zebra-0,zebra-1}">
	{FIELDS_TABULAR}
	{+START,IF_NON_EMPTY,{VIEW_URL}}
		<td>
			<!--VIEWLINK-->
			<a class="btn btn-primary btn-scri buttons--more" href="{VIEW_URL*}"><span>{+START,INCLUDE,ICON}NAME=buttons/more{+END} {!VIEW}</span></a>
		</td>
	{+END}
	{$, Uncomment to show ratings
	<td>
		{$SET-,rating,{$RATING,catalogue_entry__{CATALOGUE},{ID},{SUBMITTER},,,RATING_INLINE_STATIC}}
		{+START,IF_NON_EMPTY,{$TRIM,{$GET,rating}}}
			{$GET,rating}
		{+END}
		{+START,IF_EMPTY,{$TRIM,{$GET,rating}}}
			{!UNRATED}
		{+END}
	</td>
	}
</tr>
