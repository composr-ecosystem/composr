{$,The IF filter makes sure we only show fields we haven't shown elsewhere (hard-coded list)}
{+START,IF,{$NEQ,{FIELDID},0,2,7}}{+START,IF_NON_EMPTY,{VALUE}}
	<tr>
		<th width="30%">{FIELD*}</th>
		<td width="70%">{VALUE}</td>
	</tr>
{+END}{+END}
