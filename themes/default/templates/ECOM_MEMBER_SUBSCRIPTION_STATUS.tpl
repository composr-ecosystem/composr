<div data-toggleable-tray="{}">
	<h2 class="toggleable-tray-unstyled">
		<a class="toggleable-tray-button js-tray-onclick-toggle-tray" href="#!" title="{!CONTRACT}">{+START,INCLUDE,ICON}
			NAME=trays/contract
			ICON_SIZE=20
		{+END}</a>
		<span class="js-tray-onclick-toggle-tray">{!MODULE_TRANS_NAME_subscriptions}</span>
	</h2>

	<div class="toggleable-tray js-tray-content" style="display: block">
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
						</td>

						<td>
							{PAYMENT_GATEWAY*}
						</td>
					</tr>
				{+END}
			</tbody>
		</table>

		<p class="associated-link suggested-link"><a title="{!MODULE_TRANS_NAME_subscriptions}" href="{$PAGE_LINK*,_SEARCH:subscriptions:browse:{MEMBER_ID}}">{!MORE}</a></p>
	</div>
</div>
