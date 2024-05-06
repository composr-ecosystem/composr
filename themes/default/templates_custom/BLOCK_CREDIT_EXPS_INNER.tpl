{$REQUIRE_JAVASCRIPT,cms_homesite_tracker}
{$SET,purchase_url,{$PAGE_LINK,_SEARCH:purchase:terms:member_id={$MEMBER}}}
<form action="{$URL_FOR_GET_FORM*,{$GET,purchase_url}}" method="get" style="margin-bottom: 0" data-tpl="blockCreditExpsInner">
	{$HIDDENS_FOR_GET_FORM,{$GET,purchase_url}}

	<div class="selCdt">
		<label for="type_code">{!SUPPORT_CREDITS_BUY}</label>
		<select name="type_code" id="type_code" class="form-control js-change-update-product-info-display">
			{+START,LOOP,CREDIT_KINDS}
				<option {+START,IF,{$EQ,{NUM_CREDITS},50}} selected="selected"{+END} value="{NUM_CREDITS*}_CREDITS">{$INTEGER_FORMAT*,{NUM_CREDITS},0} credits</option>
			{+END}
		</select>
	</div>

	{+START,LOOP,CREDIT_KINDS}
		<div class="creditsInfo" id="info-{NUM_CREDITS*}-credits">
			<p>{!BLOCK_CREDITS_EXP_INNER_MSG,{$INTEGER_FORMAT*,{NUM_CREDITS},0},{$CURRENCY,{PRICE},{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}}</p>

			<table class="columned-table topTble">
				<thead>
					<tr>
						<th>
							{!PRIORITY_LEVEL}
						</th>
						<th>
							{!NUMBER_OF_MINUTES}
						</th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<td>
							{!SUPPORT_PRIORITY_backburner}
						</td>
						<td>
							{!MINUTES,<strong>{$INTEGER_FORMAT*,{$MULT,{NUM_CREDITS},{BACKBURNER_MINUTES}}}</strong>}
						</td>
					</tr>

					<tr>
						<td>
							{!SUPPORT_PRIORITY_regular}
						</td>
						<td>
							{!MINUTES,<strong>{$INTEGER_FORMAT*,{$MULT,{NUM_CREDITS},{REGULAR_MINUTES}}}</strong>}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	{+END}

	<div class="purchaseBtn">
		<button type="submit">Purchase</button>
	</div>
</form>
