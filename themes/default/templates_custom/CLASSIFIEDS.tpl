<p class="accessibility-hidden">
	{!CLASSIFIEDS_PRICING}
</p>

<table class="columned-table wide-table results-table autosized-table spaced-table">
	<thead>
		<tr>
			<th>
				{!CLASSIFIED_DISPLAY_PERIOD}
			</th>
			<th>
				{!PRICE}
			</th>
		</tr>
	</thead>

	<tbody>
		{+START,LOOP,DATA}
			<tr>
				<td>
					{LABEL*}
				</td>
				<td>
					{+START,IF,{$EQ,{PRICE},0}}
						<em>{!CLASSIFIED_FREE}</em>
					{+END}
					{+START,IF,{$NEQ,{PRICE},0}}
						{$CURRENCY,{PRICE},{CURRENCY},{$?,{$CONFIG_OPTION,currency_auto,1},{$CURRENCY_USER},{$CURRENCY}}}
					{+END}
				</td>
			</tr>
		{+END}
	</tbody>
</table>
