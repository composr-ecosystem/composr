{$,Read the catalogue tutorial for information on custom catalogue layouts}

<div class="wide-table-wrap" itemprop="mainContentOfPage" content="true" itemscope="itemscope" itemtype="http://schema.org/Table">
	<table class="columned-table results-table wide-table catalogue-table responsive-table autosized-table">
		{+START,IF,{$DESKTOP}}
			<colgroup>
				{$SET,INC,0}
				{+START,WHILE,{$NEQ,{$GET,INC},{FIELD_COUNT}}}
					<col />
					{$INC,INC}
				{+END}
				{+START,IF,{$IN_STR,{CONTENT},<!--VIEWLINK-->}}
					<col class="catalogue-tabular-view-link-column" />
				{+END}
				{$, Uncomment to show ratings
					<col class="catalogue-tabular-rating-column" />
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
