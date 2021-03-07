{$,The IF filter makes sure we only show fields we haven't shown elsewhere (hard-coded list)}
{+START,IF,{$NEQ,{FIELDID},0,1,2,3,9,7}}{+START,IF,{$NEQ,{VALUE},,{!NA_EM}}}
	{+START,IF,{$PREG_MATCH,^.*: ,{FIELD}}}
		{$SET,next_title,{$PREG_REPLACE,: .*$,,{FIELD}}}
		{+START,IF,{$NEQ,{$GET,just_done_title},{$GET,next_title}}}
			<tr class="form-table-field-spacer">
				<th colspan="2" class="table-heading-cell vertical-alignment">
					<h3>{$GET*,next_title}</h3>
				</th>
			</tr>
		{+END}
		{$SET,just_done_title,{$GET,next_title}}
	{+END}

	<tr>
		<th style="width: 30%">{$PREG_REPLACE,^.*: ,,{FIELD*}}</th>
		<td style="width: 70%">{VALUE}</td>
	</tr>
{+END}{+END}
