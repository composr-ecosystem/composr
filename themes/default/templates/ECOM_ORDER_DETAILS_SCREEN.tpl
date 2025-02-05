{TITLE}

{+START,INCLUDE,HANDLE_CONFLICT_RESOLUTION}{+END}
{+START,IF_PASSED,WARNING_DETAILS}
	{WARNING_DETAILS}
{+END}

{+START,IF_NON_EMPTY,{TEXT}}
	<p>{TEXT*}</p>
{+END}

<table class="map-table results-table wide-table autosized-table">
	<tbody>
		<tr>
			<th>{!ORDER_NUMBER}</th>
			<td>
				#{ORDER_NUMBER*}
			</td>
		</tr>

		<tr>
			<th>{!ORDERED_BY}</th>
			<td>
				{+START,IF_NON_EMPTY,{$USERNAME,{ORDERED_BY_MEMBER_ID}}}
					<a href="{$MEMBER_PROFILE_URL*,{ORDERED_BY_MEMBER_ID}}">{$DISPLAYED_USERNAME*,{ORDERED_BY_USERNAME}}</a>
				{+END}
				{+START,IF_EMPTY,{$USERNAME,{ORDERED_BY_MEMBER_ID}}}
					{ORDERED_BY_USERNAME*}
				{+END}
			</td>
		</tr>

		<tr>
			<th>{!ORDER_PLACED_ON}</th>
			<td>
				{ADD_DATE*}
			</td>
		</tr>

		<tr>
			<th>{!PRICE}</th>
			<td>
				{$CURRENCY_SYMBOL,{CURRENCY}}{TOTAL_PRICE*}
			</td>
		</tr>

		<tr>
			<th>{$TAX_LABEL}</th>
			<td>
				{$CURRENCY_SYMBOL,{CURRENCY}}{TOTAL_TAX*}
			</td>
		</tr>

		<tr>
			<th>{!SHIPPING_COST}</th>
			<td>
				{$CURRENCY_SYMBOL,{CURRENCY}}{TOTAL_SHIPPING_COST*}
			</td>
		</tr>

		<tr>
			<th>{!NOTES}</th>
			<td>
				{NOTES*}
			</td>
		</tr>

		<tr>
			<th>{!ORDER_STATUS}</th>
			<td>
				{ORDER_STATUS}
			</td>
		</tr>

		<tr>
			<th>{!SHIPPING_ADDRESS}</th>
			<td>
				{SHIPPING_ADDRESS}
			</td>
		</tr>

		{+START,IF_NON_EMPTY,{ORDER_ACTIONS}}
			<tr>
				<th>{!ORDER_ACTIONS}</th>
				<td>
					{ORDER_ACTIONS}
				</td>
			</tr>
		{+END}
	</tbody>
</table>

<h2>{!ORDERED_PRODUCTS}</h2>

{RESULTS_TABLE}
