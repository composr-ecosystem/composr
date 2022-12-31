{TITLE}

<table class="columned-table wide-table results-table autosized-table responsive-table" itemprop="significantLinks">
	<thead>
		<tr>
			<th>{!ECOM_ORDER}</th>
			<th>{!PRICE}</th>
			<th>{$TAX_LABEL}</th>
			<th>{!SHIPPING_COST}</th>
			<th>{!ORDERED_DATE}</th>
			<th>{!STATUS}</th>
			<th>{!TRANSACTION}</th>
		</tr>
	</thead>

	<tbody>
		{+START,LOOP,ORDERS}
			{$SET,cycle,{$CYCLE,results_table_zebra,zebra-0,zebra-1}}

			<tr class="{$GET,cycle}{+START,IF,{$NEQ,{_loop_key},0}} thick-border{+END}">
				<td>
					{+START,IF_NON_EMPTY,{ORDER_DET_URL}}
						<strong><a href="{ORDER_DET_URL*}">{ORDER_TITLE*}</a></strong>
					{+END}
					{+START,IF_EMPTY,{ORDER_DET_URL}}
						<strong>{ORDER_TITLE*}</strong>
					{+END}
				</td>
				<td>
					{TOTAL_PRICE}
				</td>
				<td>
					{$CURRENCY_SYMBOL,{CURRENCY}}{TOTAL_TAX*}
				</td>
				<td>
					{$CURRENCY_SYMBOL,{CURRENCY}}{TOTAL_SHIPPING_COST*}
				</td>
				<td>
					{DATE*}
				</td>
				<td>
					{STATUS*}
				</td>
				<td>
					{TRANSACTION_LINKER}
				</td>
			</tr>
			{+START,IF_NON_EMPTY,{NOTE}}
				<tr>
					<td colspan="7" data-th="{!NOTE}">
						<span class="block-desktop"><span class="field-name">{!NOTE}</span>: </span>{NOTE*}
					</td>
				</tr>
			{+END}
		{+END}
	</tbody>
</table>
