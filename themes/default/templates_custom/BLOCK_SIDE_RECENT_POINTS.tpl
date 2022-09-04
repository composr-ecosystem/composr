{+START,IF_EMPTY,{TRANSACTIONS}}
	<p class="nothing-here">{!NONE}</p>
{+END}

{+START,IF_NON_EMPTY,{TRANSACTIONS}}
	<div class="wide-table-wrap">
		<table class="columned-table results-table wide-table autosized-table">
			<thead>
				<tr>
					<th>To</th>
					<th>&times;</th>
					<th>For</th>
				</tr>
			</thead>

			<tbody>
				{+START,LOOP,TRANSACTIONS}
					<tr>
						<td>{TO_LINK}</td>

						<td>{AMOUNT*}</td>

						<td>
							{+START,SET,gift_reason}
								{REASON*}

								{+START,IF,{$NOT,{ANONYMOUS}}}
									({FROM_NAME*})
								{+END}
								{+START,IF,{ANONYMOUS}}
									(Anonymous)
								{+END}
							{+END}
							{$TRUNCATE_LEFT,{$GET,gift_reason},20,1,1}
						</td>
					</tr>
				{+END}
			</tbody>
		</table>
	</div>
{+END}
