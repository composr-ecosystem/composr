<div class="wide-table-wrap" itemprop="mainContentOfPage" content="true" itemscope="itemscope" itemtype="http://schema.org/Table">
	<table class="columned-table results-table wide-table responsive-table autosized-table">
		{+START,IF,{$DESKTOP}}
			<colgroup>
				{$SET,INC,0}
				{+START,WHILE,{$NEQ,{$GET,INC},{FIELD_COUNT}}}
					<col />
					{$INC,INC}
				{+END}
			</colgroup>
		{+END}

		<thead>
			<tr>
				{HEAD}
			</tr>
		</thead>

		<tbody>
			{CONTENT}
		</tbody>
	</table>
</div>
