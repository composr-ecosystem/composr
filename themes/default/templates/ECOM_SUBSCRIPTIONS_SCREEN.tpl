{TITLE}

<p>{!SUBSCRIPTIONS_SCREEN}</p>

{+START,IF_NON_EMPTY,{SUBSCRIPTIONS}}
	<table class="columned-table wide-table results-table autosized-table responsive-table" itemprop="significantLinks">
		<thead>
			<tr>
				<th>
					{!TITLE}
				</th>

				<th>
					{!PRICE}
				</th>

				<th>
					{$TAX_LABEL}
				</th>

				<th>
					{!DATE}
				</th>

				<th>
					{!PAYMENT_GATEWAY}
				</th>

				<th>
					{!RENEWAL_STATUS}
				</th>

				<th>
					{!ACTIONS}
				</th>
			</tr>
		</thead>

		<tbody>
			{+START,LOOP,SUBSCRIPTIONS}
				<tr>
					<th>
						{+START,IF_PASSED,USERGROUP_SUBSCRIPTION_DESCRIPTION}
							<span class="comcode-concept-inline" data-cms-tooltip="{USERGROUP_SUBSCRIPTION_DESCRIPTION*}">{ITEM_NAME*}</span>
						{+END}
						{+START,IF_NON_PASSED,USERGROUP_SUBSCRIPTION_DESCRIPTION}
							{ITEM_NAME*}
						{+END}
					</th>

					<td>
						{$CURRENCY_SYMBOL,{CURRENCY}}{PRICE*}, {PER}
					</td>

					<td>
						{$CURRENCY_SYMBOL,{CURRENCY}}{TAX*}
					</td>

					<td>
						{START_TIME*}
						{+START,IF_NON_EMPTY,{EXPIRY_TIME}}
							&ndash; {EXPIRY_TIME*}
						{+END}
					</td>

					<td>
						{PAYMENT_GATEWAY*}
					</td>

					<td>
						{STATE*}
					</td>

					<td class="subscriptions-cancel-button">
						{+START,IF_PASSED,CANCEL_BUTTON}
							{CANCEL_BUTTON}
						{+END}
						{+START,IF_NON_PASSED,CANCEL_BUTTON}
							<a data-cms-confirm-click="{!SUBSCRIPTION_CANCEL_WARNING_GENERAL}" href="{$PAGE_LINK*,_SELF:_SELF:cancel:{SUBSCRIPTION_ID}}">{!SUBSCRIPTION_CANCEL}</a>
						{+END}
					</td>
				</tr>
			{+END}
		</tbody>
	</table>
{+END}

{+START,IF_EMPTY,{SUBSCRIPTIONS}}
	<p class="nothing-here">
		{!NO_ENTRIES}
	</p>
{+END}

<p class="buttons-group">
	<span class="buttons-group-inner">
		<a class="btn btn-primary btn-scr buttons--proceed" rel="add" href="{$PAGE_LINK*,_SEARCH:purchase:type_filter={$PRODUCT_SUBSCRIPTION}}"><span>{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!START_NEW_SUBSCRIPTION}</span></a>
	</span>
</p>
