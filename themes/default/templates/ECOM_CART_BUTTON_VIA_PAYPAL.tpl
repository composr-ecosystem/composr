<form title="{!MAKE_PAYMENT}" class="ecommerce-button" action="{FORM_URL*}" method="post">
	<input type="hidden" name="cmd" value="_cart" />
	<input type="hidden" name="charset" value="{!charset}" />
	<input type="hidden" name="business" value="{PAYMENT_ADDRESS*}" />
	<input type="hidden" name="notify_url" value="{$FIND_SCRIPT*,ecommerce}?from=paypal" />
	<input type="hidden" name="return" value="{$PAGE_LINK*,_SEARCH:purchase:finish:type_code={TYPE_CODE}:from=paypal}" />
	<input type="hidden" name="cancel_return" value="{$PAGE_LINK*,_SEARCH:purchase:finish:type_code={TYPE_CODE*}:cancel=1:from=paypal}" />
	<input type="hidden" name="no_shipping" value="1" />
	<input type="hidden" name="shipping_1" value="{SHIPPING_COST*}" />
	<input type="hidden" name="tax_cart" value="{TAX*}" />
	<input type="hidden" name="currency_code" value="{CURRENCY*}" />
	<input type="hidden" name="custom" value="{TRANS_EXPECTING_ID*}" />
	<input type="hidden" name="rm" value="2" />
	<input type="hidden" name="bn" value="composr_SP" />
	{+START,IF_NON_EMPTY,{MEMBER_ADDRESS}}
		<!-- <input type="hidden" name="address_override" value="1" /> -->
		{+START,LOOP,MEMBER_ADDRESS}
			{+START,IF_NON_EMPTY,{_loop_key}}
				<input type="hidden" name="{_loop_key*}" value="{_loop_var*}" />
			{+END}
		{+END}
	{+END}

	<input type="hidden" name="upload" value="1" />
	{+START,LOOP,ITEMS}
		<input type="hidden" name="item_name_{$ADD*,1,{_loop_key}}" value="{PRODUCT_NAME*}" />
		<input type="hidden" name="amount_{$ADD*,1,{_loop_key}}" value="{PRICE*}" />
		<input type="hidden" name="quantity_{$ADD*,1,{_loop_key}}" value="{QUANTITY*}" />

		{+START,COMMENT}
			Composr does not support per-product options, but PayPal does, and will record the choice for you. Uncomment this bit as an example of asking for T-shirt sizes.

			<p>
				<input type="hidden" name="on0_{$ADD*,1,{_loop_key}}" value="Size" />
				<label for="os0_{$ADD*,1,{_loop_key}}">Size of {PRODUCT_NAME*} <span class="associated-details">(if applicable)</span></label>
				<select name="os0_{$ADD*,1,{_loop_key}}" id="os0_{$ADD*,1,{_loop_key}}" class="form-control">
					<option>Small</option>
					<option selected="selected">Medium</option>
					<option>Large</option>
					<option>Extra Large</option>
				</select>
			</p>
		{+END}
	{+END}

	<p class="purchase-button">
		<button id="purchase-button" data-disable-on-click="1" class="btn btn-primary btn-scr buttons--cart-checkout" type="submit" name="submit">{+START,INCLUDE,ICON}NAME=buttons/cart_checkout{+END} {!MAKE_PAYMENT}</button>
	</p>
</form>
