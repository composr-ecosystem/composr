{$,The IF filter makes sure we only show fields we haven't shown elsewhere (hard-coded list)}
{+START,IF,{$NOR,{$EQ,{FIELDID},0},{$AND,{$EQ,{FIELDID},1},{$EQ,{TYPE},picture}}}}{+START,IF_NON_EMPTY,{VALUE}}
	<tr>
		<th style="width: 30%">{FIELD*}</th>
		<td style="width: 70%">{VALUE}</td>
	</tr>
{+END}{+END}
