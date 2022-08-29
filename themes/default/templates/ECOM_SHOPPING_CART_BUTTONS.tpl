{$,Embedded into catalogue views for example, not used for the shopping cart itself}

<div class="clearfix">
	<div class="add-to-cart">
		{+START,IF_NON_EMPTY,{ACTION_URL}}
			<div class="purchase-buy">
				<form title="{!ADD_TO_CART}" method="post" action="{ACTION_URL*}">
					{$INSERT_FORM_POST_SECURITY}

					<button class="btn btn-primary btn-scri buttons--cart-add"{+START,IF,{OUT_OF_STOCK}} data-click-alert="{!OUT_OF_STOCK}" data-click-pd="1"{+END} type="submit">{+START,INCLUDE,ICON}NAME=buttons/cart_add{+END} <span>{!ADD_TO_CART}</span></button>
					<input type="hidden" id="quantity" name="quantity" value="1" />
					<input type="hidden" name="type_code" value="{TYPE_CODE*}" />
				</form>
			</div>
		{+END}

		{$,Re-enable this if you want to be able to buy with bypassing the cart}
		{+START,SET,commented_out}
			{+START,IF_NON_EMPTY,{PURCHASE_ACTION_URL}}
				<div class="purchase-buy">
					<form title="{!BUY_NOW}" method="post" enctype="multipart/form-data" action="{PURCHASE_ACTION_URL*}">
						{$INSERT_FORM_POST_SECURITY}

						<button class="btn btn-primary btn-scri buttons--cart-checkout" type="submit">{+START,INCLUDE,ICON}NAME=buttons/cart_checkout{+END} <span>{!BUY_NOW}</span></button>
					</form>
				</div>
			{+END}
		{+END}
	</div>
</div>

{+START,IF,{$IS_GUEST}}
	<p class="associated-details">{!COOKIES_AS_GUEST}</p>
{+END}
