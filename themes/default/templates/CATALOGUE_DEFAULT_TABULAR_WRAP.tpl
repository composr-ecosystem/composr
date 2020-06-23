{$,Read the catalogue tutorial for information on custom catalogue layouts}

<div class="wide_table_wrap" itemprop="mainContentOfPage" content="true" itemscope="itemscope" itemtype="http://schema.org/Table">
	<table class="columned_table results_table wide_table catalogue_table autosized_table">
		{+START,IF,{$NOT,{$MOBILE}}}
			<colgroup>
				{$SET,INC,0}
				{+START,WHILE,{$NEQ,{$GET,INC},{FIELD_COUNT}}}
					<col />
					{$INC,INC}
				{+END}
				{+START,IF,{$IN_STR,{CONTENT},<!--VIEWLINK-->}}
					<col class="catalogue_tabular_view_link_column" />
				{+END}
				{$, Uncomment to show ratings
					<col class="catalogue_tabular_rating_column" />
				}
			</colgroup>
		{+END}

		<thead>
			<tr>
				{HEAD}
				{+START,IF,{$IN_STR,{CONTENT},<!--VIEWLINK-->}}
					<th></th>
				{+END}
				{$, Uncomment to show ratings
					<th>{!RATING}</th>
				}
			</tr>
		</thead>

		<tbody>
			{CONTENT}
		</tbody>
	</table>
</div>

