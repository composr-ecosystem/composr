{TITLE}

<div class="wide-table-wrap"><table class="columned-table wide-table results-table autosized-table responsive-table" itemprop="significantLinks">
	<thead>
		<tr>
			<th>{!IDENTIFIER}</th>
			<th>{!NAME}</th>
			<th>{!PRICE}</th>
			<th>{$TAX_LABEL}</th>
			<th>{!DATE_TIME}</th>
			{+START,IF,{$DESKTOP}}
				<th>{!STATUS}</th>
			{+END}
			<th>{!ACTIONS}</th>
		</tr>
	</thead>

	<tbody>
		{+START,LOOP,INVOICES}
			{$SET,cycle,{$CYCLE,results_table_zebra,zebra-0,zebra-1}}

			<tr class="{$GET,cycle} thick-border">
				<td>
					{INVOICE_ID*}
				</td>
				<td>
					{INVOICE_TITLE}

					<p class="assocated-details block-mobile">
						<span class="field-name">{!STATUS}:</span> {STATE*}
					</p>
				</td>
				<td>
					{$CURRENCY,{PRICE},{CURRENCY},{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}
				</td>
				<td>
					{$CURRENCY,{TAX},{CURRENCY},{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}
				</td>
				<td>
					{DATE*}
				</td>
				{+START,IF,{$DESKTOP}}
					<td class="cell-desktop">
						{STATE*}
					</td>
				{+END}
				<td>
					<ul class="horizontal-links horiz-field-sep">
						{+START,IF,{$HAS_ACTUAL_PAGE_ACCESS,admin_invoices}}
							<li>
								<a title="{!DELETE}: #{INVOICE_ID}" href="{$PAGE_LINK*,adminzone:admin_invoices:delete:{INVOICE_ID}}">{+START,INCLUDE,ICON}
									NAME=admin/delete
									ICON_SIZE=18
								{+END}</a>
							</li>
							{+START,IF,{FULFILLABLE}}
								<li><a title="{!MARK_AS_FULFILLED}: #{INVOICE_ID}" href="{$PAGE_LINK*,adminzone:admin_invoices:fulfill:{INVOICE_ID}}">{!FULFILL}</a></li>
							{+END}
						{+END}
						{+START,IF,{PAYABLE}}
							<li>{TRANSACTION_BUTTON}</li>
						{+END}
					</ul>
				</td>
			</tr>
			{+START,IF_NON_EMPTY,{NOTE}}
				<tr class="{$GET,cycle}">
					<td class="responsive-table-no-prefix" colspan="7">
						<span class="field-name">{!NOTE}</span>: {NOTE*}
					</td>
				</tr>
			{+END}
		{+END}
	</tbody>
</table></div>
