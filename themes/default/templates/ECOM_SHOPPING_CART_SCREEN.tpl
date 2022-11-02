{$REQUIRE_JAVASCRIPT,shopping}

<div data-tpl="ecomShoppingCartScreen" data-tpl-params="{+START,PARAMS_JSON,TYPE_CODES,EMPTY_CART_URL}{_*}{+END}">
	{TITLE}

	<form title="{!PRIMARY_PAGE_FORM}" action="{UPDATE_CART_URL*}" method="post" itemscope="itemscope" itemtype="http://schema.org/CheckoutPage" class="js-submission-flow" data-view="SubmissionFlow" data-view-params="{+START,PARAMS_JSON,JS_FUNCTION_CALLS}{_*}{+END}">
		{$INSERT_FORM_POST_SECURITY}

		{RESULTS_TABLE}

		<div class="cart-buttons">
			<div class="buttons-group cart-update-buttons" itemprop="significantLinks">
				<div class="buttons-group-inner">
					{$,Put first, so it associates with the enter key}
					{+START,IF_NON_EMPTY,{TYPE_CODES}}
						<button id="cart-update-button" class="btn btn-secondary btn-scr buttons--cart-update button-faded js-click-btn-cart-update" type="submit" name="update" title="{!UPDATE_CART}">{+START,INCLUDE,ICON}NAME=buttons/cart_update{+END} {!_UPDATE_CART}</button>
					{+END}

					{+START,IF_NON_EMPTY,{EMPTY_CART_URL}}
						<button class="btn btn-secondary btn-scri buttons--cart-empty js-click-btn-cart-empty" type="submit">{+START,INCLUDE,ICON}NAME=buttons/cart_empty{+END} <span>{!EMPTY_CART}</span></button>
					{+END}
				</div>
			</div>

			<div class="buttons-group cart-continue-button" itemprop="significantLinks">
				<div class="buttons-group-inner">
					<input type="hidden" name="type_codes" id="type_codes" value="{TYPE_CODES*}" />

					{+START,IF_NON_EMPTY,{CONTINUE_SHOPPING_URL}}
						<a class="btn btn-secondary btn-scri menu--rich-content--catalogues--products" href="{CONTINUE_SHOPPING_URL*}"><span>{+START,INCLUDE,ICON}NAME=menu/rich_content/catalogues/products{+END} {!CONTINUE_SHOPPING}</span></a>
					{+END}
				</div>
			</div>
		</div>
	</form>

	{+START,IF_NON_EMPTY,{TYPE_CODES}}
		<div class="cart-payment-line">
			{!SHIPPING}:
			<span class="tax">{$CURRENCY,{$ADD,{TOTAL_SHIPPING_COST},{TOTAL_SHIPPING_TAX}},{CURRENCY},{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}</span>
		</div>

		<div class="cart-payment-summary">
			{!GRAND_TOTAL}:
			<span class="price">{$CURRENCY,{GRAND_TOTAL},{CURRENCY},{$?,{$CONFIG_OPTION,currency_auto},{$CURRENCY_USER},{$CURRENCY}}}</span>
		</div>
	{+END}

	{+START,IF_NON_EMPTY,{EMPTY_CART_URL}}
		<form title="{!PRIMARY_PAGE_FORM}" method="post" enctype="multipart/form-data" action="{NEXT_URL*}">
			{$INSERT_FORM_POST_SECURITY}

			{+START,IF_PASSED,FIELDS}
				<div class="wide-table-wrap"><table class="map-table form-table wide-table">
					{+START,IF,{$NOT,{$MOBILE}}}
						<colgroup>
							<col class="purchase-field-name-column" />
							<col class="purchase-field-input-column" />
						</colgroup>
					{+END}

					<tbody>
						{FIELDS}
					</tbody>
				</table></div>
			{+END}

			<p class="purchase-button">
				<button id="proceed-button" class="btn btn-primary btn-scr buttons--proceed js-btn-main-submit-form" accesskey="u" type="button">{+START,INCLUDE,ICON}NAME=buttons/proceed{+END} {!CHECKOUT}</button>
			</p>
		</form>
	{+END}
</div>
