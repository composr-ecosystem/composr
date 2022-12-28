{+START,SET,tooltip}
	{+START,IF_NON_EMPTY,{TOOLTIP_VALUES}}
		<table class="results-table wide-table map-table">
			<tbody>
				{+START,LOOP,TOOLTIP_VALUES}
					<tr>
						<th>{_loop_key*}</th>
						<td>{_loop_var*}</td>
					</tr>
				{+END}
			</tbody>
		</table>
	{+END}
{+END}

<tr{+START,IF_NON_EMPTY,{$TRIM,{$GET,tooltip}}} style="cursor: pointer" data-cms-tooltip="{$GET*,tooltip}"{+END}>
	{+START,LOOP,VALUES}
		{+START,SET,class}{+START,OF,CLASSES,{_loop_key}}{+END}{+END}
		{+START,SET,style}{+START,OF,STYLINGS,{_loop_key}}{+END}{+END}
		<td{+START,IF_NON_EMPTY,{$GET,style}} style="{$GET*,style}"{+END}{+START,IF_NON_EMPTY,{$GET,class}} class="{$GET*,class}"{+END}>{_loop_var*}</td>
	{+END}
</tr>
