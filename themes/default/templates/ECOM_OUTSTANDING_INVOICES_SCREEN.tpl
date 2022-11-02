{TITLE}

<div class="wide-table-wrap"><table class="columned-table wide-table results-table autosized-table responsive-table" itemprop="significantLinks">
	<thead>
		<tr>
			<th>{!TITLE}</th>
			<th>{!USERNAME}</th>
			<th>{!AMOUNT}</th>
			<th>{$TAX_LABEL}</th>
			<th>{!DATE_TIME}</th>
			<th>{!ACTIONS}</th>
		</tr>
	</thead>

	<tbody>
		{+START,LOOP,INVOICES}
			{$SET,cycle,{$CYCLE,results_table_zebra,zebra-0,zebra-1}}

			<tr class="{$GET,cycle} thick-border">
				<td>
					{INVOICE_TITLE*}
				</td>
				<td>
					<a href="{PROFILE_URL*}">{USERNAME*}</a>
				</td>
				<td>
					{$CURRENCY,{AMOUNT},{CURRENCY},{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}
				</td>
				<td>
					{$CURRENCY,{TAX},{CURRENCY},{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}
				</td>
				<td>
					{DATE*}
				</td>
				<td>
					<a title="{!DELETE}: #{ID}" href="{$PAGE_LINK*,_SELF:_SELF:delete:{ID}:from={FROM}}">{+START,INCLUDE,ICON}
						NAME=admin/delete
						ICON_SIZE=18
					{+END}</a>
					{+START,IF,{$EQ,{STATE},paid}}
						<a title="{!MARK_AS_FULFILLED}: #{ID}" href="{$PAGE_LINK*,_SELF:_SELF:fulfill:{ID}}">{!MARK_AS_FULFILLED}</a>
					{+END}
				</td>
			</tr>
			{+START,IF_NON_EMPTY,{NOTE}}
				<tr class="{$GET,cycle}">
					<td class="responsive-table-no-prefix" colspan="6">
						<span class="field-name">{!NOTE}</span>: {NOTE*}
					</td>
				</tr>
			{+END}
		{+END}
	</tbody>
</table></div>
